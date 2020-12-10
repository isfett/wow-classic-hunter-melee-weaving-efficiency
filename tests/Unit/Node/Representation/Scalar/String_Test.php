<?php
declare(strict_types = 1);

namespace Isfett\WowClassicHunterMeleeWeavingEfficiency\Tests\Unit\Node\Representation\Scalar;

use Isfett\WowClassicHunterMeleeWeavingEfficiency\Node\Representation\Scalar\String_;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\Tests\Unit\Node\Representation\AbstractNodeRepresentationTest;
use PhpParser\Node;

/**
 * Class String_Test
 */
class String_Test extends AbstractNodeRepresentationTest
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
        $node = new Node\Scalar\String_(
            'test',
            $this->getNodeAttributes()
        );

        $representation = new String_($this->nodeRepresentationService, $node);

        $this->assertSame("'test'", $representation->representation());
    }
}
