<?php

declare(strict_types=1);

namespace JoistTest\Lexer;

use Joist\Lexer\Location;
use PHPUnit\Framework\TestCase;

final class LocationTest extends TestCase
{
    public function testSetGet(): void
    {
        $line = 15;
        $col = 35;
        $length = 12;
        $objectUnderTest = new Location(
            $line,
            $col,
            $length
        );

        self::assertSame($line, $objectUnderTest->getLine());
        self::assertSame($col, $objectUnderTest->getCol());
        self::assertSame($length, $objectUnderTest->getLength());
    }

    public function testJsonSerialize(): void
    {
        $line = 15;
        $col = 35;
        $length = 12;
        $objectUnderTest = new Location(
            $line,
            $col,
            $length
        );

        $expected = [
            'line'   => $line,
            'col'    => $col,
            'length' => $length
        ];

        self::assertSame($expected, $objectUnderTest->jsonSerialize());
    }
}
