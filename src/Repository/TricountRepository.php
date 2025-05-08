<?php

namespace App\Repository;

use App\Entity\Tricount;
use App\Entity\User;
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

    public function getByUser($user)
    {
        return $this->createQueryBuilder('t')
        ->where('t.owner = :user')
        ->orWhere(':user MEMBER of t.users')
        ->setParameter('user', $user)
        ->orderBy('t.createdAt', 'DESC')
        ->getQuery()
        ->getResult();
    }

    public function getByToken(string $token): Tricount | null
    {
        return $this->createQueryBuilder('t')
        ->where('t.token = :token')
        ->setParameter('token', $token)
        ->getQuery()
        ->getOneOrNullResult();
    }

    public function findActiveTricounts()
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.isActive = :isActive')
            ->setParameter('isActive', true)
            ->orderBy('t.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findTricountsByUser(User $user)
    {
        return $this->createQueryBuilder('t')
            ->join('t.users', 'u')
            ->andWhere('u = :user')
            ->setParameter('user', $user)
            ->orderBy('t.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findTricountsByOwner(User $user)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.owner = :owner')
            ->setParameter('owner', $user)
            ->orderBy('t.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findTricountsByToken(string $token)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.token = :token')
            ->andWhere('t.isActive = :isActive')
            ->setParameter('token', $token)
            ->setParameter('isActive', true)
            ->getQuery()
            ->getResult();
    }

    public function findTricountsByDateRange(\DateTimeInterface $startDate, \DateTimeInterface $endDate)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.createdAt BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->orderBy('t.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findTricountsWithUnpaidTransactions()
    {
        return $this->createQueryBuilder('t')
            ->join('t.transactions', 'tr')
            ->join('tr.splits', 's')
            ->andWhere('s.isPaid = :isPaid')
            ->setParameter('isPaid', false)
            ->groupBy('t.id')
            ->getQuery()
            ->getResult();
    }

    public function findTricountsByCurrency(string $currencyCode)
    {
        return $this->createQueryBuilder('t')
            ->join('t.currency', 'c')
            ->andWhere('c.code = :currencyCode')
            ->setParameter('currencyCode', $currencyCode)
            ->orderBy('t.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findTricountsByLabel(string $labelName)
    {
        return $this->createQueryBuilder('t')
            ->join('t.label', 'l')
            ->andWhere('l.name = :labelName')
            ->setParameter('labelName', $labelName)
            ->orderBy('t.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
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
