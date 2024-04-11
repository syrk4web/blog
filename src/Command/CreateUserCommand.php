<?php
namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
// Metadata
#[AsCommand(
    name: 'app:create-user',
    description: 'Create user with username and password',
    hidden: false,
)]
class CreateUserCommand extends Command
{
  // Here you can add checks/conditions to construct commands
  public function __construct(private ValidatorInterface $validator, private EntityManagerInterface $entityManager) {    
     parent::__construct();
  }
  // Detail about command and possible needed inputs
    protected function configure(): void {
        // Description and help
        $this
            ->setDescription('Create user with username and password')
            ->setHelp('This command allows you to create a user...')
        ;
        // Add arguments
        $this
            ->addArgument('username', InputArgument::REQUIRED, 'The username.')
            ->addArgument('password', InputArgument::REQUIRED, 'The password.')
            ;
    }

  // Execute logic with input and output access
    protected function execute(InputInterface $input, OutputInterface $output): int {
        // Log entries
        $output->writeln('==========================');
        $output->writeln('Trying to create user...');
        $output->writeln('Username: ' . $input->getArgument('username'));
        $output->writeln('Password: ' . $input->getArgument('password'));

        // Create user with arguments
        $user = new User();
        $user->setUsername($input->getArgument('username'));
        $user->setPassword($input->getArgument('password'));

        // Check if valid after setting values and before hash
        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            $output->writeln('Validation failed');
            $output->writeln($errors);
            $output->writeln('==========================');
            return Command::FAILURE;
        }

        // Store on DB
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $output->writeln('User created successfully');
        $output->writeln('==========================');

        // All worked fine
        return Command::SUCCESS;
    }
}