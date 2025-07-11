<?php

use Amplify\System\Cms\Models\MenuGroup;
use Amplify\Frontend\Store\StoreDataBus;
use Amplify\System\Utility\Helpers\CurrencyHelper;

if (! function_exists('currency')) {

    function currency(?string $code = null): object
    {
        return CurrencyHelper::config($code);
    }
}

if (! function_exists('currency_format')) {
    /**
     * @param  null  $value
     */
    function currency_format($value = null, ?string $code = null, bool $withSymbol = false): string
    {
        return CurrencyHelper::format($value, $code, $withSymbol);
    }
}

if (! function_exists('menu')) {
    /**
     * @return MenuGroup|string
     */
    function menu(?string $shortCode = null, bool $isReturnHtml = true)
    {
        if ($shortCode != null) {
            $menuGroup = MenuGroup::where('short_code', $shortCode)
                ->isActive()
                ->with([
                    'menus' => function ($menu) {
                        return $menu
                            ->select('menus.*', 'pages.slug as page_slug')
                            ->whereNull('menus.parent_id')
                            ->leftJoin('pages', 'pages.id', 'menus.page_id')
                            ->orderBy('menus.lft')
                            ->with([
                                'children' => function ($child) {
                                    return $child->select('menus.*', 'pages.slug as page_slug')
                                        ->join('pages', 'pages.id', 'menus.page_id');
                                },
                                'megaMenus',
                            ]);
                    },
                ])
                ->first();
            if (! empty($menuGroup)) {
                if ($isReturnHtml) {
                    return menuContent($menuGroup);
                } else {
                    return $menuGroup;
                }
            }

            return 'Menu group is not active or not found.';
        }

        return 'Please enter short code of this menu group';
    }
}

if (! function_exists('store')) {
    /**
     * @param  null  $default
     * @return mixed|StoreDataBus
     *
     * @throws ErrorException
     */
    function store(?string $key = null, $default = null): mixed
    {
        $store = StoreDataBus::init();

        if ($key) {

            if (! in_array($key, array_keys($store->map_setters), true)) {

                if ($default != null) {
                    return $default;
                }

                throw new ErrorException("{$key} does not exists in \\`" . StoreDataBus::class." class`");
            }

            return $store->{$key};

        } else {
            return $store;
        }
    }
}

if (! function_exists('template')) {
    /** Return a template from configuration
     * if a index of cms config file templates array provided
     * it will return else it will return current active one (default)
     * passing words 'fallback' will return fallback template
     *
     * @param  string|int|null  $index
     *
     * @throw \InvalidArgumentException
     *
     * @see config/amplify/cms.php
     */
    function template($index = null): object
    {
        $index = ($index == null) ? config('amplify.cms.default') : $index;

        $fallback = config('amplify.cms.fallback');

        if (is_numeric($index)) {
            foreach (config('amplify.cms.templates') as $key => $template) {
                if ($template['id'] == $index) {
                    return (object) config("amplify.cms.templates.{$key}", config("amplify.cms.templates.{$fallback}"));
                }
            }

            throw new InvalidArgumentException("Invalid Template ID=`{$index}` provided.");
        }

        if ($index == 'fallback') {
            return (object) config("amplify.cms.templates.{$fallback}");
        }

        return (object) config("amplify.cms.templates.{$index}", config("amplify.cms.templates.{$fallback}"));
    }
}

if (! function_exists('component_view')) {

    function component_view($view): string
    {
        return template_view($view, 'components');
    }
}

if (! function_exists('template_view')) {

    function template_view($view, ?string $directory = null): string
    {
        $directory = ($directory != null) ? ".{$directory}" : '';

        $target_view = trim('template::'.template()->component_folder."{$directory}.{$view}", '.');
        $fallback_view = trim('template::'.template('fallback')->component_folder."{$directory}.{$view}", '.');

        if (view()->exists($target_view)) {
            return $target_view;
        } elseif (view()->exists($fallback_view)) {
            return $fallback_view;
        } else {
            throw new InvalidArgumentException("View ({$view}) doesn't exists in ( ".str_replace('.', '/', $target_view).' ) or ( '.str_replace('.', '/', $fallback_view).' ) directory.');
        }
    }
}

if (! function_exists('template_asset')) {

    /**
     * this function return asset full url
     * of assets in active template
     *
     * @param  null  $template_id
     */
    function template_asset($path, $template_id = null): string
    {
        $template_root_path = template($template_id)->asset_folder;

        return "frontend/{$template_root_path}/{$path}";
    }
}

if (! function_exists('template_option')) {

    function template_option($key, ?string $template = null, $default = null): string
    {
        $template = template($template);

        $templateOptions = collect($template->options);

        $config = @json_decode(
            @file_get_contents(
                @base_path("templates/{$template->slug}/config.json")
            ),
            true
        );

        if ($config == null) {
            $config = [];
        }

        $configOptions = $config['options'] ?? [];

        foreach ($configOptions as $configOptionKey => $configOption) {
            if (!empty($templateOptions->firstWhere('name', '=', $configOption['name']))) {
                unset($configOptions[$configOptionKey]);
            }
        }

        $option = $templateOptions
            ->merge($configOptions)
            ->firstWhere('name', '=', $key);

        if ($option == null && $default == null) {
            throw new InvalidArgumentException("Invalid key [{$key}] or doesn't exists for template {$template->name}");
        }

        return $option['value'] ?? $default;
    }
}

if (! function_exists('get_uri_parameter')) {
    function get_uri_parameter(string $uri)
    {
        $output_array = [];

        preg_match_all('/(\{[a-z0-9_\-\?]+})/iu', $uri, $output_array);

        return (count($output_array) > 1) ? $output_array[1] : [];
    }
}
