<?php
declare(strict_types = 1);

namespace Isfett\WowClassicHunterMeleeWeavingEfficiency\Exception;

/**
 * Class HunterSelectionInvalidException
 */
class HunterSelectionInvalidException extends \RuntimeException
{
    /**
     * HunterSelectionInvalidException constructor.
     *
     * @param string          $hunterId
     * @param \Throwable|null $previous
     */
    public function __construct(string $hunterId, ?\Throwable $previous = null)
    {
        parent::__construct(sprintf('Hunter %s does not exist', $hunterId), 0, $previous);
    }
}
