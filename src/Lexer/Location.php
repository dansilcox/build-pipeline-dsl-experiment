<?php

declare(strict_types=1);

namespace Joist\Lexer;

use JsonSerializable;

class Location implements JsonSerializable {
    private int $line;

    private int $col;

    private int $length;

    public function __construct(int $line, int $col, int $length)
    {
        $this->line = $line;
        $this->col = $col;
        $this->length = $length;
    }

    public function getLine(): int
    {
        return $this->line;
    }

    public function getCol(): int
    {
        return $this->col;
    }

    public function getLength(): int
    {
        return $this->length;
    }

    public function jsonSerialize(): array
    {
        return [
            'line'   => $this->line,
            'col'    => $this->col,
            'length' => $this->length
        ];
    }
}
