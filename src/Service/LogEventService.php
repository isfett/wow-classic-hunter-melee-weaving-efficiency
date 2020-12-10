<?php
declare(strict_types = 1);

namespace Isfett\WowClassicHunterMeleeWeavingEfficiency\Service;

use Isfett\WowClassicHunterMeleeWeavingEfficiency\DAO\Fight;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\DAO\Player;

/**
 * Class LogEventService
 */
class LogEventService
{
    /** @var WebClientService */
    private $webClientService;

    /**
     * LogEventService constructor.
     *
     * @param WebClientService $webClientService
     */
    public function __construct(WebClientService $webClientService)
    {
        $this->webClientService = $webClientService;
    }

    /**
     * @param string        $reportHash
     * @param Player\Hunter $player
     * @param Fight         $fight
     * @param int|null      $overrideStartTime
     *
     * @return array
     */
    public function getEventsForPlayerForFight(
        string $reportHash,
        Player\Hunter $player,
        Fight $fight,
        ?int $overrideStartTime = null
    ): array {
        $eventsResponse = $this->webClientService->get(sprintf('summary-events/%x0/Any/0/-1.0.-1/0', $reportHash, 
            $fight->getId(), $overrideStartTime ?? $fight->getStartTime(), $fight->getEndTime(), $player->getId()));
        $eventsJson = json_decode($eventsResponse, true);
        $events = $eventsJson['events'];

        if (array_key_exists('nextPageTimestamp', $eventsJson)) {
            $nextPage = $this->getEventsForPlayerForFight($reportHash, $player, $fight, $eventsJson['nextPageTimestamp']);
            $events = array_merge($events, $nextPage);
        }

        if (null === $overrideStartTime) {
            foreach ($events as $eventKey => $event) {
                if (array_key_exists('targetIsFriendly', $event) && $event['targetIsFriendly']) {
                    unset($events[$eventKey]);
                }
                if (array_key_exists('sourceID', $event) && $event['sourceID'] === $player->getPet()->getId()) {
                    unset($events[$eventKey]);
                }
                if ('combatantinfo' === $event['type']) {
                    unset($events[$eventKey]);
                }
            }
        }

        return $events;
    }
}
