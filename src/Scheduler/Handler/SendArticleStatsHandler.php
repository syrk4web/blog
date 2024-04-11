<?php
// src/Scheduler/Message/SendaArticleStatsHandler.php
namespace App\Scheduler\Handler;
use App\Scheduler\Message\SendArticleStats;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Article;

#[AsMessageHandler]
class SendArticleStatsHandler
{
    public function __construct(private LoggerInterface $logger, private EntityManagerInterface $entityManagerInterface) {

    }

    public function getStats(): int
    {
        // Access database to get stats
        $articles = $this->entityManagerInterface->getRepository(Article::class)->findAll();
        $total_articles = count($articles);
        // Show log
        $this->logger->info("Total articles: $total_articles");
        return $total_articles;
    }
    public function __invoke(SendArticleStats $message)
    {
        // logic when message fire 
        $this->getStats();
    }
}