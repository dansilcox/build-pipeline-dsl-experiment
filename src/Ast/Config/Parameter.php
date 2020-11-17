<?php

declare(strict_types=1);

namespace Joist\Ast\Config;

use Joist\Ast\AstComponent;

class Parameter implements AstComponent
{
    private string $identifierName;
    private string $identifierType;
    private array $allowedValues;

    public function __construct(string $identifierName, string $identifierType, array $allowedValues = [])
    {
        $this->identifierName = $identifierName;
        $this->identifierType = $identifierType;
        $this->allowedValues = $allowedValues;
    }

    public function getName(): string
    {
        return $this->identifierName;
    }

    public function getType(): string
    {
        return $this->identifierType;
    }

    public function getAllowedValues(): array
    {
        return $this->allowedValues;
    }
}
