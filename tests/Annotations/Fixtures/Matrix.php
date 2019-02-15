<?php
declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Annotations\Tests\Fixtures;

use Spiral\Annotations\AbstractNode;
use Spiral\Annotations\Parser;

class Matrix extends AbstractNode
{
    protected const NAME   = 'matrix';
    protected const SCHEMA = [
        'value' => [
            [Parser::INTEGER]
        ],
    ];
}