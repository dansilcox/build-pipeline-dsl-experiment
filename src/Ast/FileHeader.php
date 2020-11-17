<?php

declare(strict_types=1);

namespace Joist\Ast;

use Joist\Ast\AstComponent;
use Joist\Lexer\Lexeme;
use Joist\Lexer\Location;
use Joist\Lexer\TokenType;
use Joist\Lexer\Token;
use Joist\Exception\ErrorCode;
use Joist\Exception\Parser\SyntaxException;

class FileHeader implements AstComponent
{
    public const DEFAULT_VERSION = '0.1.0';

    private string $version;

    public function __construct(string $version = null)
    {
        $this->version = $version ?? self::DEFAULT_VERSION;
    }

    public function getVersion(): string
    {
        return $this->version;
    }
}
