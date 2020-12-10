<?php
declare(strict_types = 1);

namespace Isfett\WowClassicHunterMeleeWeavingEfficiency\Service;

use Isfett\WowClassicHunterMeleeWeavingEfficiency\DAO\Ability;

/**
 * Class AbilityService
 */
class AbilityService
{
    private const GUID_MAP = [
        //14290 => Ability\MultiShot::class,
        75 => Ability\AutoShot::class,
        //20904 => Ability\AimedShot::class,
        1 => Ability\Melee::class,
        14266 => Ability\RaptorStrike::class
    ];

    /**
     * @param int $guid
     *
     * @return Ability|null
     */
    public function createAbility(int $guid): ?Ability
    {
        if (!array_key_exists($guid, self::GUID_MAP)) {
            return null;
        }
        $className = self::GUID_MAP[$guid];

        return new $className();
    }
}
