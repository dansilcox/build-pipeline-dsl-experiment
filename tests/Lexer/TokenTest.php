<?php

declare(strict_types=1);

namespace JoistTest\Lexer;

use Joist\Lexer\Token;
use Joist\Lexer\Location;
use PHPUnit\Framework\TestCase;

final class TokenTest extends TestCase
{
    public function testSetGetWithNullLiteral(): void
    {
        $type = 'file_header';
        $lexeme = '##joist:';
        $location = new Location(1, 1, 8);
        $objectUnderTest = new Token(
            $type,
            $lexeme,
            $location
        );

        self::assertSame($type, $objectUnderTest->getType());
        self::assertSame($lexeme, $objectUnderTest->getLexeme());
        self::assertSame($location, $objectUnderTest->getLocation());
        self::assertNull($objectUnderTest->getLiteral());
    }

    public function testSetGetWithNonNullLiteral(): void
    {
        $type = 'string';
        $lexeme = '"';
        $location = new Location(1, 9, 5);
        $literal = '0.1.0';
        $objectUnderTest = new Token(
            $type,
            $lexeme,
            $location,
            $literal
        );

        self::assertSame($type, $objectUnderTest->getType());
        self::assertSame($lexeme, $objectUnderTest->getLexeme());
        self::assertSame($location, $objectUnderTest->getLocation());
        self::assertSame($literal, $objectUnderTest->getLiteral());
    }

    public function testJsonSerialise(): void
    {
        $type = 'string';
        $lexeme = '"';
        $location = new Location(1, 9, 5);
        $literal = '0.1.0';
        $objectUnderTest = new Token(
            $type,
            $lexeme,
            $location,
            $literal
        );

        $expected = [
            'type'     => $type,
            'lexeme'   => $lexeme,
            'literal'  => $literal,
            'location' => [
                'line'   => $location->getLine(),
                'col'    => $location->getCol(),
                'length' => $location->getLength()
            ]
        ];

        self::assertSame($expected, $objectUnderTest->jsonSerialize());
    }
}
