<?php
declare(strict_types = 1);

namespace Isfett\WowClassicHunterMeleeWeavingEfficiency\Tests\Unit\Node\Processor\MagicNumber;

use Isfett\WowClassicHunterMeleeWeavingEfficiency\DAO\OccurrenceList;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\Node\Processor\MagicNumber\IgnoreForLoopProcessor;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\Tests\Unit\Node\AbstractNodeTestCase;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Stmt\Case_;
use PhpParser\Node\Stmt\For_;

/**
 * Class IgnoreForLoopProcessorTest
 */
class IgnoreForLoopProcessorTest extends AbstractNodeTestCase
{
    /** @var IgnoreForLoopProcessor */
    private $processor;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->processor = new IgnoreForLoopProcessor();
    }

    /**
     * @return void
     */
    public function testProcessWillRemoveOccurrencesInForLoops(): void
    {
        $node = new LNumber(3, [
            'parent' => new Assign(
                $this->createVariableNode('i'),
                new LNumber(3),
                ['parent'  => new For_()]
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
    public function testProcessWithoutForLoopWillNotGetRemoved(): void
    {
        $node = new LNumber(3, [
            'parent' => new Assign(
                $this->createVariableNode('i'),
                new LNumber(3),
                ['parent'  => new Case_(new LNumber(3))]
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
