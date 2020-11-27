<?php

declare(strict_types=1);

namespace Joist\Ast\Stage;

use Joist\Ast\AstComponent;
use Joist\Ast\Stage\Conditional\Always as AlwaysAst;
use Joist\Ast\Stage\Conditional\AstStageConditional;

class StageHeader implements AstComponent
{
    private string $name;

    private AstStageConditional $conditional;

    public function __construct(string $name, ?AstStageConditional $conditional = null)
    {
        $this->name = $name;
        $this->conditional = $conditional ?? new AlwaysAst(true);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getConditional(): AstStageConditional
    {
        return $this->conditional;
    }
}
