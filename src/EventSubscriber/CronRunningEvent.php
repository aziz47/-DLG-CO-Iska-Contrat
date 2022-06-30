<?php

namespace App\EventSubscriber;

use Symfony\Component\Messenger\Event\WorkerRunningEvent;

class CronRunningEvent implements \Symfony\Component\EventDispatcher\EventSubscriberInterface
{
    public function onWorkerRunning(WorkerRunningEvent $event): void
    {
        if ($event->isWorkerIdle()) {
            $event->getWorker()->stop();
        }
    }

    /**
     * @return array<string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            WorkerRunningEvent::class => 'onWorkerRunning',
        ];
    }
}
