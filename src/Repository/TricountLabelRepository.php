<?php

namespace App\Repository;

use App\Entity\TricountLabel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TricountLabel>
 *
 * @method TricountLabel|null find($id, $lockMode = null, $lockVersion = null)
 * @method TricountLabel|null findOneBy(array $criteria, array $orderBy = null)
 * @method TricountLabel[]    findAll()
 * @method TricountLabel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TricountLabelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TricountLabel::class);
    }

//    /**
//     * @return TricountLabel[] Returns an array of TricountLabel objects
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

//    public function findOneBySomeField($value): ?TricountLabel
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
