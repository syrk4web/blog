<?php

namespace App\Scheduler;
// Say to app this is scheduler
use Symfony\Component\Scheduler\Attribute\AsSchedule;
// Instanciate scheduler
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;
use Symfony\Component\Scheduler\RecurringMessage;
use App\Scheduler\Message\SendUserStats;

#[AsSchedule(name:"stats")]
class StatsScheduler implements ScheduleProviderInterface
{
    public function __construct()
    {
        
    }
    public function getSchedule(): Schedule
    {
        return (new Schedule())->add(
            RecurringMessage::every('5 seconds', new SendUserStats())
            // Add more RecurringMessage here
            //RecurringMessage::every('5 seconds', new SendArticleStats())
            );
    }
}