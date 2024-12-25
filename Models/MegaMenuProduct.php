<?php

namespace Amplify\System\Cms\Models;

use Amplify\System\Sayt\Sayt;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class MegaMenuProduct extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $guarded = ['id'];

    protected $casts = [
        'attribute_access' => 'array',
    ];

    protected $easyAskPageService;

    public function getProductInfoAttribute()
    {
        $this->easyAskPageService = $this->easyAskPageService ?: new Sayt;
        $easyAskProduct = $this->easyAskPageService->getProductById($this->product_id);

        if (! empty($easyAskProduct)) {
            $product = $easyAskProduct->items[0];
            $product->isSkuProduct = isset($product->Sku_Id) ? true : false;
            $product->seopath = $easyAskProduct->seoPath;

            return $product;
        }

        return null;
    }

    public function getProductColumnAttribute()
    {
        return "col-md-{$this->product_column_size}";
    }
}
