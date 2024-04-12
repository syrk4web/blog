<?php

namespace App\Scheduler\Handler;
use App\Scheduler\Message\SendUserStats;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

#[AsMessageHandler]
class SendUserStatsHandler{
    public function __construct(
        private LoggerInterface $logger, 
        private EntityManagerInterface $entityManager
        ) {
        
    }

    public function getStats():int {
        // Retrieve all total users
        $count_user = $this->entityManager->getRepository(User::class)->count();
        $this->logger->info('Total users: '.$count_user);
        return $count_user;

    }

    // Need function that will be fire on exec
    public function __invoke(SendUserStats $message) {
        $this->getStats();
    }
}