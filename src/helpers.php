<?php

use Amplify\Frontend\Store\StoreDataBus;
use Amplify\System\Cms\Models\MenuGroup;
use Amplify\System\Utility\Helpers\CurrencyHelper;
use Symfony\Component\Yaml\Yaml;

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
                    return menu_content($menuGroup);
                } else {
                    return $menuGroup;
                }
            }

            return 'Menu group is not active or not found.';
        }

        return 'Please enter short code of this menu group';
    }
}

if (! function_exists('menu_content')) {
    function menu_content($menu_group)
    {
        if (! empty($menu_group)) {
            if ($menu_group->blade_location) {
                return view(component_view($menu_group->blade_location), compact('menu_group'))->render();
            }

            return view('components.menu')
                ->with('menu_group', $menu_group)
                ->render();
        } else {
            return '';
        }
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

if (!function_exists('theme')) {
    /** Return a template from configuration
     * if an index of cms config file themes array provided
     * it will return else it will return current active one (default)
     * passing words 'fallback' will return fallback template
     *
     * @param string|int|null $key
     *
     * @throw \InvalidArgumentException
     *
     * @throws Throwable
     * @see config/amplify/cms.php
     */
    function theme($key = null): object
    {
        $key = !empty($key) ? $key : config('amplify.cms.default', config('amplify.cms.fallback'));

        if ($key == 'fallback') {
            $key = config('amplify.cms.fallback');
        }

        $configuration = config("amplify.cms.themes.{$key}");

        logger("Theme: {$key}");

        throw_if(empty($configuration), "No Theme has been registered with given keyword ($key).");

        return (object)config("amplify.cms.themes.{$key}");
    }
}

if (! function_exists('component_view')) {

    function component_view($view): string
    {
        return theme_view($view, 'components');
    }
}

if (!function_exists('theme_view')) {

    /**
     * @throws Throwable
     */
    function theme_view($view, ?string $directory = null): string
    {
        $directory = ($directory != null) ? ".{$directory}" : '';

        $theme = theme();

        $paths = implode(".",
            array_filter(
                [$theme->slug, $theme->layout, $directory, $view],
                fn($f) => !empty($f))
        );

        $fullPath = trim("theme::{$paths}");

        if (!view()->exists($fullPath)) {
            throw new InvalidArgumentException("View ({$view}) doesn't exists in ( " . str_replace(['.', '::'], DIRECTORY_SEPARATOR, $fullPath) . ' ) directory.');
        }

        return $fullPath;
    }
}

if (! function_exists('theme_asset')) {

    /**
     * this function return asset full url
     * of assets in active template
     *
     * @param null $template_id
     * @throws Throwable
     */
    function theme_asset($path, $template_id = null): string
    {
        $template = theme($template_id);

        return asset("themes/{$template->slug}/{$template->asset}/{$path}");
    }
}

if (!function_exists('theme_option')) {
    /**
     * Get a theme option using dot notation.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     * @throws Throwable
     */
    function theme_option(string $key, mixed $default = null): mixed
    {
        static $options = null;

        if ($options === null) {
            $active = theme();

            if (!$active) {
                return $default;
            }

            $dbOptions = collect($active->options ?? [])
                ->filter(fn($option) => is_array($option) && isset($option['name']))
                ->mapWithKeys(fn($option) => [
                    $option['name'] => $option['value'] ?? null,
                ])
                ->all();

            $yamlOptions = [];

            $yamlPath = base_path('themes' . DIRECTORY_SEPARATOR .
                $active->slug . DIRECTORY_SEPARATOR
                . 'theme.yaml');

            if (is_file($yamlPath)) {
                $yaml = Yaml::parseFile($yamlPath);

                $yamlOptions = is_array($yaml)
                    ? ($yaml['options'] ?? [])
                    : [];
            }

            // Database values override YAML defaults.
            $options = array_replace_recursive(
                $yamlOptions,
                $dbOptions
            );
        }

        return data_get($options, $key) ?? $default;
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

if (! function_exists('mega_menu_columns')) {
    /**
     * Dynamically calculate the mega menu column length
     *
     * @return int
     */
    function mega_menu_columns($parentColumn)
    {
        $parentColumn = (int) (strpos($parentColumn, 'col-md-') !== false)
            ? str_replace('col-md-', '', $parentColumn)
            : $parentColumn;

        if ($parentColumn != null && is_numeric($parentColumn)) {
            if ($parentColumn > 6) {
                return 4;
            } elseif ($parentColumn > 3) {
                return 2;
            }
        }

        return 1;
    }
}

if (! function_exists('mega_menu_max_height')) {
    function mega_menu_max_height()
    {
        $height = config('amplify.frontend.mega_menu_max_height', '310px');
        if (is_numeric($height)) {
            return intval($height).'px';
        }

        return $height;
    }
}