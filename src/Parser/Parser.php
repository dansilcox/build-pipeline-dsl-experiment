<?php

declare(strict_types=1);

namespace Joist\Parser;

use Joist\Ast\Build;
use Joist\Lexer\TokenType;
use Joist\Lexer\Token;
use Joist\Exception\ErrorCode;
use Joist\Exception\Parser\SyntaxException;

class Parser
{
    private const DEFAULT_VERSION = '0.1.0';

    private Build $build;

    public function __construct(array $tokens)
    {
        $fileHeaderTokens = array_values(array_filter($tokens, static function (Token $token): bool {
            return $token->getLocation()->getLine() === 1;
        }));
        $version = null;
        $gotFileHeaderToken = false;
        $fileHeaderTokenLocation = null;
        foreach ($fileHeaderTokens as $fileHeaderToken) {
            if ($fileHeaderToken->getType() === TokenType::FILE_HEADER) {
                $gotFileHeaderToken = true;
                $fileHeaderTokenLocation = $fileHeaderToken->getLocation();
            }

            if ($gotFileHeaderToken && $fileHeaderToken->getType() === TokenType::STRING) {
                $version = $fileHeaderToken->getLiteral();
            }
        }

        if ($version === null) {
            if ($gotFileHeaderToken) {
                throw new SyntaxException(
                    'Invalid file header, missing version identifier',
                    ErrorCode::SYNTAX_ERROR_FILE_HEADER,
                    $fileHeaderTokenLocation
                );
            }
            $version = self::DEFAULT_VERSION;
        }

        $this->build = new Build($version);
    }

    public function getBuild(): Build
    {
        return $this->build;
    }
}
