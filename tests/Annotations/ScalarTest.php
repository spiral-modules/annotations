<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Annotations\Tests;

use Spiral\Annotations\Parser;
use Spiral\Annotations\Tests\Annotation\Scalar;

class ScalarTest extends BaseTest
{
    /**
     * @scalar (string = "message")
     */
    public function testString()
    {
        $p = new Parser();
        $p->register(new Scalar());

        $nodes = $p->parse($this->getDoc('testString'));
        $this->assertInstanceOf(Scalar::class, $nodes['scalar']);
        $this->assertSame("message", $nodes['scalar']->string);
    }

    /**
     * @scalar (string = "message 1")
     * @scalar (string = "message 2")
     */
    public function testMultiple()
    {
        $p = new Parser();
        $p->register(new Scalar());

        $nodes = $p->parse($this->getDoc('testMultiple'));
        $this->assertInstanceOf(Scalar::class, $nodes['scalar'][0]);
        $this->assertInstanceOf(Scalar::class, $nodes['scalar'][1]);
        $this->assertSame("message 1", $nodes['scalar'][0]->string);
        $this->assertSame("message 2", $nodes['scalar'][1]->string);
    }

    /**
     * @scalar (string = message)
     */
    public function testStringIdentifier()
    {
        $p = new Parser();
        $p->register(new Scalar());

        $nodes = $p->parse($this->getDoc('testStringIdentifier'));
        $this->assertInstanceOf(Scalar::class, $nodes['scalar']);
        $this->assertSame("message", $nodes['scalar']->string);
    }


    /**
     * @scalar (bool = true)
     */
    public function testBool()
    {
        $p = new Parser();
        $p->register(new Scalar());

        $nodes = $p->parse($this->getDoc('testBool'));
        $this->assertInstanceOf(Scalar::class, $nodes['scalar']);
        $this->assertSame(true, $nodes['scalar']->bool);
    }

    /**
     * @scalar (bool = false)
     */
    public function testBoolFalse()
    {
        $p = new Parser();
        $p->register(new Scalar());

        $nodes = $p->parse($this->getDoc('testBoolFalse'));
        $this->assertInstanceOf(Scalar::class, $nodes['scalar']);
        $this->assertSame(false, $nodes['scalar']->bool);
    }

    /**
     * @scalar (integer = 101)
     */
    public function testInteger()
    {
        $p = new Parser();
        $p->register(new Scalar());

        $nodes = $p->parse($this->getDoc('testInteger'));
        $this->assertInstanceOf(Scalar::class, $nodes['scalar']);
        $this->assertSame(101, $nodes['scalar']->integer);
    }

    /**
     * @scalar (float = 101.555)
     */
    public function testFloat()
    {
        $p = new Parser();
        $p->register(new Scalar());

        $nodes = $p->parse($this->getDoc('testFloat'));
        $this->assertInstanceOf(Scalar::class, $nodes['scalar']);
        $this->assertEquals(101.555, $nodes['scalar']->float);
    }

    /**
     * @scalar (string="message", bool=false, integer=101, float = 101.555)
     */
    public function testFull()
    {
        $p = new Parser();
        $p->register(new Scalar());

        $nodes = $p->parse($this->getDoc('testFull'));
        $this->assertInstanceOf(Scalar::class, $nodes['scalar']);
        $this->assertEquals(101.555, $nodes['scalar']->float);
        $this->assertSame(false, $nodes['scalar']->bool);
        $this->assertSame(101, $nodes['scalar']->integer);
        $this->assertSame("message", $nodes['scalar']->string);
    }

    /**
     * @scalar (
     *     string="message",
     *     bool=false,
     *     integer=101,
     *     float = 101.555
     * )
     */
    public function testFullSpaced()
    {
        $p = new Parser();
        $p->register(new Scalar());

        $nodes = $p->parse($this->getDoc('testFullSpaced'));
        $this->assertInstanceOf(Scalar::class, $nodes['scalar']);
        $this->assertEquals(101.555, $nodes['scalar']->float);
        $this->assertSame(false, $nodes['scalar']->bool);
        $this->assertSame(101, $nodes['scalar']->integer);
        $this->assertSame("message", $nodes['scalar']->string);
    }

    /**
     * @scalar (mixed = "message")
     */
    public function testMixed1()
    {
        $p = new Parser();
        $p->register(new Scalar());

        $nodes = $p->parse($this->getDoc('testMixed1'));
        $this->assertInstanceOf(Scalar::class, $nodes['scalar']);
        $this->assertSame("message", $nodes['scalar']->mixed);
    }

    /**
     * @scalar (mixed = 123)
     */
    public function testMixed2()
    {
        $p = new Parser();
        $p->register(new Scalar());

        $nodes = $p->parse($this->getDoc('testMixed2'));
        $this->assertInstanceOf(Scalar::class, $nodes['scalar']);
        $this->assertSame(123, $nodes['scalar']->mixed);
    }

    /**
     * @scalar (mixed = null)
     */
    public function testMixed3()
    {
        $p = new Parser();
        $p->register(new Scalar());

        $nodes = $p->parse($this->getDoc('testMixed3'));
        $this->assertInstanceOf(Scalar::class, $nodes['scalar']);
        $this->assertSame(null, $nodes['scalar']->mixed);
    }

    /**
     * @scalar (
     *     string="message",
     *     bool=false,
     *     integer=101,
     *     float = 101.555,
     * )
     */
    public function testFullSpacedEndComma()
    {
        $p = new Parser();
        $p->register(new Scalar());

        $nodes = $p->parse($this->getDoc('testFullSpacedEndComma'));
        $this->assertInstanceOf(Scalar::class, $nodes['scalar']);
        $this->assertEquals(101.555, $nodes['scalar']->float);
        $this->assertSame(false, $nodes['scalar']->bool);
        $this->assertSame(101, $nodes['scalar']->integer);
        $this->assertSame("message", $nodes['scalar']->string);
    }
}