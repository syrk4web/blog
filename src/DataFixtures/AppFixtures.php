<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use App\Entity\Article;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private ValidatorInterface $validator, 
        private UserPasswordHasherInterface $passwordHasher
        ) {
    }

    public function load(ObjectManager $manager): void
    {
        // Create user
        $user = new User();
        $user->setUsername("admin");
        $user->setPassword("P@ssw0rd");
        $user->setRoles(["ROLE_ADMIN"]);

        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            throw new \Exception("Invalid User");
        }

        $hashedPassword = $this->passwordHasher->hashPassword($user, $user->getPassword());
        $user->setPassword($hashedPassword);
        $manager->persist($user);

        // Create article
        $article = new Article();
        $article->setTitle("My awesome article");
        $article->setDate("02/04/1988");
        $article->setContent("This is a great article about...");
        $errors = $this->validator->validate($article);
        if (count($errors) > 0) {
            throw new \Exception("Invalid User");
        }
        $manager->persist($article);
        $manager->flush();
    }
}
