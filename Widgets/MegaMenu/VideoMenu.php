<?php

namespace Amplify\System\Cms\Widgets\MegaMenu;

use Amplify\Widget\Abstracts\BaseComponent;
use Amplify\Widget\Traits\MegaMenuTrait;
use Closure;
use Illuminate\Contracts\View\View;

/**
 * @class VideoMenu
 */
class VideoMenu extends BaseComponent
{
    use MegaMenuTrait;

    public function render(): View|Closure|string
    {
        $this->setDefaultClasses(['mega-li', "col-md-{$this->menu->menu_column_size }"]);

        return view('cms::mega-menu.video-menu');
    }
}
