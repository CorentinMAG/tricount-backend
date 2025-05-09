<?php

namespace App\Repository;

use App\Entity\Transaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Transaction>
 *
 * @method Transaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transaction[]    findAll()
 * @method Transaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

//    /**
//     * @return Transaction[] Returns an array of Transaction objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Transaction
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    /**
     * @return Transaction[] Returns an array of Transaction objects
     */
    public function findByTricount($tricount, array $filters = []): array
    {
        $qb = $this->createQueryBuilder('t')
            ->leftJoin('t.splits', 's')
            ->andWhere('t.tricount = :tricount')
            ->setParameter('tricount', $tricount);

        if (isset($filters['user'])) {
            $qb->andWhere('t.owner = :user OR s.user = :user')
               ->setParameter('user', $filters['user']);
        }

        if (isset($filters['dateFrom'])) {
            $qb->andWhere('t.createdAt >= :dateFrom')
               ->setParameter('dateFrom', new \DateTime($filters['dateFrom']));
        }

        if (isset($filters['dateTo'])) {
            $qb->andWhere('t.createdAt <= :dateTo')
               ->setParameter('dateTo', new \DateTime($filters['dateTo']));
        }

        if (isset($filters['isActive'])) {
            $qb->andWhere('t.isActive = :isActive')
               ->setParameter('isActive', $filters['isActive']);
        }

        return $qb->orderBy('t.createdAt', 'DESC')
                 ->distinct()
                 ->getQuery()
                 ->getResult();
    }
}
