<?php

declare(strict_types=1);

namespace JoistTest\Command;

use Joist\Command\ConfigProvider;
use Joist\Command\Hello;
use PHPUnit\Framework\TestCase;

final class ConfigProviderTest extends TestCase
{
    public function testInvoke(): void
    {
        $objectUnderTest = new ConfigProvider();

        $expectedCommands = [
            new Hello()
        ];
        $actualCommands = $objectUnderTest();

        self::assertEquals($expectedCommands, $actualCommands);
    }
}
