<?php

namespace Amplify\System\Cms\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Theme extends Model implements Auditable
{
    use CrudTrait;
    use \OwenIt\Auditing\Auditable;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'templates';

    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];

    protected $casts = ['options' => 'json', 'is_new' => 'boolean', 'is_updated' => 'boolean', 'is_active' => 'boolean'];

    protected $appends = ['label'];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    public static function getTemplateSlug($slug, $id = null): string
    {
        $where = $id
            ? [['slug', 'LIKE', '%'.$slug.'%'], ['id', '!=', $id]]
            : ['slug' => $slug];

        $count = Theme::query()->where($where)->count();

        return $count
            ? "$slug-$count"
            : $slug;
    }

    public function setActiveTemplate(): string
    {
        return ! $this->is_active
            ? '<a class="btn btn-sm btn-link" href="/admin/theme/set-template-active/'.$this->id
               .'" data-toggle="tooltip" title="Activate This Template?"><i class="la la-check"></i>Activate</a>' : '';
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeIsActive($query)
    {
        return $query->where('is_active', 1);
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */
    public function getLabelAttribute()
    {
        return $this->attributes['label'] = $this->attributes['name'];
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
