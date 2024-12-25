<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => config('backpack.base.route_prefix', 'backpack'),
    'middleware' => array_merge(config('backpack.base.web_middleware', ['web']),
        (array) config('backpack.base.middleware_key', 'admin')),
    ['admin_password_reset_required'],
    'namespace' => 'Amplify\System\Cms\Http\Controllers',
], function () {
    Route::crud('banner-zone', 'BannerZoneCrudController');
    Route::crud('banner', 'BannerCrudController');
    Route::crud('content-category', 'ContentCategoryCrudController');
    Route::crud('content', 'ContentCrudController');
    Route::crud('footer', 'FooterCrudController');
    Route::crud('landing-page', 'LandingPageCrudController');
    Route::crud('mega-menu', 'MegaMenuCrudController');
    Route::get('mega-menu/categories', 'MegaMenuCrudController@getEACategories')->name('mega-menu.category');
    Route::crud('menu', 'MenuCrudController');
    Route::crud('menu-group', 'MenuGroupCrudController');
    Route::crud('navigation', 'NavigationCrudController');
    Route::crud('page', 'PageCrudController');
    Route::post('page/bulk-status', 'PageCrudController@bulkStatus')->name('bulk-status');
    Route::crud('script-manager', 'ScriptManagerCrudController');
    Route::crud('sitemap', 'SitemapCrudController');
    Route::crud('template', 'TemplateCrudController');
    Route::crud('form', 'FormCrudController');
    Route::crud('form-response', 'FormResponseCrudController');
});
