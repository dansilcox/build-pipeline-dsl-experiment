<?php

declare(strict_types=1);

namespace Joist\Ast\Stage\Conditional;

interface AstStageConditional
{
    /**
     * @return string
     */
    public function __toString(): string;
}
