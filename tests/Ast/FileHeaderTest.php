<?php

declare(strict_types=1);

namespace JoistTest\Ast;

use PHPUnit\Framework\TestCase;
use Joist\Ast\FileHeader;
use Joist\Lexer\Lexeme;
use Joist\Lexer\Location;
use Joist\Lexer\TokenType;
use Joist\Lexer\Token;
use Joist\Exception\ErrorCode;
use Joist\Exception\Parser\SyntaxException;

class FileHeaderTest extends TestCase
{
    public function testFileHeaderConstructWithVersionSet(): void
    {
        $version = '2.4.6';
        $objectUnderTest = new FileHeader($version);
        self::assertSame($version, $objectUnderTest->getVersion());
    }

    public function testFileHeaderConstructWithNoVersionSet(): void
    {
        $objectUnderTest = new FileHeader();
        self::assertSame(FileHeader::DEFAULT_VERSION, $objectUnderTest->getVersion());
    }
}
