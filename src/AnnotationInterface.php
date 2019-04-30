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
 * Carries information about annotation structure and it values.
 */
interface AnnotationInterface
{
    /**
     * Public and unique node name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Return Node schema in a form of [name => Node|SCALAR|[Node]].
     *
     * @return array
     */
    public function getSchema(): array;

    /**
     * Set node attribute value.
     *
     * @param string $name
     * @param mixed  $value
     */
    public function setAttribute(string $name, $value);
}