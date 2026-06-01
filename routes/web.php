<?php

use Amplify\System\Cms\Http\Controllers\Frontend\ContentDetailController;
use Amplify\System\Cms\Http\Controllers\PageBuilderController;
use Amplify\System\Cms\Models\Content;
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
    Route::crud('page', 'PageCrudController');
    Route::post('page/bulk-status', 'PageCrudController@bulkStatus')->name('bulk-status');
    Route::crud('script', 'ScriptCrudController');
    Route::crud('sitemap', 'SitemapCrudController');
    Route::crud('theme', 'ThemeCrudController');
    Route::crud('form', 'FormCrudController');
    Route::crud('form-response', 'FormResponseCrudController');
    Route::get('page-builder', [PageBuilderController::class, 'index']);
    Route::crud('custom-style', 'CustomStyleController');
    Route::crud('robots-text', 'RobotTextController');
    Route::crud('faq', 'FaqCrudController');
    Route::crud('faq-category', 'FaqCategoryCrudController');
});

Route::name('frontend.')->middleware(['web', 'frontend'])->group(function () {
    Route::get('articles/{content:slug}', ContentDetailController::class)->name('contents.show');
});
