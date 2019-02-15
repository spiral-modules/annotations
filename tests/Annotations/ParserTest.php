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
use Spiral\Annotations\Tests\Annotation\Nested;
use Spiral\Annotations\Tests\Annotation\Scalar;

class ParserTest extends BaseTest
{
    public function testClone()
    {
        $p = new Parser();
        $this->assertNotSame(clone $p, $p);
    }

    /**
     * @expectedException \Spiral\Annotations\Exception\ParserException
     */
    public function testEmpty()
    {
        $p = new Parser();
        $p->register(new Nested());
        $p->register(new Nested());
    }

    /**
     * @expectedException \Spiral\Annotations\Exception\ParserException
     */
    public function testNoNodes()
    {
        $p = new Parser();

        $p->parse($this->getDoc('testNoNodes'));
    }

    /**
     * @nested (}
     *
     * @expectedException \Spiral\Annotations\Exception\SyntaxException
     */
    public function testParseError()
    {
        $p = new Parser();
        $p->register(new Nested());

        $p->parse($this->getDoc('testParseError'));
    }

    /**
     * @nested (name=
     *
     * @expectedException \Spiral\Annotations\Exception\SyntaxException
     */
    public function testParseError2()
    {
        $p = new Parser();
        $p->register(new Nested());

        $p->parse($this->getDoc('testParseError2'));
    }

    /**
     * @nested (name=)
     *
     * @expectedException \Spiral\Annotations\Exception\SyntaxException
     */
    public function testParseError3()
    {
        $p = new Parser();
        $p->register(new Nested());

        $p->parse($this->getDoc('testParseError3'));
    }

    /**
     * @nested (mm={)
     *
     * @expectedException \Spiral\Annotations\Exception\SyntaxException
     */
    public function testParseError4()
    {
        $p = new Parser();
        $p->register(new Nested());

        $p->parse($this->getDoc('testParseError4'));
    }

    /**
     * @nested (unknown=1)
     *
     * @expectedException \Spiral\Annotations\Exception\AttributeException
     */
    public function testParseError5()
    {
        $p = new Parser();
        $p->register(new Nested());

        $p->parse($this->getDoc('testParseError5'));
    }

    /**
     * @scalar (integer="a")
     *
     * @expectedException \Spiral\Annotations\Exception\AttributeException
     */
    public function testParseError6()
    {
        $p = new Parser();
        $p->register(new Scalar());

        $p->parse($this->getDoc('testParseError6'));
    }

    /**
     * @scalar (float=false)
     *
     * @expectedException \Spiral\Annotations\Exception\AttributeException
     */
    public function testParseError7()
    {
        $p = new Parser();
        $p->register(new Scalar());

        $p->parse($this->getDoc('testParseError7'));
    }

    /**
     * @scalar (bool="x")
     *
     * @expectedException \Spiral\Annotations\Exception\AttributeException
     */
    public function testParseError8()
    {
        $p = new Parser();
        $p->register(new Scalar());

        $p->parse($this->getDoc('testParseError8'));
    }

    /**
     * @nested (
     *     scalar=@nested(string="string")
     * )
     *
     * @expectedException \Spiral\Annotations\Exception\AttributeException
     */
    public function testParseError9()
    {
        $p = new Parser();
        $p->register(new Nested());

        $p->parse($this->getDoc('testParseError9'));
    }
}