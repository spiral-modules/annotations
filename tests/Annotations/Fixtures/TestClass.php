<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
declare(strict_types=1);

namespace Spiral\Annotations\Tests\Fixtures;

use Spiral\Annotations\Tests\Fixtures\Annotation\Another;
use Spiral\Annotations\Tests\Fixtures\Annotation\Route;
use Spiral\Annotations\Tests\Fixtures\Annotation\Value;

/**
 * @Value(value="abc")
 */
class TestClass
{
    /** @Another(id="123") */
    public $name;

    /**
     * @Route(path="/")
     */
    public function testMethod()
    {
    }
}
