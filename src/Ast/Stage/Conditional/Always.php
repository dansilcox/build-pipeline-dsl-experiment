<?php

declare(strict_types=1);

namespace Joist\Ast\Stage\Conditional;

use Joist\Ast\AstComponent;

class Always implements AstComponent, AstStageConditional
{
    public function __toString(): string
    {
        return 'always';
    }
}
