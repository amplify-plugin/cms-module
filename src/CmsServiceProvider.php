<?php

namespace Amplify\System\Cms;

use Amplify\System\Cms\Models\MegaMenu;
use Amplify\System\Cms\Models\Menu;
use Amplify\System\Cms\Models\Template;
use Amplify\System\Cms\Observers\MegaMenuObserver;
use Amplify\System\Cms\Observers\MenuObserver;
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
            __DIR__ . '/../config/cms.php',
            'amplify.cms'
        );

        $this->app->register(WidgetProvider::class);
    }

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        Menu::observe(MenuObserver::class);
        MegaMenu::observe(MegaMenuObserver::class);

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'cms');

        $this->loadViewsFrom(base_path('themes'), 'theme');

        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->loadTemplateConfiguration();
    }

    private function loadTemplateConfiguration(): void
    {

        if (Schema::hasTable('templates')) {
            //Load All Configs to Config system from DB
            Template::all()->each(function (Template $template) {
                if ($template->is_active == true) {
                    Config::set('amplify.cms.default', $template->slug);
                }
                Config::set("amplify.cms.themes.{$template->slug}", $template->toArray());
            });
        }
    }
}
