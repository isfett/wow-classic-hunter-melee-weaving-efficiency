<?php
declare(strict_types = 1);

namespace Isfett\WowClassicHunterMeleeWeavingEfficiency\Tests\Unit\Node\Processor\MagicNumber;

use Isfett\WowClassicHunterMeleeWeavingEfficiency\DAO\OccurrenceList;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\Node\Processor\MagicNumber\IgnoreArrayKeyProcessor;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\Tests\Unit\Node\AbstractNodeTestCase;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Stmt\Case_;

/**
 * Class IgnoreArrayKeyProcessorTest
 */
class IgnoreArrayKeyProcessorTest extends AbstractNodeTestCase
{
    /** @var IgnoreArrayKeyProcessor */
    private $processor;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->processor = new IgnoreArrayKeyProcessor();
    }

    /**
     * @return void
     */
    public function testProcessWillRemoveOccurrencesForArrayKeys(): void
    {
        $node = new LNumber(3);
        $node->setAttribute('parent', new ArrayItem(
            new LNumber(36),
            $node,
            false
        ));

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
    public function testProcessWillNotRemoveNullKeys(): void
    {
        $node = new LNumber(36);
        $node->setAttribute('parent', new ArrayItem(
            new LNumber(36),
            null,
            false
        ));

        $occurrence = $this->createOccurrence($node);

        $nodeOccurrenceList = new OccurrenceList();
        $nodeOccurrenceList->addOccurrence($occurrence);
        $this->processor->setNodeOccurrenceList($nodeOccurrenceList);

        $this->assertCount(1, $nodeOccurrenceList->getOccurrences());
        $this->assertEmpty($occurrence->getAffectedByProcessors());

        $this->processor->process($occurrence);

        $this->assertCount(1, $nodeOccurrenceList->getOccurrences());
    }

    /**
     * @return void
     */
    public function testProcessWillNotRemoveOtherValues(): void
    {
        $node = new LNumber(36);
        $node->setAttribute('parent', new ArrayItem(
            new LNumber(36),
            new LNumber(3),
            false
        ));

        $occurrence = $this->createOccurrence($node);

        $nodeOccurrenceList = new OccurrenceList();
        $nodeOccurrenceList->addOccurrence($occurrence);
        $this->processor->setNodeOccurrenceList($nodeOccurrenceList);

        $this->assertCount(1, $nodeOccurrenceList->getOccurrences());
        $this->assertEmpty($occurrence->getAffectedByProcessors());

        $this->processor->process($occurrence);

        $this->assertCount(1, $nodeOccurrenceList->getOccurrences());
    }

    /**
     * @return void
     */
    public function testProcessWithoutArrayItemWillNotGetRemoved(): void
    {
        $node = new LNumber(36);
        $node->setAttribute('parent', new Case_(
            new LNumber(36)
        ));

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
