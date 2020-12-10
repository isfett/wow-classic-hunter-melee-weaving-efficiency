<?php
declare(strict_types = 1);

namespace Isfett\WowClassicHunterMeleeWeavingEfficiency\DAO;

use Isfett\WowClassicHunterMeleeWeavingEfficiency\DAO\Pet;

/**
 * Class ReportOverview
 */
class ReportOverview
{
    /** @var array<Player\Hunter> */
    private $players;

    /** @var iterable<Pet\Hunter> */
    private $pets;

    /** @var iterable<Fight\Boss> */
    private $bossFights;

    /** @var iterable<Fight\Trash> */
    private $trashFights;

    /**
     * @return array<Player\Hunter>
     */
    public function getPlayers(): iterable
    {
        return $this->players;
    }

    /**
     * @return iterable<Pet\Hunter>
     */
    public function getPets(): iterable
    {
        return $this->pets;
    }

    /**
     * @return iterable<Fight\Boss>
     */
    public function getBossFights(): iterable
    {
        return $this->bossFights;
    }

    /**
     * @return iterable<Fight\Trash>
     */
    public function getTrashFights(): iterable
    {
        return $this->trashFights;
    }

    /**
     * @param Player\Hunter $player
     *
     * @return void
     */
    public function addPlayer(Player\Hunter $player): void
    {
        $this->players[$player->getId()] = $player;
    }

    /**
     * @param Pet\Hunter $pet
     *
     * @return void
     */
    public function addPet(Pet\Hunter $pet): void
    {
        $this->pets[$pet->getId()] = $pet;
    }

    /**
     * @param Fight\Boss $fight
     *
     * @return void
     */
    private function addBossFight(Fight\Boss $fight): void
    {
        $this->bossFights[$fight->getId()] = $fight;
    }

    /**
     * @param Fight\Trash $fight
     *
     * @return void
     */
    private function addTrashFight(Fight\Trash $fight): void
    {
        $this->trashFights[$fight->getId()] = $fight;
    }

    /**
     * @param Fight $fight
     *
     * @return void
     */
    public function addFight(Fight $fight): void
    {
        if ($fight instanceof Fight\Boss) {
            $this->addBossFight($fight);
        } else {
            $this->addTrashFight($fight);
        }
    }
}
