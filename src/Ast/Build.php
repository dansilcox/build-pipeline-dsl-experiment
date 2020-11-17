<?php

declare(strict_types=1);

namespace Joist\Ast;

use Joist\Ast\AstComponent;
use Joist\Ast\Config\ConfigBlock;
use Joist\Ast\Stage\Stage;

class Build implements AstComponent
{
    private FileHeader $fileHeader;

    private ?ConfigBlock $configBlock = null;

    /**
     * @var array<Stage>
     */
    private array $stages = [];

    /**
     * @var FileHeader       $fileHeader
     * @var ConfigBlock|null $configBlock
     * @var array<Stage>     $stages
     */
    public function __construct(
        FileHeader $fileHeader,
        ?ConfigBlock $configBlock = null,
        array $stages = []
    ) {
        $this->fileHeader = $fileHeader;
        $this->configBlock = $configBlock;
        $this->stages = $stages;
    }

    public function getVersion(): string
    {
        return $this->fileHeader->getVersion();
    }

    public function getConfig(): ?ConfigBlock
    {
        return $this->configBlock;
    }

    /**
     * @return array<Stage>
     */
    public function getStages(): array
    {
        return $this->stages;
    }
}
