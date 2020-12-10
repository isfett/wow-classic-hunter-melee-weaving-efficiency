<?php
declare(strict_types = 1);

namespace Isfett\WowClassicHunterMeleeWeavingEfficiency\Exception;

/**
 * Class EmptyReportHashException
 */
class EmptyReportHashException extends \RuntimeException
{
    /**
     * EmptyReportHashException constructor.
     *
     * @param \Throwable|null $previous
     */
    public function __construct(?\Throwable $previous = null)
    {
        parent::__construct('No report hash found', 0, $previous);
    }
}
