<?php
declare(strict_types = 1);

namespace Isfett\WowClassicHunterMeleeWeavingEfficiency\Service;

use Isfett\WowClassicHunterMeleeWeavingEfficiency\DAO\Ability\Melee;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\DAO\Ability\RaptorStrike;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\DAO\CombinedEvent;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\DAO\Event;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\DAO\MeleeAbility;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\DAO\RangedAbility;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\DAO\WeaveSequence;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\DAO\WeaveSequences;

/**
 * Class WeaveSequenceService
 */
class WeaveSequenceService
{
    public function addEventToWeaveSequence(Event $event, WeaveSequences $weaveSequences): void
    {
        if ($weaveSequences->count()) {
            $weaveSequence = $weaveSequences->getLast();
            if ($this->isWeaveSequenceFinished($weaveSequence)) {
                $weaveSequence = new WeaveSequence();
                $weaveSequences->addElement($weaveSequence);
            }
        } else {
            $weaveSequence = new WeaveSequence();
            $weaveSequences->addElement($weaveSequence);
        }
        $this->addEventToCombinedEvent($event, $weaveSequence);
    }

    public function purgeUnfinishedWeaveSequences(WeaveSequences $weaveSequences): void
    {
        /** @var WeaveSequence $weaveSequence */
        foreach ($weaveSequences->getElements() as $weaveSequenceKey => $weaveSequence) {
            if (!$weaveSequence->isFinished()) {
                $weaveSequences->removeElement($weaveSequenceKey);
            }
        }
    }

    private function isWeaveSequenceFinished(WeaveSequence $weaveSequence): bool
    {
        return count($weaveSequence->getMeleeHits()) >= 1 && null !== $weaveSequence->getShotBefore() && $weaveSequence->getShotBefore()->isFinished() && null !== $weaveSequence->getShotAfter() && $weaveSequence->getShotAfter()->isFinished();
    }

    private function addEventToCombinedEvent(Event $event, WeaveSequence $weaveSequence): void
    {
        $type = $this->classNameWithoutNamespace($event);
        if ($event instanceof Event\Cast && $event->getAbility() instanceof MeleeAbility) {
            $newEvent = new Event\BeginCast($event->getSourceId(), $event->getTargetId(), $event->getTimestamp(), $event->getAbility());
            $this->addEventToCombinedEvent($newEvent, $weaveSequence);
        }
        echo $type.' - '.$this->classNameWithoutNamespace($event->getAbility()).PHP_EOL;
        if ($event instanceof Event\BeginCast) {
            $combinedEvent = new CombinedEvent();
            $combinedEvent->setAbility($event->getAbility());

            if ($event->getAbility() instanceof RangedAbility && !$weaveSequence->isFinished() && null !== $weaveSequence->getShotBefore() && count($weaveSequence->getMeleeHits()) === 0) {
                $weaveSequence->reset();
            }

            try {
                $weaveSequence->addEvent($combinedEvent, $event->getAbility());
            } catch (\RuntimeException $e) {
                echo $e->getMessage().PHP_EOL;
                return;
            }
        } else {
            $combinedEvent = $weaveSequence->getLast($event->getAbility());
            if (null === $combinedEvent) {
                $combinedEvent = new CombinedEvent();
                $combinedEvent->setAbility($event->getAbility());
                try {
                    $weaveSequence->addEvent($combinedEvent, $event->getAbility());
                } catch (\RuntimeException $e) {
                    echo $e->getMessage().PHP_EOL;
                    return;
                }
            }
        }

        if (get_class($event->getAbility()) === get_class($combinedEvent->getAbility())) {
            $setterName = 'set'.$type;
            $combinedEvent->$setterName($event);
        }
    }

    private function classNameWithoutNamespace($classObject): string
    {
        return (new \ReflectionClass($classObject))->getShortName();
    }
}
