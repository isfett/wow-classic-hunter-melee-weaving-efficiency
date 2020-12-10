<?php
declare(strict_types = 1);

namespace Isfett\WowClassicHunterMeleeWeavingEfficiency\Validator\Question;

use Isfett\WowClassicHunterMeleeWeavingEfficiency\Exception\EmptyReportHashException;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\Exception\ReportHashInvalidException;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\Service\LogOverviewService;

/**
 * Class ReportHash
 */
class ReportHash implements QuestionValidatorInterface
{
    /** @var LogOverviewService */
    private $logOverviewService;

    /**
     * ReportHash constructor.
     *
     * @param LogOverviewService $logOverviewService
     */
    public function __construct(LogOverviewService $logOverviewService)
    {
        $this->logOverviewService = $logOverviewService;
    }

    /**
     * @param string|null $userInput
     *
     * @return string
     */
    public function validate(?string $userInput): string
    {
        if (null === $userInput || '' === $userInput) {
            throw new EmptyReportHashException();
        }

        if (!$this->logOverviewService->isReportHashValid($userInput)) {
            throw new ReportHashInvalidException($userInput);
        }

        return $userInput;
    }
}
