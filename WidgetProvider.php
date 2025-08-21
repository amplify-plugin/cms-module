<?php

namespace Amplify\System\Cms;

use Amplify\System\Sayt\Commands\ReconfigureSaytSearchCommand;
use Amplify\System\Sayt\Controllers\SearchProductController;
use Amplify\System\Sayt\Widgets\ShopAttributeFilter;
use Amplify\System\Sayt\Widgets\ShopCategories;
use Amplify\System\Sayt\Widgets\ShopCurrentFilter;
use Amplify\System\Sayt\Widgets\ShopEmptyResult;
use Amplify\System\Sayt\Widgets\ShopInStockFilter;
use Amplify\System\Sayt\Widgets\SiteSearch;
use Amplify\System\Sayt\Widgets\ShopPagination;
use Amplify\System\Sayt\Widgets\ShopSearchInResult;
use Amplify\System\Sayt\Widgets\ShopSidebar;
use Amplify\System\Sayt\Widgets\ShopToolbar;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;

class WidgetProvider extends ServiceProvider
{

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $widgets = [
            \Amplify\System\Cms\Widgets\Content::class => [
                'name' => 'content',
                'reserved' => true,
                'internal' => false,
                '@inside' => null,
                '@client' => null,
                'model' => ['static_page'],
                '@attributes' => [
                    [
                        'name' => 'content-id',
                        'type' => 'content-dropdown',
                        'value' => '',
                    ],
                    [
                        'name' => 'show-title',
                        'type' => 'boolean',
                        'value' => true,
                    ],
                ],
                '@nestedItems' => [],
                'description' => '',
            ],
            \Amplify\System\Cms\Widgets\TopBar::class => [
                'name' => 'site.top-bar',
                'reserved' => true,
                'internal' => false,
                '@inside' => null,
                '@client' => null,
                'model' => ['static_page'],
                '@attributes' => [],
                '@nestedItems' => [],
                'description' => 'Login widget',
            ],
            \Amplify\System\Cms\Widgets\ScriptManager::class => [
                'name' => 'site.script-manager',
                'reserved' => true,
                'internal' => true,
                '@inside' => null,
                '@client' => null,
                'model' => ['static_page'],
                '@attributes' => [],
                '@nestedItems' => [],
                'description' => 'Login widget',
            ],
            \Amplify\System\Cms\Widgets\MetaTags::class => [
                'name' => 'site.meta-tags',
                'reserved' => true,
                'internal' => true,
                '@inside' => null,
                '@client' => null,
                'model' => ['static_page'],
                '@attributes' => [],
                '@nestedItems' => [],
                'description' => 'Login widget',
            ],
            \Amplify\System\Cms\Widgets\Menu\MobileMenu::class => [
                'name' => 'menu.mobile-menu',
                'reserved' => true,
                'internal' => true,
                '@inside' => null,
                '@client' => null,
                'model' => ['static_page'],
                '@attributes' => [
                    ['name' => ':show-icon', 'value' => true, 'type' => 'boolean'],
                ],
                '@nestedItems' => [],
                'description' => 'Login widget',
            ],
            \Amplify\System\Cms\Widgets\Menu\AccountSidebar::class => [
                'name' => 'account-sidebar',
                'reserved' => false,
                'internal' => false,
                '@inside' => null,
                '@client' => null,
                'model' => ['static_page'],
                '@attributes' => [
                    ['name' => ':show-icon', 'value' => true, 'type' => 'boolean'],
                ],
                '@nestedItems' => [],
                'description' => 'Customer panel account sidebar',
            ],
            \Amplify\System\Cms\Widgets\Menu\AccountMenu::class => [
                'name' => 'menu.account-menu',
                'reserved' => false,
                'internal' => true,
                '@inside' => null,
                '@client' => null,
                'model' => ['static_page'],
                '@attributes' => [
                    ['name' => ':show-icon', 'value' => true, 'type' => 'boolean'],
                ],
                '@nestedItems' => [],
                'description' => 'Login widget',
            ],
            \Amplify\System\Cms\Widgets\Menu\PrimaryMenu::class => [
                'name' => 'menu.primary-menu',
                'reserved' => true,
                'internal' => true,
                '@inside' => null,
                '@client' => null,
                'model' => ['static_page'],
                '@attributes' => [
                    ['name' => ':show-icon', 'value' => true, 'type' => 'boolean'],
                ],
                '@nestedItems' => [],
                'description' => 'Login widget',
            ],
            \Amplify\System\Cms\Widgets\Menu\SecondaryMenu::class => [
                'name' => 'menu.secondary-menu',
                'reserved' => true,
                'internal' => true,
                '@inside' => null,
                '@client' => null,
                'model' => ['static_page'],
                '@attributes' => [
                    ['name' => ':show-icon', 'value' => true, 'type' => 'boolean'],
                ],
                '@nestedItems' => [],
                'description' => 'Login widget',
            ],
            \Amplify\System\Cms\Widgets\Menu\CustomMenu::class => [
                'name' => 'custom-menu',
                'reserved' => true,
                'internal' => false,
                '@inside' => null,
                '@client' => null,
                'model' => ['static_page'],
                '@attributes' => [
                    [
                        'name' => 'group',
                        'type' => 'menugroup-dropdown',
                        'value' => '',
                    ],
                    [
                        'name' => 'is-show-title',
                        'type' => 'boolean',
                        'value' => false,
                    ],
                    [
                        'name' => 'title',
                        'type' => 'text',
                        'value' => '',
                    ],
                    ['name' => ':show-icon', 'value' => true, 'type' => 'boolean'],
                ],
                '@nestedItems' => [],
                'description' => 'Display custom menus of any group given',
            ],
            \Amplify\System\Cms\Widgets\Menu\FooterMenu::class => [
                'name' => 'menu.footer-menu',
                'reserved' => true,
                'internal' => true,
                '@inside' => null,
                '@client' => null,
                'model' => ['static_page'],
                '@attributes' => [
                    ['name' => ':show-icon', 'value' => true, 'type' => 'boolean'],
                ],
                '@nestedItems' => [],
                'description' => 'Login widget',
            ],
            \Amplify\System\Cms\Widgets\Menu\CartMenu::class => [
                'name' => 'menu.cart-menu',
                'reserved' => true,
                'internal' => true,
                '@inside' => null,
                '@client' => null,
                'model' => ['static_page'],
                '@attributes' => [],
                '@nestedItems' => [],
                'description' => 'Login widget',
            ],
            \Amplify\System\Cms\Widgets\MegaMenu\DefaultMenu::class => [
                'name' => 'mega-menu.default-menu',
                'reserved' => true,
                'internal' => true,
                '@inside' => null,
                '@client' => null,
                'model' => ['static_page'],
                '@attributes' => [],
                '@nestedItems' => [],
                'description' => 'Login widget',
            ],
            \Amplify\System\Cms\Widgets\MegaMenu\CategoryMenu::class => [
                'name' => 'mega-menu.category-menu',
                'reserved' => true,
                'internal' => true,
                '@inside' => null,
                '@client' => null,
                'model' => ['static_page'],
                '@attributes' => [],
                '@nestedItems' => [],
                'description' => 'Login widget',
            ],
            \Amplify\System\Cms\Widgets\MegaMenu\SubCategoryMenu::class => [
                'name' => 'mega-menu.sub-category-menu',
                'reserved' => true,
                'internal' => true,
                '@inside' => null,
                '@client' => null,
                'model' => ['static_page'],
                '@attributes' => [],
                '@nestedItems' => [],
                'description' => 'Login widget',
            ],
            \Amplify\System\Cms\Widgets\MegaMenu\MerchandiseZoneMenu::class => [
                'name' => 'mega-menu.merchandise-zone-menu',
                'reserved' => true,
                'internal' => true,
                '@inside' => null,
                '@client' => null,
                'model' => ['static_page'],
                '@attributes' => [],
                '@nestedItems' => [],
                'description' => 'Login widget',
            ],
            \Amplify\System\Cms\Widgets\MegaMenu\ProductMenu::class => [
                'name' => 'mega-menu.product-menu',
                'reserved' => true,
                'internal' => true,
                '@inside' => null,
                '@client' => null,
                'model' => ['static_page'],
                '@attributes' => [],
                '@nestedItems' => [],
                'description' => 'Login widget',
            ],
            \Amplify\System\Cms\Widgets\MegaMenu\HtmlMenu::class => [
                'name' => 'mega-menu.html-menu',
                'reserved' => true,
                'internal' => true,
                '@inside' => null,
                '@client' => null,
                'model' => ['static_page'],
                '@attributes' => [],
                '@nestedItems' => [],
                'description' => 'Login widget',
            ],
            \Amplify\System\Cms\Widgets\MegaMenu\ImageMenu::class => [
                'name' => 'mega-menu.image-menu',
                'reserved' => true,
                'internal' => true,
                'model' => ['static_page'],
                '@inside' => null,
                '@client' => null,
                '@attributes' => [],
                '@nestedItems' => [],
                'description' => 'Mega menu image menu widget',
            ],
            \Amplify\System\Cms\Widgets\MegaMenu\VideoMenu::class => [
                'name' => 'mega-menu.video-menu',
                'reserved' => true,
                'internal' => true,
                'model' => ['static_page'],
                '@inside' => null,
                '@client' => null,
                '@attributes' => [],
                '@nestedItems' => [],
                'description' => 'Mega menu video menu widget',
            ],
            \Amplify\System\Cms\Widgets\MegaMenu\ManufacturerMenu::class => [
                'name' => 'mega-menu.manufacturer-menu',
                'reserved' => true,
                'internal' => true,
                'model' => ['static_page'],
                '@inside' => null,
                '@client' => null,
                '@attributes' => [],
                '@nestedItems' => [],
                'description' => 'Mega menu manufacturer menu widget',
            ],
            \Amplify\System\Cms\Widgets\Menu\AccountMenu\Profile::class => [
                'name' => 'menu.account-menu.profile',
                'reserved' => true,
                'internal' => true,
                '@inside' => null,
                '@client' => null,
                'model' => ['static_page'],
                '@attributes' => [],
                '@nestedItems' => [],
                'description' => 'Login widget',
            ],
            \Amplify\System\Cms\Widgets\Menu\AccountMenu\Logout::class => [
                'name' => 'menu.account-menu.logout',
                'reserved' => true,
                'internal' => true,
                '@inside' => null,
                '@client' => null,
                'model' => ['static_page'],
                '@attributes' => [],
                '@nestedItems' => [],
                'description' => 'Login widget',
            ],
            \Amplify\System\Cms\Widgets\Menu\AccountSidebar\Profile::class => [
                'name' => 'menu.account-sidebar.profile',
                'reserved' => true,
                'internal' => true,
                '@inside' => null,
                '@client' => null,
                'model' => ['static_page'],
                '@attributes' => [],
                '@nestedItems' => [],
                'description' => 'Login widget',
            ],
            \Amplify\System\Cms\Widgets\Menu\ExchangeRewardMenu::class => [
                'name' => 'menu.exchange-reward-menu',
                'reserved' => true,
                'internal' => true,
                '@inside' => null,
                '@client' => null,
                'model' => ['static_page'],
                '@attributes' => [],
                '@nestedItems' => [],
                'description' => 'Login widget',
            ],
            \Amplify\System\Cms\Widgets\CustomForm::class => [
                'name' => 'custom-form',
                'reserved' => true,
                'internal' => false,
                '@inside' => null,
                '@client' => null,
                'model' => ['static_page'],
                '@attributes' => [
                    [
                        'name' => 'action-url',
                        'type' => 'text',
                        'value' => '#',
                    ],
                    [
                        'name' => 'allow-reset',
                        'type' => 'boolean',
                        'value' => false,
                    ],
                    [
                        'name' => 'allow-captcha',
                        'type' => 'boolean',
                        'value' => false,
                    ],
                    [
                        'name' => 'submit-button-title',
                        'type' => 'text',
                        'value' => 'Submit',
                    ],
                    [
                        'name' => 'clear-button-title',
                        'type' => 'text',
                        'value' => 'Reset',
                    ],
                    [
                        'name' => 'fields',
                        'type' => 'array',
                        'array_format' => [
                            [
                                'name' => 'type',
                                'type' => 'select',
                                'default' => 'rText',
                                'value' => 'rText',
                                'options' => [
                                    [
                                        'name' => 'Text',
                                        'value' => 'rText',
                                    ],
                                    [
                                        'name' => 'Email',
                                        'value' => 'rEmail',
                                    ],
                                    [
                                        'name' => 'Telephone',
                                        'value' => 'rTel',
                                    ],
                                    [
                                        'name' => 'URL',
                                        'value' => 'rUrl',
                                    ],
                                    [
                                        'name' => 'Search',
                                        'value' => 'rSearch',
                                    ],
                                    [
                                        'name' => 'Password',
                                        'value' => 'rPassword',
                                    ],
                                    [
                                        'name' => 'Number',
                                        'value' => 'rNumber',
                                    ],
                                    [
                                        'name' => 'Textarea',
                                        'value' => 'rTextarea',
                                    ],
                                    [
                                        'name' => 'Date',
                                        'value' => 'rDate',
                                    ],
                                ],
                            ],
                            [
                                'name' => 'label',
                                'type' => 'text',
                            ],
                            [
                                'name' => 'name',
                                'type' => 'text',
                            ],
                            [
                                'name' => 'required',
                                'type' => 'checkbox',
                            ],
                            [
                                'name' => 'inline',
                                'type' => 'checkbox',
                            ],
                            [
                                'name' => 'default',
                                'type' => 'text',
                            ],
                        ],
                        'value' => [],
                    ],
                ],
                '@nestedItems' => [],
                'description' => 'Allow user to create a dynamic form with basic options.',
            ],
            \Amplify\System\Cms\Widgets\Form::class => [
                'name' => 'form',
                'reserved' => true,
                'internal' => false,
                'model' => ['static_page'],
                '@inside' => null,
                '@client' => null,
                '@attributes' => [
                    [
                        'name' => 'code',
                        'type' => 'form-dropdown',
                        'value' => null,
                    ],
                ],
                '@nestedItems' => [],
                'description' => 'Form widget',
            ],
            \Amplify\System\Cms\Widgets\Banner::class => [
                'name' => 'banner',
                'reserved' => true,
                'internal' => false,
                'model' => ['static_page'],
                '@inside' => null,
                '@client' => null,
                '@attributes' => [
                    [
                        'name' => 'background-image',
                        'type' => 'image',
                        'value' => '',
                    ],
                    [
                        'name' => 'banner-content',
                        'type' => 'array',
                        'array_format' => [
                            [
                                'name' => 'title',
                                'type' => 'text',
                            ],
                            [
                                'name' => 'title-color',
                                'type' => 'select',
                                'options' => [
                                    ['primary' => 'primary'],
                                    ['secondary' => 'secondary'],
                                    ['success' => 'success'],
                                    ['danger' => 'danger'],
                                    ['warning' => 'warning'],
                                    ['info' => 'info'],
                                    ['light' => 'light'],
                                    ['dark' => 'dark'],
                                    ['muted' => 'muted'],
                                    ['white' => 'white'],
                                ],
                                'value' => 'primary',
                            ],
                            [
                                'name' => 'suffix',
                                'type' => 'text',
                            ],
                            [
                                'name' => 'suffix-color',
                                'type' => 'select',
                                'options' => [
                                    ['primary' => 'primary'],
                                    ['secondary' => 'secondary'],
                                    ['success' => 'success'],
                                    ['danger' => 'danger'],
                                    ['warning' => 'warning'],
                                    ['info' => 'info'],
                                    ['light' => 'light'],
                                    ['dark' => 'dark'],
                                    ['muted' => 'muted'],
                                    ['white' => 'white'],
                                ],
                                'value' => 'warning',
                            ],
                        ],
                        'value' => [],
                    ],
                    [
                        'name' => 'height',
                        'type' => 'text',
                        'value' => '',
                    ],
                    [
                        'name' => 'banner-class',
                        'type' => 'text',
                        'value' => '',
                    ],
                ],
                '@nestedItems' => [],
                'description' => 'Banner widget',
            ],
            \Amplify\System\Cms\Widgets\Banner\BannerSlider::class => [
                'name' => 'banner-slider',
                'reserved' => true,
                'internal' => false,
                '@inside' => null,
                '@client' => null,
                'model' => ['static_page'],
                '@attributes' => [
                    ['name' => 'banner-zone', 'type' => 'banner-zone', 'value' => true],
                    ['name' => 'nav', 'type' => 'boolean', 'value' => true],
                    ['name' => 'dots', 'type' => 'boolean', 'value' => true],
                    ['name' => 'loop', 'type' => 'boolean', 'value' => false],
                    ['name' => 'autoplay', 'type' => 'boolean', 'value' => false],
                    ['name' => 'pause-on-hover', 'type' => 'boolean', 'value' => false],
                    ['name' => 'show-on-mobile', 'type' => 'boolean', 'value' => true],
                    ['name' => 'full-width', 'type' => 'boolean', 'value' => true],
                    ['name' => 'height', 'type' => 'text', 'value' => '200px'],
                    ['name' => 'background-image', 'type' => 'text', 'value' => ''],
                    ['name' => 'background-img-class', 'type' => 'text', 'value' => ''],
                    ['name' => 'autoplay-timeout', 'type' => 'number', 'value' => '5000'],
                ],
                '@nestedItems' => null,
                'description' => '',
            ],
            \Amplify\System\Cms\Widgets\Banner\BannerItem::class => [
                'name' => 'banner-item',
                'reserved' => true,
                'internal' => false,
                '@inside' => null,
                '@client' => null,
                'model' => ['static_page'],
                '@attributes' => [
                    ['name' => 'banner-zone', 'type' => 'banner-zone', 'value' => true],
                    ['name' => 'nav', 'type' => 'boolean', 'value' => true],
                    ['name' => 'dots', 'type' => 'boolean', 'value' => true],
                    ['name' => 'loop', 'type' => 'boolean', 'value' => false],
                    ['name' => 'autoplay', 'type' => 'boolean', 'value' => false],
                    ['name' => 'pause-on-hover', 'type' => 'boolean', 'value' => false],
                    ['name' => 'show-on-mobile', 'type' => 'boolean', 'value' => true],
                    ['name' => 'full-width', 'type' => 'boolean', 'value' => true],
                    ['name' => 'height', 'type' => 'text', 'value' => '200px'],
                    ['name' => 'background-image', 'type' => 'text', 'value' => ''],
                    ['name' => 'background-img-class', 'type' => 'text', 'value' => ''],
                    ['name' => 'autoplay-timeout', 'type' => 'number', 'value' => '5000'],
                ],
                '@nestedItems' => null,
                'description' => '',
            ],
        ];

        foreach ($widgets as $namespace => $options) {
            Config::set("amplify.widget.{$namespace}", $options);
        }
    }

}
