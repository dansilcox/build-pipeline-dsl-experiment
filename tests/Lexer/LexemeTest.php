<?php

declare(strict_types=1);

namespace JoistTest\Lexer;

use PHPUnit\Framework\TestCase;
use Joist\Lexer\Lexeme;

class LexemeTest extends TestCase
{
    public function testCallableIsNumeric(): void
    {
        self::assertTrue(is_callable(Lexeme::NUMBER));
    }

    /**
     * @var mixed $value
     * @var bool $expectedResult
     *
     * @dataProvider isNumericDataProvider
     */
    public function testIsNumeric($value, bool $expectedResult): void
    {
        self::assertSame($expectedResult, Lexeme::isNumeric($value));
    }

    /**
     * @return array
     */
    public function isNumericDataProvider(): array
    {
        return [
            // True
            'Int' => [
                123456,
                true,
            ],
            'Float' => [
                123.456,
                true,
            ],
            'Numeric string' => [
                '123456',
                true,
            ],
            'Numeric string' => [
                '123.456',
                true,
            ],
            // False
            'Alphanumeric String' => [
                '123456a',
                false,
            ],
            'Alphanumeric String 2' => [
                'a123456',
                false,
            ],
            'Non-numeric string' => [
                'abcd',
                false,
            ],
            'String with symbols' => [
                'abcd$',
                false,
            ],
        ];
    }
}
