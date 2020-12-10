<?php
declare(strict_types = 1);

namespace Isfett\WowClassicHunterMeleeWeavingEfficiency\DAO\Event;

use Isfett\WowClassicHunterMeleeWeavingEfficiency\DAO\Ability;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\DAO\Event;

/**
 * Class Damage
 */
class Damage extends Event
{
    /** @var int|null */
    private $amount;

    public function __construct(int $sourceId, int $targetId, int $timestamp, Ability $ability, ?int $damage = null)
    {
        parent::__construct($sourceId, $targetId, $timestamp, $ability);

        $this->amount = $damage;
    }

    /**
     * @return int|null
     */
    public function getAmount(): ?int
    {
        return $this->amount;
    }
}
