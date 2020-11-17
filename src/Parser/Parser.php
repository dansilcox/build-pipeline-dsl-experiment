<?php

declare(strict_types=1);

namespace Joist\Parser;

use Joist\Ast\Build;
use Joist\Ast\FileHeader as FileHeaderAst;
use Joist\Ast\Config\ConfigBlock as ConfigBlockAst;
use Joist\Lexer\Token;
use Joist\Parser\ConfigBlock as ConfigBlockParser;
use Joist\Parser\FileHeader as FileHeaderParser;

class Parser
{
    private array $tokens;

    private Build $build;

    private ?int $searchLine = null;

    public function __construct(array $tokens)
    {
        $this->tokens = $tokens;

        $this->build = new Build(
            $this->parseFileHeader(),
            $this->parseConfigBlock()
        );
    }

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
    public function filterByLine(Token $token): bool
    {
        return $token->getLocation()->getLine() === $this->searchLine;
    }

    public function getTokensByLine(int $line): array
    {
        $this->searchLine = $line;
        $filtered = array_values(array_filter($this->tokens, [$this, 'filterByLine']));
        $this->searchLine = null;

        return $filtered;
    }

    private function parseFileHeader(): FileHeaderAst
    {
        return (new FileHeaderParser($this))->parse($this->tokens);
    }

    private function parseConfigBlock(): ?ConfigBlockAst
    {
        return (new ConfigBlockParser($this))->parse($this->tokens);
    }
}
