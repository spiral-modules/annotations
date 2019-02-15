<?php
declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Annotations;

use Spiral\Annotations\Exception\AttributeException;
use Spiral\Annotations\Exception\ParserException;
use Spiral\Annotations\Exception\SyntaxException;
use Spiral\Annotations\Exception\ValueException;

/**
 * Parser parses docComments using list of node enter-points. Each node must define list of it's attributes. Attribute
 * can point to nested node or array or nested nodes which will be parsed recursively.
 */
final class Parser
{
    // Embedded node types
    public const STRING  = 1;
    public const INTEGER = 2;
    public const FLOAT   = 3;
    public const BOOL    = 4;
    public const NULL    = 16;

    /** @var DocLexer */
    private $lexer;

    /** @var NodeInterface[] */
    private $nodes = [];

    /** @var string */
    private $context = '';

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
        $this->context = $body;

        if ($this->nodes === []) {
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

    /**
     * Finds the first valid annotation
     *
     * @param string $input The docblock string to parse
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

    /**
     * Iterate over all node definitions.
     *
     * @return \Generator
     */
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

    /**
     * Parse node definition.
     *
     * @return \Generator
     */
    private function node(): \Generator
    {
        $this->match([DocLexer::T_AT]);

        // check if we have an annotation
        $name = $this->identifier();

        if (!isset($this->nodes[$name])) {
            // undefined node or not a node at all
            return;
        }

        /** @var NodeInterface $node */
        $node = clone $this->nodes[$name];

        if ($this->lexer->isNextToken(DocLexer::T_OPEN_PARENTHESIS)) {
            foreach ($this->attributes($this->nodes[$name]) as $attribute => $value) {
                $node->setAttribute($attribute, $value);
            }
        }

        yield $name => $node;
    }

    /**
     * Parse node attributes;
     *
     * @param NodeInterface $node
     * @return \Generator
     */
    private function attributes(NodeInterface $node): \Generator
    {
        $this->match([DocLexer::T_OPEN_PARENTHESIS]);

        // Parsing thought list of attributes
        while ($this->lexer->lookahead !== null) {
            if ($this->lexer->isNextToken(DocLexer::T_CLOSE_PARENTHESIS)) {
                $this->lexer->moveNext();

                // done with attributes definition
                return;
            }

            if ($this->lexer->isNextToken(DocLexer::T_COMMA)) {
                $this->lexer->moveNext();

                // next attribute
                continue;
            }

            $name = $this->identifier();
            $this->match([DocLexer::T_EQUALS]);

            if (!isset($node->getSchema()[$name])) {
                throw new AttributeException(sprintf(
                    "Undefined node attribute %s->%s",
                    get_class($node),
                    $name
                ));
            }

            try {
                yield $name => $this->value($node->getSchema()[$name]);
            } catch (ValueException $e) {
                throw new AttributeException(
                    sprintf("Invalid attribute %s.%s: %s", get_class($node), $name, $e->getMessage()),
                    0,
                    $e
                );
            }
        }
    }

    /**
     * Parse single value definition (including nested values).
     *
     * @param mixed $type Expected value type.
     * @return mixed
     */
    private function value($type)
    {
        if (is_array($type)) {
            return iterator_to_array($this->array(current($type)));
        }

        if ($type instanceof NodeInterface) {
            // name clarification (Doctrine like)
            if ($this->lexer->isNextToken(DocLexer::T_AT)) {
                $this->lexer->moveNext();

                $name = $this->identifier();
                if ($name != $type->getName()) {
                    throw new AttributeException(sprintf(
                        "Expected node type %s given %s",
                        $type->getName(),
                        $name
                    ));
                }
            }

            $node = clone $type;
            foreach ($this->attributes($node) as $attribute => $value) {
                $node->setAttribute($attribute, $value);
            }

            return $node;
        }

        return $this->filter($this->rawValue(), $type);
    }

    /**
     * Ensure value type of throw an error.
     *
     * @param mixed $value
     * @param int   $type
     * @return mixed
     */
    private function filter($value, int $type)
    {
        if (is_null($value) && $type & self::NULL === 0) {
            throw new ValueException("Value can not be null");
        }

        switch ($type) {
            case self::INTEGER:
                if (!is_integer($value)) {
                    throw new ValueException("value `{$value}` must be integer");
                }

                return (int)$value;

            case self::FLOAT:
                if (!is_float($value)) {
                    throw new ValueException("value `{$value}` must be float");
                }

                return (float)$value;

            case self::BOOL:
                if (!is_bool($value)) {
                    throw new ValueException("value `{$value}` must be boolean");
                }

                return (bool)$value;
        }

        return $value;
    }

    /**
     * Parse array definition.
     *
     * @param mixed $type
     * @return \Generator
     */
    private function array($type): \Generator
    {
        $this->match([DocLexer::T_OPEN_CURLY_BRACES]);

        while ($this->lexer->lookahead !== null) {

            if ($this->lexer->isNextToken(DocLexer::T_CLOSE_CURLY_BRACES)) {
                $this->lexer->moveNext();

                // done with node definition
                return;
            }

            if ($this->lexer->isNextToken(DocLexer::T_COMMA)) {
                $this->lexer->moveNext();

                // next element
                continue;
            }

            $next = $this->lexer->glimpse();
            if (is_array($next) && $next['type'] === DocLexer::T_COLON) {
                $key = $this->rawValue();
                $this->match([DocLexer::T_COLON]);

                // indexed element
                yield $key => $this->value($type);
                continue;
            }

            // un-indexed element
            yield $this->value($type);
        }
    }

    /**
     * Parse simple raw value definition.
     *
     * @return bool|float|int|string|null
     * @return mixed
     *
     * @throws SyntaxException
     */
    private function rawValue()
    {
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
        }

        throw $this->syntaxError(
            [
                DocLexer::T_NULL,
                DocLexer::T_FALSE,
                DocLexer::T_TRUE,
                DocLexer::T_FLOAT,
                DocLexer::T_INTEGER,
                DocLexer::T_STRING
            ],
            $this->lexer->lookahead
        );
    }

