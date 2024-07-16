<?php
// src/Entity/Log.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: "App\Repository\LogRepository")]
#[ORM\Table(name: "logs")]
#[ORM\Index(name: "serviceName_idx", columns: ["service_name"])]
#[ORM\Index(name: "statusCode_idx", columns: ["status_code"])]
#[ORM\Index(name: "logTimestamp_idx", columns: ["log_timestamp"])]
class Log
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $serviceName;

    #[ORM\Column(type: 'integer')]
    private $statusCode;

    #[ORM\Column(type: 'text')]
    private $logMessage;

    #[ORM\Column(type: 'datetime')]
    private $logTimestamp;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getServiceName(): ?string
    {
        return $this->serviceName;
    }

    public function setServiceName(string $serviceName): self
    {
        $this->serviceName = $serviceName;

        return $this;
    }

    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }

    public function setStatusCode(int $statusCode): self
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    public function getLogMessage(): ?string
    {
        return $this->logMessage;
    }

    public function setLogMessage(string $logMessage): self
    {
        $this->logMessage = $logMessage;

        return $this;
    }

    public function getLogTimestamp(): ?\DateTimeInterface
    {
        return $this->logTimestamp;
    }

    public function setLogTimestamp(\DateTimeInterface $logTimestamp): self
    {
        $this->logTimestamp = $logTimestamp;

        return $this;
    }
}
