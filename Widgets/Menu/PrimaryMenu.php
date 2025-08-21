<?php

namespace Amplify\System\Cms\Widgets\Menu;

use Amplify\System\Cms\Models\Menu;
use Amplify\Widget\Abstracts\BaseComponent;
use Amplify\Widget\Traits\DefaultMenuTrait;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

/**
 * @class PrimaryMenu
 */
class PrimaryMenu extends BaseComponent
{
    use DefaultMenuTrait;

    public Collection $menus;

    private ?string $menuCode;

    public array $userPermissions;

    /**
     * Create a new component instance.
     */
    public function __construct(public bool $showIcon = false)
    {
        parent::__construct();

        $this->menus = collect();

        $this->userPermissions = [];

        $this->menuCode = config('amplify.frontend.site_primary_menu', 'primary-menu');

        $this->setMenuGroup($this->menuCode);

        $this->defaultCss = 'site-menu';
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $this->menus = Cache::remember(Session::token().'-primary-menu', 0, function () {

            $this->userPermissions = customer_permissions();

            $menus = collect();

            Menu::query()
                ->select('menus.*')
                ->whereNull('menus.parent_id')
                ->join('menu_groups', function (JoinClause $join) {
                    return $join->on('menus.group_id', '=', 'menu_groups.id')
                        ->where('menu_groups.short_code', $this->menuCode);
                })
                ->whereNull('menus.parent_id')
                ->where('menus.enabled', true)
                ->orderBy('menus.lft')
                ->with([
                    'permissions', 'children',
                    'megaMenus' => function ($megaMenu) {
                        return $megaMenu->whereEnabled(true);
                    },
                ])
                ->get()->each(function (Menu $menu) use ($menus) {
                    if ($menu->type == 'default') {
                        $this->push($menu, $menus);
                    } else {
                        $this->pushMegaMenu($menu, $menus);
                    }
                });

            return $menus;
        });

        $this->setActiveMenu($this->menus);

        return view('widget::menu.primary-menu', [
            'contact' => customer(true),
        ]);
    }

    private function pushMegaMenu(Menu $menu, &$parent): void
    {

        $item = new \stdClass;
        $item->title = $menu->name ?? '';
        $item->url = $menu->link();
        $item->url_path = Menu::linkPath($item->url);
        $item->url_type = $menu->url_type;
        $item->target = ($menu->open_new_tab) ? '_blank' : '_self';
        $item->css_style = $menu->style;
        $item->css_class = $menu->class;
        $item->type = $menu->type;
        $item->is_active = false;
        $item->has_children = $menu->megaMenus->isNotEmpty();
        $item->children = collect();

        $menuPermissions = ! config('amplify.basic.is_permission_system_enabled') ? [] : $menu->permissions?->pluck('name')->toArray();

        if ($item->has_children) {
            $item->children = $menu->megaMenus;
        }

        if ($menu->onlyAuth()) {
            if (customer_check()) {

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
}
