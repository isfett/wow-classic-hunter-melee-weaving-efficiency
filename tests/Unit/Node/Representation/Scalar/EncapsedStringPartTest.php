<?php
declare(strict_types = 1);

namespace Isfett\WowClassicHunterMeleeWeavingEfficiency\Tests\Unit\Node\Representation\Scalar;

use Isfett\WowClassicHunterMeleeWeavingEfficiency\Node\Representation\Scalar\EncapsedStringPart;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\Tests\Unit\Node\Representation\AbstractNodeRepresentationTest;
use PhpParser\Node;

/**
 * Class EncapsedStringPartTest
 */
class EncapsedStringPartTest extends AbstractNodeRepresentationTest
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
        $node = new Node\Scalar\EncapsedStringPart(
            'test',
            $this->getNodeAttributes()
        );

        $representation = new EncapsedStringPart($this->nodeRepresentationService, $node);

        $this->assertSame('test', $representation->representation());
    }
}
