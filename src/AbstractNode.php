<?php
declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Annotations;

/**
 * Constant based node declaration.
 */
abstract class AbstractNode implements NodeInterface
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

        // todo: init inner classes

        return $schema;
    }

    /**
     * @inheritdoc
     */
    public function setProperty(string $name, $value)
    {
        $this->{$name} = $value;
    }
}