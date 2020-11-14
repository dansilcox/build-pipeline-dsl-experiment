<?php

declare(strict_types=1);

namespace Joist\Exception\Parser;

use UnexpectedValueException;
use Joist\Exception\ErrorCode;
use Joist\Lexer\Location;

class SyntaxException extends UnexpectedValueException {
    public function __construct(
        $message,
        $code = ErrorCode::SYNTAX_ERROR_GENERIC,
        ?Location $location = null,
        $previous = null
    ) {
        $fullMessage = $message;
        if ($location !== null) {
            $line = $location->getLine();
            $col = $location->getCol();
            $fullMessage = "Syntax error: $message - line $line, column $col";
        }

        parent::__construct($fullMessage, $code, $previous);
    }
}
