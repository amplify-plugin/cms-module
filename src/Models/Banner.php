<?php

namespace Amplify\System\Cms\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use OwenIt\Auditing\Contracts\Auditable;

class Banner extends Model implements Auditable
{
    use CrudTrait;
    use \OwenIt\Auditing\Auditable;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'banners';

    protected $guarded = ['id'];

    protected $casts = [
        'enabled' => 'bool',
        'has_button' => 'bool',
        'has_heading' => 'bool',
        'has_content' => 'bool',
    ];

    protected $attributes = [
        'content' => null,
        'has_button' => false,
        'has_advance_option' => false,
        'button_title' => null,
        'button_link' => null,
        'button_style' => 'primary',
        'alignment' => 'left',
        'open_new_tab' => false,
        'slider_ratio' => '6',
        'text_alignment' => 'center',
        'image_alignment' => 'right',
        'has_content' => false,
        'background_type' => 'image',
        'foreground_type' => 'image',
    ];

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
        static::saving(function (self $model) {
            @cache()->forget("site-banner-slider-{$model->bannerZone->code}");
            $model->code = Str::slug($model->code ?? $model->name);
            $model->getDirty();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function bannerZone(): BelongsTo
    {
        return $this->belongsTo(BannerZone::class);
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
