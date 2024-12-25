<?php

namespace Amplify\System\Cms\Models;

use Amplify\System\Cms\Models\Menu;
use Amplify\System\Cms\Models\Template;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use OwenIt\Auditing\Contracts\Auditable;
use Prologue\Alerts\Facades\Alert;

class MenuGroup extends Model implements Auditable
{
    use CrudTrait;
    use \OwenIt\Auditing\Auditable;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'menu_groups';

    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];

    // protected $fillable = [];
    // protected $hidden = [];
    protected $casts = ['is_reserved' => 'boolean'];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    public function firstLevelMenu()
    {
        return $this->menus()->parentItems()->orderBy('lft')->get()->filter(function ($item) {
            $isAuthenticated = ! $item->visibility_for['authenticated'] ?: customer_check() ?: false;
            $isAdmin = ! $item->visibility_for['admin'] ?: customer_check() && customer(true)->is_admin ?: false;
            $isApprover = ! $item->visibility_for['approver'] ?: customer_check() && customer(true)->is_approver ?: false;
            $isGuest = ! $item->visibility_for['guest'] ?: ! customer_check() ?: false;

            if (! $isAuthenticated || ! $isAdmin || ! $isApprover || ! $isGuest) {
                return false;
            }

            return true;
        });
    }

    protected static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->short_code = Str::slug($model->name);
        });
        self::deleting(function (self $model) {
            if ($model->is_reserved) {
                Alert::error('Cannot delete reserved menu group.');

                return false;
            }
        });
    }

    public function content()
    {
        $menu_group = $this;
        if ($this->blade_location) {
            return view(component_view($this->blade_location), compact('menu_group'))->render();
        }

        return view('components.menu', compact('menu_group'))->render();
    }

    /**
     * Backpack List view button
     *
     * @return string
     */
    public function buttonForMenus()
    {
        return '<a class="btn btn-sm btn-link" href="'.route('menu.index')
            .'?group_id='.$this->id.'" data-toggle="tooltip" title="Menu Manage"><i class="la la-list mr-2"></i> Menu Items</a>';
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Get all the menus for the MenuGroup
     */
    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class, 'group_id', 'id');
    }

    /**
     * Get the template that owns the Menu
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class, 'template_id');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeIsActive($query)
    {
        return $query->where('active', 1);
    }

    /**
     * return menu group and children if cache not available
     * query database
     *
     * @return mixed
     */
    public static function findByCode(string $code)
    {
        return Cache::remember(Session::token().'-'.$code, ((App::environment('production')) ? 1 * HOUR : 0), function () use ($code) {
            return MenuGroup::whereShortCode($code)
                ->isActive()
                ->with([
                    'menus' => function ($menu) {
                        return $menu
                            ->select('menus.*', 'pages.slug as page_slug')
                            ->whereNull('menus.parent_id')
                            ->leftJoin('pages', 'pages.id', 'menus.page_id')
                            ->orderBy('menus.lft')
                            ->with([
                                'children' => function ($child) {
                                    return $child->select('menus.*', 'pages.slug as page_slug')
                                        ->leftJoin('pages', 'pages.id', 'menus.page_id');
                                },
                                'megaMenus',
                            ]);
                    },
                ])
                ->first();
        });
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
