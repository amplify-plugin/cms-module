<?php

namespace Amplify\System\Cms\Widgets\MegaMenu;

use Amplify\Widget\Abstracts\BaseComponent;
use Amplify\System\Cms\Traits\MegaMenuTrait;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;

/**
 * @class DefaultMenu
 */
class DefaultMenu extends BaseComponent
{
    use MegaMenuTrait;

    public function render(): View|Closure|string
    {
        $this->setDefaultClasses(['mega-li', "col-md-{$this->menu->menu_column_size }"]);
        $links = Cache::remember('site-mega-menu-'.$this->menu->getKey().'-default-menu', HOUR, function () {
            return $this->menu->defaultLinks();
        });

        return view('cms::mega-menu.default-menu', compact('links'));
    }
}
