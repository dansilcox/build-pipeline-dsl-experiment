<?php

declare(strict_types=1);

namespace Joist\Lexer;

use Joist\Exception\LexerException;

class Lexer
{
    public const ALPHA_NUMERIC_REGEX = '/[^A-Za-z0-9\.]+/';

    public const NON_ALPHA_REGEX = '/[A-Za-z0-9\.]+/';

    private ?string $srcFilePath;

    private ?string $lastError;

    /**
     * @var array<string, Token>
     */
    private array $tokenisedOutput = [];

    private array $lineTokens = [];

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

    public function tokeniseFromString(string $sourceString, bool $forceFileHeader = true): bool
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

        if (empty($lines)) {
            $this->lastError = 'No valid lines found';
            return false;
        }

        if (
            $forceFileHeader
            && strpos((string) $lines[0], Lexeme::FILE_HEADER) === false
        ) {
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
        $words = array_map('trim', explode(' ', $line));
        foreach ($words as $word) {
            $alphaNumericWord = preg_replace(self::ALPHA_NUMERIC_REGEX, '', $word);
            $nonAlphaWord = preg_replace(self::NON_ALPHA_REGEX, '', $word);

            if (strpos($word, Lexeme::FILE_HEADER) !== false) {
                $type = TokenType::FILE_HEADER;
                $position = 0;
                $chosenLexeme = Lexeme::FILE_HEADER;
                $literal = null;
                $this->addLineToken(
                    $type,
                    $lineNumber,
                    $position,
                    $chosenLexeme,
                    $literal
                );
            }

            foreach (str_split($nonAlphaWord) as $char) {
                if (in_array($char, Lexeme::SYMBOLS, true)) {
                    $type = TokenType::SYMBOL;
                    $position = strpos($line, $char) ?: 0;
                    $chosenLexeme = $char;
                    $literal = null;
                    $this->addLineToken(
                        $type,
                        $lineNumber,
                        $position,
                        $chosenLexeme,
                        $literal
                    );
                }

                if (in_array($char, Lexeme::STRING, true)) {
                    $type = TokenType::STRING;
                    $position = strpos($line, $char) ?: 0;
                    $endPosition = strpos($line, $char, 1);
                    $chosenLexeme = $char;

                    $subWord = substr($word, $position + 1);
                    [$literal, $extra] = explode($char, $subWord);
                    $this->addLineToken(
                        $type,
                        $lineNumber,
                        $position,
                        $chosenLexeme,
                        $literal
                    );
                }
            }

            $foundKeyword = null;
            foreach (Lexeme::KEYWORDS as $keyword) {
                if (strpos($alphaNumericWord, $keyword) !== false) {
                    $foundKeyword = $keyword;
                }
            }
            if (in_array($alphaNumericWord, Lexeme::KEYWORDS, true)) {
                $foundKeyword = $alphaNumericWord;
            }

            if ($foundKeyword !== null) {
                $type = TokenType::KEYWORD;
                $position = strpos($line, $foundKeyword) ?: 0;
                $chosenLexeme = $foundKeyword;
                $literal = null;
                $this->addLineToken(
                    $type,
                    $lineNumber,
                    $position,
                    $chosenLexeme,
                    $literal
                );
            }

            if (in_array($alphaNumericWord, Lexeme::IDENTIFIER_TYPES, true)) {
                $type = TokenType::IDENTIFIER_TYPE;
                $position = strpos($line, $alphaNumericWord) ?: 0;
                $chosenLexeme = $alphaNumericWord;
                $literal = null;
                $this->addLineToken(
                    $type,
                    $lineNumber,
                    $position,
                    $chosenLexeme,
                    $literal
                );
            }

            if (
                strpos($word, Lexeme::IDENTIFIER) !== false
                && strpos($line, Lexeme::FILE_HEADER) === false
            ) {
                $type = TokenType::IDENTIFIER;
                $position = strpos($line, Lexeme::IDENTIFIER) ?: 0;
                $chosenLexeme = Lexeme::IDENTIFIER;
                $literal = $alphaNumericWord;

                $this->addLineToken(
                    $type,
                    $lineNumber,
                    $position,
                    $chosenLexeme,
                    $literal
                );
            }

            if (Lexeme::isNumeric($alphaNumericWord)) {
                $type = TokenType::NUMBER;
                $position = strpos($line, $alphaNumericWord) ?: 0;
                $chosenLexeme = $alphaNumericWord;
                $literal = $alphaNumericWord;
                $this->addLineToken(
                    $type,
                    $lineNumber,
                    $position,
                    $chosenLexeme,
                    $literal
                );
            }
        }

        // foreach (Lexeme::$lexemes as $type => $typeLexemes) {
        //     $chosenLexeme = null;
        //     $position = 0;

        //     // Callable only works for whole lines
        //     if (
        //         is_callable($typeLexemes)
        //         && $typeLexemes(trim($line))
        //     ) {
        //         $literal = trim($line);
        //         $chosenLexeme = $literal;
        //         $this->addLineToken(
        //             $type,
        //             $lineNumber,
        //             $position,
        //             $chosenLexeme,
        //             $literal
        //         );
        //         break;
        //     }
        //     // Normalise format (some types have 1, some have more than 1 lexeme)
        //     if (!is_array($typeLexemes)) {
        //         $typeLexemes = [$typeLexemes];
        //     }

        //     $literal = null;
        //     foreach ($typeLexemes as $lex) {
        //         $position = strpos($line, $lex);
        //         if ($position !== false) {
        //             // Found a lexeme for this type, no need to check this type any further
        //             $chosenLexeme = $lex;
        //             break;
        //         }
        //     }

        //     if ($chosenLexeme === null) {
        //         // Try next type's lexemes
        //         continue;
        //     }

        //     if (in_array($type, array_keys(TokenType::$typesWithLiterals), true)) {
        //         $lookupType = TokenType::$typesWithLiterals[$type];
        //         if ($lookupType === TokenType::TYPE_LOOK_AHEAD) {
        //             $subLine = substr($line, $position + 1);
        //             $endPosition = strpos($subLine, $chosenLexeme);
        //             if ($endPosition !== false) {
        //                 $literal = substr($subLine, 0, $endPosition);
        //                 $this->addLineToken(
        //                     $type,
        //                     $lineNumber,
        //                     $position,
        //                     $chosenLexeme,
        //                     $literal
        //                 );
        //                 continue;
        //             }
        //         }

        //         if ($lookupType === TokenType::TYPE_LOOK_BEHIND) {
        //             // Hack to avoid file header
        //             if (strpos($line, Lexeme::FILE_HEADER) !== false) {
        //                 continue;
        //             }
        //             $literal = trim(substr($line, 0, $position));
        //             $this->addLineToken(
        //                 $type,
        //                 $lineNumber,
        //                 $position,
        //                 $chosenLexeme,
        //                 $literal
        //             );
        //             continue;
        //         }
        //     }
        //     $this->addLineToken(
        //         $type,
        //         $lineNumber,
        //         $position,
        //         $chosenLexeme,
        //         $literal
        //     );
        // }

        return $this->lineTokens;
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

    private function addLineToken(
        string $type,
        int $lineNumber,
        int $position,
        string $chosenLexeme,
        ?string $literal = null
    ): void {
        $uniqueId = "$lineNumber.$position.$type";
        if (isset($this->lineTokens[$uniqueId])) {
            // Got this token, onto the next lexeme for this line
            return;
        }

        $this->lineTokens[$uniqueId] = new Token(
            $type,
            $chosenLexeme,
            new Location($lineNumber + 1, $position + 1, strlen($chosenLexeme)),
            $literal
        );
    }
}
