<?php

namespace Amplify\System\Cms\Models;

use Amplify\Marketing\Models\MerchandisingZone;
use Amplify\System\Cms\Models\MegaMenuProduct;
use Amplify\System\Sayt\Sayt;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class MegaMenu extends Model implements Auditable
{
    use CrudTrait;
    use \OwenIt\Auditing\Auditable;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    const TYPES = [
        'default' => 'Default',
        'category' => 'Category',
        'merchandise-zone' => 'Merchandising Zones',
        'sub-category' => 'Sub Categories',
        'product' => 'Product',
        'html' => 'HTML',
        //        'image' => 'Image(s)',
        //        'video' => 'Video',
        'manufacturer' => 'Manufacturer',
    ];

    protected $table = 'mega_menus';

    protected $guarded = ['id'];

    protected $casts = [
        'merchandising_attribute_access' => 'array',
        'show_name' => 'bool',
        'only_featured_manufacturer' => 'bool',
    ];

    protected $easyAskPageService;

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    public function getCategoriesAttribute()
    {
        $this->easyAskPageService = $this->easyAskPageService ?: new Sayt;

        $categories = $this->easyAskPageService->getCategory();

        return collect($categories->categoryList ?? [])->slice(0, $this->number_of_categories);
    }

    public function getMerchandisingZonesAttribute()
    {
        $this->easyAskPageService = $this->easyAskPageService ?: new Sayt;
        $marchProducts = $this->easyAskPageService->marchProducts('$$'.$this->merchandising_zone->easyask_key, $this->number_of_merchandising_products);

        if (isset($marchProducts['products'])) {
            return (object) [
                'products' => $marchProducts['products']->items,
                'seopath' => $marchProducts['seopath'],
            ];
        }

        return [];
    }

    public function isDefault()
    {
        return $this->type == 'default';
    }

    public function displayName()
    {
        return $this->name;
    }

    public function defaultLinks()
    {
        if ($this->isDefault()) {
            return json_decode($this->links);
        }

        return [];
    }

    public function isCategories()
    {
        return $this->type == 'category' && $this->category_id == null;
    }

    public function isMerchandisingZone()
    {
        return $this->type == 'merchandising-zones';
    }

    public function isSubsubCategory()
    {
        return $this->type == 'subsubcategory';
    }

    public function isProduct()
    {
        return $this->type == 'product';
    }

    public function isHtml()
    {
        return $this->type == 'html';
    }

    public function html()
    {
        return html_entity_decode($this->html_content);
    }

    public static function menuFreeColumn($menu_id = null, $except_menu_id = null)
    {
        return 12 - self::where('menu_id', $menu_id)
            ->where(function ($query) use ($except_menu_id) {
                return $except_menu_id ? $query->where('id', '!=', $except_menu_id) : $query;
            })->sum('menu_column_size');
    }

    /* Add create button */
    public function createMegaMenuButton()
    {
        return '<a href="'.route('mega-menu.create').'?menuId='.request()->menuId.'" class="btn btn-primary"><span class="ladda-label"><i class="la la-plus"></i> create New</span></a>';
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function products()
    {
        return $this->hasMany(MegaMenuProduct::class);
    }

    public function merchandising_zone()
    {
        return $this->belongsTo(MerchandisingZone::class);
    }

    /**
     * Get the menu that owns the MegaMenu
     */
    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getMenuColumnAttribute()
    {
        return "col-md-{$this->menu_column_size}";
    }

    public function getMerchandisingColumnAttribute()
    {
        return "col-md-{$this->number_of_column_merchandising_zone}";
    }

    public function getMenuTypeAttribute()
    {
        return 'mega-menu.'.$this->type.'-menu';
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
