<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

$app = new Application();

$app->add(new \App\Command\DebugCommand());

$app->run(
    new ArgvInput(),
    new ConsoleOutput()
);