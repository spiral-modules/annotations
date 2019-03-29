<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Annotations\Tests\Annotation;

use Spiral\Annotations\AbstractAnnotation;
use Spiral\Annotations\Parser;

class Matrix extends AbstractAnnotation
{
    protected const NAME   = 'matrix';
    protected const SCHEMA = [
        'value' => [
            [Parser::INTEGER]
        ],
    ];
}