<?php
declare(strict_types = 1);

namespace Isfett\WowClassicHunterMeleeWeavingEfficiency\Tests\Integration\Console;

use Isfett\WowClassicHunterMeleeWeavingEfficiency\Builder\FinderBuilder;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\Builder\ProcessorBuilder;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\Builder\SortConfigurationBuilder;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\Builder\VisitorBuilder;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\Console\Application;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\Console\Command\EmptyCommand;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\Node\ProcessorRunner;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\Service\NodeRepresentationService;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\Service\SortService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Class MagicNumberDetectorCommandTest
 */
class EmptyCommandTest extends TestCase
{
    /** @var Application */
    private $application;

    /** @var EmptyCommand */
    private $emptyCommand;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->emptyCommand = new EmptyCommand();

        $this->application = new Application();
        $this->application->addCommands([$this->emptyCommand]);
    }

    /**
     * @return void
     * @throws \Exception
     * @throws \Throwable
     */
    public function testRun(): void
    {
        $input = new ArrayInput([], $this->emptyCommand->getDefinition());

        $output = new BufferedOutput();
        $exitCode = $this->emptyCommand->run($input, $output);
        $outputText = $output->fetch();

        $this->assertSame(1, $exitCode);
        /*$this->assertStringStartsWith(
            '<command-start>Starting magic-number-detector command</command-start>',
            $outputText
        );
        */
    }
}
