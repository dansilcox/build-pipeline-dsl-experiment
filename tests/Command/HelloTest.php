<?php

declare(strict_types=1);

namespace JoistTest\Command;

use Joist\Command\Hello;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HelloTest extends TestCase
{

    /** @var string */
    private $versionFilePath = __DIR__ . '/version';

    /** @var Hello */
    private $objectUnderTest;

    public function setUp(): void
    {
        $this->objectUnderTest = new Hello($this->versionFilePath);
    }

    public function testInstanceOf(): void
    {
        self::assertInstanceOf(Hello::class, $this->objectUnderTest);
        self::assertInstanceOf(SymfonyCommand::class, $this->objectUnderTest);
    }

    public function testRun(): void
    {
        $inputMock = $this->createMock(InputInterface::class);
        $outputMock = $this->createMock(OutputInterface::class);

        $version = '1.0.2';

        $helloThere = <<<EOF

       #####    ####   #####    ####   #####    #
         #     #    #    #     #         #      #
         #     #    #    #      ###      #      #
    #    #     #    #    #         #     #
     ####       ####   #####   ####      #      #

    
    A PHP-based build pipeline domain specific language experiment...
    Version: $version

EOF;
        $generalKenobi = 'General Kenobi, you are a bold one!';

        $outputMock
            ->expects(self::exactly(2))
            ->method('writeLn')
            ->withConsecutive([$helloThere], [$generalKenobi]);

        self::assertSame(0, $this->objectUnderTest->run($inputMock, $outputMock));
    }
}
