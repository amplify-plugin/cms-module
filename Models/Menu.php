<?php

namespace Amplify\System\Cms\Models;

use Amplify\System\Cms\Models\MegaMenu;
use Amplify\System\Cms\Models\MenuGroup;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Backpack\CRUD\app\Models\Traits\SpatieTranslatable\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use JsonException;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\PermissionRegistrar;

class Menu extends Model implements Auditable
{
    use CrudTrait, HasTranslations;
    use \OwenIt\Auditing\Auditable;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    const URL_TYPES = [
        'page' => 'Page',
        'external' => 'External',
    ];

    const MENU_TYPES = [
        'default' => 'Default',
        'mega-menu' => 'Mega Menu',
    ];

    protected $table = 'menus';

    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];

    protected $casts = ['for_authenticated' => 'bool', 'for_guest' => 'bool', 'open_new_tab' => 'bool'];

    protected $translatable = ['name'];

    //    protected $appends = ['local_name'];
    // protected $fillable = [];
    // protected $hidden = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    public function hasChilds()
    {
        return $this->childs->count() > 0;
    }

    public function hasMegaMenu()
    {
        return $this->megaMenus->count() > 0;
    }

    public function childList()
    {
        return $this->children()->get();
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            config('permission.models.permission'),
            'menu_has_permissions',
            'menu_id',
            PermissionRegistrar::$pivotPermission
        );
    }

    public static function linkPath(string $url): bool|int|array|string|null
    {
        $path = parse_url($url, PHP_URL_PATH);

        if (is_string($path)) {
            return ltrim($path, '/');
        }

        return $path;
    }

    /**
     * @return string
     */
    public function displayName()
    {
        return $this->name ?? '';
    }

    /**
     * @throws JsonException
     */
    public function link()
    {
        $menu_url = null;

        if ($this->url_type == 'external') {
            $menu_url = $this->url;
        }

        if ($this->url_type == 'page') {

            if (property_exists('page_slug', $this) && $this->page_slug == '#') {
                $menu_url = url('#');
            } elseif ($page = $this->page) {
                $menu_url = $page->full_url;
            } else {
                $menu_url = url('#');
            }

        }

        $queries = trim($this->prepareQueries($this->queries));

        if ($queries == '?') {
            $queries = '';
        }

        $menu_url .= $queries;

        return $menu_url;
    }

    /**
     * @throws JsonException
     */
    public function prepareQueries($queries): string
    {
        if ($queries != null) {
            $queryBuilder = [];

            foreach (json_decode($queries, true, 512, JSON_THROW_ON_ERROR) as $query) {
                $queryBuilder[$query['name']] = $query['value'];
            }

            return '?'.http_build_query($queryBuilder);
        }

        return '';
    }

    /**
     * @return mixed
     *
     * @throws JsonException
     */
    //    public function linkPath()
    //    {
    //        $path = parse_url($this->link(), PHP_URL_PATH);
    //
    //        if (is_string($path)) {
    //            return ltrim($path, '/');
    //        }
    //
    //        return $path;
    //
    //    }

    public function addNew()
    {
        return '<a href="'.route('menu.create').'?group_id='.request('group_id').'" class="btn btn-primary" data-style="zoom-in"><span class="ladda-label"><i class="la la-plus"></i> Add Item </span></a>';
    }

    public function editButton()
    {

        $button = '';
        $localizedLinks = '';

        foreach ($this->getAvailableLocales() as $key => $locale) {
            $localizedLinks .= '<a class="dropdown-item" href="'.route('menu.edit', $this->id).'?group_id='.$this->group_id.'&locale='.$key.'">'.$locale.'</a>';
        }

        if (! $this->translationEnabled()) {
            $button = '<a href="'.route('menu.edit', $this->id).'?group_id='.$this->group_id.'" class="btn btn-sm btn-link"><i class="la la-edit"></i>'.trans('backpack::crud.edit').'</a>';
        } else {
            $button =
                '<div class="btn-group">
            <a href="'.route('menu.edit', $this->id).'?group_id='.$this->group_id.'" class="btn btn-sm btn-link pr-0"><i class="la la-edit"></i> '.trans('backpack::crud.edit').'</a>
            <a class="btn btn-sm btn-link dropdown-toggle text-primary pl-1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="caret"></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-right">
                <li class="dropdown-header">'.trans('backpack::crud.edit_translations').':</li>'
                .$localizedLinks.
                '</ul>
            </div>';
        }

        return $button;
    }

    public function reorderButton()
    {
        return '<a href="'.route('menu.reorder').'?group_id='.request()->group_id.'" class="btn btn-outline-primary" data-style="zoom-in"><span class="ladda-label"><i class="la la-arrows"></i> Items Reorder</span></a>';
    }

    public function addMegaMenuItemButton()
    {
        if ($this->type == 'mega-menu') {
            return '<a href="'.route('mega-menu.create').'?group_id='.request('group_id', '').'&menuId='.$this->id.'" class="btn btn-sm btn-link" data-style="zoom-in"><span class="ladda-label"><i class="la la-plus"></i> Add </span></a>';
        }

        return '';
    }

    public function listMegaMenuItems()
    {
        if ($this->type == 'mega-menu') {
            return '<a href="'.route('mega-menu.index').'?group_id='.request('group_id', '').'&menuId='.$this->id.'" class="btn btn-sm btn-link" data-style="zoom-in"><span class="ladda-label"><i class="la la-list"></i> List </span></a>';
        }

        return '';
    }

    public function onlyAuth()
    {
        return $this->for_authenticated;
    }

    public function onlyPublic()
    {
        return $this->for_guest;
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    /**
     * Get all the children for the Menu
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id')
            ->where('enabled', true)
            ->orderBy('lft');
    }

    /**
     * Get the page that owns the Menu
     */
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'page_id')->withDefault(['slug' => '#']);
    }

    /**
     * Get the menu that owns the Menu
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(MenuGroup::class, 'group_id');
    }

    /**
     * Get all the megaMenus for the Menu
     */
    public function megaMenus(): HasMany
    {
        return $this->hasMany(MegaMenu::class, 'menu_id')->orderBy('lft');
    }

    /**
     * Get the parent that owns the Menu
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * @throws JsonException
     */
    private function getDataOfAvailableLocale($original_field_name): ?string
    {
        $locale = $_GET['locale'] ?? app()->getLocale();
        $data = $this->getTranslation($original_field_name, $locale);

        if (empty($data)) {
            $original_data = json_decode($this->attributes[$original_field_name] ?? '',
                false,
                512,
                JSON_THROW_ON_ERROR);
            $data = $original_data->{$locale} ?? collect($original_data)->first();
        }

        return $data;
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */
    public function scopeParentItems($query)
    {
        return $query->where('parent_id', null);
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */
    public function getLocalNameAttribute()
    {
        return $this->attributes['local_name'] = $this->getDataOfAvailableLocale('name');
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
