<?php

declare(strict_types=1);

namespace Joist\Parser;

use Joist\Ast\Stage\Stage as StageAst;
use Joist\Ast\Stage\StageHeader as StageHeaderAst;
use Joist\Ast\Stage\Conditional\Always as AlwaysAst;
use Joist\Lexer\Token;
use Joist\Parser\Parser;
use Joist\Lexer\TokenType;

class Stage
{
    private int $startLine = 0;
    private int $endLine = 0;

    private Parser $parser;

    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * @param array<Token> $tokens
     *
     * @return StageAst|null
     */
    public function parse(array $tokens): ?StageAst
    {
        if (empty($tokens)) {
            return null;
        }

        $this->setStartEndLines($tokens);

        if ($this->startLine === 0 && $this->startLine === $this->endLine) {
            return null;
        }

        $name = null;
        $conditional = null;

        $currentLine = $this->startLine;

        $stageHeader = null;
        while ($currentLine <= $this->endLine) {
            $lineTokens = $this->parser->getTokensByLine($currentLine);

            if ($currentLine === $this->startLine) {
                $stageHeader = $this->parseStageHeader($lineTokens);
            }

            $currentLine++;
        }

        if ($stageHeader === null) {
            return null;
        }

        return new StageAst(
            $stageHeader
        );
    }

    /**
     * Find the start and end lines to parse between
     *
     * @param array $tokens
     */
    private function setStartEndLines(array $tokens): void
    {
        $expectedStartLine = null;
        foreach ($tokens as $token) {
            if (
                $token->getType() === TokenType::KEYWORD
                && $token->getLexeme() === 'stage'
            ) {
                if ($this->startLine === 0) {
                    $this->startLine = $token->getLocation()->getLine();
                }
                continue;
            }

            if (
                $token->getType() === TokenType::SYMBOL
                && $token->getLexeme() === '}'
            ) {
                if ($this->endLine === 0 && $this->startLine !== 0) {
                    $this->endLine = $token->getLocation()->getLine();
                }
                continue;
            }
        }
    }

    /**
     * @param array $lineTokens
     *
     * @return StageHeaderAst|null
     */
    private function parseStageHeader(array $lineTokens): ?StageHeaderAst
    {
        $name = null;
        $conditional = null;
        foreach ($lineTokens as $token) {
            if (
                $token->getType() === TokenType::STRING
            ) {
                $name = $token->getLiteral();
                continue;
            }

            if (
                $token->getType() === TokenType::KEYWORD
                && $token->getLexeme() === 'always'
            ) {
                $conditional = new AlwaysAst();

                continue;
            }
        }
        if ($name === null) {
            return null;
        }

        return new StageHeaderAst($name, $conditional);
    }
}
