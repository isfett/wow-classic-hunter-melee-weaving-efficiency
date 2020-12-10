<?php
declare(strict_types = 1);

namespace Isfett\WowClassicHunterMeleeWeavingEfficiency\Service;

use Isfett\WowClassicHunterMeleeWeavingEfficiency\DAO\Event;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\DAO\Fight;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\DAO\Player;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\DAO\WeaveSequences;

/**
 * Class MeleeWeaveService
 */
class MeleeWeaveService
{
    /** @var LogEventService */
    private $logEventService;

    /** @var EventService */
    private $eventService;

    /** @var WeaveSequenceService */
    private $weaveSequenceService;

    /**
     * MeleeWeaveService constructor.
     *
     * @param LogEventService $logEventService
     * @param EventService $eventService
     * @param WeaveSequenceService $weaveSequenceService
     */
    public function __construct(
        LogEventService $logEventService,
        EventService $eventService,
        WeaveSequenceService $weaveSequenceService
    ) {
        $this->logEventService = $logEventService;
        $this->eventService = $eventService;
        $this->weaveSequenceService = $weaveSequenceService;
    }

    public function getWeaveSequencesForPlayerInFight(string $reportHash, Player $player, Fight $fight): WeaveSequences
    {
        $weaveSequences = new WeaveSequences();

        $eventsForFight = $this->logEventService->getEventsForPlayerForFight($reportHash, $player, $fight);

        foreach ($eventsForFight as $eventTemp) {
            /*if ($eventTemp['type'] === 'damage') {
                var_dump($eventTemp);
                die();
            }*/
            $event = $this->eventService->createEvent($eventTemp['type'], $eventTemp['sourceID'], $eventTemp['targetID'] ?? 0, $eventTemp['timestamp'], $eventTemp['ability']['guid'], $eventTemp['amount'] ?? null);
            if (null !== $event) {
                $this->weaveSequenceService->addEventToWeaveSequence($event, $weaveSequences);
            }
        }
        $this->weaveSequenceService->purgeUnfinishedWeaveSequences($weaveSequences);
        return $weaveSequences;
    }
}
