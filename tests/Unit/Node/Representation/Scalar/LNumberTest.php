<?php
declare(strict_types = 1);

namespace Isfett\WowClassicHunterMeleeWeavingEfficiency\Tests\Unit\Node\Representation\Scalar;

use Isfett\WowClassicHunterMeleeWeavingEfficiency\Node\Representation\Scalar\LNumber;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\Tests\Unit\Node\Representation\AbstractNodeRepresentationTest;
use PhpParser\Node;

/**
 * Class LNumberTest
 */
class LNumberTest extends AbstractNodeRepresentationTest
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
        $node = new Node\Scalar\LNumber(
            1337,
            $this->getNodeAttributes()
        );

        $representation = new LNumber($this->nodeRepresentationService, $node);

        $this->assertSame('1337', $representation->representation());
    }
}
