<?php

namespace Amplify\System\Cms\Widgets\MegaMenu;

use Amplify\System\Backend\Models\Manufacturer;
use Amplify\Widget\Abstracts\BaseComponent;
use Amplify\Cms\Traits\MegaMenuTrait;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

/**
 * @class ManufacturerMenu
 */
class ManufacturerMenu extends BaseComponent
{
    use MegaMenuTrait;

    public function render(): View|Closure|string
    {
        $this->setDefaultClasses(['mega-li', "col-md-{$this->menu->menu_column_size }"]);
        $manufacturers = Cache::remember('site-mega-menu-'.$this->menu->getKey().'-manufacturer-menu', HOUR, function () {
            return Manufacturer::select('*')
                ->when($this->menu->only_featured_manufacturer == true, function (Builder $builder) {
                    return $builder->where('featured', true);
                })->when(is_numeric($this->menu->number_of_categories), function (Builder $builder) {
                    return $builder->limit($this->menu->number_of_categories);
                })->get();
        });

        return view('cms::mega-menu.manufacturer-menu', compact('manufacturers'));
    }
}
