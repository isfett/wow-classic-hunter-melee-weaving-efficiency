<?php
declare(strict_types = 1);

namespace Isfett\WowClassicHunterMeleeWeavingEfficiency\DAO;

/**
 * Class Event
 */
abstract class Event
{
    /** @var int */
    private $timestamp;

    /** @var Ability */
    private $ability;

    /** @var int */
    private $sourceId;

    /** @var int */
    private $targetId;

    /**
     * Event constructor.
     *
     * @param int      $sourceId
     * @param int      $targetId
     * @param int      $timestamp
     * @param Ability  $ability
     * @param int|null $damage
     */
    public function __construct(int $sourceId, int $targetId, int $timestamp, Ability $ability, ?int $damage = null)
    {
        $this->timestamp = $timestamp;
        $this->ability = $ability;
        $this->sourceId = $sourceId;
        $this->targetId = $targetId;
    }

    /**
     * @return int
     */
    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    /**
     * @return Ability
     */
    public function getAbility(): Ability
    {
        return $this->ability;
    }

    /**
     * @return int
     */
    public function getSourceId(): int
    {
        return $this->sourceId;
    }

    /**
     * @return int
     */
    public function getTargetId(): int
    {
        return $this->targetId;
    }
}
