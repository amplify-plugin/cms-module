<?php

namespace Amplify\System\Cms\Models;

use Amplify\System\Backend\Models\User;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @method static Builder published()
 */
class Content extends Model implements Auditable
{
    use CrudTrait;
    use \OwenIt\Auditing\Auditable;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'contents';

    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];

    // protected $fillable = [];
    // protected $hidden = [];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->author_id = backpack_auth()->id();
        });

        static::saving(function ($model) {
            if ($model->status == 1 && $model->published_at == null) {
                $model->published_at = now();
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    public static function guessContentModel()
    {
        return self::when(
            request()->route('content') != null, fn (Builder $builder) => $builder->published()->whereSlug(request()->route('content')))
            ->firstOrFail();
    }

    public function changeApprovalBtn(): string
    {
        return '<a class="btn btn-sm btn-link" href="' . route('content.changeApproval', $this->id)
            . '" data-toggle="tooltip" title="Create Classifcation"><i class="lar la-user"></i> Set as ' . ($this->is_approved ? 'rejected' : 'approved') . '</a>';
    }

    public function changeStatusBtn(): string
    {
        return '
            <div class="btn-group">
                <a class="btn btn-sm btn-link dropdown-toggle text-primary pl-1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="cursor: pointer">
                    Status <span class="caret"></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-right">
                    <li class="dropdown-header">Change to:</li>
                    <a class="dropdown-item" href="' . route('content.changeStatus', [$this->id, 0]) . '">Draft</a>
                    <a class="dropdown-item" href="' . route('content.changeStatus', [$this->id, 1]) . '">Publish</a>
                    <a class="dropdown-item" href="' . route('content.changeStatus', [$this->id, 2]) . '">Archive</a>
                </ul>
            </div>
        ';
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(ContentCategory::class, 'content_category_content');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */
    public function scopePublished(Builder $builder)
    {
        return $builder->where('status', '=', 1);
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
