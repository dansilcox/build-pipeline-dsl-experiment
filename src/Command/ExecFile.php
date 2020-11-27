<?php

declare(strict_types=1);

namespace Joist\Command;

use Joist\Lexer\Lexer;
use Joist\Parser\Parser;
use Joist\Parser\Mapper;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExecFile extends SymfonyCommand
{
    private const ARGUMENT_FILE_NAME = 'fileName';

    /** @var string Name of this command */
    protected static $defaultName = 'exec:file';

    /**
     * Configure command
     */
    protected function configure(): void
    {
        $this
            ->addArgument(self::ARGUMENT_FILE_NAME, InputArgument::REQUIRED, 'Please select a file to run...');
        $this
            ->setDescription('Execute a Joist file')
            ->setHelp(
                'Execute a Joist build pipeline!'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filename = $input->getArgument(self::ARGUMENT_FILE_NAME) ?? '';
        if (is_array($filename)) {
            $filename = implode('', $filename);
        }

        $output->writeLn('Parsing ' . $filename);

        $lexer = new Lexer($filename);

        if (!$lexer->tokenise()) {
            $output->writeLn('Error: ' . $lexer->getLastError());
            return 1;
        }

        $tokens = $lexer->getTokenisedOutput()['tokens'] ?? [];
        if (empty($tokens)) {
            $output->writeLn('No tokens found');
            return 2;
        }

        $parser = new Parser($tokens, new Mapper());
        $build = $parser->getBuild();

        $output->writeLn('Version: ' . $build->getVersion());

        $configBlock = $build->getConfig();
        if ($configBlock !== null) {
            $output->writeLn('Config: ');
            $output->writeLn('---');

            $parameters = $configBlock->getParameters();
            if (empty($parameters)) {
                $output->writeLn('No parameters found');
            } else {
                foreach ($parameters as $parameter) {
                    $output->writeLn('Parameter: ' . $parameter);
                }
            }
            $output->writeLn('---');
        }

        $output->writeLn('Stages: ');
        $output->writeLn('---');
        foreach ($build->getStages() as $stage) {
            $output->writeLn('Name: ' . $stage->getName());
            $output->writeLn('Conditional: ' . $stage->getConditional());
            $output->writeLn('Steps: ');
            $output->writeLn('-');
            foreach ($stage->getSteps() as $step) {
                $output->writeLn('Name: ' . $step);
            }
            $output->writeLn('-');
        }
        $output->writeLn('---');

        return SymfonyCommand::SUCCESS;
    }
}
