<?php
namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Console\Input\StringInput;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Output\BufferedOutput;

// Metadata
#[AsCommand(
    name: 'app:reset-db',
    description: 'Delete database and create a new one using environment variables',
    hidden: false,
)]
class ResetDBCommand extends Command
{
  // Here you can add checks/conditions to construct commands
  public function __construct(private ValidatorInterface $validator, private EntityManagerInterface $entityManager) {    
     parent::__construct();
  }
  // Detail about command and possible needed inputs
    protected function configure(): void {
        // Description and help
        $this
            ->setDescription('Reset database and create a new one using environment variables')
            ->setHelp('This command will run another built-in commands under the hood...')
        ;
    }
   
  // Execute logic with input and output access
    protected function execute(InputInterface $input, OutputInterface $output): int {
        // Log entries
        $output->writeln('==========================');
        $output->writeln('Trying to reset DB...');

        // Handle case command is not found
        $is_delete_ok = $this->is_run_cli_ok("Error while deleting database", "Deleting database done...", $output, new ArrayInput([
            // the command name is passed as first argument
            'command' => 'doctrine:database:drop', // main
            '--force'  => true, // flag
            // "arg" => "value"
        ]));

        if(!$is_delete_ok) {
            return Command::FAILURE;
        }

        $is_create_ok = $this->is_run_cli_ok("Error while creating database", "Creating database done...", $output, new ArrayInput([
            // the command name is passed as first argument
            'command' => 'doctrine:database:create',
        ]));

        if(!$is_create_ok) {
            return Command::FAILURE;
        }

        // doctrine:migrations:migrate is interactive, so we need to handle it differently
        $migrate =new ArrayInput([
            'command' => 'doctrine:migrations:migrate',
        ]);

        // Avoid default interactive mode
        $migrate->setInteractive(false);

        $is_table_ok = $this->is_run_cli_ok("Error while migrating table", "Migrating database done...", $output, $migrate);

        if(!$is_table_ok) {
            return Command::FAILURE;
        }

        $output->writeln('Database reset successfully!');
        $output->writeln('==========================');

        // All worked fine
        return Command::SUCCESS;
    }

    function is_run_cli_ok($err, $ok, $output, $inpArray) : bool {
        // Try handle case command is not found
        try {
            $code = $this->getApplication()->doRun($inpArray, $output);
            // Case command found, check if it worked
            if($code == 0) {
                $output->writeln($ok);
                return true;
            } else {
                $output->writeln($err);
                $output->writeln('==========================');
                return false;   
            }
        }catch(\Exception $e) {
            // Show custom message and traceback for debugging
            $output->writeln($err);
            $output->writeln($e);
            $output->writeln('==========================');
            return false;
        }
      

    }
}
