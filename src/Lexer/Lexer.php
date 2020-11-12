<?php

declare(strict_types=1);

namespace Joist\Lexer;

use Joist\Exception\LexerException;

class Lexer
{
    private ?string $srcFilePath;

    private ?string $lastError;

    /**
     * @var array<string, Token>
     */
    private array $tokenisedOutput = [];

    public function __construct(?string $srcFilePath = null)
    {
        $this->validateSrcFilePath($srcFilePath);

        $this->srcFilePath = $srcFilePath;
    }

    public function tokenise(): bool
    {
        $this->validateSrcFilePath($this->srcFilePath);

        $string = file_get_contents($this->srcFilePath) ?: '';
        return $this->tokeniseFromString($string);
    }

    public function tokeniseFromString(string $sourceString): bool
    {
        // Reset 'last error' for a fresh tokenisation run
        $this->lastError = null;

        if ($sourceString === '') {
            $this->lastError = 'Cannot tokenise empty string';
            return false;
        }

        $lines = array_values(array_filter(array_map(
            [$this, 'trimWhitespaceAndComments'],
            explode("\n", $sourceString)
        )));

        if (strpos((string) $lines[0], Lexeme::FILE_HEADER) === false) {
            $this->lastError = 'Expected ' . Lexeme::FILE_HEADER . '"<version>" header, none found';
            return false;
        }

        foreach ($lines as $lineNumber => $line) {
            $tokens = $this->getTokensPerLine($line, $lineNumber);
            /** @var string $uniqueId => Token $token */
            foreach ($tokens as $uniqueId => $token) {
                $this->tokenisedOutput[$uniqueId] = $token;
            }
        }

        return true;
    }

    /**
     * @var array<string, array>
     */
    public function getTokenisedOutput(): array
    {
        return [
            'tokens' => array_map(
                [$this, 'jsonSerializeToken'],
                array_values($this->tokenisedOutput)
            )
        ];
    }

    public function getLastError(): ?string
    {
        return $this->lastError;
    }

    /**
     * @var array<string, Token>
     */
    private function getTokensPerLine(string $line, int $lineNumber): array
    {
        $lexemes = [
            TokenType::FILE_HEADER  => Lexeme::FILE_HEADER,
            TokenType::STRING       => Lexeme::STRING,
            TokenType::KEYWORD      => Lexeme::KEYWORDS,
            TokenType::BRACKET_OPEN => Lexeme::BRACKET_OPEN,
        ];
        $typesWithLiterals = [
            TokenType::STRING
        ];

        $tokens = [];
        foreach ($lexemes as $type => $lexeme) {
            $lexemes = $lexeme;
            $chosenLexeme = null;
            $position = null;
            if (!is_array($lexeme)) {
                $lexemes = [$lexeme];
            }
            $literal = null;
            foreach ($lexemes as $lex) {
                $position = strpos($line, $lex);
                if ($position !== false) {
                    $chosenLexeme = $lex;
                }
            }

            if ($chosenLexeme === null || $position === null) {
                // Skip lexeme set altogether if still no match
                continue;
            }
            if (in_array($type, $typesWithLiterals, true)) {
                $subLine = substr($line, $position + 1);
                $endPosition = strpos($subLine, $chosenLexeme);
                if ($endPosition !== false) {
                    $literal = substr($subLine, 0, $endPosition);
                }
            }
            $location = new Location($lineNumber + 1, $position + 1, strlen($chosenLexeme));
            $uniqueId = "$lineNumber.$position.$type";
            if (isset($this->tokenisedOutput[$uniqueId])) {
                // Got this token, onto the next
                continue;
            }

            $tokens[$uniqueId] = new Token(
                $type,
                $chosenLexeme,
                $location,
                $literal
            );
        }

        return $tokens;
    }

    private function jsonSerializeToken(Token $token): array
    {
        return $token->jsonSerialize();
    }

    /**
     * Validate that a given file path exists or throw exception
     */
    private function validateSrcFilePath(?string $srcFilePath): void
    {
        if ($srcFilePath !== null && !file_exists($srcFilePath)) {
            throw new LexerException('Source file not found: ' . $srcFilePath);
        }
    }

    /**
     * Trim whitespace and comments out from each line in the source during lexing
     */
    private function trimWhitespaceAndComments(string $line): string
    {
        $lineParts = explode(Lexeme::LINE_COMMENT, trim($line));
        return $lineParts[0] ?? '';
    }
}
