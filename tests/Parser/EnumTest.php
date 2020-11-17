<?php

declare(strict_types=1);

namespace JoistTest\Parser;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Joist\Ast\Build;
use Joist\Ast\FileHeader;
use Joist\Ast\Config\ConfigBlock;
use Joist\Ast\Config\Parameter as ParameterAst;
use Joist\Lexer\Location;
use Joist\Lexer\Token;
use Joist\Lexer\TokenType;
use Joist\Parser\Enum as EnumParser;
use Joist\Parser\Parser;
use Joist\Exception\Parser\SyntaxException;

class EnumTest extends TestCase
{
    /** @var Parser|MockObject */
    private $parserMock;

    public function testParseNoTokens(): void
    {
        $objectUnderTest = new EnumParser($this->createMock(Parser::class));
        self::assertNull($objectUnderTest->parse([]));
    }

    public function testParseNoValidLinesToParse(): void
    {
        $tokenStart = new Token(
            TokenType::IDENTIFIER_TYPE,
            'enum',
            new Location(0, 1, 1)
        );
        $tokenEnd = new Token(
            TokenType::SYMBOL,
            ']',
            new Location(0, 1, 1)
        );
        $objectUnderTest = new EnumParser($this->createMock(Parser::class));
        self::assertNull($objectUnderTest->parse([$tokenStart, $tokenEnd]));
    }

    public function testParse(): void
    {
        $this->parserMock = $this->configureParserMock();

        $identifier = 'buildType';
        $identiferType = 'enum';
        $allowedValues = [
            'a',
            'b',
            'c',
        ];
        $tokens = [
            new Token(
                TokenType::IDENTIFIER,
                ':',
                new Location(1, 1, 1),
                $identifier
            ),
            new Token(
                TokenType::IDENTIFIER_TYPE,
                'enum',
                new Location(1, 5, 4)
            ),
            new Token(
                TokenType::SYMBOL,
                '[',
                new Location(1, 6, 1)
            ),
            new Token(
                TokenType::STRING,
                '"',
                new Location(2, 1, 1),
                $allowedValues[0]
            ),
            new Token(
                TokenType::STRING,
                '"',
                new Location(3, 1, 1),
                $allowedValues[1]
            ),
            new Token(
                TokenType::STRING,
                '"',
                new Location(4, 1, 1),
                $allowedValues[2]
            ),
            new Token(
                TokenType::SYMBOL,
                ']',
                new Location(5, 1, 1)
            ),
            new Token(
                TokenType::STRING,
                '"',
                new Location(6, 1, 1),
                'not-in-enum'
            ),
        ];
        $metadata = [];

        $expected = new ParameterAst($identifier, $identiferType, $allowedValues);
        $objectUnderTest = new EnumParser($this->parserMock);

        $actual = $objectUnderTest->parse($tokens, $metadata);
        self::assertEquals($expected, $actual);
    }

    public function testParseNoIdentifier(): void
    {
        $this->parserMock = $this->configureParserMock();

        $identiferType = 'enum';
        $allowedValues = [
            'a',
            'b',
            'c',
        ];
        $tokens = [
            new Token(
                TokenType::IDENTIFIER_TYPE,
                'enum',
                new Location(1, 5, 4)
            ),
            new Token(
                TokenType::SYMBOL,
                '[',
                new Location(1, 6, 1)
            ),
            new Token(
                TokenType::STRING,
                '"',
                new Location(2, 1, 1),
                $allowedValues[0]
            ),
            new Token(
                TokenType::STRING,
                '"',
                new Location(3, 1, 1),
                $allowedValues[1]
            ),
            new Token(
                TokenType::STRING,
                '"',
                new Location(4, 1, 1),
                $allowedValues[2]
            ),
            new Token(
                TokenType::SYMBOL,
                ']',
                new Location(5, 1, 1)
            ),
            new Token(
                TokenType::STRING,
                '"',
                new Location(6, 1, 1),
                'not-in-enum'
            ),
        ];
        $metadata = [];

        $objectUnderTest = new EnumParser($this->parserMock);

        self::assertNull($objectUnderTest->parse($tokens, $metadata));
    }

    private function configureParserMock(): Parser
    {
        $mock = $this->createMock(Parser::class);
        $mock
            ->expects(self::atLeastOnce())
            ->method('setSearchLine')
            ->with(self::logicalOr(self::isType('int'), self::isNull()));
        $mock
            ->expects(self::atLeastOnce())
            ->method('filterByLine')
            ->with(self::isInstanceOf(Token::class))
            ->willReturn(true);
        return $mock;
    }
}
