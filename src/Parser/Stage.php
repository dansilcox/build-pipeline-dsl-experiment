<?php

declare(strict_types=1);

namespace Joist\Parser;

use Joist\Ast\Stage\Stage as StageAst;
use Joist\Lexer\Token;
use Joist\Parser\Parser;

class Stage
{
    private Parser $parser;

    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * @param array<Token> $tokens
     *
     * @return array<StageAst>
     */
    public function parse(array $tokens): array
    {
        return [];
    }
}
