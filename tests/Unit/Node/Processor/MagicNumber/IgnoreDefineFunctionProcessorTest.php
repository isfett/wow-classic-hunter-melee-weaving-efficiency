<?php
declare(strict_types = 1);

namespace Isfett\WowClassicHunterMeleeWeavingEfficiency\Tests\Unit\Node\Processor\MagicNumber;

use Isfett\WowClassicHunterMeleeWeavingEfficiency\DAO\OccurrenceList;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\Node\Processor\MagicNumber\IgnoreDefineFunctionProcessor;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\Tests\Unit\Node\AbstractNodeTestCase;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\LNumber;

/**
 * Class IgnoreDefineFunctionProcessorTest
 */
class IgnoreDefineFunctionProcessorTest extends AbstractNodeTestCase
{
    /** @var IgnoreDefineFunctionProcessor */
    private $processor;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->processor = new IgnoreDefineFunctionProcessor();
    }

    /**
     * @return void
     */
    public function testProcessWillRemoveOccurrencesForDefineFunctions(): void
    {
        $node = new LNumber(3, [
            'parent' => new Arg(
                new LNumber(3),
                false,
                false,
                ['parent'  => new FuncCall(new Name('define'))]
            ),
        ]);

        $occurrence = $this->createOccurrence($node);

        $nodeOccurrenceList = new OccurrenceList();
        $nodeOccurrenceList->addOccurrence($occurrence);
        $this->processor->setNodeOccurrenceList($nodeOccurrenceList);

        $this->assertCount(1, $nodeOccurrenceList->getOccurrences());
        $this->assertEmpty($occurrence->getAffectedByProcessors());

        $this->processor->process($occurrence);

        $this->assertCount(0, $nodeOccurrenceList->getOccurrences());
    }

    /**
     * @return void
     */
    public function testProcessWillNotRemoveOtherFunctionCalls(): void
    {
        $node = new LNumber(3, [
            'parent' => new Arg(
                new LNumber(3),
                false,
                false,
                ['parent'  => new FuncCall(new Name('someotherfunctionname'))]
            ),
        ]);

        $occurrence = $this->createOccurrence($node);

        $nodeOccurrenceList = new OccurrenceList();
        $nodeOccurrenceList->addOccurrence($occurrence);
        $this->processor->setNodeOccurrenceList($nodeOccurrenceList);

        $this->assertCount(1, $nodeOccurrenceList->getOccurrences());
        $this->assertEmpty($occurrence->getAffectedByProcessors());

        $this->processor->process($occurrence);

        $this->assertCount(1, $nodeOccurrenceList->getOccurrences());
    }
}
