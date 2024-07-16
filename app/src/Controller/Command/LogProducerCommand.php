<?php

// src/Command/LogProducerCommand.php
namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\HttpKernel\KernelInterface;

#[AsCommand(name: 'app:log-producer')]
class LogProducerCommand extends Command
{
    private $kernel;

    public function __construct(KernelInterface $kernel)
    {
        parent::__construct();
        $this->kernel = $kernel;
    }

    protected static $defaultName = 'app:log-producer';

    protected function configure()
    {
        $this->setDescription('Reads log file, formats log entries, and publishes messages to RabbitMQ');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $connection = new AMQPStreamConnection($_ENV['RabiitMQ_Url'], $_ENV['RabiitMQ_Port'], $_ENV['RabiitMQ_User'], $_ENV['RabiitMQ_Password']); 
        $channel = $connection->channel();
        $channel->queue_declare('log_queue', false, true, false, false);
    
        $logFile = $this->kernel->getProjectDir() . '/../logs/logs.log';  // Adjust path to your log file
        $batchSize = 5; // move to config
        $batch = [];
    
        if ($handle = fopen($logFile, "r")) {
            while (($line = fgets($handle)) !== false) {
                $logData = $this->parseLogLine($line, $output);
                if ($logData !== null) {
                    $batch[] = json_encode($logData);
                    if (count($batch) >= $batchSize) {
                        $msg = new AMQPMessage(implode("\n", $batch));
                        $channel->basic_publish($msg, '', 'log_queue');
                        $batch = [];
                    }
                }
            }
    
            if (count($batch) > 0) {
                $msg = new AMQPMessage(implode("\n", $batch));
                $channel->basic_publish($msg, '', 'log_queue');
            }
            fclose($handle);
        } else {
            $output->writeln('Error opening the log file.');
            return Command::FAILURE;
        }
    
        $channel->close();
        $connection->close();
    
        return Command::SUCCESS;
    }

    private function parseLogLine(string $line, OutputInterface $output): array
    {
        $parts = explode(' - - ', $line, 2); // Split by ' - - '
        $serviceName = trim(explode(' ', $parts[0])[0], '-'); // Extract service name

        // Extract timestamp from [17/Aug/2018:09:21:53 +0000] format
        $timestampPart = substr($parts[1], strpos($parts[1], '[') + 1, strpos($parts[1], ']') - strpos($parts[1], '[') - 1);
        $logTimestamp = \DateTime::createFromFormat('d/M/Y:H:i:s O', $timestampPart);

        // Extract log message
        $logMessage = trim(explode('"', $parts[1])[1]);

        // Extract status code (last part of the line)
        $statusCode = (int) substr(strrchr(trim($parts[1]), ' '), 1);

        return [
            'serviceName' => $serviceName,
            'logTimestamp' => $logTimestamp->format('Y-m-d H:i:s'),
            'logMessage' => $logMessage,
            'statusCode' => $statusCode,
        ];
    }
}
