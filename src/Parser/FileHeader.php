<?php

declare(strict_types=1);

namespace Joist\Parser;

use Joist\Exception\Parser\SyntaxException;
use Joist\Exception\ErrorCode;
use Joist\Lexer\Token;
use Joist\Lexer\TokenType;
use Joist\Ast\FileHeader as FileHeaderAst;

class FileHeader implements ParserComponent
{

    private Parser $parser;

    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    public function parse(array $tokens, array $metadata = []): ?FileHeaderAst
    {
        if (empty($tokens)) {
            return null;
        }

        $filteredTokens = $this->parser->getTokensByLine(1);

        return new FileHeaderAst($this->getVersionFromTokens($filteredTokens));
    }

    /**
     * @param array $fileHeaderTokens
     *
     * @return string
     */
    private function getVersionFromTokens(array $fileHeaderTokens): string
    {
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
                break;
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
            $version = FileHeaderAst::DEFAULT_VERSION;
        }

        return $version;
    }
}
