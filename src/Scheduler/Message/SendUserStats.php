<?php
// src/Scheduler/Message/SendUserStats.php
namespace App\Scheduler\Message;

class SendUserStats
{
    public function __construct() {
        // we can add some useful values here if needed to be use by handler
        // For example, we can add a user id to get stats for a specific user
    }

}