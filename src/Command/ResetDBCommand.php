<?php

namespace App\Command;

// Instanciate a command
use Symfony\Component\Console\Command\Command;
// Indicate that the class is a command
use Symfony\Component\Console\Attribute\AsCommand;
// Allow to work with input and ouput
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use App\Entity\User;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name:"app:reset-db", description:"Drop, create and migrate DB", hidden: false)]
class ResetDBCommand extends Command
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
        $this->setDescription('Drop, create and migrate DB')
            ->setHelp('This command will execute others commands...');
    }

    function is_run_cli($err, $ok, $output, $inpArray): bool {
        // Case command is not found
        try {
            // Code is 0 if success else 1
            $code = $this->getApplication()->doRun($inpArray, $output);
            if($code === 0) {
                $output->writeln($ok);
                return true;
            } else {
                $output->writeln($err);
                return false;
            }
        } catch (\Exception $e) {
            $output->writeln($err);
            $output->writeln($e);
            $output->writeln('=============================');
            return false;
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Output info to console
        $output->writeln('=============================');
        $output->writeln('Trying to reset DB...');

        // Drop DB
        $is_delete = $this->is_run_cli(
            "Error while drop DB...", 
            "DB drop successfully...", 
            $output, 
            new ArrayInput(['command' => 'doctrine:database:drop', '--force' => true]));
        
        if(!$is_delete) {
            return Command::FAILURE;
        }

        // Create DB
        $is_create = $this->is_run_cli(
            "Error while creating DB...", 
            "DB created successfully...", 
            $output, 
            new ArrayInput(['command' => 'doctrine:database:create']));
        
        if(!$is_create) {
            return Command::FAILURE;
        }

        // Migrate DB
        $migrate = new ArrayInput(['command' => 'doctrine:migrations:migrate']);
        // Disable interactive
        $migrate->setInteractive(false);

        $is_migrate = $this->is_run_cli(
            "Error while migrating DB...", 
            "DB migrated successfully...", 
            $output, 
            $migrate);
        
        if(!$is_migrate) {
            return Command::FAILURE;
        }

        $output->writeln('DB reset successfully...');
        $output->writeln('=============================');
        return Command::SUCCESS;
    }
}