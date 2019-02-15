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
use Spiral\Annotations\Reader;
use Spiral\Annotations\Tests\Annotation\Matrix;
use Spiral\Annotations\Tests\Annotation\Scalar;

/**
 * @nested (scalar=@scalar(string="magic"))
 * @scalar (string="value")
 */
class ReaderTest extends BaseTest
{
    public function testClass()
    {
        $reader = new Reader(new Parser(), [new Scalar()]);

        $annotations = $reader->classAnnotations(new \ReflectionClass($this));
        $this->assertSame('value', $annotations['scalar']->string);
    }

    /**
     * @matrix (value={ {1,2,3}, {4,5,6}, {7,8,9} })
     */
    public function testMethod()
    {
        $reader = new Reader(new Parser(), [], [new Matrix()]);

        $annotations = $reader->methodAnnotations(new \ReflectionClass($this), 'testMethod');
        $this->assertEquals([
            [1, 2, 3],
            [4, 5, 6],
            [7, 8, 9]
        ], $annotations['matrix']->value);
    }

    /**
     * @scalar (float=100.10)
     */
    protected $name;

    /**
     * @matrix (value={ {1,2,3}, {4,5,6}, {7,8,9} })
     */
    public function testProperty()
    {
        $reader = new Reader(new Parser(), [], [], [new Scalar()]);

        $annotations = $reader->propertyAnnotations(new \ReflectionClass($this), 'name');
        $this->assertEquals(100.10, $annotations['scalar']->float);
    }
}