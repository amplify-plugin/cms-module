<?php

namespace Amplify\System\Cms\Widgets\MegaMenu;

use Amplify\Widget\Abstracts\BaseComponent;
use Amplify\Widget\Traits\MegaMenuTrait;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;

/**
 * @class SubCategoryMenu
 */
class SubCategoryMenu extends BaseComponent
{
    use MegaMenuTrait;

    public function render(): View|Closure|string
    {
        $this->setDefaultClasses(['mega-li', "col-md-{$this->menu->menu_column_size }"]);
        $subCategories = Cache::remember('site-mega-menu-'.$this->menu->getKey().'-category-menu', 1 * HOUR, function () {

            $subCategories = collect();

            if ($this->menu->category_seopath != null) {

                $subCategories = \Sayt::getSubCategoriesByCategory($this->menu->category_seopath)?->categoryList ?? [];

                foreach ($subCategories as $subCategory) {
                    $this->push($subCategory, $subCategories);
                }
            }

            return $subCategories;
        });

        return view('widget::mega-menu.sub-category-menu', compact('subCategories'));
    }

    public function push(mixed $item, &$subCategories) {}
}
