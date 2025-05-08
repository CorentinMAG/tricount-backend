<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Currency;
use App\Entity\TricountLabel;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UserFixtures extends Fixture
{

    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
        $this->rootDir = __DIR__ . '/../..';
    }

    public function load(ObjectManager $manager): void
    {
        $currency1 = new Currency();
        $currency1->setName("USD");
        $currency1->setLabel("$");
        copy($this->rootDir . "/fixtures/usd.png", $this->rootDir . "/tmp/usd.png");
        $currency1->setImageFile(new UploadedFile($this->rootDir . "/tmp/usd.png", "usd.png", null, null, true));
        $manager->persist($currency1);

        $currency2 = new Currency();
        $currency2->setName("EUR");
        $currency2->setLabel("€");
        copy($this->rootDir . "/fixtures/euro.png", $this->rootDir . "/tmp/euro.png");
        $currency2->setImageFile(new UploadedFile($this->rootDir . "/tmp/euro.png", "euro.png", null, null, true));
        $manager->persist($currency2);

        $label1 = new TricountLabel();
        $label1->setName("event");
        copy($this->rootDir . "/fixtures/event.png", $this->rootDir . "/tmp/event.png");
        $label1->setImageFile(new UploadedFile($this->rootDir . "/tmp/event.png", "event.png", null, null, true));
        $manager->persist($label1);

        $label2 = new TricountLabel();
        $label2->setName("travel");
        copy($this->rootDir . "/fixtures/travel.png", $this->rootDir . "/tmp/travel.png");
        $label2->setImageFile(new UploadedFile($this->rootDir . "/tmp/travel.png", "travel.png", null, null, true));
        $manager->persist($label2);

        $label3 = new TricountLabel();
        $label3->setName("shopping");
        copy($this->rootDir . "/fixtures/shopping.png", $this->rootDir . "/tmp/shopping.png");
        $label3->setImageFile(new UploadedFile($this->rootDir . "/tmp/shopping.png", "shopping.png", null, null, true));
        $manager->persist($label3);

        $label4 = new TricountLabel();
        $label4->setName("food");
        copy($this->rootDir . "/fixtures/food.png", $this->rootDir . "/tmp/food.png");
        $label4->setImageFile(new UploadedFile($this->rootDir . "/tmp/food.png", "food.png", null, null, true));
        $manager->persist($label4); 

        $label5 = new TricountLabel();
        $label5->setName("transport");
        copy($this->rootDir . "/fixtures/transport.png", $this->rootDir . "/tmp/transport.png");
        $label5->setImageFile(new UploadedFile($this->rootDir . "/tmp/transport.png", "transport.png", null, null, true));
        $manager->persist($label5);

        $label6 = new TricountLabel();
        $label6->setName("other");
        copy($this->rootDir . "/fixtures/other.png", $this->rootDir . "/tmp/other.png");
        $label6->setImageFile(new UploadedFile($this->rootDir . "/tmp/other.png", "other.png", null, null, true));
        $manager->persist($label6);

        $label7 = new TricountLabel();
        $label7->setName("holidays");
        copy($this->rootDir . "/fixtures/holidays.png", $this->rootDir . "/tmp/holidays.png");
        $label7->setImageFile(new UploadedFile($this->rootDir . "/tmp/holidays.png", "holidays.png", null, null, true));
        $manager->persist($label7);

        $label8 = new TricountLabel();
        $label8->setName("project");
        copy($this->rootDir . "/fixtures/project.png", $this->rootDir . "/tmp/project.png");
        $label8->setImageFile(new UploadedFile($this->rootDir . "/tmp/project.png", "project.png", null, null, true));
        $manager->persist($label8);

        $label9 = new TricountLabel();
        $label9->setName("work");
        copy($this->rootDir . "/fixtures/work.png", $this->rootDir . "/tmp/work.png");
        $label9->setImageFile(new UploadedFile($this->rootDir . "/tmp/work.png", "work.png", null, null, true));
        $manager->persist($label9);
        
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
        $user2->setRoles(["ROLE_USER"]);
        $manager->persist($user2);

        $userAdmin = new User();
        $userAdmin->setEmail("corentin.magyar@gmail.com");
        $userAdmin->setPassword($this->hasher->hashPassword($userAdmin, "vlgklm91810@K"));
        $userAdmin->setUsername("coco");
        $userAdmin->setRoles(["ROLE_ADMIN"]);
        $manager->persist($userAdmin);

        $manager->flush();
    }
}
