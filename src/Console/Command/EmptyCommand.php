<?php
declare(strict_types = 1);

namespace Isfett\WowClassicHunterMeleeWeavingEfficiency\Console\Command;

use http\Client;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;

/**
 * Class MagicNumberDetectorCommand
 */
class EmptyCommand extends AbstractCommand
{
    /**
     * MagicNumberDetectorCommand constructor.
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct() {
        parent::__construct('empty');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function run(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');
        $reportQuestion = new Question('Enter report hash: ');
        $reportHash = $helper->ask($input, $output, $reportQuestion);
        $baseUrl = 'https://classic.warcraftlogs.com/reports/';
        $reportUrl = sprintf('%s/%s/', $baseUrl, $reportHash);

        $webClient = HttpClient::createForBaseUri($baseUrl);
        $reportOverview = $webClient->request('GET',$reportHash);

        $crawler = new Crawler();
        $crawler->addHtmlContent($reportOverview->getContent());
        $fightSelectionNode = $crawler->filter('#report-fight-selection-area');
        if (0 === $fightSelectionNode->count()) {
            throw new \RuntimeException('Report not found');
        }
        $players = [];
        $hunterPets = [];
        $fights = ['bosses' => [], 'trash' => []];
        $overviewJson = $webClient->request('GET', sprintf('fights-and-participants/%s/0', $reportHash));
        $overviewJson = json_decode($overviewJson->getContent(), false);
        foreach ($overviewJson->friendlies as $friendly) {
            if ('Hunter' !== $friendly->type) {
                continue;
            }
            $players[$friendly->id] = $friendly;
        }
        foreach ($overviewJson->friendlyPets as $friendlyPet) {
            if ('Pet' !== $friendlyPet->type) {
                continue;
            }
            if (!array_key_exists($friendlyPet->petOwner, $players)) {
                continue;
            }
            $hunterPets[$friendlyPet->petOwner] = $friendlyPet;
        }
        foreach ($overviewJson->fights as $fight) {
            if (property_exists($fight, 'originalBoss')) {
                continue;
            }
            $fightKey = 'trash';
            if (property_exists($fight, 'boss') && $fight->boss > 0) {
                $fightKey = 'bosses';
            }
            $fights[$fightKey][$fight->id] = $fight;
        }

        $hunterQuestion = new Question(
            'Select a hunter by ID'.\PHP_EOL.' > ',
            null
        );
        $hunterQuestion->setValidator(function($value) use ($players) {
            if (!array_key_exists($value, $players)) {
                throw new \RuntimeException('Id not existent');
            }
            return $value;
        });
        $output->writeln('Available Hunters:');
        foreach ($players as $player) {
            $output->writeln('Name: '.$player->name.', ID: '.$player->id);
        }
        $hunterResult = $helper->ask($input, $output, $hunterQuestion);
        $hunter = $players[$hunterResult];
        $hunterPet = $hunterPets[$hunter->id];
        $output->writeln('Fights:');
        foreach ($fights as $fightType => $fightsTemp) {
            foreach ($fightsTemp as $fightId=>$fight) {
                $output->writeln('Type: '.$fightType.', Name: '.$fight->name.', ID: '.$fight->id);
            }
        }
        $fightQuestion = new Question(
            'Select a fight by ID'.\PHP_EOL.' > ',
            null
        );
        $fightQuestion->setValidator(function($value) use ($fights) {
            if (!array_key_exists($value, $fights['bosses']) && !array_key_exists($value, $fights['trash'])) {
                throw new \RuntimeException('Id not existent');
            }
            return $value;
        });
        $fightResult = $helper->ask($input, $output, $fightQuestion);
        $fight = null;
        if (array_key_exists($fightResult, $fights['bosses'])) {
            $fight = $fights['bosses'][$fightResult];
        } else {
            $fight = $fights['trash'][$fightResult];
        }
        $output->writeln('Showing Weaving-Timers for '.$hunter->name.' for fight '.$fight->name);

        $events = $this->getEvents($webClient, $reportHash, $fight->id, $fight->start_time, $fight->end_time, $hunter->id);

        $counter = 0;
        $weaveSequence = 0;
        $filteredEvents = [];
        foreach ($events as $event) {
            if (in_array($event->type, ['combatantinfo', 'applybuff', 'removebuff', 'refreshbuff', 'refreshdebuff', 'removedebuff', 'applydebuff', 'energize', 'heal'], true)) {
                continue;
            }
            if (property_exists($event, 'sourceID') && $event->sourceID !== $hunter->id) {
                continue;
            }
            //if (in_array($event->ability->guid, [14290 /* Multi-Shot */, 20904 /* Aimed Shot */, 14287 /* Arcane Shot */, 6150 /* Quickshots */, 3045 /* Rapid Fire */, 24352 /* Devilsaur Fury */, 14268 /* Wing Clip */])) {
            //    continue;
            //}
            if (!in_array($event->ability->guid, [1 /* Melee */, 75 /* Auto Shot */, 14266 /* Raptor Strike */])) {
                continue;
            }
            $type = $event->type;
            if (in_array($event->ability->guid, [14266 /* Raptor Strike */, 1 /* Melee */], true) && $type === 'cast') {
                $type = 'begincast';
            }
            $abilityName = strtolower(str_replace(' ','', $event->ability->name));

            if (!array_key_exists($weaveSequence, $filteredEvents)) {
                $filteredEvents[$weaveSequence] = [];
            }

            //print_r($event);
            if ('begincast' === $type) {
                if (count($filteredEvents[$weaveSequence]) && $event->ability->guid === 75 /* Auto Shot */ && end($filteredEvents[$weaveSequence])['begincast']->ability->guid === 75 /* Auto Shot */) {
                    array_pop($filteredEvents[$weaveSequence]);
                }
                $filteredEvents[$weaveSequence][] = [
                    'begincast' => $event,
                    'cast' => $event,
                    'damage' => null,
                ];
            } else {
                $filteredEvent = array_pop($filteredEvents[$weaveSequence]);
                $filteredEvent[$type] = $event;
                $filteredEvents[$weaveSequence][] = $filteredEvent;
            }
            if (count($filteredEvents[$weaveSequence]) >= 3 && $type === 'damage' && in_array($event->ability->guid, [75 /* Auto Shot */, 14290 /* Multi Shot */], true) && in_array($filteredEvents[$weaveSequence][count($filteredEvents[$weaveSequence]) - 2]['damage']->ability->guid,[14266 /* Raptor Strike */, 1 /* Melee */], true)) {
                $weaveSequence++;
            }

            //print_r($event);
            $counter++;
        }

        $fightStartTime = $fight->start_time;
        foreach ($filteredEvents as $weaveSequence => $events) {
            if (count($events) < 3) {
                continue;
            }
            $firstEvent = null;
            $lastEvent = null;
            echo 'Sequence: '.$weaveSequence;
            echo PHP_EOL;
            foreach ($events as $event) {
                if (null === $firstEvent) {
                    $firstEvent = $event;
                }
                //var_dump($event);
                echo $this->formatMilliseconds($event['begincast']->timestamp - $fightStartTime).': '.$event['begincast']->ability->name.' - '.($event['damage']->amount ?? '0');
                echo PHP_EOL;
                $lastEvent = $event;
            }
            $firstAuto = $firstEvent['begincast']->timestamp;
            $secondAuto = $lastEvent['begincast']->timestamp;
            $timeElapsed = $secondAuto - $firstAuto;
            echo 'Elapsed time between Auto Shots (begincast): '.$this->formatMilliseconds($timeElapsed);
            echo PHP_EOL;
            echo PHP_EOL;
        }

        return 0;
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setName('empty')
            ->setHelp('help');
    }

    private function formatMilliseconds(int $milliSeconds): string
    {
        $uSec = str_pad((string) ($milliSeconds % 1000), 3, "0",STR_PAD_LEFT);
        $input = floor($milliSeconds / 1000);

        $seconds = str_pad((string) ($input % 60), 2, "0", STR_PAD_LEFT);
        $input = floor($input / 60);

        $minutes = str_pad((string) ($input % 60), 2, "0", STR_PAD_LEFT);
        $input = floor($input / 60);
        //var_dump($milliSeconds);
        return $minutes.':'.$seconds.'.'.$uSec;
    }

    /**
     * @param \Symfony\Contracts\HttpClient\HttpClientInterface $webClient
     * @param                                                   $reportHash
     * @param                                                   $fight
     * @param                                                   $hunter
     *
     * @return mixed
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    private function getEvents(
        \Symfony\Contracts\HttpClient\HttpClientInterface $webClient,
        $reportHash,
        int $fightId,
        int $startTime,
        int $endTime,
        ?int $playerId
    ): array {
        $eventsJson = $webClient->request('GET',
            sprintf('summary-events/%s/%s/%s/%s/%s/0/Any/0/-1.0.-1/0', $reportHash, $fightId, $startTime,
                $endTime, $playerId));
        $eventsJson = json_decode($eventsJson->getContent(), false);
        $events = $eventsJson->events;

        if (property_exists($eventsJson, 'nextPageTimestamp')) {
            $nextPage = $this->getEvents($webClient, $reportHash, $fightId, $eventsJson->nextPageTimestamp, $endTime, $playerId);
            $events = array_merge($events, $nextPage);
        }

        return $events;
    }
}
