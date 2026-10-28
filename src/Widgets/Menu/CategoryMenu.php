<?php

namespace Amplify\System\Cms\Widgets\Menu;

use Amplify\System\Sayt\Classes\NavigateCategory;
use Amplify\System\Sayt\Facade\Sayt;
use Amplify\Frontend\Abstracts\BaseComponent;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;

/**
 * @class CategoryMenu
 * @package Amplify\Frontend\Components\Menu
 *
 */
class CategoryMenu extends BaseComponent
{
    public mixed $categories;

    public function __construct(public ?\stdClass        $menu = null,
                                public ?NavigateCategory $category = null,
                                public bool              $showIcon = false,
                                public bool              $showProductCount = false,
                                public string            $subMenuClass = 'sub-menu',
                                public ?int              $maxItems = null
    )
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
        $menuHeader = ['label' => 'Categories', 'link' => route('frontend.categories')];

        if ($this->menu !== null) {
            $this->categories = Cache::remember("menu-category-menu", DAY, function () {
                $options = [
                    'with_sub_category' => !in_array($this->menu->sub_category_depth, [0, null]),
                    'sub_category_depth' => $this->menu->sub_category_depth ?? null,
                    'product_count' => $this->showProductCount,
                ];

                return Sayt::storeCategories($this->menu->seo_path ?? null, $options);
            });
        } else {

            $menuHeader = ['label' => $this->category->getName(), 'link' => route('frontend.categories', $this->category->getSEOPath())];

            $this->categories = $this->category->getSubCategories();
        }

        return view('cms::menu.category-menu', compact('menuHeader'));
    }

    public function htmlAttributes(): string
    {
        $this->attributes = $this->attributes->class([$this->subMenuClass]);

        return $this->subMenuClass == 'offcanvas-submenu'
            ? $this->attributes->toHtml()
            :parent::htmlAttributes();
    }

    public function onMobileMenu(): bool
    {
        return $this->subMenuClass == 'offcanvas-submenu';
    }
}
