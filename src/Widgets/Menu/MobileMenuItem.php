<?php

namespace Amplify\System\Cms\Widgets\Menu;

use Amplify\System\Cms\Models\Menu;
use Amplify\Frontend\Abstracts\BaseComponent;
use Amplify\System\Cms\Traits\DefaultMenuTrait;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use stdClass;

/**
 * @class MobileMenuItem
 */
class MobileMenuItem extends BaseComponent
{
    public function __construct(public stdClass $menu, public bool $showIcon = false)
    {
        parent::__construct();
    }

    /**
     * Get the view / view contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\Support\Htmlable|\Closure|string
     */
    public function render()
    {
        return view('cms::menu.mobile-menu-item');
    }
}
