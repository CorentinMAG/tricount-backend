<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{

    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {

        $user = new User();
        $user->setEmail("loic.magyar@gmail.com");
        $user->setPassword($this->hasher->hashPassword($user, "vlgklm91810@K"));
        $user->setUsername("lolo");
        $user->setRoles(["ROLE_USER"]);
        $manager->persist($user);

        $user2 = new User();
        $user2->setEmail("laurine.magyar@gmail.com");
        $user2->setPassword($this->hasher->hashPassword($user2, "vlgklm91810@K"));
        $user2->setUsername("laulau");
        $user2->setGoogleId("23397URHHiuhfgu_34");
        $user2->setRoles(["ROLE_USER"]);
        $manager->persist($user2);

        $userAdmin = new User();
        $userAdmin->setEmail("corentin.magyar@gmail.com");
        $userAdmin->setPassword($this->hasher->hashPassword($userAdmin, "vlgklm91810@K"));
        $userAdmin->setUsername("coco");
        $userAdmin->setGoogleId("aztydfsyt435749fuyg328");
        $userAdmin->setRoles(["ROLE_ADMIN"]);
        $manager->persist($userAdmin);

        $manager->flush();
    }
}
