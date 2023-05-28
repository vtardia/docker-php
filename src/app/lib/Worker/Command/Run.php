<?php
namespace Worker\Command;

use Psr\Log\LoggerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class Run extends Command
{

    protected ?OutputInterface $output = null;

    protected LoggerInterface $logger;

    /**
     * Termination flag
     */
    protected bool $terminate = false;

    /**
     * Debug flag
     */
    protected bool $debug = false;

    public function __construct(
        protected ContainerInterface $container,
        protected string $currentUser
    ) {
        declare(ticks = 1);
        $this->logger = $container->get('logger');

        parent::__construct();

        pcntl_signal(SIGTERM, [$this, 'onSignal']);
        pcntl_signal(SIGINT, [$this, 'onSignal']);
        pcntl_signal(SIGQUIT, [$this, 'onSignal']);
    }

    protected function configure()
    {
        $this->setName('run')->setDescription('Start worker and run jobs')
            ->setHelp('Start the queue worker and process queue tasks');

        $this->addOption('debug', null, InputOption::VALUE_NONE, 'Verbose logging');

        $this->setProcessTitle('worker-' . $this->currentUser);
    }

    /**
     * Manage process signals
     */
    protected function onSignal(int $signal): void
    {
        $signals = [SIGTERM => 'SIGTERM', SIGINT => 'SIGINT', SIGQUIT => 'SIGQUIT'];
        switch ($signal) {
            case SIGTERM:
                // shutdown taks, or 'kill <pid>'
            case SIGINT:
                // ctrl+c pressed
            case SIGQUIT:
                $message = sprintf('Received %s signal, will exit after current job finishes', $signals[$signal]);
                $this->logger->info($message);
                $this->output->writeln($message);
                $this->terminate = true;
                return;
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Capture output interface to be used by signal processing handler
        $this->output = $output;

        // Set custom options
        $this->debug = $input->getOption('debug');

        if ($this->debug) {
            $this->logger->info('Running in DEBUG mode');
        }
        $this->logger->info('Starting Worker loop', ['user' => $this->currentUser]);

        while (!$this->terminate) {
            $postgres = $this->container->get('postgres');
            $mysql = $this->container->get('mysql');
            
            $this->logger->info('Working hard...');
            $sth = $postgres->prepare('select * from users;');
            $sth->execute();
            $data = $sth->fetchAll(\PDO::FETCH_ASSOC);
            $this->logger->info('Postgres users', ['count' => count($data)]);

            $sth = $mysql->prepare('select * from users;');
            $sth->execute();
            $data = $sth->fetchAll(\PDO::FETCH_ASSOC);
            $this->logger->info('MySQL users', ['count' => count($data)]);

            $this->logger->info('Disconnecting from Postgres');
            $postgres = null;

            $this->logger->info('Disconnecting from MySQL');
            $mysql = null;
            sleep(5);

            // Cleanup
            gc_collect_cycles();

            pcntl_signal_dispatch();
        }

        // Clean exit
        return Command::SUCCESS;
    }
}
