<?php

declare(strict_types=1);

namespace Joist\Lexer;

class Lexeme
{
    public const FILE_HEADER = '##joist:';

    public const STRING = [
        '"',
        "'"
    ];

    public const NUMBER = [Lexeme::class, 'isNumeric'];

    public const KEYWORDS = [
        'always',
        'config',
        'sh',
        'stage',
    ];

    public const SYMBOLS = [
        '{',
        '}',
        '(',
        ')',
        '[',
        ']',
        ','
    ];

    public const IDENTIFIER = ':';

    public const IDENTIFIER_TYPES = [
        'string',
        'number',
        'enum'
    ];

    public const LINE_COMMENT = '//';

    // public const ONE_CHAR_LEX = ['(', ')', ':', ',', '.', '-', '+', '/', '*', '"'];

    // public const ONE_OR_TWO_CHAR_LEX = ['!', '=', '>', '<'];

    public static $lexemes = [
        TokenType::FILE_HEADER     => self::FILE_HEADER,
        TokenType::SYMBOL          => self::SYMBOLS,
        TokenType::STRING          => self::STRING,
        TokenType::NUMBER          => self::NUMBER,
        TokenType::IDENTIFIER      => self::IDENTIFIER,
        TokenType::IDENTIFIER_TYPE => self::IDENTIFIER_TYPES,
        TokenType::KEYWORD         => self::KEYWORDS,
    ];

    /**
     * @var mixed $value
     *
     * @return bool
     */
    public static function isNumeric($value): bool
    {
        return is_numeric($value);
    }
}
