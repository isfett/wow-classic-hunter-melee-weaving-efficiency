<?php
declare(strict_types = 1);

namespace Isfett\WowClassicHunterMeleeWeavingEfficiency\Tests\Unit\Node\Representation\Expr;

use Isfett\WowClassicHunterMeleeWeavingEfficiency\Node\Representation\Expr\PropertyFetch;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\Tests\Unit\Node\Representation\AbstractNodeRepresentationTest;
use PhpParser\Node;

/**
 * Class PropertyFetchTest
 */
class PropertyFetchTest extends AbstractNodeRepresentationTest
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
        $node = new Node\Expr\PropertyFetch(
            $this->createVariableNode('this'),
            'x',
            $this->getNodeAttributes()
        );

        $this->nodeRepresentationService
            ->method('representationForNode')
            ->willReturn('$this', 'x');

        $representation = new PropertyFetch($this->nodeRepresentationService, $node);

        $this->assertSame('$this->x', $representation->representation());
    }
}
