<?php

namespace Amplify\System\Cms\Widgets\MegaMenu;

use Amplify\System\Backend\Models\Product;
use Amplify\System\Support\Money;
use Amplify\Widget\Abstracts\BaseComponent;
use Amplify\System\Cms\Traits\MegaMenuTrait;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;

/**
 * @class ProductMenu
 */
class ProductMenu extends BaseComponent
{
    use MegaMenuTrait;

    public function render(): View|Closure|string
    {
        $this->setDefaultClasses(['mega-li', "col-md-{$this->menu->menu_column_size }"]);

        $products = Cache::remember('site-mega-menu-'.$this->menu->getKey().'-product-menu', 1 * HOUR, function () {

            $products = collect();

            $this->menu->load('products.productImage');
            foreach ($this->menu->products as $product) {
                $this->push($product, $products);
            }

            return $products;
        });

        $showPrice = (bool) (customer_check() ? true : config('amplify.basic.enable_guest_pricing', false));

        $products->each(function ($item) use ($showPrice) {
            $item->display_price = $showPrice && $item->price_attribute_enabled;
        });

        return view('cms::mega-menu.product-menu', compact('products'));
    }

    private function push(Product $product, \Illuminate\Support\Collection &$products)
    {
        $item = new \stdClass;

        $item->column_size = $product->pivot->product_column;

        $item->url = frontendSingleProductURL($product);

        $item->display_image = $product->pivot->attribute_access['image'] ?? false;
        $item->image = assets_image($product->productImage?->main);

        $item->display_name = $product->pivot->attribute_access['name'] ?? false;
        $item->name = $product->product_name;

        $item->price_attribute_enabled = $product->pivot->attribute_access['price'] ?? false;
        $item->price = !empty($product->msrp) ? Money::parse($product->msrp) : product_out_stock_message();

        $item->display_description = $product->pivot->attribute_access['short_desc'] ?? false;
        $item->description = strip_tags($product->short_description ?? '');

        $products->push($item);
    }
}
