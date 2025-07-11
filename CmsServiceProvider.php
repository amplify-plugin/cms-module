<?php

namespace Amplify\System\Cms;

use Amplify\System\Cms\Models\MegaMenu;
use Amplify\System\Cms\Models\Menu;
use Amplify\System\Cms\Models\Navigation;
use Amplify\System\Cms\Models\Template;
use Amplify\System\Cms\Observers\MegaMenuObserver;
use Amplify\System\Cms\Observers\MenuObserver;
use Amplify\System\Cms\Observers\NavigationObserver;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class CmsServiceProvider extends ServiceProvider
{
    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/Config/cms.php',
            'amplify.cms'
        );

        error_log("Cms ServiceProvider is running",0, 'I:\LARAGON\www\EasyAsk\Project\amplify\storage\logs\amplify.log');

        $this->loadTemplateConfiguration();
    }

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        Navigation::observe(NavigationObserver::class);
        Menu::observe(MenuObserver::class);
        MegaMenu::observe(MegaMenuObserver::class);

        $this->loadViewsFrom(__DIR__ . '/Views', 'cms');

        $this->loadRoutesFrom(__DIR__ . '/Routes/web.php');
    }

    private function loadTemplateConfiguration(): void
    {
        $this->loadViewsFrom(base_path('templates'), 'template');

        if (Schema::hasTable('templates')) {
            //Load All Configs to Config system from DB
            Template::all()->each(function (Template $template) {
                if ($template->is_active == true) {
                    Config::set('amplify.cms.default', $template->slug);
                }
                Config::set("amplify.cms.templates.{$template->slug}", $template->toArray());
            });
        }
    }
}
