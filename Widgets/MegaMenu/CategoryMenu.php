<?php

namespace Amplify\System\Cms\Widgets\MegaMenu;

use Amplify\Widget\Abstracts\BaseComponent;
use Amplify\System\Cms\Traits\MegaMenuTrait;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;

/**
 * @class CategoryMenu
 */
class CategoryMenu extends BaseComponent
{
    use MegaMenuTrait;

    public function render(): View|Closure|string
    {
        $this->setDefaultClasses(['mega-li', "col-md-{$this->menu->menu_column_size }"]);

        $categories = Cache::remember('site-mega-menu-'.$this->menu->getKey().'-category-menu', HOUR, function () {

            $categories = collect();

            foreach ($this->menu->categories as $category) {
                $categories->push($category);
            }

            return $categories;
        });

        return view('cms::mega-menu.category-menu', compact('categories'));
    }
}
