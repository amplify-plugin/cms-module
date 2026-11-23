<?php

namespace Amplify\System\Cms\Jobs;

use Amplify\System\Cms\Models\Theme;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class ThemeCheckUpdateJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Number of times the job may be attempted.
     */
    public int $tries = 3;

    private Theme $theme;

    /**
     * Number of seconds before retrying.
     */
    public int $backoff = 60;

    public function __construct(public int $id)
    {
        $this->theme = Theme::findOrFail($id);
    }

    /**
     * Prevent overlapping theme updates.
     */
    public function middleware(): array
    {
        return [
            new \Illuminate\Queue\Middleware\WithoutOverlapping("theme-update-{$this->id}"),
        ];
    }

    public function handle(): void
    {

    }
}
