<?php
namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
// Metadata
#[AsCommand(
    name: 'app:check-article',
    description: 'Check if article values are valid',
    hidden: false,
)]
class CheckArticleCommand extends Command
{
  // Here you can add checks/conditions to construct commands
  public function __construct(private ValidatorInterface $validator, private EntityManagerInterface $entityManager) {    
     parent::__construct();
  }
  // Detail about command and possible needed inputs
    protected function configure(): void {
        // Description and help
        $this
            ->setDescription('Check if article values are valid')
            ->setHelp('Enter values related to Article entity ...')
        ;
        // Add arguments
        $this
            ->addArgument('title', InputArgument::REQUIRED, 'The title.')
            ->addArgument('date', InputArgument::REQUIRED, 'The date.')
            ->addArgument('content', InputArgument::REQUIRED, 'The content.')
            ;
    }

  // Execute logic with input and output access
    protected function execute(InputInterface $input, OutputInterface $output): int {
        // Log entries
        $output->writeln('==========================');
        $output->writeln('Checking values with Article...');
        $output->writeln('Title: ' . $input->getArgument('title'));
        $output->writeln('Date: ' . $input->getArgument('date'));
        $output->writeln('Content: ' . $input->getArgument('content'));

        // Create user with arguments
        $article = new Article();
        $article->setTitle($input->getArgument('title'));
        $article->setDate($input->getArgument('date'));
        $article->setContent($input->getArgument('content'));

        // Check if valid after setting values and before hash
        $errors = $this->validator->validate($article);
        if (count($errors) > 0) {
            $output->writeln('Validation failed');
            $output->writeln($errors);
            $output->writeln('==========================');
            return Command::FAILURE;
        }

        // Store on DB
        $this->entityManager->persist($article);
        $this->entityManager->flush();
        $output->writeln('This article is valid');
        $output->writeln('==========================');

        // All worked fine
        return Command::SUCCESS;
    }
}