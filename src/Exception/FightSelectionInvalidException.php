<?php
declare(strict_types = 1);

namespace Isfett\WowClassicHunterMeleeWeavingEfficiency\Exception;

/**
 * Class FightSelectionInvalidException
 */
class FightSelectionInvalidException extends \RuntimeException
{
    /**
     * FightSelectionInvalidException constructor.
     *
     * @param string          $fightId
     * @param \Throwable|null $previous
     */
    public function __construct(string $fightId, ?\Throwable $previous = null)
    {
        parent::__construct(sprintf('Fight %s does not exist', $fightId), 0, $previous);
    }
}
