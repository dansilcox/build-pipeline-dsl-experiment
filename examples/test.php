#!/usr/bin/env php
<?php

declare(strict_types=1);

use Joist\Lexer\Lexer;

require __DIR__ . '/../vendor/autoload.php';

$srcPath =__DIR__ . '/build.joist';
$lexer = new Lexer($srcPath);

$tokeniseStatus = $lexer->tokenise();
if ($tokeniseStatus) {
    echo 'Success!' . PHP_EOL;
    $tokenJson = json_encode($lexer->getTokenisedOutput(), JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
    file_put_contents(__DIR__ . '/tokenised.build.joist.json', $tokenJson);
    echo 'Errors: (' . $lexer->getLastError() . ')' . PHP_EOL;
} else {
    echo 'Failure: ' . $lexer->getLastError() . PHP_EOL;
}
