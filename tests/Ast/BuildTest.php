<?php

declare(strict_types=1);

namespace JoistTest\Ast;

use Joist\Ast\Build;
use Joist\Ast\FileHeader;
use PHPUnit\Framework\TestCase;

class BuildTest extends TestCase
{
    public function testConstruct(): void
    {
        $version = '1.3.5';

        $fileHeader = new FileHeader($version);
        $build = new Build($fileHeader);

        self::assertSame($version, $build->getVersion());
    }
}
