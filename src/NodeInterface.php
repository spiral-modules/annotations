<?php
declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Annotations;

// todo: must be cloneable
interface NodeInterface
{
    public function getName(): string;

    /**
     * Return Node schema in a form of [name => Node|SCALAR|[Node]].
     *
     * @return array
     */
    public function getSchema(): array;

    public function setProperty(string $name, $value);
}