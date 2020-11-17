<?php

declare(strict_types=1);

namespace Joist\Ast;

use Joist\Ast\AstComponent;
use Joist\Ast\Config\ConfigBlock;

class Build implements AstComponent
{
    private FileHeader $fileHeader;

    private ?ConfigBlock $configBlock = null;

    public function __construct(
        FileHeader $fileHeader,
        ?ConfigBlock $configBlock = null
    ) {
        $this->fileHeader = $fileHeader;
        $this->configBlock = $configBlock;
    }

    public function getVersion(): string
    {
        return $this->fileHeader->getVersion();
    }
}
