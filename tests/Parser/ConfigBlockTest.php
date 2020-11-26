<?php

declare(strict_types=1);

namespace JoistTest\Parser;

use PHPUnit\Framework\TestCase;
use Joist\Ast\Config\ConfigBlock as ConfigBlockAst;
use Joist\Parser\ConfigBlock as ConfigBlockParser;
use Joist\Parser\Parser;
use Joist\Lexer\Location;
use Joist\Lexer\Token;
use Joist\Lexer\TokenType;

class ConfigBlockTest extends TestCase
{
    public function testParse(): void
    {
        $tokens = [
            new Token(
                TokenType::KEYWORD,
                'config',
                new Location(1, 3, 6)
            ),
            new Token(
                TokenType::SYMBOL,
                '{',
                new Location(1, 4, 1)
            ),
            new Token(
                TokenType::IDENTIFIER,
                ':',
                new Location(2, 1, 1),
                'scmUrl'
            ),
            new Token(
                TokenType::IDENTIFIER_TYPE,
                'string',
                new Location(2, 9, 6)
            ),
            new Token(
                TokenType::IDENTIFIER,
                ':',
                new Location(3, 1, 1),
                'awesomeness'
            ),
            new Token(
                TokenType::IDENTIFIER_TYPE,
                'number',
                new Location(3, 9, 6)
            ),
            new Token(
                TokenType::IDENTIFIER,
                ':',
                new Location(4, 1, 1),
                'having_fun_yet'
            ),
            new Token(
                TokenType::IDENTIFIER_TYPE,
                'enum',
                new Location(4, 9, 6)
            ),
            new Token(
                TokenType::SYMBOL,
                '[',
                new Location(4, 1, 1)
            ),
            new Token(
                TokenType::STRING,
                '\'',
                new Location(5, 1, 1),
                'yes'
            ),
            new Token(
                TokenType::STRING,
                '\'',
                new Location(6, 1, 1),
                'no'
            ),
            new Token(
                TokenType::STRING,
                '\'',
                new Location(7, 1, 1),
                'ish'
            ),
            new Token(
                TokenType::SYMBOL,
                ']',
                new Location(8, 1, 1)
            ),
            new Token(
                TokenType::SYMBOL,
                '}',
                new Location(9, 1, 1)
            ),
        ];

        $expected = new ConfigBlockAst();
        $expected->addParameter('scmUrl', 'string');
        $expected->addParameter('awesomeness', 'number');
        $expected->addParameter('having_fun_yet', 'enum', ['yes', 'no', 'ish']);

        $parserMock = $this->configureParserMock($tokens);

        $objectUnderTest = new ConfigBlockParser($parserMock);
        $actual = $objectUnderTest->parse($tokens);

        self::assertEquals($expected, $actual);
        self::assertEquals($expected->getParameters(), $actual->getParameters());
    }

    public function testParseNoTokens(): void
    {
        $objectUnderTest = new ConfigBlockParser($this->createMock(Parser::class));
        self::assertNull($objectUnderTest->parse([]));
    }

    private function configureParserMock(array $tokens): Parser
    {
        $mock = $this->createMock(Parser::class);
        $mock
            ->expects(self::atLeastOnce())
            ->method('getTokensByLine')
            ->with(self::isType('int'))
            ->willReturn($tokens);
        return $mock;
    }
}
