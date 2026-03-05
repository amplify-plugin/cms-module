<?php

namespace Amplify\System\Cms\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Relations\Pivot;

class MegaMenuProduct extends Pivot
{
    protected $casts = [
        'attribute_access' => 'array',
    ];

    protected $appends = [
        'product_column',
    ];

//    public function getProductInfoAttribute()
//    {
//        //$this->easyAskPageService = $this->easyAskPageService ?: new \Sayt;
//        $easyAskProduct = \Sayt::storeProductDetail($this->product_id);
//
//        $product = $easyAskProduct->getFirstProduct() ?? new \stdClass();
//        $product->isSkuProduct = isset($product->Sku_Id) ? true : false;
//        $product->seopath = $easyAskProduct->getCurrentSeoPath();
//
//        return $product;
//    }

    public function getProductColumnAttribute()
    {
        return "col-md-{$this->product_column_size}";
    }
}
