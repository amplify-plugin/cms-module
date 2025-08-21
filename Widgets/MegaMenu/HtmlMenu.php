<?php

namespace Amplify\System\Cms\Widgets\MegaMenu;

use Amplify\Widget\Abstracts\BaseComponent;
use Amplify\Widget\Traits\MegaMenuTrait;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\HtmlString;

/**
 * @class HtmlMenu
 */
class HtmlMenu extends BaseComponent
{
    use MegaMenuTrait;

    public function render(): View|Closure|string
    {
        $this->setDefaultClasses(['mega-li', "col-md-{$this->menu->menu_column_size }"]);
        $html = Cache::remember('site-mega-menu-'.$this->menu->getKey().'-html-menu', 1 * HOUR, function () {
            return new HtmlString($this->menu->html_content ?? '');
        });

        return view('widget::mega-menu.html-menu', compact('html'));
    }
}
