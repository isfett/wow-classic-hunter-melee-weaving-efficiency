<?php
declare(strict_types = 1);

namespace Isfett\WowClassicHunterMeleeWeavingEfficiency\Tests\Unit\Node\Representation\Expr;

use Isfett\WowClassicHunterMeleeWeavingEfficiency\Node\Representation\Expr\Assign;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\Tests\Unit\Node\Representation\AbstractNodeRepresentationTest;
use PhpParser\Node;

/**
 * Class AssignTest
 */
class AssignTest extends AbstractNodeRepresentationTest
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
        $node = new Node\Expr\Assign(
            $this->createVariableNode('x'),
            $this->createVariableNode('y'),
            $this->getNodeAttributes()
        );

        $this->nodeRepresentationService
            ->method('representationForNode')
            ->willReturn('$x', '$y');

        $representation = new Assign($this->nodeRepresentationService, $node);

        $this->assertSame('$x = $y', $representation->representation());
    }
}
