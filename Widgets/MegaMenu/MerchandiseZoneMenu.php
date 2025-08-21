<?php

namespace Amplify\System\Cms\Widgets\MegaMenu;

use Amplify\ErpApi\Facades\ErpApi;
use Amplify\Widget\Abstracts\BaseComponent;
use Amplify\Widget\Traits\MegaMenuTrait;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;

/**
 * @class MerchandiseZoneMenu
 */
class MerchandiseZoneMenu extends BaseComponent
{
    /**
     * @var mixed|string
     */
    private string $seopath;

    use MegaMenuTrait;

    public function render(): View|Closure|string
    {
        $this->setDefaultClasses(['mega-li', "col-md-{$this->menu->menu_column_size }"]);
        $products = Cache::remember('site-mega-menu-'.$this->menu->getKey().'-merchandising-zone-menu', 1 * HOUR, function () {

            $result = \Sayt::marchProducts($this->menu->merchandising_zone->easyask_key, ['per_page' => $this->menu->number_of_merchandising_products]);

            $this->seopath = $result->getCurrentSeoPath();

            $products = collect();

            foreach ($result->getProducts() as $product) {
                $this->push($products, $product);
            }

            return $products;
        });

        return view('cms::mega-menu.merchandise-zone-menu', compact('products'));
    }

    private function push(&$collection, $product)
    {
        $item = new \stdClass;

        $item->column_size = $this->menu->number_of_column_merchandising_zone;

        $item->id = $product->Product_Id;

        $item->url = $this->productDetailLink($product);

        $item->display_name = (bool) $this->menu->merchandising_attribute_access['name'] ?? false;
        $item->name = $this->productTitle($product);

        $item->display_image = (bool) $this->menu->merchandising_attribute_access['image'] ?? false;
        $item->image = $this->productImage($product);

        $item->display_price = (bool) $this->menu->merchandising_attribute_access['price'] ?? false;
        $item->price = $this->productPrice($product);

        $item->display_description = (bool) $this->menu->merchandising_attribute_access['short_desc'] ?? false;
        $item->description = strip_tags($product->product_info?->Sku_Name ?? '');

        $item->old_price = $item->price;

        $item->discount = '10% off';

        $collection->push($item);
    }

    private function productDetailLink($product): string
    {
        $query = [
            'has_sku' => isset($product->Sku_List) && count(json_decode($product->Sku_List)) > 0 ? 1 : 0,
            'seopath' => $this->seopath,
        ];

        return frontendSingleProductURL($product).'?'.http_build_query($query);
    }

    private function productImage($product): string
    {
        $image = $product->Product_Image ?? '';

        if (! empty($product->Sku_List) && count(json_decode($product->Sku_List, true)) === 1) {
            if (! empty($product->Sku_ProductImage)) {
                $image = $product->Sku_ProductImage;
            }
        } elseif (! empty($product->Sku_Count) && ! empty($product->Full_Sku_Count) && $product->Sku_Count > 1 && $product->Sku_Count !== $product->Full_Sku_Count) {
            $image = ! empty($product->Sku_ProductImage) ? $product->Sku_ProductImage : $productImage ?? '';
        }

        return assets_image($image);

    }

    private function productTitle($product)
    {
        $name = $product->Product_Name ?? '';

        if (! empty($product->Sku_Name)) {
            $name = $product->Sku_Name;
        }

        return $name;
    }

    private function productPrice($product): float|int|string
    {

        $price = isset($product->Msrp) ? $product->Msrp : $product->Price ?? 0.00;
        if (ErpApi::enabled()) {
            $productPrice = ErpApi::getProductPriceAvailability([
                'items' => [['item' => $product->Sku_ProductCode ?? $product->Product_Code, 'qty' => 1]],
            ])->first();

            if ($productPrice?->Price != null) {
                $price = $productPrice->Price;
            }
        }

        $price = filter_var($price, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

        if (! is_numeric($price)) {
            return 'Upcoming';
        }

        return price_format($price);
    }
}
