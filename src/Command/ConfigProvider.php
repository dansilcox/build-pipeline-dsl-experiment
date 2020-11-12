<?php

declare(strict_types=1);

namespace Joist\Command;

use Symfony\Component\Console\Command\Command as SymfonyCommand;

class ConfigProvider
{
  /**
   * @return array<SymfonyCommand>
   */
    public function __invoke(): array
    {
        return [
            new Hello()
        ];
    }
}
