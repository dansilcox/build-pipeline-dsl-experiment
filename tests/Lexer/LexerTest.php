<?php

declare(strict_types=1);

namespace JoistTest\Lexer;

use Joist\Exception\LexerException;
use Joist\Lexer\Lexer;
use PHPUnit\Framework\TestCase;

final class LexerTest extends TestCase
{
    private string $joistSrcFilePath = __DIR__ . '/sample.joist';

    private string $tokenisedFilePath = __DIR__ . '/tokenised.sample.joist.json';

    /**
     * Test that the alphanumeric regex does what we think it does...
     *
     * @param string $inputWord
     * @param string $expectedOutput
     *
     * @dataProvider alphaRegexWordDataProvider
     */
    public function testAlphaRegex(string $inputWord, string $expectedOutput): void
    {
        self::assertSame($expectedOutput, preg_replace(Lexer::ALPHA_NUMERIC_REGEX, '', $inputWord));
    }

    public function alphaRegexWordDataProvider(): array
    {
        return [
            'Contains symbols' => [
                'ABCDabcd1234.!$:£@~Z><?',
                'ABCDabcd1234.Z'
            ],
            'Identifier' => [
                'buildType:',
                'buildType'
            ]
        ];
    }

    /**
     * Test that the alphanumeric regex does what we think it does...
     *
     * @param string $inputWord
     * @param string $expectedOutput
     *
     * @dataProvider nonAlphaRegexWordDataProvider
     */
    public function testNonAlphaRegex(string $inputWord, string $expectedOutput): void
    {
        self::assertSame($expectedOutput, preg_replace(Lexer::NON_ALPHA_REGEX, '', $inputWord));
    }

    public function nonAlphaRegexWordDataProvider(): array
    {
        return [
            'Contains symbols' => [
                'ABCDabcd1234.!$:£@~Z><?',
                '!$:£@~><?'
            ],
            'Identifier' => [
                'buildType:',
                ':'
            ]
        ];
    }

    public function testTokeniseFileNotExist(): void
    {
        $nonexistentFile = __DIR__ . '/nonexistent.joist';

        self::assertFileDoesNotExist($nonexistentFile);

        $this->expectException(LexerException::class);
        $this->expectExceptionMessage('Source file not found: ' . $nonexistentFile);

        $objectUnderTest = new Lexer(
            $nonexistentFile
        );
    }

    /**
     * @param string $sourceString
     * @param array  $expectedTokens
     *
     * @dataProvider specificStringDataProvider
     */
    public function testTokeniseSpecificStrings(string $sourceString, array $expectedTokens): void
    {
        $objectUnderTest = new Lexer();

        $tokeniseResult = $objectUnderTest->tokeniseFromString($sourceString, false);
        $lastError = $objectUnderTest->getLastError();

        self::assertTrue($tokeniseResult, $lastError ?? 'Unknown');
        self::assertNull($lastError);
        $actualTokens = $objectUnderTest->getTokenisedOutput();
        self::assertIsArray($actualTokens);
        self::assertArrayHasKey('tokens', $actualTokens);
        sort($expectedTokens);
        sort($actualTokens['tokens']);
        self::assertSame($expectedTokens, $actualTokens['tokens']);
    }

