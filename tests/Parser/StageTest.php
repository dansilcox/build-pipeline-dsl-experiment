<?php

declare(strict_types=1);

namespace JoistTest\Parser;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Joist\Lexer\Location;
use Joist\Lexer\Token;
use Joist\Lexer\TokenType;
use Joist\Parser\Stage as StageParser;
use Joist\Parser\Parser;
use Joist\Exception\Parser\SyntaxException;

class StageTest extends TestCase
{
    /** @var Parser|MockObject */
    private $parserMock;

    public function testParseNoTokens(): void
    {
        $objectUnderTest = new StageParser($this->createMock(Parser::class));
        self::assertSame([], $objectUnderTest->parse([]));
    }

    public function testParseNoValidLinesToParse(): void
    {
        $tokenStart = new Token(
            TokenType::KEYWORD,
            'stage',
            new Location(0, 1, 1)
        );
        $tokenEnd = new Token(
            TokenType::SYMBOL,
            '}',
            new Location(0, 1, 1)
        );
        $objectUnderTest = new StageParser($this->createMock(Parser::class));
        self::assertSame([], $objectUnderTest->parse([$tokenStart, $tokenEnd]));
    }
}
