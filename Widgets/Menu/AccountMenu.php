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
 * @class AccountMenu
 */
class AccountMenu extends BaseComponent
{
    use DefaultMenuTrait;

    public Collection $menus;

    public array $userPermissions;

    public string $avatar;

    private ?string $menuCode;

    /**
     * Create a new component instance.
     */
    public function __construct(public bool $showContactAvatar = false, public bool $showIcon = false)
    {
        parent::__construct();

        $this->userPermissions = [];

        $this->menuCode = config('amplify.frontend.user_account_top_menu', 'account-menu');

        $this->setMenuGroup($this->menuCode);

        $this->defaultCss = ' account flex-shrink-0';
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $contact = customer(true);

        $this->menus = Cache::remember(Session::token().'-account-menu', HOUR, function () {

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
        $this->avatar = ($this->showContactAvatar && ! empty($contact)) ? generateUserAvatar($contact->name) : '';

        return view('widget::menu.account-menu', [
            'contact' => $contact,
        ]);
    }
}
