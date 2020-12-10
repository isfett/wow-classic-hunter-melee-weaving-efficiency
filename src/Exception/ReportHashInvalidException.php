<?php
declare(strict_types = 1);

namespace Isfett\WowClassicHunterMeleeWeavingEfficiency\Exception;

/**
 * Class ReportHashInvalidException
 */
class ReportHashInvalidException extends \RuntimeException
{
    /**
     * ReportHashInvalidException constructor.
     *
     * @param string          $reportHash
     * @param \Throwable|null $previous
     */
    public function __construct(string $reportHash, ?\Throwable $previous = null)
    {
        parent::__construct(sprintf('Report hash %s invalid', $reportHash), 0, $previous);
    }
}
