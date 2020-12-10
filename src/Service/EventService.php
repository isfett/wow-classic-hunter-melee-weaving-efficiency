<?php
declare(strict_types = 1);

namespace Isfett\WowClassicHunterMeleeWeavingEfficiency\Service;

use Isfett\WowClassicHunterMeleeWeavingEfficiency\DAO\Event;

/**
 * Class EventService
 */
class EventService
{
    /** @var AbilityService */
    private $abilityService;

    /**
     * EventService constructor.
     *
     * @param AbilityService $abilityService
     */
    public function __construct(AbilityService $abilityService)
    {
        $this->abilityService = $abilityService;
    }

    /**
     * @param string   $type
     * @param int      $sourceId
     * @param int      $targetId
     * @param int      $timestamp
     * @param int      $abilityGuid
     * @param int|null $damage
     *
     * @return Event|null
     */
    public function createEvent(string $type, int $sourceId, int $targetId, int $timestamp, int $abilityGuid, ?int $damage = null): ?Event
    {
        $ability = $this->abilityService->createAbility($abilityGuid);
        if (null === $ability) {
            return null;
        }

        $type = ucfirst($type);
        if ('Begincast' === $type) {
            $type = 'BeginCast';
        }
        $eventClassName = 'Isfett\\WowClassicHunterMeleeWeavingEfficiency\\DAO\\Event\\'.$type;
        $event = null;
        if (class_exists($eventClassName)) {
            $event = new $eventClassName($sourceId, $targetId, $timestamp, $ability, $damage);
        }

        return $event;
    }
}
