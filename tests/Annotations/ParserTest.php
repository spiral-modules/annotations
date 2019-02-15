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
use Spiral\Annotations\Tests\Node\Nested;

class ParserTest extends BaseTest
{
    /**
     * @expectedException \Spiral\Annotations\Exception\ParserException
     */
    public function testEmpty()
    {
        $p = new Parser();
        $p->register(new Nested());
        $p->register(new Nested());
    }
}