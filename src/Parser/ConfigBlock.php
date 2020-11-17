<?php

declare(strict_types=1);

namespace Joist\Parser;

use Joist\Lexer\Token;
use Joist\Ast\Config\ConfigBlock as ConfigBlockAst;
use Joist\Ast\Config\Parameter as ParameterAst;
use Joist\Lexer\TokenType;
use Joist\Parser\Enum as EnumParser;
use Joist\Parser\Parser;

class ConfigBlock implements ParserComponent
{
    private Parser $parser;

    private int $startLine = 0;
    private int $endLine = 0;

    private ?ConfigBlockAst $astConfigBlock;

    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    public function parse(array $tokens, array $metadata = []): ?ConfigBlockAst
    {
        if (empty($tokens)) {
            return null;
        }

        $this->setStartEndLines($tokens);

        if ($this->startLine === 0 && $this->startLine === $this->endLine) {
            return null;
        }

        $this->astConfigBlock = new ConfigBlockAst();

        // Loop through the relevant lines and find tokens relevant to a config block
        $currentLine = $this->startLine;
        while ($currentLine < $this->endLine) {
            $currentLine++;

            $this->parser->setSearchLine($currentLine);
            $tokensForLine = array_values(array_filter($tokens, [$this->parser, 'filterByLine']));
            $this->parser->setSearchLine(null);

            $identifierName = null;
            $identifierType = null;
            foreach ($tokensForLine as $tokenForLine) {
                if ($tokenForLine->getType() === TokenType::IDENTIFIER) {
                    $identifierName = $tokenForLine->getLiteral();
                }

                if ($tokenForLine->getType() === TokenType::IDENTIFIER_TYPE) {
                    $identifierType = $tokenForLine->getLexeme();
                    if ($identifierType === 'enum') {
                        $this->parseEnum($tokens, ['name' => $identifierName]);
                        // Reset name/type/values
                        $identifierName = null;
                        $identifierType = null;
                        break;
                    }
                }

                if (isset($identifierName, $identifierType)) {
                    $this->astConfigBlock->addParameter(
                        $identifierName,
                        $identifierType,
                    );
                    // Reset name/type/values
                    $identifierName = null;
                    $identifierType = null;
                }
            }
        }

        return $this->astConfigBlock;
    }

    private function parseEnum(array $tokens, array $metadata = []): void
    {
        $enumParser = new EnumParser($this->parser);
        $enumAst = $enumParser->parse($tokens, $metadata);
        if ($enumAst !== null) {
            $this->astConfigBlock->addParameterAst($enumAst);
        }
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
                $token->getType() === TokenType::KEYWORD
                && $token->getLexeme() === 'config'
            ) {
                $this->startLine = $token->getLocation()->getLine();
            }

            if (
                $token->getType() === TokenType::SYMBOL
                && $token->getLexeme() === '}'
            ) {
                $this->endLine = $token->getLocation()->getLine();
            }
        }
    }
}
