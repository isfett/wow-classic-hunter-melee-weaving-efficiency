<?php
declare(strict_types = 1);

namespace Isfett\WowClassicHunterMeleeWeavingEfficiency\Console\Command;

use http\Client;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\Console\AbstractCommand;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\DAO\CombinedEvent;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\DAO\Fight\Boss;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\DAO\Player\Hunter;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\DAO\ReportOverview;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\DAO\WeaveSequence;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\Service\LogOverviewService;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\Service\MeleeWeaveService;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\Validator;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;

/**
 * Class WeaveTimingsCommand
 */
class WeaveTimingsCommand extends AbstractCommand
{
    /** @var QuestionHelper */
    private $questionHelper;

    /** @var Validator\Question\ReportHash */
    private $reportHashValidator;

    /** @var LogOverviewService */
    private $logOverviewService;

    /** @var MeleeWeaveService */
    private $meleeWeaveService;

    /**
     * WeaveTimingsCommand constructor.
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     * @param Validator\Question\ReportHash $reportHashValidator
     * @param LogOverviewService            $logOverviewService
     */
    public function __construct(Validator\Question\ReportHash $reportHashValidator, LogOverviewService $logOverviewService, MeleeWeaveService $meleeWeaveService) {
        parent::__construct('weave-timings');

        $this->reportHashValidator = $reportHashValidator;
        $this->logOverviewService = $logOverviewService;
        $this->meleeWeaveService = $meleeWeaveService;
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
        $this->questionHelper = $this->getHelper('question');

        $reportHash = $this->askForReportHash($input, $output);
        $reportOverview = $this->logOverviewService->fetchReportOverview($reportHash);
        $hunterId = $this->askForHunter($reportOverview, $input, $output);
        /** @var Hunter $hunter */
        $hunter = $reportOverview->getPlayers()[$hunterId];
        $running = true;
        while ($running) {
            $fightId = $this->askForFight($reportOverview, $input, $output);
            if (null === $fightId) {
                break;
            }
            /** @var Boss $fight */
            $fight = $reportOverview->getBossFights()[$fightId];
            $fightStartTime = $fight->getStartTime();
            $weaveSequences = $this->meleeWeaveService->getWeaveSequencesForPlayerInFight($reportHash, $hunter, $fight);
            /** @var WeaveSequence $weaveSequence */
            $weaveSequenceCounter = 1;
            $output->writeln('Showing Weaving-Timers for '.$hunter->getName().' for fight '.$fight->getName());
            foreach ($weaveSequences->getElements() as $weaveSequence) {
                echo 'Sequence: '.$weaveSequenceCounter.PHP_EOL;
                echo $this->formatMilliseconds($weaveSequence->getShotBefore()->getBeginCast()->getTimestamp() - $fightStartTime).': '.$this->getClassnameWithoutNamespace($weaveSequence->getShotBefore()->getBeginCast()->getAbility()).' - '.($weaveSequence->getShotBefore()->getDamage()->getAmount() ?? '0').PHP_EOL;
                /** @var CombinedEvent $meleeHit */
                foreach ($weaveSequence->getMeleeHits() as $meleeHit) {
                    echo $this->formatMilliseconds($meleeHit->getBeginCast()->getTimestamp() - $fightStartTime).': '.$this->getClassnameWithoutNamespace($meleeHit->getBeginCast()->getAbility()).' - '.($meleeHit->getDamage()->getAmount() ?? '0').PHP_EOL;
                }
                echo $this->formatMilliseconds($weaveSequence->getShotAfter()->getBeginCast()->getTimestamp() - $fightStartTime).': '.$this->getClassnameWithoutNamespace($weaveSequence->getShotAfter()->getBeginCast()->getAbility()).' - '.($weaveSequence->getShotAfter()->getDamage()->getAmount() ?? '0').PHP_EOL;
                $timeElapsed = $weaveSequence->getShotAfter()->getBeginCast()->getTimestamp() - $weaveSequence->getShotBefore()->getBeginCast()->getTimestamp();
                echo PHP_EOL;
                echo 'Elapsed time between Auto Shots (begincast): '.$this->formatMilliseconds($timeElapsed);
                echo PHP_EOL.PHP_EOL;

                $weaveSequenceCounter++;
            }
        }
        var_dump('end');
        die();
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
            ->setName('weave-timings')
            ->setHelp('help');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return string
     */
    private function askForReportHash(InputInterface $input, OutputInterface $output): string
    {
        $reportQuestion = new Question('Enter report hash: ');
        $reportQuestion->setValidator([$this->reportHashValidator, 'validate']);
        $reportHash = $this->questionHelper->ask($input, $output, $reportQuestion);

        return $reportHash;
    }

    /**
     * @param ReportOverview  $reportOverview
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return string
     */
    private function askForHunter(ReportOverview $reportOverview, InputInterface $input, OutputInterface $output): string
    {
        $hunterQuestion = new Question(
            'Select a hunter by ID'.\PHP_EOL.' > ',
            null
        );
        $hunterQuestion->setValidator([new Validator\Question\HunterSelection($reportOverview), 'validate']);
        $output->writeln('Available Hunters:');
        foreach ($reportOverview->getPlayers() as $player) {
            $output->writeln('Name: '.$player->getName().', ID: '.$player->getId());
        }
        $hunterResult = $this->questionHelper->ask($input, $output, $hunterQuestion);

        return $hunterResult;
    }

    /**
     * @param ReportOverview  $reportOverview
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return string|null
     */
    private function askForFight(ReportOverview $reportOverview, InputInterface $input, OutputInterface $output): ?string
    {
        $fightQuestion = new Question(
            'Select a fight by ID (leave empty for ending the script)'.\PHP_EOL.' > ',
            null
        );
        $fightQuestion->setValidator([new Validator\Question\FightSelection($reportOverview), 'validate']);
        $output->writeln('Available Fights:');
        foreach ($reportOverview->getBossFights() as $fight) {
            $output->writeln('Name: '.$fight->getName().', ID: '.$fight->getId());
        }
        $fightResult = $this->questionHelper->ask($input, $output, $fightQuestion);

        return '' === $fightResult ? null : $fightResult;
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
}
