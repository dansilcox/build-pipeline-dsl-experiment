<?php

declare(strict_types=1);

namespace Joist\Ast\Config;

use Joist\Ast\AstComponent;

class ConfigBlock implements AstComponent
{
    private array $params = [];

    public function addParameter(
        string $identifier,
        string $identifierType,
        array $values = []
    ): void {
        $this->params["$identifier:$identifierType"] = new Parameter(
            $identifier,
            $identifierType,
            $values
        );
    }

    public function addParameterAst(
        Parameter $parameterAst
    ): void {
        $identifier = $parameterAst->getName();
        $identifierType = $parameterAst->getType();

        $this->params["$identifier:$identifierType"] = $parameterAst;
    }

    public function getParameters(): array
    {
        return $this->params;
    }
}
