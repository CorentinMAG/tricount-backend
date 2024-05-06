<?php

namespace App\Repository;

use App\Entity\TransactionLabel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TransactionLabel>
 *
 * @method TransactionLabel|null find($id, $lockMode = null, $lockVersion = null)
 * @method TransactionLabel|null findOneBy(array $criteria, array $orderBy = null)
 * @method TransactionLabel[]    findAll()
 * @method TransactionLabel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionLabelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TransactionLabel::class);
    }

//    /**
//     * @return TransactionLabel[] Returns an array of TransactionLabel objects
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

//    public function findOneBySomeField($value): ?TransactionLabel
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
