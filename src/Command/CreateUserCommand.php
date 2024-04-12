<?php

namespace App\Command;

// Instanciate a command
use Symfony\Component\Console\Command\Command;
// Indicate that the class is a command
use Symfony\Component\Console\Attribute\AsCommand;
// Allow to work with input and ouput
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use App\Entity\User;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name:"app:create-user", description:"Create a new user", hidden: false)]
class CreateUserCommand extends Command
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private ValidatorInterface $validator, 
        private EntityManagerInterface $entityManager
        )
    {
        parent::__construct();
    }
    // Configure the command before execution
    protected function configure(): void
    {
        //Description and help of the command
        $this->setDescription('Create a new user')
            ->setHelp('This command allows you to create a user...');
        // Add arguments
        // When executing, argument need to be passed from top to bottom
        $this->addArgument('username', InputArgument::REQUIRED,'The username of the user.');
        $this->addArgument('password', InputArgument::REQUIRED,'The password of the user.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Output info to console
        $output->writeln('=============================');
        $output->writeln('Trying to create a new user...');
        $output->writeln('username : '.$input->getArgument('username'));
        $output->writeln('password : '.$input->getArgument('password'));

        // Create user
        $user = new User();
        $user->setUsername($input->getArgument('username'));
        $user->setPassword($input->getArgument('password'));

        // Add validation
        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            $output->writeln('Impossible to create User...');
            $output->writeln((string) $errors);
            $output->writeln('=============================');
            return Command::FAILURE;
        }

        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $input->getArgument('password')
            );
            
        $user->setPassword($hashedPassword);

        // Store User
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $output->writeln('Create User successfully...');
        $output->writeln('=============================');
        return Command::SUCCESS;
    }
}