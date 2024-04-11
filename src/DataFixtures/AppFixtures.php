<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Article;
use App\Entity\User;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private ValidatorInterface $validator, private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        // create a user
        $user = new User();
        $user->setUsername('admin');
        $user->setPassword('P@ssw0rd');
        $user->setRoles(['ROLE_ADMIN']);
        // hash password

        // add validation
        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            $errorsString = (string) $errors;
            throw new \Exception($errorsString);
        }

        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            'P@ssw0rd'
            );
            
        $user->setPassword($hashedPassword);

        $manager->persist($user);


        $article = new Article();
        $article->setTitle('Awesome title');
        $article->setDate('02/12/2014');
        $article->setContent('Article about something...');
        $errors = $this->validator->validate($article);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;
            throw new \Exception($errorsString);
        }

        $manager->persist($article);

        // add more  articles if needed

        $manager->flush();
    }
}
