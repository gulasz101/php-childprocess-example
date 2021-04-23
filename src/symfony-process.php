<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

/** @var Symfony\Component\Process\Process[] $processPool */
$processPool = [];

for ($i = 0; $i < 5; $i++) {
    $process = new Symfony\Component\Process\Process(['php', '/app/src/run.php', 'app:debug']);
    $process->disableOutput();

    $process->start(function ($type, $buffer) {
        $stream = ($type === \Symfony\Component\Process\Process::ERR ? STDERR : STDOUT);
        fwrite($stream, $buffer);
    });

    $processPool[$process->getPid()] = $process;
}

while (0 !== count($processPool)) {
    foreach ($processPool as $pid => $process) {
        if (
            !$process->isRunning()
            || \Carbon\Carbon::createFromTimestamp($process->getStartTime())
                ->isBefore(
                    \Carbon\Carbon::now()->subSeconds(30)
                )
        ) {
            $process->stop(0);
            unset($processPool[$pid]);
        }
    }
}

