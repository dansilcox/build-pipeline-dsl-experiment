<?php

declare(strict_types=1);

namespace Joist\Ast\Stage;

use Joist\Ast\AstComponent;
use Joist\Ast\Stage\Step as StepAst;
use Joist\Ast\Stage\Conditional\Always as AlwaysAst;
use Joist\Ast\Stage\Conditional\AstStageConditional;

class Stage implements AstComponent
{
    private string $name;

    /** @var array<StepAst> */
    private array $steps = [];

    private AstStageConditional $conditional;

    public function __construct(string $name, ?AstStageConditional $conditional = null)
    {
        $this->name = $name;
        $this->conditional = $conditional ?? new AlwaysAst();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getConditional(): AstStageConditional
    {
        return $this->conditional;
    }

    public function addStepAst(StepAst $step): void
    {
        $this->steps[] = $step;
    }

    /**
     * @return array<StepAst>
     */
    public function getSteps(): array
    {
        return $this->steps;
    }
}
