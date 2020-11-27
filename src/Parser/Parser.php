<?php

declare(strict_types=1);

namespace Joist\Parser;

use Joist\Ast\Build;
use Joist\Ast\FileHeader as FileHeaderAst;
use Joist\Ast\Config\ConfigBlock as ConfigBlockAst;
use Joist\Lexer\Token;
use Joist\Lexer\TokenType;
use Joist\Parser\ConfigBlock as ConfigBlockParser;
use Joist\Parser\FileHeader as FileHeaderParser;
use Joist\Parser\Mapper as TokenMapper;
use Joist\Parser\Stage as StageParser;
use Joist\Exception\Parser\SyntaxException;

class Parser
{
    private array $tokens;

    private Build $build;

    private ?int $searchLine = null;

    /**
     * @param array<Token> $tokens
     */
    public function __construct(array $tokens, TokenMapper $mapper)
    {
        if (isset($tokens['tokens'])) {
            $tokens = $tokens['tokens'];
        }

        if (empty($tokens) || !isset($tokens[0])) {
            throw new SyntaxException('Cannot parse an empty array');
        }
        
        if (!($tokens[0] instanceof Token)) {
            $tokens = $mapper->fromArray($tokens);
        }
        
        $this->tokens = $tokens;

        $this->build = new Build(
            $this->parseFileHeader(),
            $this->parseConfigBlock(),
            $this->getStages()
        );
    }

    /** 
     * @return Build
     */
    public function getBuild(): Build
    {
        return $this->build;
    }

    /**
     * Search line 'find by line' filter
     *
     * @param int|null $searchLine
     * @deprecated use getTokensByLine()
     */
    public function setSearchLine(?int $searchLine): void
    {
        $this->searchLine = $searchLine;
    }

    /**
     * @return int|null
     *
     * @internal Mostly just for unit test purposes
     * @deprecated use getTokensByLine()
     */
    public function getSearchLine(): ?int
    {
        return $this->searchLine;
    }

    /**
     * Filter by line
     *
     * @param Token $token
     *
     * @return bool
     * @deprecated use getTokensByLine()
     */
    private function filterByLine(Token $token): bool
    {
        return $token->getLocation()->getLine() === $this->searchLine;
    }

    /**
     * @param int $line
     * 
     * @return array
     */
    public function getTokensByLine(int $line): array
    {
        $this->searchLine = $line;
        $filtered = array_values(array_filter($this->tokens, [$this, 'filterByLine']));
        $this->searchLine = null;

        return $filtered;
    }

    /**
     * @return FileHeaderAst
     */
    private function parseFileHeader(): FileHeaderAst
    {
        return (new FileHeaderParser($this))->parse($this->tokens);
    }

    /**
     * @return ConfigBlockAst|null
     */
    private function parseConfigBlock(): ?ConfigBlockAst
    {
        return (new ConfigBlockParser($this))->parse($this->tokens);
    }

    /**
     * @return array<StageAst>
     */
    private function getStages(): array
    {
        $insideStageBlock = false;
        $tokenBlock = [];
        $stages = [];
        
        foreach ($this->tokens as $token) {
            $stageParser = new StageParser($this);
            if (
                $token->getType() === TokenType::KEYWORD
                && $token->getLexeme() === 'stage'
            ) {
                $insideStageBlock = true;
                $tokenBlock = [];
            }

            if ($insideStageBlock) {
                $tokenBlock[] = $token;
            }

            if (
                $token->getType() === TokenType::SYMBOL
                && $token->getLexeme() === '}'
                && $insideStageBlock
            ) {
                $insideStageBlock = false;
                if (!empty($tokenBlock)) {
                    $stage = $stageParser->parse($tokenBlock);
                    if ($stage !== null) {
                        $stages[$stage->getName()] = $stage;
                    }
                }
            }
        }

        return $stages;
    }
}
