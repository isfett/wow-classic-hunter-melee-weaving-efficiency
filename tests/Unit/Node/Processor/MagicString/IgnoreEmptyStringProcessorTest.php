<?php
declare(strict_types = 1);

namespace Isfett\WowClassicHunterMeleeWeavingEfficiency\Tests\Unit\Node\Processor\MagicString;

use Isfett\WowClassicHunterMeleeWeavingEfficiency\DAO\OccurrenceList;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\Node\Processor\MagicString\IgnoreEmptyStringProcessor;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\Tests\Unit\Node\AbstractNodeTestCase;
use PhpParser\Node\Scalar\String_;

/**
 * Class IgnoreEmptyStringProcessorTest
 */
class IgnoreEmptyStringProcessorTest extends AbstractNodeTestCase
{
    /** @var IgnoreEmptyStringProcessor */
    private $processor;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->processor = new IgnoreEmptyStringProcessor();
    }

    /**
     * @return void
     */
    public function testProcess(): void
    {
        $node = new String_('');

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
    public function testProcessOnNonEmptyStringWillNotRemoveOccurrence(): void
    {
        $node = new String_('foo');

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
