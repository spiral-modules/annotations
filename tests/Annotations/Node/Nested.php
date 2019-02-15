<?php
declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Annotations\Tests\Node;

use Spiral\Annotations\Node;
use Spiral\Annotations\Parser;

class Nested extends Node
{
    protected const NAME   = 'nested';
    protected const SCHEMA = [
        'name'       => Parser::STRING,
        'scalar'     => Scalar::class,
        'scalar_arr' => [Scalar::class],
        'mm'         => [[Matrix::class]]
    ];
}