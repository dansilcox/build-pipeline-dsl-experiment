<?php

declare(strict_types=1);

namespace Joist\Command;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Hello extends SymfonyCommand
{
  /** @var string Default version string (unknown) */
    private const DEFAULT_VERSION = 'unknown';

  /** @var string Name of this command */
    protected static $defaultName = 'hello-there';

  /** @var string */
    private $versionFilePath;

    public function __construct(?string $versionFilePath = null)
    {
        $this->versionFilePath = $versionFilePath ?? __DIR__ . '/../../config/version';
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Check version of Joist')
            ->setHelp(
                'This command simply checks that Joist is installed correctly and tells you the version'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $version = trim((@file_get_contents($this->versionFilePath) ?: self::DEFAULT_VERSION));

        $string = <<<EOF

       #####    ####   #####    ####   #####    #
         #     #    #    #     #         #      #
         #     #    #    #      ###      #      #
    #    #     #    #    #         #     #
     ####       ####   #####   ####      #      #

    
    A PHP-based build pipeline domain specific language experiment...
    Version: $version

EOF;

        $output->writeln($string);
        $output->writeln('General Kenobi, you are a bold one!');

        return SymfonyCommand::SUCCESS;
    }
}