    public function specificStringDataProvider(): array
    {
        return [
            'File header' => [
                '##joist:"1.3.5"',
                [
                    [
                        'type'     => 'FILE_HEADER',
                        'lexeme'   => '##joist:',
                        'literal'  => null,
                        'location' => [
                            'line'   => 1,
                            'col'    => 1,
                            'length' => 8,
                        ]
                    ],
                    [
                        'type'     => 'STRING',
                        'lexeme'   => '"',
                        'literal'  => "1.3.5",
                        'location' => [
                            'line'   => 1,
                            'col'    => 9,
                            'length' => 1,
                        ]
                    ]
                ]
            ],
            'Config opener' => [
                'config {',
                [
                    [
                        'type'     => 'KEYWORD',
                        'lexeme'   => 'config',
                        'literal'  => null,
                        'location' => [
                            'line'   => 1,
                            'col'    => 1,
                            'length' => 6,
                        ]
                    ],
                    [
                        'type'     => 'SYMBOL',
                        'lexeme'   => '{',
                        'literal'  => null,
                        'location' => [
                            'line'   => 1,
                            'col'    => 8,
                            'length' => 1,
                        ]
                    ],
                ]
            ],
            'Config closer' => [
                '}',
                [
                    [
                        'type'     => 'SYMBOL',
                        'lexeme'   => '}',
                        'literal'  => null,
                        'location' => [
                            'line'   => 1,
                            'col'    => 1,
                            'length' => 1,
                        ]
                    ]
                ]
            ],
            'String config param' => [
                '  projectName: string',
                [
                    [
                        'type'     => 'IDENTIFIER',
                        'lexeme'   => ':',
                        'literal'  => 'projectName',
                        'location' => [
                            'line'   => 1,
                            'col'    => 12,
                            'length' => 1,
                        ]
                    ],
                    [
                        'type'     => 'IDENTIFIER_TYPE',
                        'lexeme'   => 'string',
                        'literal'  => null,
                        'location' => [
                            'line'   => 1,
                            'col'    => 14,
                            'length' => 6,
                        ]
                    ],
                ]
            ],
            'Number config param' => [
                '  projectId: number',
                [
                    [
                        'type'     => 'IDENTIFIER',
                        'lexeme'   => ':',
                        'literal'  => 'projectId',
                        'location' => [
                            'line'   => 1,
                            'col'    => 10,
                            'length' => 1,
                        ]
                    ],
                    [
                        'type'     => 'IDENTIFIER_TYPE',
                        'lexeme'   => 'number',
                        'literal'  => null,
                        'location' => [
                            'line'   => 1,
                            'col'    => 12,
                            'length' => 6,
                        ]
                    ],
                ]
            ],
            'Enum config param opener' => [
                '  buildType: enum[',
                [
                    [
                        'type'     => 'IDENTIFIER',
                        'lexeme'   => ':',
                        'literal'  => 'buildType',
                        'location' => [
                            'line'   => 1,
                            'col'    => 10,
                            'length' => 1,
                        ]
                    ],
                    [
                        'type'     => 'IDENTIFIER_TYPE',
                        'lexeme'   => 'enum',
                        'literal'  => null,
                        'location' => [
                            'line'   => 1,
                            'col'    => 12,
                            'length' => 4,
                        ]
                    ],
                    [
                        'type'     => 'SYMBOL',
                        'lexeme'   => '[',
                        'literal'  => null,
                        'location' => [
                            'line'   => 1,
                            'col'    => 16,
                            'length' => 1,
                        ]
                    ]
                ]
            ],
            'Enum string option' => [
                '  \'dev\'',
                [
                    [
                        'type'     => 'STRING',
                        'lexeme'   => '\'',
                        'literal'  => 'dev',
                        'location' => [
                            'line'   => 1,
                            'col'    => 1,
                            'length' => 1,
                        ]
                    ],
                ]
            ],
            'Enum number (int) option' => [
                '  135',
                [
                    [
                        'type'     => 'NUMBER',
                        'lexeme'   => '135',
                        'literal'  => '135',
                        'location' => [
                            'line'   => 1,
                            'col'    => 1,
                            'length' => 3,
                        ]
                    ],
                ]
            ],
            'Enum number (float) option' => [
                '  135.531',
                [
                    [
                        'type'     => 'NUMBER',
                        'lexeme'   => '135.531',
                        'literal'  => '135.531',
                        'location' => [
                            'line'   => 1,
                            'col'    => 1,
                            'length' => 7,
                        ]
                    ],
                ]
            ],
            'Enum closer (square bracket)' => [
                ']',
                [
                    [
                        'type'     => 'SYMBOL',
                        'lexeme'   => ']',
                        'literal'  => null,
                        'location' => [
                            'line'   => 1,
                            'col'    => 1,
                            'length' => 1,
                        ]
                    ],
                ]
            ],
            'Complete enum sample' => [
                <<<EOF
                
  buildType: enum[
    'a'
    'b'
    'c'
  ]
EOF,
                [
                    [
                        'type'     => 'IDENTIFIER',
                        'lexeme'   => ':',
                        'literal'  => 'buildType',
                        'location' => [
                            'line'   => 1,
                            'col'    => 10,
                            'length' => 1,
                        ]
                    ],
                    [
                        'type'     => 'IDENTIFIER_TYPE',
                        'lexeme'   => 'enum',
                        'literal'  => null,
                        'location' => [
                            'line'   => 1,
                            'col'    => 12,
                            'length' => 4,
                        ]
                    ],
                    [
                        'type'     => 'SYMBOL',
                        'lexeme'   => '[',
                        'literal'  => null,
                        'location' => [
                            'line'   => 1,
                            'col'    => 16,
                            'length' => 1,
                        ]
                    ],
                    [
                        'type'     => 'STRING',
                        'lexeme'   => '\'',
                        'literal'  => 'a',
                        'location' => [
                            'line'   => 2,
                            'col'    => 1,
                            'length' => 1,
                        ]
                    ],
                    [
                        'type'     => 'STRING',
                        'lexeme'   => '\'',
                        'literal'  => 'b',
                        'location' => [
                            'line'   => 3,
                            'col'    => 1,
                            'length' => 1,
                        ]
                    ],
                    [
                        'type'     => 'STRING',
                        'lexeme'   => '\'',
                        'literal'  => 'c',
                        'location' => [
                            'line'   => 4,
                            'col'    => 1,
                            'length' => 1,
                        ]
                    ],
                    [
                        'type'     => 'SYMBOL',
                        'lexeme'   => ']',
                        'literal'  => null,
                        'location' => [
                            'line'   => 5,
                            'col'    => 1,
                            'length' => 1,
                        ]
                    ],
                ]
            ],
            'Stage' => [
                'stage(\'bla\')',
                [
                    [
                        'type'     => 'KEYWORD',
                        'lexeme'   => 'stage',
                        'literal'  => null,
                        'location' => [
                            'line'   => 1,
                            'col'    => 1,
                            'length' => 5
                        ]
                    ],
                    [
                        'type'     => 'SYMBOL',
                        'lexeme'   => '(',
                        'literal'  => null,
                        'location' => [
                            'line'   => 1,
                            'col'    => 6,
                            'length' => 1
                        ]
                    ],
                    [
                        'type'     => 'STRING',
                        'lexeme'   => '\'',
                        'literal'  => 'bla',
                        'location' => [
                            'line'   => 1,
                            'col'    => 7,
                            'length' => 1
                        ]
                    ],
                    [
                        'type'     => 'SYMBOL',
                        'lexeme'   => ')',
                        'literal'  => null,
                        'location' => [
                            'line'   => 1,
                            'col'    => 12,
                            'length' => 1
                        ]
                    ]
                ]
            ],
            'Stage opener' => [
                'stage(\'bla\', always) {',
                [
                    [
                        'type'     => 'KEYWORD',
                        'lexeme'   => 'stage',
                        'literal'  => null,
                        'location' => [
                            'line'   => 1,
                            'col'    => 1,
                            'length' => 5
                        ]
                    ],
                    [
                        'type'     => 'SYMBOL',
                        'lexeme'   => '(',
                        'literal'  => null,
                        'location' => [
                            'line'   => 1,
                            'col'    => 6,
                            'length' => 1
                        ]
                    ],
                    [
                        'type'     => 'STRING',
                        'lexeme'   => '\'',
                        'literal'  => 'bla',
                        'location' => [
                            'line'   => 1,
                            'col'    => 7,
                            'length' => 1
                        ]
                    ],
                    [
                        'type'     => 'SYMBOL',
                        'lexeme'   => ',',
                        'literal'  => null,
                        'location' => [
                            'line'   => 1,
                            'col'    => 12,
                            'length' => 1
                        ]
                    ],
                    [
                        'type'     => 'KEYWORD',
                        'lexeme'   => 'always',
                        'literal'  => null,
                        'location' => [
                            'line'   => 1,
                            'col'    => 14,
                            'length' => 6
                        ]
                    ],
                    [
                        'type'     => 'SYMBOL',
                        'lexeme'   => ')',
                        'literal'  => null,
                        'location' => [
                            'line'   => 1,
                            'col'    => 20,
                            'length' => 1
                        ]
                    ],
                    [
                        'type'     => 'SYMBOL',
                        'lexeme'   => '{',
                        'literal'  => null,
                        'location' => [
                            'line'   => 1,
                            'col'    => 22,
                            'length' => 1
                        ]
                    ]
                ]
            ]
        ];
    }

