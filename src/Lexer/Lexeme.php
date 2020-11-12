<?php

declare(strict_types=1);

namespace Joist\Lexer;

class Lexeme
{
    public const FILE_HEADER = '##joist:';

    public const STRING = '"';

    public const KEYWORDS = [
        'always',
        'config',
        'enum',
        'sh',
        'stage',
    ];

    public const BRACKET_OPEN = '{';

    public const LINE_COMMENT = '//';

    public const ONE_CHAR_LEX = ['(', ')', ':', ',', '.', '-', '+', '/', '*', '"'];

    public const ONE_OR_TWO_CHAR_LEX = ['!', '=', '>', '<'];
}
