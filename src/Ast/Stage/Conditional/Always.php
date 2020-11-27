<?php

declare(strict_types=1);

namespace Joist\Ast\Stage\Conditional;

use Joist\Ast\AstComponent;

class Always implements AstComponent, AstStageConditional
{
    private bool $isDefault;

    public function __construct(bool $isDefault = false)
    {
        $this->isDefault = $isDefault;
    }

    public function isDefault(): bool
    {
        return $this->isDefault;
    }

    public function __toString(): string
    {
        return sprintf(
            'always%s',
            $this->isDefault ? '(default)' : ''
        );
    }
}
