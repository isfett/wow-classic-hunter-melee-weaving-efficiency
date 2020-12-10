<?php
declare(strict_types = 1);

namespace Isfett\WowClassicHunterMeleeWeavingEfficiency\Tests\Unit\Node\Representation;

use Isfett\WowClassicHunterMeleeWeavingEfficiency\Service\NodeRepresentationService;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\Tests\Unit\Node\AbstractNodeTestCase;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class AbstractNodeRepresentationTest
 */
abstract class AbstractNodeRepresentationTest extends AbstractNodeTestCase
{
    /** @var MockObject|NodeRepresentationService */
    protected $nodeRepresentationService;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->nodeRepresentationService = $this->createMock(NodeRepresentationService::class);
    }
}
