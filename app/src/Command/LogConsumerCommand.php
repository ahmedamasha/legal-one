<?php

// src/Command/LogConsumerCommand.php
namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use App\Entity\Log;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

#[AsCommand(name: 'app:log-consumer')]
class LogConsumerCommand extends Command
{
    private $kernel;
    private $entityManager;

    public function __construct(KernelInterface $kernel, EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->kernel = $kernel;
        $this->entityManager = $entityManager;
    }

    protected static $defaultName = 'app:log-consumer';

    protected function configure()
    {
        $this->setDescription('Consumes messages from RabbitMQ and saves them to the database');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $connection = new AMQPStreamConnection($_ENV['RabiitMQ_Url'], $_ENV['RabiitMQ_Port'], $_ENV['RabiitMQ_User'], $_ENV['RabiitMQ_Password']);
        $channel = $connection->channel();
        $channel->queue_declare('log_queue', false, true, false, false);

        $callback = function ($msg) use ($output) {
            $logMessages = explode("\n", $msg->body);
            foreach ($logMessages as $logMessage) {
                $logData = json_decode($logMessage, true);
                if ($logData === null) {
                    $output->writeln("Invalid JSON message: $logMessage");
                    continue;
                }

                $log = new Log();
                $log->setServiceName($logData['serviceName'] ?? 'unknown');
                $log->setStatusCode($logData['statusCode'] ?? 0);
                $log->setLogMessage($logData['logMessage'] ?? '');
                $log->setLogTimestamp(new \DateTime($logData['logTimestamp'] ?? 'now'));

                $this->entityManager->persist($log);
            }
            $this->entityManager->flush();
            $output->writeln(count($logMessages) . ' logs inserted into the database.');
        };

        $channel->basic_consume('log_queue', '', false, true, false, false, $callback);

        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();

        return Command::SUCCESS;
    }
}
