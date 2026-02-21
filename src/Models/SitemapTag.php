<?php

namespace Amplify\System\Cms\Models;

use Amplify\System\Cms\Models\Sitemap;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class SitemapTag extends Model implements Auditable
{
    use CrudTrait;
    use \OwenIt\Auditing\Auditable;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'sitemap_tags';

    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];

    // protected $fillable = [];
    // protected $hidden = [];
    protected $casts = [
        'fields' => 'array'
    ];

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

    public function mappable()
    {
        return $this->morphTo();
    }

    public function sitemap()
    {
        return $this->belongsTo(Sitemap::class);
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

    public function getLocationAttribute()
    {
        return $this->attributes['fields']['url '] ?? null;
    }

    public function getTitleAttribute()
    {
        return $this->attributes['fields']['title'] ?? null;
    }

    public function getDescriptionAttribute()
    {
        return $this->attributes['fields']['description'] ?? null;
    }

    public function getThumbnailLocAttribute()
    {
        return $this->attributes['fields']['thumbnail_loc'] ?? null;
    }

    public function getContentLocAttribute()
    {
        return $this->attributes['fields']['content_loc'] ?? null;
    }

    public function getPlayerLocAttribute()
    {
        return $this->attributes['fields']['player_loc'] ?? null;
    }

    public function getPublicationDateAttribute()
    {
        return $this->attributes['fields']['publication_date'] ?? null;
    }

    public function getFamilyFriendlyAttribute()
    {
        return $this->attributes['fields']['family_friendly'] ?? null;
    }

    public function getLiveAttribute()
    {
        return $this->attributes['fields']['live'] ?? null;
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
