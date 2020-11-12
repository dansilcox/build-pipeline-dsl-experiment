<?php

declare(strict_types=1);

namespace Joist\Lexer;

use JsonSerializable;

class Token implements JsonSerializable {
    private string $type;

    private string $lexeme;

    private Location $location;

    private ?string $literal;

    public function __construct(string $type, string $lexeme, Location $location, ?string $literal = null)
    {
        $this->type = $type;
        $this->lexeme = $lexeme;
        $this->location = $location;
        $this->literal = $literal;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getLexeme(): string
    {
        return $this->lexeme;
    }

    public function getLocation(): Location
    {
        return $this->location;
    }

    public function getLiteral(): ?string
    {
        return $this->literal;
    }

    public function jsonSerialize(): array
    {
        return [
            'type'     => $this->type,
            'lexeme'   => $this->lexeme,
            'literal'  => $this->literal,
            'location' => $this->location->jsonSerialize()
        ];
    }
}
