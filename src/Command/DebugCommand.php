<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * Class DebugCommand
 * @package App\Command
 */
class DebugCommand extends Command
{
    protected static $defaultName = 'app:debug';

    protected function configure()
    {
        parent::configure();

        $this->setDescription('Test');
    }
    /**
     * Initializes the command after the input has been bound and before the input
     * is validated.
     *
     * This is mainly useful when a lot of commands extends one main command
     * where some things need to be initialized based on the input arguments and options.
     *
     * @see InputInterface::bind()
     * @see InputInterface::validate()
     */
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {

    }

    /**
     * Executes the current command.
     *
     * This method is not abstract because you can use this class
     * as a concrete class. In this case, instead of defining the
     * execute() method, you set the code to execute by passing
     * a Closure to the setCode() method.
     *
     * @return int 0 if everything went fine, or an exit code
     *
     * @throws \LogicException When this abstract method is not implemented
     *
     * @see setCode()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('STDOUT from pid#%s', getmypid()));

        $logger = new ConsoleLogger($output);

        // run on host level to not interfere with php process.
        (new Process(['sleep', '5']))->mustRun();

        for ($i = 0; $i < 5; $i++) {
            $logger->log(
                ConsoleLogger::ERROR,
                sprintf('STDERR #%s from pid#%s',$i, getmypid())
            );

            if ($i == rand(0, 5)) {
                throw new \Error(sprintf("Something really bad happened on process: %s", getmygid()));
            }
        }

        return Command::SUCCESS;
    }
}
