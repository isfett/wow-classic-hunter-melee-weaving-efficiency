<?php
declare(strict_types = 1);

namespace Isfett\WowClassicHunterMeleeWeavingEfficiency\Tests\Unit\Node\Representation\Expr\AssignOp;

use Isfett\WowClassicHunterMeleeWeavingEfficiency\Node\Representation\Expr\AssignOp\ShiftRight;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\Tests\Unit\Node\Representation\AbstractNodeRepresentationTest;
use PhpParser\Node;

/**
 * Class ShiftRightTest
 */
class ShiftRightTest extends AbstractNodeRepresentationTest
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
        $node = new Node\Expr\AssignOp\ShiftRight(
            $this->createVariableNode('variable'),
            $this->createVariableNode('variable2'),
            $this->getNodeAttributes()
        );

        $this->nodeRepresentationService
            ->method('representationForNode')
            ->willReturn('$variable', '$variable2');

        $representation = new ShiftRight($this->nodeRepresentationService, $node);

        $this->assertSame('$variable >>= $variable2', $representation->representation());
    }
}
