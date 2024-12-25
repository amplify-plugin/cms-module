<?php

namespace Amplify\System\Cms\Observers;

use Amplify\System\Cms\Models\Menu;
use Illuminate\Support\Facades\Artisan;

class MenuObserver
{
    /**
     * Handle the Menu "created" event.
     */
    public function created(Menu $menu): void
    {
        $this->clearMenuCache($menu);
    }

    /**
     * Handle the Menu "updated" event.
     */
    public function updated(Menu $menu): void
    {
        $this->clearMenuCache($menu);
    }

    /**
     * Handle the Menu "deleted" event.
     */
    public function deleted(Menu $menu): void
    {
        $this->clearMenuCache($menu);
    }

    private function clearMenuCache(Menu $menu): void
    {
        Artisan::call('cache:clear');
    }
}
