<?php

declare(strict_types=1);

namespace Joist\Parser;

use Joist\Ast\AstComponent;
use Joist\Ast\Config\Parameter as ParameterAst;
use Joist\Lexer\Token;
use Joist\Lexer\TokenType;

class Enum implements ParserComponent
{
    private Parser $parser;

    private ?int $startLine = null;
    private ?int $endLine = null;

    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    public function parse(array $tokens, array $metadata = []): ?ParameterAst
    {
        if (empty($tokens)) {
            return null;
        }

        $this->setStartEndLines($tokens);

        if ($this->startLine === 0 && $this->startLine === $this->endLine) {
            return null;
        }

        $identifier = $metadata['name'] ?? null;
        $type = 'enum';
        $allowedValues = [];

        // Loop through the relevant lines and find tokens relevant to a config block
        $currentLine = $this->startLine;
        while ($currentLine < $this->endLine) {
            $currentLine++;
            $tokensForLine = $this->parser->getTokensByLine($currentLine);

            foreach ($tokensForLine as $token) {
                if ($token->getType() === TokenType::IDENTIFIER) {
                    $identifier = $token->getLiteral();
                    continue;
                }

                if (
                    $token->getType() === TokenType::STRING
                    || $token->getType() === TokenType::NUMBER
                ) {
                    $allowedValues[] = $token->getLiteral();
                    continue;
                }

                if (
                    $token->getType() === TokenType::SYMBOL
                    && $token->getLexeme() === ']'
                ) {
                    break 2;
                }
            }
        }

        if ($identifier === null) {
            return null;
        }

        return new ParameterAst(
            $identifier,
            $type,
            empty($allowedValues) ? null : $allowedValues
        );
    }

    /**
     * Find the start and end lines to parse between
     *
     * @param array $tokens
     */
    private function setStartEndLines(array $tokens): void
    {
        foreach ($tokens as $token) {
            if (
                $token->getType() === TokenType::IDENTIFIER_TYPE
                && $token->getLexeme() === 'enum'
            ) {
                $this->startLine = $token->getLocation()->getLine();
            }

            if (
                $token->getType() === TokenType::SYMBOL
                && $token->getLexeme() === ']'
            ) {
                $this->endLine = $token->getLocation()->getLine();
            }
        }
    }
}
