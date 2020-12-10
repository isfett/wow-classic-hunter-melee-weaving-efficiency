<?php
declare(strict_types = 1);

namespace Isfett\WowClassicHunterMeleeWeavingEfficiency\Tests\Unit\Node\Representation;

use Isfett\WowClassicHunterMeleeWeavingEfficiency\Node\Representation\VarLikeIdentifier;
use PhpParser\Node;

/**
 * Class VarLikeIdentifierTest
 */
class VarLikeIdentifierTest extends AbstractNodeRepresentationTest
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
        $node = new Node\VarLikeIdentifier(
            'test',
            $this->getNodeAttributes()
        );

        $representation = new VarLikeIdentifier($this->nodeRepresentationService, $node);

        $this->assertSame('$test', $representation->representation());
    }
}