    /**
     * Fetch name identifier (string value).
     *
     * @return string
     *
     * @throws SyntaxException
     */
    private function identifier(): string
    {
        if (!$this->lexer->isNextTokenAny([DocLexer::T_STRING, DocLexer::T_IDENTIFIER])) {
            throw $this->syntaxError([DocLexer::T_STRING, DocLexer::T_IDENTIFIER]);
        }

        $this->lexer->moveNext();
        return $this->lexer->token['value'];
    }

    /**
     * Attempts to match the given token with the current lookahead token.
     * If they match, updates the lookahead token; otherwise raises a syntax error.
     *
     * @param array $expected Type of token.
     * @return boolean True if tokens match; false otherwise.
     *
     * @throws SyntaxException
     */
    private function match(array $expected): bool
    {
        if (!$this->lexer->isNextTokenAny($expected)) {
            throw $this->syntaxError($expected, $this->lexer->lookahead);
        }

        return $this->lexer->moveNext();
    }

    /**
     * Throw syntax exception.
     *
     * @param array      $expected
     * @param null|array $token
     * @return SyntaxException
     */
    private function syntaxError(array $expected, array $token = null): SyntaxException
    {
        if ($token === null) {
            $token = $this->lexer->lookahead;
        }

        foreach ($expected as &$ex) {
            $ex = DocLexer::TOKEN_MAP[$ex];
            unset($ex);
        }

        $message = sprintf(
            'Expected %s, got %s in %s',
            join('|', $expected),
            ($this->lexer->lookahead === null)
                ? 'end of string'
                : sprintf("'%s' at position %s", $token['value'], $token['position']),
            $this->context
        );

        return new SyntaxException($message);
    }
}
