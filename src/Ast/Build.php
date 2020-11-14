<?php

declare(strict_types=1);

namespace Joist\Ast;

class Build
{
    private string $joistVersion;

    public function __construct(string $joistVersion)
    {
        $this->joistVersion = $joistVersion;
    }

    public function getJoistVersion(): string
    {
        return $this->joistVersion;
    }
}
