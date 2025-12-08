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
    protected $casts = ['options' => 'json'];

    protected $attributes = [
        'options' => [],
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
        return $this->attributes['options']['location'] ?? null;
    }

    public function getTitleAttribute()
    {
        return $this->attributes['options']['title'] ?? null;
    }

    public function getDescriptionAttribute()
    {
        return $this->attributes['options']['description'] ?? null;
    }

    public function getThumbnailLocAttribute()
    {
        return $this->attributes['options']['thumbnail_loc'] ?? null;
    }

    public function getContentLocAttribute()
    {
        return $this->attributes['options']['content_loc'] ?? null;
    }

    public function getPlayerLocAttribute()
    {
        return $this->attributes['options']['player_loc'] ?? null;
    }

    public function getPublicationDateAttribute()
    {
        return $this->attributes['options']['publication_date'] ?? null;
    }

    public function getFamilyFriendlyAttribute()
    {
        return $this->attributes['options']['family_friendly'] ?? null;
    }

    public function getLiveAttribute()
    {
        return $this->attributes['options']['live'] ?? null;
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */

    public function setLocationAttribute($value)
    {
        $this->attributes['options']['location'] = $value;
    }

    public function setTitleAttribute($value)
    {
        $this->attributes['options']['title'] = $value;
    }

    public function setDescriptionAttribute($value)
    {
        $this->attributes['options']['description'] = $value;
    }

    public function setThumbnailLocAttribute($value)
    {
        $this->attributes['options']['thumbnail_loc'] = $value;
    }

    public function setContentLocAttribute($value)
    {
        $this->attributes['options']['content_loc'] = $value;
    }

    public function setPlayerLocAttribute($value)
    {
        $this->attributes['options']['player_loc'] = $value;
    }

    public function setPublicationDateAttribute($value)
    {
        $this->attributes['options']['publication_date'] = $value;
    }

    public function setFamilyFriendlyAttribute($value)
    {
        $this->attributes['options']['family_friendly'] = $value;
    }

    public function setLiveAttribute($value)
    {
        $this->attributes['options']['live'] = $value;
    }
}