    public function testTokeniseFromString(): void
    {
        $objectUnderTest = new Lexer();

        $joistSrc = $this->getSourceFromFile();
        $expectedTokenised = $this->unserialiseTokenisedFile();

        self::assertTrue($objectUnderTest->tokeniseFromString($joistSrc));

        $actualTokenised = $objectUnderTest->getTokenisedOutput();

        self::assertIsArray($actualTokenised);
        self::assertArrayHasKey('tokens', $actualTokenised);
        sort($actualTokenised['tokens']);

        self::assertSame($expectedTokenised, $actualTokenised);
    }

    public function testTokenise(): void
    {
        self::assertFileExists($this->joistSrcFilePath);

        $objectUnderTest = new Lexer(
            $this->joistSrcFilePath
        );

        self::assertTrue($objectUnderTest->tokenise());
    }

    public function testTokeniseTwiceDontKeepLastError(): void
    {
        $joistSrcBad = 'I have a very bad feeling about this...';

        $objectUnderTest = new Lexer();

        self::assertFalse($objectUnderTest->tokeniseFromString($joistSrcBad));
        self::assertSame('Expected ##joist:"<version>" header, none found', $objectUnderTest->getLastError());

        $joistSrcGood = $this->getSourceFromFile();

        self::assertTrue($objectUnderTest->tokeniseFromString($joistSrcGood));
        self::assertNull($objectUnderTest->getLastError());
    }

