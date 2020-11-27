<?php

declare(strict_types=1);

namespace JoistTest\Ast;

use Joist\Ast\Build;
use Joist\Ast\FileHeader;
use Joist\Ast\Stage\StageHeader;
use Joist\Ast\Config\ConfigBlock;
use Joist\Ast\Stage\Stage;
use PHPUnit\Framework\TestCase;

class BuildTest extends TestCase
{
    public function testConstruct(): void
    {
        $version = '1.3.5';

        $fileHeader = new FileHeader($version);
        $configBlock = new ConfigBlock();
        $stages = [
            new Stage(new StageHeader('Test'))
        ];
        $build = new Build(
            $fileHeader,
            $configBlock,
            $stages
        );

        self::assertSame($version, $build->getVersion());
        self::assertSame($configBlock, $build->getConfig());
        self::assertSame($stages, $build->getStages());
    }
}
