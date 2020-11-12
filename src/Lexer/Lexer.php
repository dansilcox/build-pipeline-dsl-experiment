<?php

declare(strict_types=1);

namespace Joist\Lexer;

use Joist\Exception\LexerException;

class Lexer {
    private ?string $srcFilePath;

    private ?string $lastError;
    
    public function __construct(?string $srcFilePath = null) {
        $this->validateSrcFilePath($srcFilePath);

        $this->srcFilePath = $srcFilePath;
    }

    public function tokenise(): bool
    {
        $this->validateSrcFilePath($this->srcFilePath);

        $string = file_get_contents($this->srcFilePath);
        return $this->tokeniseFromString($string);
    }

    public function tokeniseFromString(string $sourceString): bool
    {
        // Reset 'last error' for a fresh tokenisation run
        $this->lastError = null;

        $lines = array_values(array_filter(array_map(
            [$this, 'trimWhitespaceAndComments'], 
            explode("\n", $sourceString)
        )));
        
        if (strpos((string) $lines[0], Token::FILE_HEADER) === false) {
            $this->lastError = 'Expected ' . TOKEN::FILE_HEADER . ' header, none found';
            return false;
        }

        return true;
    }

    public function getLastError(): ?string
    {
        return $this->lastError;
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
        $lineParts = explode('//', trim($line));
        return $lineParts[0] ?? '';
    }
}