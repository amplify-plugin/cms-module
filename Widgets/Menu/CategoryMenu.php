<?php

namespace Amplify\System\Cms\Widgets\Menu;

use Amplify\System\Sayt\Classes\NavigateCategory;
use Amplify\System\Sayt\Facade\Sayt;
use Amplify\Widget\Abstracts\BaseComponent;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;

/**
 * @class CategoryMenu
 * @package Amplify\Widget\Components\Menu
 *
 */
class CategoryMenu extends BaseComponent
{
    public mixed $categories;

    public function __construct(public ?\stdClass $menu = null, public ?NavigateCategory $category = null, public bool $showIcon = false)
    {
        parent::__construct();
    }

    /**
     * Whether the component should be rendered
     */
    public function shouldRender(): bool
    {
        return true;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        if ($this->menu !== null) {
            $this->categories = Cache::remember('menu-category-menu', DAY, function () {
                return Sayt::storeCategories($this->menu->seo_path ?? null, ['with_sub_category' => true]);
            });

        } else {
            $this->categories = $this->category->getSubCategories();
        }

        return view('cms::menu.category-menu');
    }

    public function htmlAttributes(): string
    {
        $this->attributes = $this->attributes->class(['sub-menu']);

        return parent::htmlAttributes();
    }
}
