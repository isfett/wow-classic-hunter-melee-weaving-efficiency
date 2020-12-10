<?php
declare(strict_types = 1);

namespace Isfett\WowClassicHunterMeleeWeavingEfficiency\DAO;

use Isfett\WowClassicHunterMeleeWeavingEfficiency\DAO\Event\Damage;

/**
 * Class CombinedEvent
 */
class CombinedEvent
{
    /** @var Event|null */
    private $beginCast;

    /** @var Event|null */
    private $cast;

    /** @var Damage|null */
    private $damage;

    /** @var Ability|null */
    private $ability;

    /**
     * @return Event|null
     */
    public function getBeginCast(): ?Event
    {
        return $this->beginCast;
    }

    /**
     * @param Event $beginCast
     *
     * @return void
     */
    public function setBeginCast(Event $beginCast): void
    {
        $this->beginCast = $beginCast;
    }

    /**
     * @return Event|null
     */
    public function getCast(): ?Event
    {
        return $this->cast;
    }

    /**
     * @param Event $cast
     *
     * @return void
     */
    public function setCast(Event $cast): void
    {
        $this->cast = $cast;
    }

    /**
     * @return Damage|null
     */
    public function getDamage(): ?Damage
    {
        return $this->damage;
    }

    /**
     * @param Event $damage
     *
     * @return void
     */
    public function setDamage(Event $damage): void
    {
        $this->damage = $damage;
    }

    /**
     * @return Ability|null
     */
    public function getAbility(): ?Ability
    {
        return $this->ability;
    }

    /**
     * @param Ability $ability
     *
     * @return void
     */
    public function setAbility(Ability $ability): void
    {
        $this->ability = $ability;
    }

    /**
     * @return bool
     */
    public function isFinished(): bool
    {
        return null !== $this->beginCast && null !== $this->cast && null !== $this->damage;
    }
}
