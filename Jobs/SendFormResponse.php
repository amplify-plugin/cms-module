<?php

namespace Amplify\System\Cms\Jobs;

use App\Traits\NotificationEventTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendFormResponse implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, NotificationEventTrait, Queueable, SerializesModels;

    public array $values;

    /**
     * Create a new job instance.
     */
    public function __construct(string $event_code, array $values)
    {
        $this->eventCode = $event_code;

        $this->values = $values;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->getNecessaryItems();
        foreach ($this->eventInfo->eventActions ?? [] as $eventAction) {
            if ($eventAction->eventTemplate->notification_type == 'emailable') {
                $this->emailService->sendFormResponseToTargets($eventAction, $this->values);
            }
        }
    }
}
