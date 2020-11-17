<?php

declare(strict_types=1);

namespace JoistTest\Parser;

use PHPUnit\Framework\TestCase;
use Joist\Lexer\Location;
use Joist\Exception\ErrorCode;
use Joist\Exception\Parser\SyntaxException;

class SyntaxExceptionTest extends TestCase
{
    public function testException(): void
    {
        $customMessage = 'Bad things happened';
        $location = new Location(5, 3, 1);

        $line = $location->getLine();
        $col = $location->getCol();
        $expectedMessage = "Syntax error: $customMessage - line $line, column $col";

        self::expectException(SyntaxException::class);
        self::expectExceptionMessage($expectedMessage);

        throw new SyntaxException($customMessage, ErrorCode::SYNTAX_ERROR_GENERIC, $location);
    }
}
