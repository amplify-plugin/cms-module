<?php

namespace Amplify\System\Cms\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\File;
use OwenIt\Auditing\Contracts\Auditable;
use Symfony\Component\Yaml\Yaml;

/**
 * @property-read bool $upgradable
 */
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

    public function activateButton($crud): string
    {
        if (!$crud->hasAccess('activate')) {
            return '';
        }

        if ($this->is_active) {
            return '';
        }

        $url = backpack_url("theme/{$this->id}/activate");

        return <<<HTML
<a class='btn btn-sm btn-link' 
bp-button='activate' href="{$url}" data-toggle="tooltip" title='Activate This Template?'>
<i class="la la-check"></i> Activate
</a>
HTML;

    }

    public function checkUpdateButton($crud): string
    {
        if (!$crud->hasAccess('update')) {
            return '';
        }

        if (!$this->upgradable) {
            return '';
        }

        $url = backpack_url("theme/{$this->id}/check-update");

        return <<<HTML
<a href="{$url}" bp-button="check-update" class="btn btn-sm btn-link">
<i class="la la-cloud-download"></i> <span>Check Update</span>
</a>
HTML;

    }

    public function configPath(): string
    {
        return base_path("themes/{$this->slug}/theme.yaml");
    }

    public function config(): mixed
    {
        return Yaml::parseFile($this->configPath());
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

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */
    public function getUpgradableAttribute(): bool
    {
        $config = $this->config();

        if (empty($config['updates']) || !$config['updates']['enabled']) {
            return false;
        }

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
