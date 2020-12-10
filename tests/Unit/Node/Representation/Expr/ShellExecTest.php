<?php
declare(strict_types = 1);

namespace Isfett\WowClassicHunterMeleeWeavingEfficiency\Tests\Unit\Node\Representation\Expr;

use Isfett\WowClassicHunterMeleeWeavingEfficiency\Node\Representation\Expr\ShellExec;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\Tests\Unit\Node\Representation\AbstractNodeRepresentationTest;
use PhpParser\Node;

/**
 * Class ShellExecTest
 */
class ShellExecTest extends AbstractNodeRepresentationTest
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
        $node = new Node\Expr\ShellExec(
            [
                $this->createVariableNode('command'),
            ],
            $this->getNodeAttributes()
        );

        $this->nodeRepresentationService
            ->method('representationForArguments')
            ->willReturn(['$command']);

        $representation = new ShellExec($this->nodeRepresentationService, $node);

        $this->assertSame('shell_exec($command)', $representation->representation());
    }
}
