<?php

declare(strict_types=1);

namespace JoistTest\Parser;

use PHPUnit\Framework\TestCase;
use Joist\Ast\Build;
use Joist\Ast\FileHeader;
use Joist\Ast\Config\ConfigBlock;
use Joist\Ast\Config\Parameter;
use Joist\Lexer\Location;
use Joist\Lexer\Token;
use Joist\Lexer\TokenType;
use Joist\Parser\Mapper;
use Joist\Parser\Parser;
use Joist\Exception\Parser\SyntaxException;

class ParserTest extends TestCase
{
    /** @var Mapper&MockObject */
    private $mapperMock;

    private string $tokenFilePath = __DIR__ . '/../Lexer/tokenised.sample.joist.json';

    public function setUp(): void
    {
        $this->mapperMock = $this->createMock(Mapper::class);
    }

    public function testParserConstructWithFileHeaderVersion(): void
    {
        $tokens = $this->getSampleTokensFromFile();

        $objectUnderTest = new Parser($tokens, $this->mapperMock);

        $paramProjectId = new Parameter('projectId', 'number');
        $paramProjectName = new Parameter('projectName', 'string');
        $paramBuildType = new Parameter('buildType', 'enum', [
            'a',
            'b',
            'c'
        ]);

        $configBlock = new ConfigBlock();
        $configBlock->addParameterAst($paramProjectName);
        $configBlock->addParameterAst($paramProjectId);
        $configBlock->addParameterAst($paramBuildType);

        $expectedStages = [];

        $expectedVersion = '1.3.5';
        $expectedBuild = new Build(
            new FileHeader($expectedVersion),
            $configBlock,
            $expectedStages
        );

        $actualBuild = $objectUnderTest->getBuild();
        self::assertEquals($expectedBuild, $actualBuild);
        self::assertSame($expectedVersion, $actualBuild->getVersion());
        self::assertEquals($expectedStages, $actualBuild->getStages());
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
        $objectUnderTest = new Parser($tokens, $this->mapperMock);

        $expectedVersion = '0.1.0';
        $expectedBuild = new Build(new FileHeader($expectedVersion));

        self::assertEquals($expectedBuild, $objectUnderTest->getBuild());
        self::assertSame($expectedVersion, $objectUnderTest->getBuild()->getVersion());
    }

    public function testGetTokensForLine(): void
    {
        $line = 12;
        $token1 = new Token(
            TokenType::IDENTIFIER,
            ':',
            new Location($line, 1, 1)
        );
        $token2 = new Token(
            TokenType::KEYWORD,
            'config',
            new Location(4, 1, 1)
        );
        $tokens = [
            $token1,
            $token2
        ];
        $expectedTokens = [
            $token1
        ];
        $objectUnderTest = new Parser($tokens, $this->mapperMock);
        self::assertSame($expectedTokens, $objectUnderTest->getTokensByLine($line));
    }

    /**
     * Get sample tokens from a JSON file and perform numerous validations to ensure we have valid tokens
     *
     * @return array
     */
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
