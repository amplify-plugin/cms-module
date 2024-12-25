<?php

namespace Amplify\System\Cms\Models;

use Amplify\System\Cms\Models\MenuGroup;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Navigation extends Model implements Auditable
{
    use CrudTrait;
    use \OwenIt\Auditing\Auditable;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'navigations';

    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];

    protected $fillable = ['name', 'layout', 'top_bar', 'content', 'template_id', 'menu_group_id', 'cms_config_logo', 'is_enabled', 'is_new', 'is_updated', 'account_menu_id'];

    protected $hidden = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function menu_group()
    {
        return $this->belongsTo(MenuGroup::class);
    }

    public function mobile_menu()
    {
        return $this->belongsTo(MenuGroup::class);
    }

    public function account_menu()
    {
        return $this->belongsTo(MenuGroup::class);
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

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
