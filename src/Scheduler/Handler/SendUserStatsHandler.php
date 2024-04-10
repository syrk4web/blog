<?php
// src/Scheduler/Message/SendUserStatsHandler.php
namespace App\Scheduler\Handler;
use App\Scheduler\Message\SendUserStats;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

#[AsMessageHandler]
class SendUserStatsHandler
{
    public function __construct(private LoggerInterface $logger, private EntityManagerInterface $entityManagerInterface) {

    }

    public function getStats(): int
    {
        // Access database to get stats
        $users = $this->entityManagerInterface->getRepository(User::class)->findAll();
        $total_users = count($users);
        // Show log
        $this->logger->info("Total users: $total_users");
        return $total_users;
    }
    public function __invoke(SendUserStats $message)
    {
        // logic when message fire 
        $this->getStats();
    }
}