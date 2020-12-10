<?php
declare(strict_types = 1);

namespace Isfett\WowClassicHunterMeleeWeavingEfficiency\DAO\Player;

use Isfett\WowClassicHunterMeleeWeavingEfficiency\DAO\Pet;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\DAO\Player;

/**
 * Class Hunter
 */
class Hunter extends Player
{
    /** @var Pet\Hunter */
    private $pet;

    /**
     * @return Pet\Hunter
     */
    public function getPet(): Pet\Hunter
    {
        return $this->pet;
    }

    /**
     * @param Pet\Hunter $pet
     *
     * @return void
     */
    public function setPet(Pet\Hunter $pet): void
    {
        $this->pet = $pet;
    }
}
