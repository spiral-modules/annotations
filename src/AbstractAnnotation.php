<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
declare(strict_types=1);

namespace Spiral\Annotations;

/**
 * Constant based node declaration.
 */
abstract class AbstractAnnotation implements AnnotationInterface
{
    protected const NAME   = '';
    protected const SCHEMA = [];

    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return static::NAME;
    }

    /**
     * @inheritdoc
     */
    public function getSchema(): array
    {
        $schema = static::SCHEMA;

        array_walk_recursive($schema, function (&$v) {
            if (is_string($v) && class_exists($v)) {
                $v = new $v;
            }
        });

        return $schema;
    }

    /**
     * @inheritdoc
     */
    public function setAttribute(string $name, $value)
    {
        $this->{$name} = $value;
    }
}