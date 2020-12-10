<?php
declare(strict_types = 1);

namespace Isfett\WowClassicHunterMeleeWeavingEfficiency\Validator\Question;

use Isfett\WowClassicHunterMeleeWeavingEfficiency\DAO\ReportOverview;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\Exception\FightSelectionInvalidException;

/**
 * Class FightSelection
 */
class FightSelection implements QuestionValidatorInterface
{
    /** @var ReportOverview */
    private $reportOverview;

    /**
     * FightSelection constructor.
     *
     * @param ReportOverview $reportOverview
     */
    public function __construct(ReportOverview $reportOverview)
    {
        $this->reportOverview = $reportOverview;
    }

    /**
     * @param string|null $userInput
     *
     * @return string
     */
    public function validate(?string $userInput): string
    {
        if (null === $userInput || '' === $userInput) {
            return '';
        }

        if (!array_key_exists($userInput, $this->reportOverview->getBossFights())) {
            throw new FightSelectionInvalidException($userInput);
        }

        return $userInput;
    }
}
