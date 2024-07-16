<?php

namespace App\Service;

use App\Repository\LogRepository;

class LogCountService
{
    private $logRepository;

    public function __construct(LogRepository $logRepository)
    {
        $this->logRepository = $logRepository;
    }

    public function countLogs(array $filters): int
    {
        // Prepare criteria based on filters
        $criteria = [];

        if (isset($filters['serviceNames'])) {
            $criteria['serviceName'] = $filters['serviceNames'];
        }
        if (isset($filters['statusCode'])) {
            $criteria['statusCode'] = $filters['statusCode'];
        }

        if (isset($filters['startDate'])) {
            $startDate = \DateTime::createFromFormat('Y-m-d H:i:s', $filters['startDate']);
            if ($startDate instanceof \DateTime) {
                $criteria['startDate'] = $startDate;
            }
        }
        if (isset($filters['endDate'])) {
            $endDate = \DateTime::createFromFormat('Y-m-d H:i:s', $filters['endDate']);
            if ($endDate instanceof \DateTime) {
                $criteria['endDate'] = $endDate;
            }
        }

        $count = $this->logRepository->countMatchingLogs($criteria);

        return $count;
    }
}
