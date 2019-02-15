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
use Spiral\Annotations\Tests\Node\Matrix;
use Spiral\Annotations\Tests\Node\Scalar;

class ArrayTest extends BaseTest
{
    /**
     * @scalar (array_str = {"a", "b"})
     */
    public function testString()
    {
        $p = new Parser();
        $p->register(new Scalar());

        $nodes = $p->parse($this->getDoc('testString'));
        $this->assertInstanceOf(Scalar::class, $nodes['scalar']);
        $this->assertSame(["a", "b"], $nodes['scalar']->array_str);
    }

    /**
     * @scalar (array_int = {1, 2, 3})
     */
    public function testInteger()
    {
        $p = new Parser();
        $p->register(new Scalar());

        $nodes = $p->parse($this->getDoc('testInteger'));
        $this->assertInstanceOf(Scalar::class, $nodes['scalar']);
        $this->assertSame([1, 2, 3], $nodes['scalar']->array_int);
    }

    /**
     * @scalar (array_int = {"a": 1, "b": 2, "c": 3})
     */
    public function testIndexedInteger()
    {
        $p = new Parser();
        $p->register(new Scalar());

        $nodes = $p->parse($this->getDoc('testIndexedInteger'));
        $this->assertInstanceOf(Scalar::class, $nodes['scalar']);
        $this->assertSame([
            'a' => 1,
            'b' => 2,
            'c' => 3
        ], $nodes['scalar']->array_int);
    }

    /**
     * @scalar (array_float = {1.5, 2.3, 3.4})
     */
    public function testFloat()
    {
        $p = new Parser();
        $p->register(new Scalar());

        $nodes = $p->parse($this->getDoc('testFloat'));
        $this->assertInstanceOf(Scalar::class, $nodes['scalar']);
        $this->assertEquals([1.5, 2.3, 3.4], $nodes['scalar']->array_float);
    }

    /**
     * @matrix (value={ {1,2,3}, {4,5,6}, {7,8,9} })
     */
    public function testMatrix()
    {
        $p = new Parser();
        $p->register(new Matrix());

        $nodes = $p->parse($this->getDoc('testMatrix'));
        $this->assertInstanceOf(Matrix::class, $nodes['matrix']);
        $this->assertEquals([
            [1, 2, 3],
            [4, 5, 6],
            [7, 8, 9]
        ], $nodes['matrix']->value);
    }
}