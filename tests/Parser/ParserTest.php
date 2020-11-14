<?php

declare(strict_types=1);

namespace JoistTest\Parser;

use PHPUnit\Framework\TestCase;
use Joist\Ast\Build;
use Joist\Lexer\Location;
use Joist\Lexer\Token;
use Joist\Lexer\TokenType;
use Joist\Parser\Parser;
use Joist\Exception\Parser\SyntaxException;

class ParserTest extends TestCase
{
    private string $tokenFilePath = __DIR__ . '/../Lexer/tokenised.sample.joist.json';

    public function testParserConstructWithFileHeaderVersion(): void
    {
        $tokens = $this->getSampleTokensFromFile();
        $objectUnderTest = new Parser($tokens);

        $expectedVersion = '1.3.5';
        $expectedBuild = new Build($expectedVersion);

        self::assertEquals($expectedBuild, $objectUnderTest->getBuild());
        self::assertSame($expectedVersion, $objectUnderTest->getBuild()->getJoistVersion());
    }

    public function testParserConstructWithNoFileHeaderVersion(): void
    {
        $tokens = [
            new Token(
                TokenType::STRING,
                '"',
                new Location(
                    1,
                    1,
                    1
                ),
                '1.3.5'
            )
        ];
        $objectUnderTest = new Parser($tokens);

        $expectedVersion = '0.1.0';
        $expectedBuild = new Build($expectedVersion);

        self::assertEquals($expectedBuild, $objectUnderTest->getBuild());
        self::assertSame($expectedVersion, $objectUnderTest->getBuild()->getJoistVersion());
    }

    public function testParserConstructWithInvalidFileHeader(): void
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

        $this->expectException(SyntaxException::class);
        $this->expectExceptionMessage(
            'Syntax error: Invalid file header, missing version identifier - line 1, column 1'
        );
        new Parser($tokens);
    }

    private function getSampleTokensFromFile(): array
    {
        self::assertFileExists($this->tokenFilePath);

        $tokensString = file_get_contents($this->tokenFilePath) ?: '';
        self::assertNotEquals('', $tokensString);

        $parsed = json_decode($tokensString, true, 512, JSON_THROW_ON_ERROR);
        self::assertIsArray($parsed);
        self::assertArrayHasKey('tokens', $parsed);
        
        $expectedTokenKeys = [
            'type',
            'lexeme',
            'literal',
            'location',
        ];
        $expectedLocationKeys = [
            'line',
            'col',
            'length',
        ];
        $tokens = [];
        foreach ($parsed['tokens'] as $tokenFromJson) {
            self::assertIsArray($tokenFromJson);
            foreach ($expectedTokenKeys as $expectedTokenKey) {
                self::assertArrayHasKey($expectedTokenKey, $tokenFromJson);
            }
            foreach ($expectedLocationKeys as $expectedLocationKey) {
                self::assertArrayHasKey($expectedLocationKey, $tokenFromJson['location']);
            }

            $tokens[] = new Token(
                $tokenFromJson['type'],
                $tokenFromJson['lexeme'],
                new Location(
                    $tokenFromJson['location']['line'],
                    $tokenFromJson['location']['col'],
                    $tokenFromJson['location']['length']
                ),
                $tokenFromJson['literal'] ?? null
            );
        }
        return $tokens;
    }
}
