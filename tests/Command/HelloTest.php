<?php

declare(strict_types=1);

namespace JoistTest\Command;

use Joist\Command\Hello;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

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
}
