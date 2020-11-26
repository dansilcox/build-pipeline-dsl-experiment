<?php

declare(strict_types=1);

namespace JoistTest\Command;

use Joist\Command\ExecFile;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Joist\Lexer\Lexer;
use Joist\Parser\Parser;

class ExecFileTest extends TestCase
{
    /** @var string */
    private $fileName = __DIR__ . '/test.joist';

    /** @var ExecFile */
    private $objectUnderTest;

    public function setUp(): void
    {
        $this->lexerMock = $this->createMock(Lexer::class);
        $this->parserMock = $this->createMock(Parser::class);
        $this->objectUnderTest = new ExecFile();
    }

    public function testInstanceOf(): void
    {
        self::assertInstanceOf(ExecFile::class, $this->objectUnderTest);
        self::assertInstanceOf(SymfonyCommand::class, $this->objectUnderTest);
    }

    public function testRun(): void
    {
        $inputMock = $this->createMock(InputInterface::class);
        $outputMock = $this->createMock(OutputInterface::class);

        $inputMock
            ->expects(self::once())
            ->method('getArgument')
            ->with('fileName')
            ->willReturn($this->fileName);

        $outputMock
            ->expects(self::atLeastOnce())
            ->method('writeLn')
            ->with(self::isType('string'));

        self::assertSame(0, $this->objectUnderTest->run($inputMock, $outputMock));
    }
}
