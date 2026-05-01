<?php

namespace Amplify\System\Cms\Traits;

trait HasBackendMenu
{
    public function registerBackendMenu(): void
    {
        $sidebar = app('sidebar');

        $sidebar->group('CMS')
            ->icon('la la-wordpress')
            ->canAny('template.list', 'menu.list', 'menu-item.list', 'content.list',
                'content-category.list', 'page.list', 'banner-item.list', 'banner-zone.list',
                'footer.list', 'faq-category.list', 'faq.list', 'script.list', 'robots-text.list',
                'custom-style.list', 'sitemap.list', 'localization.list')
            ->items(function ($cms) {
                $cms->item('Themes')
                    ->icon('la la-image')
                    ->can('theme.list')
                    ->url(backpack_url('theme'));

                $cms->item('Menus')
                    ->icon('la la-list')
                    ->can('menu-group.list')
                    ->url(backpack_url('menu-group'));

                $cms->group('Content Manager')
                    ->icon('la la-pencil')
                    ->canAny('content.list', 'content-category.list')
                    ->items(function ($content) {
                        $content->item('Content Categories')
                            ->icon('la la-icons')
                            ->can('content-category.list')
                            ->url(backpack_url('content-category'));

                        $content->item('Contents')
                            ->icon('las la-edit')
                            ->can('content.list')
                            ->url(backpack_url('content'));
                    });

                $cms->item('Pages')
                    ->icon('las la-book')
                    ->can('page.list')
                    ->url(backpack_url('page'));

                $cms->item('Custom Styles')
                    ->icon('la la-css3')
                    ->can('custom-style.list')
                    ->url(backpack_url('custom-style'));

                $cms->group('Banners')
                    ->icon('las la-file-powerpoint')
                    ->canAny('banner-item.list', 'banner-zone.list')
                    ->items(function ($banners) {
                        $banners->item('Banner Zone')
                            ->icon('las la-file-image')
                            ->can('banner-zone.list')
                            ->url(backpack_url('banner-zone'));

                        $banners->item('Banner Item')
                            ->icon('las la-images')
                            ->can('banner-item.list')
                            ->url(backpack_url('banner'));

                    });

                $cms->item('Footers')
                    ->icon('las la-sort-down')
                    ->can('footer.list')
                    ->url(backpack_url('footer'));

                $cms->item('FAQ categories')
                    ->icon('las la-comments')
                    ->can('faq-category.list')
                    ->url(backpack_url('faq-category'));

                $cms->item('FAQs')
                    ->icon('lar la-question-circle')
                    ->can('faq.list')
                    ->url(backpack_url('faq'));

                $cms->item('Scripts')
                    ->icon('las la-scroll')
                    ->can('script.list')
                    ->url(backpack_url('script'));

//                $cms->item('Google Analytics')
//                    ->icon('las la-chart-bar')
//                    ->can('script-manager.list')
//                    ->url(backpack_url('google-analytic'));

                $cms->item('Robots Text')
                    ->icon('la la-robot')
                    ->can('robots-text.list')
                    ->url(backpack_url('robots-text'));

                $cms->item('Sitemap')
                    ->icon('la la-sitemap')
                    ->can('sitemap.list')
                    ->url(backpack_url('sitemap'));

                $cms->item('Localization')
                    ->icon('las la-globe')
                    ->can('localization.list')
                    ->url(backpack_url('localization'));
            });

    }
}