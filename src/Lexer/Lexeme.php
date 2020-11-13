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

    public const KEYWORDS = [
        'always',
        'config',
        'sh',
        'stage',
    ];

    public const BRACKETS = [
        '{',
        '}',
        '(',
        ')'
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
        TokenType::KEYWORD         => self::KEYWORDS,
        TokenType::BRACKET         => self::BRACKETS,
        TokenType::STRING          => self::STRING,
        TokenType::IDENTIFIER      => self::IDENTIFIER,
        TokenType::IDENTIFIER_TYPE => self::IDENTIFIER_TYPES,
    ];
}