<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

/** @var \React\ChildProcess\Process[] $processPool */
$processPool = [];
/** @var \Carbon\Carbon[] $processTimesPool */
$processTimesPool = [];

$loop = \React\EventLoop\Factory::create();
$loop->addPeriodicTimer(1.0, function (\React\EventLoop\TimerInterface $timer) use (&$processPool, &$processTimesPool, $loop): void {

    foreach ($processTimesPool as $processPid => $processTimeStarted) {
        // check process against timeout.
        if ($processTimeStarted->isBefore(\Carbon\Carbon::now()->subSeconds(30))) {
            $process = &$processPool[$processPid];
            foreach ($process->pipes as $pipe) {
                $pipe->close();
            }
            $process->terminate();
        }
    }

    if (0 === count($processPool)) {
        $loop->stop();
    }
});

$writeToStdOutClosure = function ($chunk): void {
    echo $chunk;
};

for ($i = 0; $i<5; $i++) {
    $process = new \React\ChildProcess\Process('php /app/src/run.php app:debug');
    $process->start($loop);

    $processPool[$process->getPid()] = $process;
    $processTimesPool[$process->getPid()] = \Carbon\Carbon::now();

    $process->stdout->on('data', fn ($chunk) => fwrite(STDOUT, $chunk));
    $process->stderr->on('data', fn ($chunk) => fwrite( STDERR, $chunk));

    $process->stdout->on('error', fn (Exception $e) => fwrite(STDERR, $e->getMessage()));

    $process->on('exit', function () use (&$processPool, &$processTimesPool, $process) {
//        dump('exit', $process->getPid(), array_keys($processPool));

        unset($processPool[$process->getPid()]);
        unset($processTimesPool[$process->getPid()]);
    });
}

$loop->run();
