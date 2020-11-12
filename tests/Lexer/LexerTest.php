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

    public function testTokeniseFromString(): void
    {
        $objectUnderTest = new Lexer();

        $joistSrc = $this->getSourceFromFile();
        $expectedTokenised = $this->unserialiseTokenisedFile();

        self::assertTrue($objectUnderTest->tokeniseFromString($joistSrc));
        self::assertSame($expectedTokenised, $objectUnderTest->getTokenisedOutput());
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

        self::assertFalse($objectUnderTest->tokeniseFromString($input));
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
            'Missing ##joist header' => [
              <<<EOF
// This is valid - but missing the ##joist header
stage('bla', (always)) {
  sh('bla')
}
EOF,
                'Expected ##joist:"<version>" header, none found'
            ]
        ];
    }

    private function getSourceFromFile(): string
    {
        self::assertFileExists($this->joistSrcFilePath);

        return file_get_contents($this->joistSrcFilePath) ?: '';
    }

    /**
     * @return array TODO: return a specific object rather than just an assoc array
     */
    private function unserialiseTokenisedFile(): array
    {
        self::assertFileExists($this->tokenisedFilePath);

        $source = file_get_contents($this->tokenisedFilePath) ?: '';
        self::assertJson($source);
        
        return json_decode($source, true, 512, JSON_THROW_ON_ERROR);
    }
}
