<?php

declare(strict_types=1);

namespace Joist\Lexer;

class Token {
    public const FILE_HEADER = '##joist';

    public const LINE_COMMENT = '//';

    public const WHITESPACE = [' ', "\r", "\t"];

    public const ONE_CHAR_LEX = ['(', ')', ':', ',', '.', '-', '+', '/', '*'];
    
    public const ONE_OR_TWO_CHAR_LEX = ['!', '=', '>', '<'];

    public const KEYWORD = [
        'stage',
        'config',
        'enum', 
        'always', 
        'sh'
    ];
}