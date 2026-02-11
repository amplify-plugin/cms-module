<?php

namespace Amplify\System\Cms\Widgets\MegaMenu;

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

            foreach ($this->menu->products as $product) {
                $this->push($product, $products);
            }

            return $products;
        });

        return view('cms::mega-menu.product-menu', compact('products'));
    }

    private function push(mixed $product, \Illuminate\Support\Collection &$products)
    {
        $item = new \stdClass;

        $item->column_size = $product->product_column;
        $item->url = frontendSingleProductURL($product->product_info).'?has_sku='.($product->product_info?->isSkuProduct ?? null).'&seopath='.($product->product_info?->seopath ?? null);

        $item->display_image = (bool) $product->attribute_access['image'];
        $item->image = assets_image($product->product_info?->isSkuProduct ? $product->product_info?->Sku_ProductImage ?? '' : $product->product_info?->Product_Image ?? '');

        $item->display_name = (bool) $product->attribute_access['name'];
        $item->name = $product->product_info?->isSkuProduct ? $product->product_info?->Sku_Name : $product->product_info?->Product_Name;

        $item->display_price = (bool) (customer_check() ? $product->attribute_access['price'] : config('amplify.basic.enable_guest_pricing',false));
        $item->price = $product->product_info?->Msrp ?? product_out_stock_message();

        $item->display_description = (bool) $product->attribute_access['short_desc'];
        $item->description = strip_tags($product->product_info?->Sku_Name ?? '');

        $products->push($item);
    }
}
