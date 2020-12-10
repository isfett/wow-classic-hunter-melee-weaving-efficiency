<?php
declare(strict_types = 1);

namespace Isfett\WowClassicHunterMeleeWeavingEfficiency\Exception;

/**
 * Class EmptyHunterSelectionException
 */
class EmptyHunterSelectionException extends \RuntimeException
{
    /**
     * EmptyHunterSelectionException constructor.
     *
     * @param \Throwable|null $previous
     */
    public function __construct(?\Throwable $previous = null)
    {
        parent::__construct('No hunter selected', 0, $previous);
    }
}
