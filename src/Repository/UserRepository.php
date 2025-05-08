<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Tricount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
* @implements PasswordUpgraderInterface<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findActiveUsers()
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.lastLoginAt IS NOT NULL')
            ->orderBy('u.lastLoginAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findUsersByTricount(Tricount $tricount)
    {
        return $this->createQueryBuilder('u')
            ->join('u.tricounts', 't')
            ->andWhere('t = :tricount')
            ->setParameter('tricount', $tricount)
            ->orderBy('u.username', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findUsersWithUnpaidSplits()
    {
        return $this->createQueryBuilder('u')
            ->join('u.transactions', 't')
            ->join('t.splits', 's')
            ->andWhere('s.user = u')
            ->andWhere('s.isPaid = :isPaid')
            ->setParameter('isPaid', false)
            ->groupBy('u.id')
            ->getQuery()
            ->getResult();
    }

    public function findUsersByBalanceRange(float $minBalance, float $maxBalance)
    {
        return $this->createQueryBuilder('u')
            ->select('u, SUM(t.amount) as balance')
            ->join('u.transactions', 't')
            ->groupBy('u.id')
            ->having('balance >= :minBalance')
            ->andHaving('balance <= :maxBalance')
            ->setParameter('minBalance', $minBalance)
            ->setParameter('maxBalance', $maxBalance)
            ->getQuery()
            ->getResult();
    }

    public function findUsersByLastLoginDate(\DateTimeInterface $date)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.lastLoginAt >= :date')
            ->setParameter('date', $date)
            ->orderBy('u.lastLoginAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    function findOneByEmail(string $email): ?User
    {
        return $this->findOneBy(['email' => $email]);
    }

    function findByGoogleId(string $id): ?User
    {
        return $this->findOneBy((["googleId" => $id]));
    }

//    /**
//     * @return User[] Returns an array of User objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
