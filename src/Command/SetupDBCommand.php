<?php
namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;

// Metadata
#[AsCommand(
    name: 'app:setup-db',
    description: 'Remove current schemas and migrate again with one user.',
    hidden: false,
)]
class SetupDBCommand extends Command
{
  // Here you can add checks/conditions to construct commands
  public function __construct() {    
     parent::__construct();
  }
  // Detail about command and possible needed inputs
    protected function configure(): void {
        // Description and help
        $this
            ->setDescription('Remove current schemas and migrate again with one user.')
            ->setHelp('This command will run another built-in commands and our custom command under the hood...')
        ;
    }
   
  // Execute logic with input and output access
    protected function execute(InputInterface $input, OutputInterface $output): int {
        // Log entries
        $output->writeln('==========================');
        $output->writeln('Trying to setup DB..');

        $reset = new ArrayInput([
            // the command name is passed as first argument
            'command' => 'app:reset-db', // main
        ]);
        // Handle case command is not found
        $is_reset_ok = $this->is_run_cli_ok("Error while reseting DB", "reseting DB done...", $output, $reset);

        if(!$is_reset_ok) {
            return Command::FAILURE;
        }
        // check that is_reset is bool instance
        
        $is_user_ok = $this->is_run_cli_ok("Error while adding user", "Adding user done...", $output, new ArrayInput([
            // the command name is passed as first argument
            'command' => 'app:create-user', // main
            "username" => "admin",
            "password" => "P@ssw0rd",
        ]));

        if(!$is_user_ok) {
            return Command::FAILURE;
        }

        $output->writeln('Setup DB successfully!');
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
