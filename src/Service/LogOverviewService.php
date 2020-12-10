<?php
declare(strict_types = 1);

namespace Isfett\WowClassicHunterMeleeWeavingEfficiency\Service;

use Isfett\WowClassicHunterMeleeWeavingEfficiency\DAO\Fight;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\DAO\Player;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\DAO\Pet;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\DAO\ReportOverview;

/**
 * Class LogOverviewService
 */
class LogOverviewService
{
    /** @var WebClientService */
    private $webClientService;

    /** @var DomCrawlerService */
    private $domCrawlerService;

    /**
     * LogOverviewService constructor.
     *
     * @param WebClientService  $webClientService
     * @param DomCrawlerService $domCrawlerService
     */
    public function __construct(WebClientService $webClientService, DomCrawlerService $domCrawlerService)
    {
        $this->webClientService = $webClientService;
        $this->domCrawlerService = $domCrawlerService;
    }

    /**
     * @param string $reportHash
     *
     * @return bool
     */
    public function isReportHashValid(string $reportHash): bool
    {
        $html = $this->webClientService->get($reportHash);
        $count = $this->domCrawlerService->countFilteredNodes($html, '#report-fight-selection-area');

        return 0 !== $count;
    }

    /**
     * @param string $reportHash
     *
     * @return ReportOverview
     */
    public function fetchReportOverview(string $reportHash): ReportOverview
    {
        $reportOverview = new ReportOverview();

        $overviewResponse = $this->webClientService->get(sprintf('fights-and-participants/%s/0', $reportHash));
        $overviewJson = json_decode($overviewResponse, true);

        foreach ($overviewJson['friendlies'] as $friendly) {
            if ('Hunter' !== $friendly['type']) {
                continue;
            }
            $reportOverview->addPlayer(new Player\Hunter($friendly['id'], $friendly['name']));
        }

        foreach ($overviewJson['friendlyPets'] as $friendlyPet) {
            if ('Pet' !== $friendlyPet['type']) {
                continue;
            }
            if (!array_key_exists($friendlyPet['petOwner'], $reportOverview->getPlayers())) {
                continue;
            }
            $pet = new Pet\Hunter($friendlyPet['id'], $friendlyPet['name']);
            $reportOverview->addPet($pet);
            $reportOverview->getPlayers()[$friendlyPet['petOwner']]->setPet($pet);
        }

        foreach ($overviewJson['fights'] as $fight) {
            if (array_key_exists('originalBoss', $fight)) {
                continue;
            }

            if (array_key_exists('boss', $fight) && $fight['boss'] > 0) {
                $fightClass = Fight\Boss::class;
            } else {
                $fightClass = Fight\Trash::class;
            }

            $fight = new $fightClass(
                $fight['id'],
                $fight['name'],
                $fight['start_time'],
                $fight['end_time']
            );

            $reportOverview->addFight($fight);
        }

        return $reportOverview;
    }
}
