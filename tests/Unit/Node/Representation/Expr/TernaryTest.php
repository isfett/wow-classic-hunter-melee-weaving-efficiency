<?php
declare(strict_types = 1);

namespace Isfett\WowClassicHunterMeleeWeavingEfficiency\Tests\Unit\Node\Representation\Expr;

use Isfett\WowClassicHunterMeleeWeavingEfficiency\Node\Representation\Expr\Ternary;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\Tests\Unit\Node\Representation\AbstractNodeRepresentationTest;
use PhpParser\Node;

/**
 * Class TernaryTest
 */
class TernaryTest extends AbstractNodeRepresentationTest
{
    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @return void
     */
    public function testGetRepresentation(): void
    {
        $node = new Node\Expr\Ternary(
            $this->createVariableNode('x'),
            $this->createVariableNode('a'),
            $this->createVariableNode('b'),
            $this->getNodeAttributes()
        );

        $this->nodeRepresentationService
            ->method('representationForNode')
            ->willReturn('$x', '$a', '$b');

        $representation = new Ternary($this->nodeRepresentationService, $node);

        $this->assertSame('$x ? $a : $b', $representation->representation());
    }
}
