<?php

namespace Amplify\System\Cms\Widgets\Menu;

use Amplify\Widget\Abstracts\BaseComponent;
use Closure;
use Illuminate\Contracts\View\View;

/**
 * @class CartMenu
 */
class CartMenu extends BaseComponent
{
    /**
     * Create a new component instance.
     */
    public function __construct(public bool $showBadge = false)
    {
        parent::__construct();

    }

    /**
     * Whether the component should be rendered
     */
    public function shouldRender(): bool
    {
        return ! customer_check() || customer(true)->can('shop.add-to-cart');
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('widget::menu.cart-menu');
    }
}
