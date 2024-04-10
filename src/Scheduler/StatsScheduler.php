<?php
// src/Scheduler/StatsScheduler.php
namespace App\Scheduler;

use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;
use Symfony\Component\Scheduler\RecurringMessage;
use App\Scheduler\Message\SendUserStats;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Scheduler\Event\PreRunEvent;

use Psr\Log\LoggerInterface;
// Name is used when executing scheduler
// php bin/console messenger:consume scheduler_stats -vvv
// if name = 'default' => php bin/console messenger:consume scheduler_default -vvv
#[AsSchedule(name: 'stats')]
class StatsScheduler implements ScheduleProviderInterface
{
    public function __construct(private LoggerInterface $logger)
    {
    }
    public function getSchedule(): Schedule
    {
        // New schedule create a background task
        $scheduler = (new Schedule())->add(
            // Every 5 seconds, we send user stats
            RecurringMessage::every('5 seconds', new SendUserStats()),
            // We can add more tasks here
            // RecurringMessage::every(<time>, new MyMessage()),
        );
        return $scheduler;
    }

    public function onMessage(PreRunEvent $event): void
    {
    $schedule = $event->getSchedule();
    $context = $event->getMessageContext();
    $message = $event->getMessage();

    // do something with the schedule, context or message

    // and/or cancel message
    // $event->shouldCancel(true);
    }

}