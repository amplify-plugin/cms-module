<div id="show-cart" class="cart position-relative flex-shrink-0" {!! $htmlAttributes !!}>
    <i class="pe-7s-cart font-weight-bolder"></i>
    @if($showBadge)
        <span @class(["cart-badge badge total_cart_items", 'd-none' => empty($itemCount)]) >0</span>
    @endif
    <span @class(["count total_cart_items", 'd-none' => empty($itemCount)]) >0</span>
    <div class="toolbar-dropdown">
        <span class="text-dark toolbar-close"><i class="icon-cross"></i></span>
        <p class="cart-dropdown-title">Cart Items (<span class="total_cart_items">0</span>)</p>
        <div class="cart-dropdown">
        </div>
        <div class="toolbar-dropdown-group" id="cart-menu-subtotal" style="display: none;">
            <div class="column">
                <span class="text-lg">Subtotal:</span>
            </div>
            <div class="column text-right">
                <span class="text-lg text-medium total_cart_amount">$0.00</span>
            </div>
        </div>
        <div class="toolbar-dropdown-group">
            <div class="column">
                <a class="btn btn-sm btn-block btn-secondary"
                   href="{{ route('frontend.carts.index') }}">
                    {{ __('View Cart') }}
                </a>
            </div>
            <div class="column">
                <a class="btn btn-sm btn-block btn-success"
                   href="{{ route('frontend.checkout') }}">
                    {{ __('Checkout') }}
                </a>
            </div>
        </div>
    </div>
</div>

