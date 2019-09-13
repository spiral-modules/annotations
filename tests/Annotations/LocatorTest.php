<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
declare(strict_types=1);

namespace Spiral\Annotations\Tests;

use Doctrine\Common\Annotations\AnnotationRegistry;
use PHPUnit\Framework\TestCase;
use Spiral\Annotations\AnnotationLocator;
use Spiral\Annotations\Tests\Fixtures\Annotation\Value;
use Spiral\Annotations\Tests\Fixtures\TestClass;
use Spiral\Tokenizer\ClassLocator;
use Symfony\Component\Finder\Finder;

class LocatorTest extends TestCase
{
    public function testLocateClasses()
    {
        $classes = $this->getLocator(__DIR__ . '/Fixtures')->findClasses(Value::class);
        $classes = iterator_to_array($classes);

        $this->assertCount(1, $classes);

        foreach ($classes as $class) {
            $this->assertSame(TestClass::class, $class->getClass()->getName());
            $this->assertSame('abc', $class->getAnnotation()->value);
        }
    }

    private function getLocator(string $directory): AnnotationLocator
    {
        AnnotationRegistry::registerLoader('class_exists');

        return new AnnotationLocator(
            (new ClassLocator((new Finder())->files()->in([$directory])))
        );
    }
}