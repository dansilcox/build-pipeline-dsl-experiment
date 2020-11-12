<?php

declare(strict_types=1);

namespace JoistTest\Lexer;

use Joist\Exception\LexerException;
use Joist\Lexer\Token;
use Joist\Lexer\Lexer;
use PHPUnit\Framework\TestCase;

final class LexerTest extends TestCase
{
    private string $joistSrcFilePath = __DIR__ . '/sample.joist';

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
        
        self::assertFileExists($this->joistSrcFilePath);
        $joistSrc = file_get_contents($this->joistSrcFilePath);

        self::assertTrue($objectUnderTest->tokeniseFromString($joistSrc));
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
        self::assertSame('Expected ##joist header, none found', $objectUnderTest->getLastError());

        $joistSrcGood = file_get_contents($this->joistSrcFilePath);
        
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

    public function invalidTokenDataProvider(): array
    {
        return [
            'Missing ##joist header' => [
              <<<EOF
// This is valid - but missing the ##joist header
stage('bla', (always)) {
  sh('bla')
}
EOF,
              'Expected ##joist header, none found'
            ]
        ];
    }
}
