<?php
declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Annotations;

use Spiral\Annotations\Exception\AnnotationException;
use Spiral\Annotations\Exception\ParserException;

/**
 * Parser parses docComments using list of node enter-points. Each node must define list of it's attributes. Attribute
 * can point to nested node or array or nested nodes which will be parsed recursively.
 */
class Parser
{
    // Embedded node types
    public const STRING  = 1;
    public const INTEGER = 2;
    public const FLOAT   = 3;
    public const BOOL    = 4;

    /** @var DocLexer */
    private $lexer;

    /** @var NodeInterface=[] */
    private $nodes = [];

    /**
     * @param DocLexer $lexer
     */
    public function __construct(DocLexer $lexer = null)
    {
        $this->lexer = $lexer ?? new DocLexer();
    }

    /**
     * Register new enter-point node. Each parsed node will be based on cloned copy of provided enter-point.
     *
     * @param NodeInterface $node
     * @return self
     *
     * @throws ParserException
     */
    public function register(NodeInterface $node): self
    {
        if (isset($this->nodes[$node->getName()])) {
            throw new ParserException("Node with name {$node->getName()} already registered");
        }

        $this->nodes[$node->getName()] = $node;
        return $this;
    }

    /**
     * Parse given docComment and return named list of captured nodes. If node has been found more than
     * once in a target comment - parse method will return named array.
     *
     * @param string $body
     * @return array
     *
     * @throws ParserException
     */
    public function parse(string $body): array
    {
        if (is_null($this->nodes)) {
            throw new ParserException("Unable to parse without starting nodes");
        }

        $this->lexer->setInput(trim(substr($body, $this->findStart($body)), '* /'));
        $this->lexer->moveNext();

        $result = [];
        foreach ($this->iterate() as $name => $node) {
            if (!isset($result[$name])) {
                $result[$name] = $node;
                continue;
            }

            // multiple occasions
            if (!is_array($result[$name])) {
                $result[$name] = [$result[$name]];
            }

            $result[$name][] = $node;
        }

        return $result;
    }

    private function iterate(): \Generator
    {
        while ($this->lexer->lookahead !== null) {
            // current token
            $t = $this->lexer->token;

            // next token
            $n = $this->lexer->lookahead;

            // looking for initial token
            if ($n['type'] !== DocLexer::T_AT) {
                $this->lexer->moveNext();
                continue;
            }

            // make sure that @ points to identifier
            if ($t !== null && $n['position'] === $t['position'] + strlen($t['value'])) {
                $this->lexer->moveNext();
                continue;
            }

            yield from $this->node();
        }
    }

    private function node(): \Generator
    {
        $this->match([DocLexer::T_AT]);

        // check if we have an annotation
        $name = $this->identifier();

        if (!isset($this->nodes[$name])) {
            // undefined node
            return;
        }

        yield $name => $this->parseNode(clone $this->nodes[$name]);
    }

    private function parseNode(NodeInterface $node): NodeInterface
    {
        $next = $this->lexer->glimpse();
        if (
            is_null($next)
            || $next['type'] === DocLexer::T_AT
            || $next['type'] == DocLexer::T_NONE
        ) {
            // empty node declaration
            return $node;
        }

        // todo: it might not be parentesis as well (empty nodes)

        $this->match([DocLexer::T_OPEN_PARENTHESIS]);

        // Parsing thought list of attributes

        while ($this->lexer->lookahead !== null) {
            // done with node definition
            if ($this->lexer->lookahead['type'] === DocLexer::T_CLOSE_PARENTHESIS) {
                $this->lexer->moveNext();
                return $node;
            }

            if ($this->lexer->lookahead['type'] === DocLexer::T_COMMA) {
                $this->lexer->moveNext();
                continue;
            }

            $this->attribute($node);
        }

        $this->match([DocLexer::T_CLOSE_PARENTHESIS]);
        return $node;
    }

    private function attribute(NodeInterface $node)
    {
        $name = $this->identifier();
        $this->match([DocLexer::T_EQUALS]);

        if (!isset($node->getSchema()[$name])) {
            throw new AnnotationException("Undefined node attribute {$name}");
        }

        $node->setProperty($name, $this->value($node->getSchema()[$name]));
    }

