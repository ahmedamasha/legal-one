<?php

namespace App\Repository;

use App\Entity\Log;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Log>
 */
class LogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Log::class);
    }

    public function countMatchingLogs(array $criteria): int
    {
        $qb = $this->createQueryBuilder('l')
            ->select('COUNT(l.id)');

        foreach ($criteria as $field => $value) {
            if ($value !== null) {
                if ($field === 'startDate') {
                    $qb->andWhere('l.createdAt >= :startDate')
                        ->setParameter('startDate', $value);
                } elseif ($field === 'endDate') {
                    $qb->andWhere('l.createdAt <= :endDate')
                        ->setParameter('endDate', $value);
                } elseif ($field === 'serviceNames') {
                    $qb->andWhere('l.serviceName IN (:serviceNames)')
                        ->setParameter('serviceNames', $value);
                } else {
                    $qb->andWhere("l.$field = :$field")
                        ->setParameter($field, $value);
                }
            }
        }
        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}
