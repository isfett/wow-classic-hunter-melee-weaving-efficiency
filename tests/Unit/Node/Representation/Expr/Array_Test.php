<?php
declare(strict_types = 1);

namespace Isfett\WowClassicHunterMeleeWeavingEfficiency\Tests\Unit\Node\Representation\Expr;

use Isfett\WowClassicHunterMeleeWeavingEfficiency\Node\Representation\Expr\Array_;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\Tests\Unit\Node\Representation\AbstractNodeRepresentationTest;
use PhpParser\Node;

/**
 * Class Array_Test
 */
class Array_Test extends AbstractNodeRepresentationTest
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
        $node = new Node\Expr\Array_(
            [
                $this->createVariableNode('test'),
            ],
            $this->getNodeAttributes()
        );

        $this->nodeRepresentationService
            ->method('representationForArguments')
            ->willReturn(['$test']);

        $representation = new Array_($this->nodeRepresentationService, $node);

        $this->assertSame('[$test]', $representation->representation());
    }
}
