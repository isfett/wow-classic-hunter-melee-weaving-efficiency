<?php
declare(strict_types = 1);

namespace Isfett\WowClassicHunterMeleeWeavingEfficiency\Tests\Unit\Node\Visitor\Condition;

use Isfett\WowClassicHunterMeleeWeavingEfficiency\DAO\Occurrence;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\Node\Visitor\Condition\TernaryVisitor;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\Tests\Unit\Node\AbstractNodeTestCase;
use PhpParser\Node\Expr\Ternary;
use PhpParser\Node\Stmt\If_;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class TernaryVisitorTest
 */
class TernaryVisitorTest extends AbstractNodeTestCase
{
    /** @var TernaryVisitor */
    private $visitor;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->visitor = new TernaryVisitor();

        /** @var MockObject|SplFileInfo $file */
        $file = $this->createSplFileInfoMock();

        $this->visitor->setFile($file);
    }

    /**
     * @return void
     */
    public function testEnterNode(): void
    {
        $this->assertCount(0, $this->visitor->getNodeOccurrenceList()->getOccurrences());

        $node = new Ternary(
            $this->createVariableNode('x'),
            $this->createVariableNode('y'),
            $this->createVariableNode('z')
        );
        $this->visitor->enterNode($node);
        $this->assertCount(1, $this->visitor->getNodeOccurrenceList()->getOccurrences());

        /** @var Occurrence $occurrence */
        $occurrence = $this->visitor->getNodeOccurrenceList()->getOccurrences()->first();
        $this->assertSame($node->cond, $occurrence->getNode());
    }

    /**
     * @return void
     */
    public function testEnterNodeWillNotAddWrongNodes(): void
    {
        $this->assertCount(0, $this->visitor->getNodeOccurrenceList()->getOccurrences());

        $node = new If_($this->createScalarStringNode('xxx'), [], $this->getNodeAttributes());
        $this->visitor->enterNode($node);
        $this->assertCount(0, $this->visitor->getNodeOccurrenceList()->getOccurrences());
    }
}
