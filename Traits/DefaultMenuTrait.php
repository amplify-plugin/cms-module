<?php

namespace Amplify\Cms\Traits;

use Amplify\System\Cms\Models\Menu;
use Amplify\System\Cms\Models\MenuGroup;
use Illuminate\Support\Collection;

trait DefaultMenuTrait
{
    public ?MenuGroup $menuGroup;

    private string $defaultCss = '';

    public function setMenuGroup($short_code = null): void
    {
        $this->menuGroup = MenuGroup::where(['short_code' => $short_code, 'active' => true])->first();
    }

    /**
     * @throws \JsonException
     */
    private function push(Menu $menu, &$parent): void
    {
        $item = new \stdClass;
        $item->children = collect();
        $item->is_active = false;
        $item->title = $menu->name ?? '';
        $item->url_type = $menu->url_type;
        $item->icon = $menu->icon;
        $item->url = $menu->link();
        $item->url_path = Menu::linkPath($item->url);
        $item->target = ($menu->open_new_tab) ? '_blank' : '_self';
        $item->css_style = $menu->style;
        $item->css_class = $menu->class;
        $item->type = $menu->type;
        $item->has_children = $menu->children->isNotEmpty();
        $item->seo_path = $menu->seo_path;
        $item->sub_category_depth = $menu->sub_category_depth;
        $item->display_product_count = (bool) ($menu->display_product_count ?? false);

        $menuPermissions = ! config('amplify.basic.is_permission_system_enabled') ? [] : $menu->permissions?->pluck('name')->toArray();

        if ($item->has_children) {
            $menu->children->each(function (Menu $child) use (&$item) {
                $this->push($child, $item->children);
            });
        }

        if ($menu->onlyAuth()) {
            if (customer_check()) {

                $menuPermissions = ! config('amplify.basic.is_permission_system_enabled') ? [] : $menu->permissions?->pluck('name')->toArray();

                if (count($menuPermissions) == 0) {
                    $parent->push($item);

                    return;
                }

                if (array_intersect($menuPermissions, $this->userPermissions)) {
                    $parent->push($item);

                    return;
                }
            }
        }

        if ($menu->onlyPublic()) {
            if (! customer_check()) {
                $parent->push($item);

                return;
            }
        }

        if (! $menu->onlyAuth() && ! $menu->onlyPublic()) {

            if (customer_check()) {
                if (count($menuPermissions) == 0) {
                    $parent->push($item);

                    return;
                }

                if (array_intersect($menuPermissions, $this->userPermissions)) {
                    $parent->push($item);

                    return;
                }

                return;
            }

            $parent->push($item);

            return;
        }
    }

    private function setActiveMenu(Collection &$menus): void
    {
        $menus->each(function ($menu) {
            $menu->is_active = $menu->url_type !== 'external' && (request()->is($menu->url_path) || request()->is($menu->url_path.'/*'));

            if ($menu->has_children) {
                $this->setActiveMenu($menu->children);
            }
        });
    }

    public function shouldRender(): bool
    {
        return $this->menuGroup != null;
    }

    public function htmlAttributes(): string
    {
        $this->menuGroup->class .= $this->defaultCss;

        $this->attributes = $this->attributes->class($this->menuGroup->class);

        if (! empty($this->menuGroup->style)) {
            $this->attributes = $this->attributes->style([$this->menuGroup->style]);
        }

        return parent::htmlAttributes();
    }
}
