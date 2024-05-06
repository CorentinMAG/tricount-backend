<?php

namespace App\Repository;

use App\Entity\Tricount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tricount>
 *
 * @method Tricount|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tricount|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tricount[]    findAll()
 * @method Tricount[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TricountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tricount::class);
    }

//    /**
//     * @return Tricount[] Returns an array of Tricount objects
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

//    public function findOneBySomeField($value): ?Tricount
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
