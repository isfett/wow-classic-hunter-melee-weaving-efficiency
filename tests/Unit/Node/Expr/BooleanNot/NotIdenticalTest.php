<?php
declare(strict_types = 1);

namespace Isfett\WowClassicHunterMeleeWeavingEfficiency\Tests\Unit\Node\Expr\BooleanNot;

use Isfett\WowClassicHunterMeleeWeavingEfficiency\Node\Expr\BooleanNot\NotIdentical;
use Isfett\WowClassicHunterMeleeWeavingEfficiency\Tests\Unit\Node\AbstractNodeTestCase;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\BinaryOp;

/**
 * Class NotIdenticalTest
 */
class NotIdenticalTest extends AbstractNodeTestCase
{
    /**
     * @return void
     */
    public function testNegation(): void
    {
        $node = new BinaryOp\NotIdentical(
            new Expr\Variable('a'),
            new Expr\Variable('b')
        );

        $negatedNode = (new NotIdentical())->negate($node);

        $this->assertInstanceOf(BinaryOp\Identical::class, $negatedNode);
        $this->assertSame($node->left, $negatedNode->left);
        $this->assertSame($node->right, $negatedNode->right);
        $this->assertSame($node->getAttributes(), $negatedNode->getAttributes());
    }
}
