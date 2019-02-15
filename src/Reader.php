<?php
declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Annotations;

final class Reader
{
    /** @var Parser */
    private $classParser;

    /** @var Parser */
    private $methodParser;

    /** @var Parser */
    private $propertyParser;

    /**
     * @param Parser|null $parser
     * @param array       $classAnnotations
     * @param array       $methodAnnotations
     * @param array       $propertyAnnotations
     */
    public function __construct(
        Parser $parser = null,
        array $classAnnotations = [],
        array $methodAnnotations = [],
        array $propertyAnnotations = []
    ) {
        $parser = $parser ?? new Parser();

        $this->classParser = clone $parser;
        foreach ($classAnnotations as $annotation) {
            $this->classParser->register($annotation);
        }

        $this->methodParser = clone $parser;
        foreach ($methodAnnotations as $annotation) {
            $this->methodParser->register($annotation);
        }

        $this->propertyParser = clone $parser;
        foreach ($propertyAnnotations as $annotation) {
            $this->propertyParser->register($annotation);
        }
    }

    /**
     * Get all annotations found in class doc comment.
     *
     * @param \ReflectionClass $class
     * @return array
     */
    public function classAnnotations(\ReflectionClass $class): array
    {
        return $this->classParser->parse($class->getDocComment());
    }

    /**
     * Get all annotations found in class method doc comment.
     *
     * @param \ReflectionClass $class
     * @param string           $method
     * @return array
     *
     * @throws \ReflectionException
     */
    public function methodAnnotations(\ReflectionClass $class, string $method): array
    {
        return $this->methodParser->parse($class->getMethod($method)->getDocComment());
    }

    /**
     * Get all annotations found in class property doc comment.
     *
     * @param \ReflectionClass $class
     * @param string           $property
     * @return array
     *
     * @throws \ReflectionException
     */
    public function propertyAnnotations(\ReflectionClass $class, string $property)
    {
        return $this->propertyParser->parse($class->getProperty($property)->getDocComment());
    }
}
