<?php

declare(strict_types=1);

namespace JoistTest\Command;

use Joist\Command\ConfigProvider;
use PHPUnit\Framework\TestCase;

final class ConfigProviderTest extends TestCase
{
    public function testInvoke(): void
    {
        $objectUnderTest = new ConfigProvider();

        $expectedCommands = [];
        $actualCommands = $objectUnderTest();

        self::assertSame($expectedCommands, $actualCommands);
    }
}
