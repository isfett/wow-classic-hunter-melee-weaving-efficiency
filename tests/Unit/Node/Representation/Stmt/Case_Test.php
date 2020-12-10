<?php
declare(strict_types = 1);

namespace Isfett\WowClassicHunterMeleeWeavingEfficiency\Tests\Unit\Node\Representation\Stmt;

use Isfett\WowClassicHunterMeleeWeavingEfficiency\Node\Representation\Stmt\Case_;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\Tests\Unit\Node\Representation\AbstractNodeRepresentationTest;
use PhpParser\Node;

/**
 * Class Case_Test
 */
class Case_Test extends AbstractNodeRepresentationTest
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
        $node = new Node\Stmt\Case_(
            $this->createLNumberNode(1),
            $this->getNodeAttributes()
        );

        $representation = new Case_($this->nodeRepresentationService, $node);

        $this->nodeRepresentationService
            ->method('representationForNode')
            ->willReturn('1');

        $this->assertSame('case 1:', $representation->representation());
    }
}
