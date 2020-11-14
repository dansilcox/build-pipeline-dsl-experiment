<?php

declare(strict_types=1);

namespace JoistTest\Ast;

use PHPUnit\Framework\TestCase;
use Joist\Ast\Build;

class BuildTest extends TestCase
{
    public function testConstruct(): void
    {
        $version = '1.3.5';
        $build = new Build($version);
        self::assertSame($version, $build->getJoistVersion());
    }
}
