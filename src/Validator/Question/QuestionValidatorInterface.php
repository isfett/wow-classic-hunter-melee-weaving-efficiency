<?php
declare(strict_types = 1);

namespace Isfett\WowClassicHunterMeleeWeavingEfficiency\Validator\Question;

/**
 * Interface QuestionValidatorInterface
 */
interface QuestionValidatorInterface
{
    /**
     * @param string|null $userInput
     *
     * @return string
     */
    public function validate(?string $userInput): string;
}