    public function testTokeniseFileDeleted(): void
    {
        $tempFile = sys_get_temp_dir() . '/temp-joist-file.joist';
        touch($tempFile);
        self::assertFileExists($tempFile);

        $objectUnderTest = new Lexer($tempFile);

        unlink($tempFile);
        self::assertFileDoesNotExist($tempFile);

        $this->expectException(LexerException::class);
        $this->expectExceptionMessage('Source file not found: ' . $tempFile);

        $objectUnderTest->tokenise();
    }

    /**
     * @var string $input
     * @var string $expectedMessage
     *
     * @dataProvider invalidTokenDataProvider
     */
    public function testTokeniseFromStringParseError(string $input, string $expectedMessage): void
    {
        $objectUnderTest = new Lexer();

        self::assertFalse($objectUnderTest->tokeniseFromString($input, true));
        self::assertSame($expectedMessage, $objectUnderTest->getLastError());
    }

    /**
     * @return array<string, array<string>>
     */
    public function invalidTokenDataProvider(): array
    {
        return [
            'Blank source string' => [
                '',
                'Cannot tokenise empty string'
            ],
            'Comment only, effectively blank' => [
                '// Comment only',
                'No valid lines found',
            ],
            'Missing ##joist header' => [
              <<<EOF
// This is valid - but missing the ##joist header
stage('bla', always) {
  sh('bla')
}
EOF,
                'Expected ##joist:"<version>" header, none found'
            ]
        ];
    }

    /**
     * Verify the source file exists, then read it into a string
     *
     * @return string
     */
    private function getSourceFromFile(): string
    {
        self::assertFileExists($this->joistSrcFilePath);

        return file_get_contents($this->joistSrcFilePath) ?: '';
    }

    /**
     * Check the tokenised file exists, then unserialise it to an associative array and return it
     *
     * @return array
     */
    private function unserialiseTokenisedFile(): array
    {
        self::assertFileExists($this->tokenisedFilePath);

        $source = file_get_contents($this->tokenisedFilePath) ?: '';
        self::assertJson($source);

        $expectedTokenised = json_decode($source, true, 512, JSON_THROW_ON_ERROR);

        self::assertIsArray($expectedTokenised);
        self::assertArrayHasKey('tokens', $expectedTokenised);

        sort($expectedTokenised['tokens']);

        return $expectedTokenised;
    }
}
