<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$processPool = [];

for ($i = 0; $i < 5; $i++) {
    $process = new Symfony\Component\Process\Process(['php', '/app/src/run.php', 'app:debug']);
    $process->setTimeout(30);
    $process->start();

    $processPool[] = $process;
}

/**
 * @param Symfony\Component\Process\Process[] $processPool
 */
function manageProcesses(array &$processPool): void {
    foreach ($processPool as $id => $process) {
        echo $process->getIncrementalErrorOutput();
        echo $process->getIncrementalOutput();

        if ($process->isSuccessful()) {
            unset($processPool[$id]);
        }

        if (count($processPool)) {
            manageProcesses($processPool);
        }
    }
};

manageProcesses($processPool);
