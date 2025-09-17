<?php

namespace Amplify\System\Cms\Widgets\Menu;

use Amplify\System\Cms\Models\Menu;
use Amplify\System\Cms\Models\MenuGroup;
use Amplify\System\Helpers\UtilityHelper;
use Amplify\Widget\Abstracts\BaseComponent;
use Amplify\Cms\Traits\DefaultMenuTrait;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

/**
 * @class CustomMenu
 */
class CustomMenu extends BaseComponent
{
    use DefaultMenuTrait;

    /**
     * @var array
     */
    public $options;

    public Collection $menus;

    private string $menuCode;

    private string $group_title;

    public bool $is_show_title;

    private array $userPermissions;

    /**
     * Create a new component instance.
     */
    public function __construct(string $group = '', string $isShowTitle = 'false', string $title = '', public bool $showIcon = false)
    {
        parent::__construct();

        $this->menus = collect();

        $this->menuCode = $group;

        $this->is_show_title = UtilityHelper::typeCast($isShowTitle, 'bool');

        $this->group_title = (strlen(trim($title)) != 0) ? $title : '';

        $this->userPermissions = [];

        $this->setMenuGroup($this->menuCode);

        $this->defaultCss = 'widget widget-links widget-light-skin';

    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $menuGroup = MenuGroup::whereShortCode($this->menuCode)->whereActive(true)->first();
        $this->group_title = $menuGroup?->name ?? $this->group_title;

        $this->menus = Cache::remember(Session::token().'-custom-menu-'.$this->menuCode, HOUR, function () {

            $this->userPermissions = customer_permissions();

            $menus = collect();

            Menu::query()
                ->select('menus.*')
                ->join('menu_groups', function (JoinClause $join) {
                    return $join->on('menus.group_id', '=', 'menu_groups.id')
                        ->where('menu_groups.short_code', $this->menuCode);
                })
                ->whereNull('menus.parent_id')
                ->where('menus.enabled', true)
                ->orderBy('menus.lft')
                ->with(['permissions', 'children'])
                ->get()->each(function (Menu $menu) use ($menus) {
                    $this->push($menu, $menus);
                });

            return $menus;

        });

        $this->setActiveMenu($this->menus);

        return view('cms::menu.custom-menu');
    }

    public function menuGroupTitle()
    {
        if ($this->is_show_title) {
            return $this->group_title;
        }

        return '';
    }
}
