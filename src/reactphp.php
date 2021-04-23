<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$loop = \React\EventLoop\Factory::create();

$dataDump = function ($chunk): void {
    echo $chunk;
};

/** @var \React\ChildProcess\Process[] $processPool */
$processPool = [];
for ($i = 0; $i<5; $i++) {
    $process = new \React\ChildProcess\Process('php /app/src/run.php app:debug');
    $process->start($loop);

    $loop->addTimer(1, function () use ($process): void {
        $process->terminate();
    });

    $processPool[$process->getPid()] = $process;

    $process->stdout->on('data', $dataDump);
    $process->stderr->on('data', $dataDump);

    $process->stdout->on('error', function (Exception $e) {
        echo $e->getMessage();
    });
    $process->stdout->on('exit', function () use (&$processPool, $process) {
        unset($processPool[$process->getPid()]);
    });
}

$loop->run();