    /**
     * PlainValue ::= integer | string | float | boolean | Array | Annotation
     *
     * @return mixed
     */
    private function value($type)
    {
        // todo: nested(!)
        if (is_array($type)) {
            return $this->array(current($type));
        }

        if ($type instanceof NodeInterface) {
            if ($this->lexer->isNextToken(DocLexer::T_AT)) {
                $this->lexer->moveNext();
                $name = $this->identifier();
                if ($name != $type->getName()) {
                    throw new AnnotationException("unexpected node type");
                }
            }

            return $this->parseNode(clone $type);
        }

        if ($this->lexer->isNextToken(DocLexer::T_IDENTIFIER)) {
            return $this->identifier();
        }

        switch ($this->lexer->lookahead['type']) {
            case DocLexer::T_STRING:
                $this->match([DocLexer::T_STRING]);
                return $this->lexer->token['value'];

            case DocLexer::T_INTEGER:
                $this->match([DocLexer::T_INTEGER]);
                return (int)$this->lexer->token['value'];

            case DocLexer::T_FLOAT:
                $this->match([DocLexer::T_FLOAT]);
                return (float)$this->lexer->token['value'];

            case DocLexer::T_TRUE:
                $this->match([DocLexer::T_TRUE]);
                return true;

            case DocLexer::T_FALSE:
                $this->match([DocLexer::T_FALSE]);
                return false;

            case DocLexer::T_NULL:
                $this->match([DocLexer::T_NULL]);
                return null;

            default:
                dump($this->lexer->lookahead);
                return 'wat';
        }
    }

    private function array($type): array
    {
        $this->match([DocLexer::T_OPEN_CURLY_BRACES]);

        // Parsing thought list of attributes
        $result = [];
        while ($this->lexer->lookahead !== null) {
            // done with node definition
            if ($this->lexer->lookahead['type'] === DocLexer::T_CLOSE_CURLY_BRACES) {
                $this->lexer->moveNext();
                return $result;
            }

            if ($this->lexer->lookahead['type'] === DocLexer::T_COMMA) {
                $this->lexer->moveNext();
                continue;
            }

            $next = $this->lexer->glimpse();
            if (is_array($next) && $next['type'] === DocLexer::T_COLON) {
                $key = $this->value(null);
                $this->match([DocLexer::T_COLON]);
                $result[$key] = $this->value($type);

                continue;
            }

            $result[] = $this->value($type);
        }

        $this->match([DocLexer::T_CLOSE_CURLY_BRACES]);

        return $result;
    }

    /**
     * Name ::= string
     *
     * @return string
     */
    private function identifier(): string
    {
        $this->lexer->moveNext();
        return $this->lexer->token['value'];
    }

    /**
     * Attempts to match the given token with the current lookahead token.
     * If they match, updates the lookahead token; otherwise raises a syntax error.
     *
     * @param array $token Type of token.
     * @return boolean True if tokens match; false otherwise.
     */
    private function match(array $token): bool
    {
        if (!$this->lexer->isNextTokenAny($token)) {
            // todo: proper errors
            throw new AnnotationException(json_encode($token));
        }

        return $this->lexer->moveNext();
    }

    /**
     * Finds the first valid annotation
     *
     * @param string $input The docblock string to parse
     *
     * @return int|null
     */
    private function findStart(string $input): ?int
    {
        $pos = 0;

        // search for first valid annotation
        while (($pos = strpos($input, '@', $pos)) !== false) {
            $preceding = substr($input, $pos - 1, 1);

            // if the @ is preceded by a space, a tab or * it is valid
            if ($pos === 0 || $preceding === ' ' || $preceding === '*' || $preceding === "\t") {
                return $pos;
            }

            $pos++;
        }

        return null;
    }

    //    private function syntaxError($expected, $token = null)
    //    {
    //        if ($token === null) {
    //            $token = $this->lexer->lookahead;
    //        }
    //
    //        $message  = sprintf('Expected %s, got ', $expected);
    //        $message .= ($this->lexer->lookahead === null)
    //            ? 'end of string'
    //            : sprintf("'%s' at position %s", $token['value'], $token['position']);
    //
    //        if (strlen($this->context)) {
    //            $message .= ' in ' . $this->context;
    //        }
    //
    //        $message .= '.';
    //
    //        throw AnnotationException::syntaxError($message);
    //    }

}
