<?php

declare(strict_types=1);

namespace JoistTest\Parser;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Joist\Ast\Build;
use Joist\Ast\FileHeader as FileHeaderAst;
use Joist\Ast\Config\ConfigBlock;
use Joist\Ast\Config\Parameter;
use Joist\Lexer\Lexeme;
use Joist\Lexer\Location;
use Joist\Lexer\Token;
use Joist\Lexer\TokenType;
use Joist\Parser\Parser;
use Joist\Parser\FileHeader as FileHeaderParser;
use Joist\Exception\Parser\SyntaxException;

class FileHeaderTest extends TestCase
{
    /** @var Parser&MockObject */
    private $parserMock;

    private string $versionFromTokens = '1.3.5';

    private FileHeaderParser $objectUnderTest;

    public function setUp(): void
    {
        $this->parserMock = $this->createMock(Parser::class);
        $this->objectUnderTest = new FileHeaderParser($this->parserMock);
    }

    public function testFileHeaderParserWithNoTokens(): void
    {
        $this->parserMock->expects(self::never())->method('getTokensByLine');

        self::assertNull($this->objectUnderTest->parse([]));
    }

    public function testFileHeaderParser(): void
    {
        $tokens = [
            new Token(
                TokenType::FILE_HEADER,
                '##joist:',
                new Location(1, 1, 8)
            ),
            new Token(
                TokenType::STRING,
                '"',
                new Location(1, 9, 1),
                '1.3.5'
            ),
            new Token(
                TokenType::STRING,
                '"',
                new Location(3, 1, 1),
                '2.4.6'
            ),
        ];

        $tokensLine1 = [
            new Token(
                TokenType::FILE_HEADER,
                '##joist:',
                new Location(1, 1, 8)
            ),
            new Token(
                TokenType::STRING,
                '"',
                new Location(1, 9, 1),
                '1.3.5'
            ),
        ];

        $tokensLine3 = [
            new Token(
                TokenType::STRING,
                '"',
                new Location(3, 1, 1),
                '2.4.6'
            ),
        ];

        $this
            ->parserMock
            ->expects(self::atLeastOnce())
            ->method('getTokensByLine')
            ->with(self::isType('int'))
            ->willReturnOnConsecutiveCalls($tokensLine1, $tokensLine3);

        $expected = new FileHeaderAst('1.3.5');
        self::assertEquals($expected, $this->objectUnderTest->parse($tokens));
    }

    public function testFileHeaderConstructWithInvalidFileHeader(): void
    {
        $tokens = [
            new Token(
                TokenType::FILE_HEADER,
                '##joist:',
                new Location(
                    1,
                    1,
                    1
                )
            )
        ];

        $this
            ->parserMock
            ->expects(self::atLeastOnce())
            ->method('getTokensByLine')
            ->with(self::isType('int'))
            ->willReturn($tokens);

        $this->expectException(SyntaxException::class);
        $this->expectExceptionMessage(
            'Syntax error: Invalid file header, missing version identifier - line 1, column 1'
        );
        $objectUnderTest = new FileHeaderParser($this->parserMock);
        $objectUnderTest->parse($tokens);
    }

    private function generateFileHeaderTokens(): array
    {
        return [
            new Token(
                TokenType::FILE_HEADER,
                Lexeme::FILE_HEADER,
                new Location(1, 1, 1),
                null
            ),
            new Token(
                TokenType::STRING,
                '"',
                new Location(1, 8, 1),
                $this->versionFromTokens
            )
        ];
    }
}
