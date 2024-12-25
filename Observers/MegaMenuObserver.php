<?php

namespace Amplify\System\Cms\Observers;

use Amplify\System\Cms\Models\MegaMenu;
use Illuminate\Support\Facades\Cache;

class MegaMenuObserver
{
    /**
     * Handle the MegaMenu "created" event.
     */
    public function created(MegaMenu $megaMenu): void
    {
        $this->clearMegaMenuCache($megaMenu);
    }

    /**
     * Handle the MegaMenu "updated" event.
     */
    public function updated(MegaMenu $megaMenu): void
    {
        $this->clearMegaMenuCache($megaMenu);
    }

    /**
     * Handle the MegaMenu "deleted" event.
     */
    public function deleted(MegaMenu $megaMenu): void
    {
        $this->clearMegaMenuCache($megaMenu);
    }

    private function clearMegaMenuCache(MegaMenu $megaMenu): void
    {
        $cache_key = "site-mega-menu-{$megaMenu->menu_id}-{$megaMenu->type}-menu";
        Cache::forget($cache_key);
    }
}
