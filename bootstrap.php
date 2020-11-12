<?php

declare(strict_types=1);

use Symfony\Component\Console\Application;
use Joist\Command\ConfigProvider as CommandConfigProvider;

require __DIR__ . '/vendor/autoload.php';

echo PHP_EOL;

echo <<<EOF
   #####    ####   #####    ####   #####    #
     #     #    #    #     #         #      #
     #     #    #    #      ###      #      #
#    #     #    #    #         #     #
 ####       ####   #####   ####      #      #


EOF;

echo 'A PHP-based build pipeline domain specific language experiment...' . PHP_EOL . PHP_EOL;

$application = new Application();

// Register commands based on the Command ConfigProvider
$commandConfig = new CommandConfigProvider();
foreach ($commandConfig() as $command) {
  $application->add($command);
}
$application->run();
