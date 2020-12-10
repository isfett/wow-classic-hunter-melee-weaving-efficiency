<?php
declare(strict_types = 1);

namespace Isfett\WowClassicHunterMeleeWeavingEfficiency\Validator\Question;

use Isfett\WowClassicHunterMeleeWeavingEfficiency\DAO\ReportOverview;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\Exception\EmptyHunterSelectionException;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\Exception\HunterSelectionInvalidException;

/**
 * Class HunterSelection
 */
class HunterSelection implements QuestionValidatorInterface
{
    /** @var ReportOverview */
    private $reportOverview;

    /**
     * HunterSelection constructor.
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
            throw new EmptyHunterSelectionException();
        }

        if (!array_key_exists($userInput, $this->reportOverview->getPlayers())) {
            throw new HunterSelectionInvalidException($userInput);
        }

        return $userInput;
    }
}
