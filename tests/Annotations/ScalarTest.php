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

class ScalarTest extends BaseTest
{
    /**
     * @scalar (string = "message")
     */
    public function testString()
    {
        $p = new Parser();
        $p->register(new ScalarNode());

        $nodes = $p->parse($this->getDoc('testString'));
        $this->assertInstanceOf(ScalarNode::class, $nodes['scalar']);
        $this->assertSame("message", $nodes['scalar']->string);
    }

    /**
     * @scalar (string = message)
     */
    public function testStringIdentifier()
    {
        $p = new Parser();
        $p->register(new ScalarNode());

        $nodes = $p->parse($this->getDoc('testStringIdentifier'));
        $this->assertInstanceOf(ScalarNode::class, $nodes['scalar']);
        $this->assertSame("message", $nodes['scalar']->string);
    }


    /**
     * @scalar (bool = true)
     */
    public function testBool()
    {
        $p = new Parser();
        $p->register(new ScalarNode());

        $nodes = $p->parse($this->getDoc('testBool'));
        $this->assertInstanceOf(ScalarNode::class, $nodes['scalar']);
        $this->assertSame(true, $nodes['scalar']->bool);
    }

    /**
     * @scalar (bool = false)
     */
    public function testBoolFalse()
    {
        $p = new Parser();
        $p->register(new ScalarNode());

        $nodes = $p->parse($this->getDoc('testBoolFalse'));
        $this->assertInstanceOf(ScalarNode::class, $nodes['scalar']);
        $this->assertSame(false, $nodes['scalar']->bool);
    }

    /**
     * @scalar (integer = 101)
     */
    public function testInteger()
    {
        $p = new Parser();
        $p->register(new ScalarNode());

        $nodes = $p->parse($this->getDoc('testInteger'));
        $this->assertInstanceOf(ScalarNode::class, $nodes['scalar']);
        $this->assertSame(101, $nodes['scalar']->integer);
    }

    /**
     * @scalar (float = 101.555)
     */
    public function testFloat()
    {
        $p = new Parser();
        $p->register(new ScalarNode());

        $nodes = $p->parse($this->getDoc('testFloat'));
        $this->assertInstanceOf(ScalarNode::class, $nodes['scalar']);
        $this->assertEquals(101.555, $nodes['scalar']->float);
    }

    public function testFull()
    {
        $p = new Parser();
        $p->register(new ScalarNode());

        $nodes = $p->parse($this->getDoc('testFull'));
        $this->assertInstanceOf(ScalarNode::class, $nodes['scalar']);
        $this->assertEquals(101.555, $nodes['scalar']->float);
        $this->assertSame(false, $nodes['scalar']->bool);
        $this->assertSame(101, $nodes['scalar']->integer);
        $this->assertSame("message", $nodes['scalar']->string);
    }
}