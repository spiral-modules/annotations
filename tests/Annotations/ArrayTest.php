<?php
declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Annotations\Tests;

use Spiral\Annotations\Parser;
use Spiral\Annotations\Tests\Fixtures\ScalarNode;

class ArrayTest extends BaseTest
{
    /**
     * @scalar (array_str = {"a", "b"})
     */
    public function testString()
    {
        $p = new Parser();
        $p->register(new ScalarNode());

        $nodes = $p->parse($this->getDoc('testString'));
        $this->assertInstanceOf(ScalarNode::class, $nodes['scalar']);
        $this->assertSame(["a", "b"], $nodes['scalar']->array_str);
    }

    /**
     * @scalar (array_int = {1, 2, 3})
     */
    public function testInteger()
    {
        $p = new Parser();
        $p->register(new ScalarNode());

        $nodes = $p->parse($this->getDoc('testInteger'));
        $this->assertInstanceOf(ScalarNode::class, $nodes['scalar']);
        $this->assertSame([1, 2, 3], $nodes['scalar']->array_int);
    }

    /**
     * @scalar (array_float = {1.5, 2.3, 3.4})
     */
    public function testFloat()
    {
        $p = new Parser();
        $p->register(new ScalarNode());

        $nodes = $p->parse($this->getDoc('testFloat'));
        $this->assertInstanceOf(ScalarNode::class, $nodes['scalar']);
        $this->assertEquals([1.5, 2.3, 3.4], $nodes['scalar']->array_float);
    }
}