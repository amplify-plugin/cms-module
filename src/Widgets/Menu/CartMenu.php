<?php

namespace Amplify\System\Cms\Widgets\Menu;

use Amplify\Frontend\Abstracts\BaseComponent;
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
        if (customer_check()) {
            return customer(true)->canAny(['cart.add', 'cart.view']);
        }

        return config('amplify.frontend.guest_add_to_cart', true);

    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $cart = getCart();

        $itemCount = cart_count_badge($cart);

        return view('cms::menu.cart-menu', compact('itemCount'));
    }

    public function htmlAttributes(): string
    {
        if (config('amplify.frontend.guest_add_to_cart') || customer_check()) {

            $this->attributes = $this->attributes->merge([
                'onclick' => 'Amplify.loadCartDropdown()',
                'id' => 'show-cart',
                'class' => 'cart',
            ]);
        }

        return parent::htmlAttributes();
    }
}
