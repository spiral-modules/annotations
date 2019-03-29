<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Annotations\Tests;

use PHPUnit\Framework\TestCase;

abstract class BaseTest extends TestCase
{
    protected function getDoc(string $method): string
    {
        $r = new \ReflectionClass($this);

        return $r->getMethod($method)->getDocComment();
    }
}