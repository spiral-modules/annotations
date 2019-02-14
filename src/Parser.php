<?php
declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Annotations;

use Doctrine\Common\Lexer\AbstractLexer;

class Parser
{
    // Embedded node types
    public const STRING  = 1;
    public const INTEGER = 2;
    public const FLOAT   = 3;
    public const BOOL    = 4;
    public const ARRAY   = 5;

    /** @var NodeInterface[] */
    private $nodes = [];

    private $lexer;

    public function __construct(AbstractLexer $lexer)
    {
        $this->lexer = $lexer;
    }
}