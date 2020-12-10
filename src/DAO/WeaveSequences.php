<?php
declare(strict_types = 1);

namespace Isfett\WowClassicHunterMeleeWeavingEfficiency\DAO;

/**
 * Class WeaveSequences
 */
class WeaveSequences
{
    /** @var array<WeaveSequence> */
    private $elements = [];

    /**
     * @return array
     */
    public function getElements(): array
    {
        return $this->elements;
    }

    public function getLast(): WeaveSequence
    {
        return end($this->elements);
    }

    public function addElement(WeaveSequence $weaveSequence): void
    {
        $this->elements[] = $weaveSequence;
    }

    public function removeElement(int $index): void
    {
        unset($this->elements[$index]);
    }

    public function count(): int
    {
        return count($this->elements);
    }
}
