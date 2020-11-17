<?php

declare(strict_types=1);

namespace Joist\Lexer;

class TokenType
{
    public const FILE_HEADER = 'FILE_HEADER';

    public const STRING = 'STRING';

    public const NUMBER = 'NUMBER';

    public const KEYWORD = 'KEYWORD';

    public const SYMBOL = 'SYMBOL';

    public const IDENTIFIER = 'IDENTIFIER';

    public const IDENTIFIER_TYPE = 'IDENTIFIER_TYPE';


    /** @var string types with literals that need to look ahead for the literal */
    public const TYPE_LOOK_AHEAD = 'LOOK_AHEAD';

    /** @var string types with literals that need to look behind for the literal */
    public const TYPE_LOOK_BEHIND = 'LOOK_BEHIND';

    /**
     * @var array<string,string>
     */
    public static $typesWithLiterals = [
        self::STRING     => self::TYPE_LOOK_AHEAD,
        self::NUMBER     => self::TYPE_LOOK_AHEAD,
        self::IDENTIFIER => self::TYPE_LOOK_BEHIND
    ];

    // public static $requiresRescan = [
    //     self::BRACKET,
    //     self::STRING
    // ];
}
