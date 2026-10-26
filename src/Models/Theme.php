<?php

namespace Amplify\System\Cms\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\File;
use OwenIt\Auditing\Contracts\Auditable;

class Theme extends Model implements Auditable
{
    use CrudTrait;
    use SoftDeletes;
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

    protected $casts = ['options' => 'json', 'is_new' => 'boolean', 'is_updated' => 'boolean', 'is_active' => 'boolean', 'author' => 'json'];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::deleted(function (self $template) {
            File::deleteDirectory(base_path("themes/{$template->slug}"));
        });
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

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
