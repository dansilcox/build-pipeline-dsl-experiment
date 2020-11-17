<?php

declare(strict_types=1);

namespace Joist\Parser;

use Joist\Ast\AstComponent;

interface ParserComponent
{
    public function parse(array $tokens, array $metadata = []): ?AstComponent;
}
