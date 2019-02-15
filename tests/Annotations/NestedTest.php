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
use Spiral\Annotations\Tests\Fixtures\Nested;

class NestedTest extends BaseTest
{
    /**
     * @nested (name="name")
     */
    public function testSimple()
    {
        $p = new Parser();
        $p->register(new Nested());

        $nodes = $p->parse($this->getDoc('testSimple'));
        $this->assertInstanceOf(Nested::class, $nodes['nested']);
        $this->assertSame("name", $nodes['nested']->name);
    }

    /**
     * @nested (
     *     name="name",
     *     scalar= (string = "string")
     * )
     */
    public function testUnnamed()
    {
        $p = new Parser();
        $p->register(new Nested());

        $nodes = $p->parse($this->getDoc('testUnnamed'));
        $this->assertInstanceOf(Nested::class, $nodes['nested']);
        $this->assertSame("name", $nodes['nested']->name);
        $this->assertSame("string", $nodes['nested']->scalar->string);
    }

    /**
     * @nested (
     *     name   = "name",
     *     scalar = @scalar(string = "string")
     * )
     */
    public function testNamed()
    {
        $p = new Parser();
        $p->register(new Nested());

        $nodes = $p->parse($this->getDoc('testNamed'));
        $this->assertInstanceOf(Nested::class, $nodes['nested']);
        $this->assertSame("name", $nodes['nested']->name);
        $this->assertSame("string", $nodes['nested']->scalar->string);
    }

    /**
     * @nested (
     *     name   = "name",
     *     scalar_arr = {
     *          (string = "string 1"),
     *          (string = "string 2"),
     *     }
     * )
     */
    public function testArrayOfNested()
    {
        $p = new Parser();
        $p->register(new Nested());

        $nodes = $p->parse($this->getDoc('testArrayOfNested'));
        $this->assertInstanceOf(Nested::class, $nodes['nested']);
        $this->assertSame("name", $nodes['nested']->name);
        $this->assertSame("string 1", $nodes['nested']->scalar_arr[0]->string);
        $this->assertSame("string 2", $nodes['nested']->scalar_arr[1]->string);
    }

    /**
     * @nested (
     *     name   = "name",
     *     scalar_arr = {
     *          @scalar(string = "string 1"),
     *          @scalar(string = "string 2"),
     *     }
     * )
     */
    public function testArrayOfNestedNamed()
    {
        $p = new Parser();
        $p->register(new Nested());

        $nodes = $p->parse($this->getDoc('testArrayOfNestedNamed'));
        $this->assertInstanceOf(Nested::class, $nodes['nested']);
        $this->assertSame("name", $nodes['nested']->name);
        $this->assertSame("string 1", $nodes['nested']->scalar_arr[0]->string);
        $this->assertSame("string 2", $nodes['nested']->scalar_arr[1]->string);
    }

    /**
     * @nested (
     *     name   = "name",
     *     mm = {{(value={{1}}),(value={{2}})},{(value={{3}}),(value={{4}})}}
     * )
     */
    public function testMatrixOfMatrixes()
    {
        $p = new Parser();
        $p->register(new Nested());

        $nodes = $p->parse($this->getDoc('testMatrixOfMatrixes'));
        $this->assertInstanceOf(Nested::class, $nodes['nested']);
        $this->assertSame("name", $nodes['nested']->name);

        $this->assertSame(1, $nodes['nested']->mm[0][0]->value[0][0]);
        $this->assertSame(2, $nodes['nested']->mm[0][1]->value[0][0]);
        $this->assertSame(3, $nodes['nested']->mm[1][0]->value[0][0]);
        $this->assertSame(4, $nodes['nested']->mm[1][1]->value[0][0]);
    }

    /**
     * @nested (
     *     name   = "name",
     *     mm = {{@matrix(value={{1}}),@matrix(value={{2}})},{@matrix(value={{3}}),@matrix(value={{4}})}}
     * )
     */
    public function testMatrixOfMatrixesNamed()
    {
        $p = new Parser();
        $p->register(new Nested());

        $nodes = $p->parse($this->getDoc('testMatrixOfMatrixesNamed'));
        $this->assertInstanceOf(Nested::class, $nodes['nested']);
        $this->assertSame("name", $nodes['nested']->name);

        $this->assertSame(1, $nodes['nested']->mm[0][0]->value[0][0]);
        $this->assertSame(2, $nodes['nested']->mm[0][1]->value[0][0]);
        $this->assertSame(3, $nodes['nested']->mm[1][0]->value[0][0]);
        $this->assertSame(4, $nodes['nested']->mm[1][1]->value[0][0]);
    }
}