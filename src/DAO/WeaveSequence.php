<?php
declare(strict_types = 1);

namespace Isfett\WowClassicHunterMeleeWeavingEfficiency\DAO;

/**
 * Class WeaveSequence
 */
class WeaveSequence
{
    /** @var CombinedEvent|null */
    private $shotBefore;

    /** @var array<CombinedEvent> */
    private $meleeHits = [];

    /** @var CombinedEvent|null */
    private $shotAfter;

    /**
     * @return CombinedEvent|null
     */
    public function getShotBefore(): ?CombinedEvent
    {
        return $this->shotBefore;
    }

    /**
     * @param CombinedEvent $shotBefore
     *
     * @return void
     */
    public function setShotBefore(CombinedEvent $shotBefore): void
    {
        $this->shotBefore = $shotBefore;
    }

    /**
     * @return array<CombinedEvent>
     */
    public function getMeleeHits(): array
    {
        return $this->meleeHits;
    }

    /**
     * @param CombinedEvent $meleeAbility
     *
     * @return void
     */
    public function addMeleeHit(CombinedEvent $meleeAbility): void
    {
        $this->meleeHits[] = $meleeAbility;
    }

    /**
     * @return CombinedEvent|null
     */
    public function getShotAfter(): ?CombinedEvent
    {
        return $this->shotAfter;
    }

    /**
     * @param CombinedEvent $shotAfter
     *
     * @return void
     */
    public function setShotAfter(CombinedEvent $shotAfter): void
    {
        $this->shotAfter = $shotAfter;
    }

    public function getLast(Ability $ability): ?CombinedEvent
    {
        if (null !== $this->shotBefore && !$this->shotBefore->isFinished() && $ability instanceof RangedAbility) {
            return $this->shotBefore;
        }

        if ($ability instanceof MeleeAbility && count($this->meleeHits)) {
            /** @var CombinedEvent $meleeHit */
            foreach ($this->meleeHits as $meleeHit) {
                if (!$meleeHit->isFinished()) {
                    return $meleeHit;
                }
            }
        }

        if (null !== $this->shotAfter && !$this->shotAfter->isFinished() && $ability instanceof RangedAbility) {
            return $this->shotAfter;
        }

        return null;
    }

    public function isFinished(): bool
    {
        return null !== $this->shotBefore && count($this->meleeHits) && null !== $this->shotAfter;
    }

    public function addEvent(CombinedEvent $event, Ability $ability): void
    {
        if (null === $this->shotBefore && $ability instanceof RangedAbility) {
            $this->setShotBefore($event);
            return;
        }

        if (null !== $this->shotBefore && $ability instanceof MeleeAbility) {
            $this->addMeleeHit($event);
            return;
        }

        if (null === $this->shotAfter && $ability instanceof RangedAbility) {
            if (count($this->meleeHits)) {
                $this->setShotAfter($event);
            } else {
                $this->setShotBefore($event);
            }
            return;
        }

        throw new \RuntimeException('not added');
    }

    public function reset(): void
    {
        $this->shotBefore = null;
        $this->shotAfter = null;
        $this->meleeHits = [];
    }
}
