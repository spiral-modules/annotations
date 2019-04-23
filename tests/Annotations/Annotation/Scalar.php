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

class Scalar extends AbstractAnnotation
{
    protected const NAME   = 'scalar';
    protected const SCHEMA = [
        'string'      => Parser::STRING,
        'integer'     => Parser::INTEGER,
        'bool'        => Parser::BOOL,
        'float'       => Parser::FLOAT,
        'mixed'       => Parser::MIXED,
        'array_str'   => [Parser::STRING],
        'array_int'   => [Parser::INTEGER],
        'array_float' => [Parser::FLOAT],
    ];
}