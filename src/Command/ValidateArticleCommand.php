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
use App\Entity\Article;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name:"app:check-article", description:"Check if article values are valid", hidden: false)]
class ValidateArticleCommand extends Command
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
        $this->setDescription('Check article values')
            ->setHelp('This command allows you to check article values related to Article Entity...');
        // Add arguments
        // When executing, argument need to be passed from top to bottom
        $this->addArgument('title', InputArgument::REQUIRED,'The title of the article.');
        $this->addArgument('date', InputArgument::REQUIRED,'The date of the article.');
        $this->addArgument('content', InputArgument::REQUIRED,'The content of the article.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Output info to console
        $output->writeln('=============================');
        $output->writeln('Check article validity...');
        $output->writeln('title : '.$input->getArgument('title'));
        $output->writeln('date : '.$input->getArgument('date'));
        $output->writeln('content : '.$input->getArgument('content'));

        // Create user
        $article = new Article();
        $article->setTitle($input->getArgument('title'));
        $article->setDate($input->getArgument('date'));
        $article->setContent($input->getArgument('content'));

        // Add validation
        $errors = $this->validator->validate($article);
        if (count($errors) > 0) {
            $output->writeln('Invalid Article...');
            $output->writeln((string) $errors);
            $output->writeln('=============================');
            return Command::FAILURE;
        }

        $output->writeln('Valid Article...');
        $output->writeln('=============================');
        return Command::SUCCESS;
    }
}