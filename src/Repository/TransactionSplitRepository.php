<?php

namespace App\Repository;

use App\Entity\TransactionSplit;
use App\Entity\User;
use App\Entity\Tricount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TransactionSplitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TransactionSplit::class);
    }

    public function findUnpaidSplitsForUser(User $user)
    {
        return $this->createQueryBuilder('ts')
            ->andWhere('ts.user = :user')
            ->andWhere('ts.isPaid = :isPaid')
            ->setParameter('user', $user)
            ->setParameter('isPaid', false)
            ->orderBy('ts.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findSplitsByTransaction($transactionId)
    {
        return $this->createQueryBuilder('ts')
            ->andWhere('ts.transaction = :transactionId')
            ->setParameter('transactionId', $transactionId)
            ->orderBy('ts.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findSplitsByTricountAndUser(Tricount $tricount, User $user)
    {
        return $this->createQueryBuilder('ts')
            ->join('ts.transaction', 't')
            ->andWhere('t.tricount = :tricount')
            ->andWhere('ts.user = :user')
            ->setParameter('tricount', $tricount)
            ->setParameter('user', $user)
            ->orderBy('t.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getTotalUnpaidAmountForUser(User $user)
    {
        return $this->createQueryBuilder('ts')
            ->select('SUM(ts.amount) as total')
            ->andWhere('ts.user = :user')
            ->andWhere('ts.isPaid = :isPaid')
            ->setParameter('user', $user)
            ->setParameter('isPaid', false)
            ->getQuery()
            ->getSingleScalarResult() ?? 0;
    }

    public function getTotalPaidAmountForUser(User $user)
    {
        return $this->createQueryBuilder('ts')
            ->select('SUM(ts.amount) as total')
            ->andWhere('ts.user = :user')
            ->andWhere('ts.isPaid = :isPaid')
            ->setParameter('user', $user)
            ->setParameter('isPaid', true)
            ->getQuery()
            ->getSingleScalarResult() ?? 0;
    }
} 