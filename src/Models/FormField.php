<?php

namespace Amplify\System\Cms\Models;

use Amplify\System\Backend\Models\Event;
use Amplify\System\Backend\Models\EventVariable;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormField extends Model
{
    use CrudTrait;
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'form_fields';

    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];

    // protected $fillable = [];
    // protected $hidden = [];
    protected $casts = [
        'is_required' => 'bool',
        'is_inline' => 'bool',
    ];

    protected static function booted(): void
    {
        static::created(function (self $field) {
            $field->syncEventForFormField($field);
        });
    }

    private function syncEventForFormField(self $field)
    {
        $event_code = $field->form->event_code;
        $event = Event::whereCode($event_code)->first();
        EventVariable::updateOrCreate([
            'event_id' => $event->getKey(),
            'name' => '__'.$field->name.'__',
        ], [
            'name' => '__'.$field->name.'__',
            'value' => '',
            'description' => $field->label,
            'for_admin' => false,
            'event_id' => $event->getKey(),
            'created_at' => now(),
        ]);
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }
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

    public function getDisplayableOptionsAttribute()
    {
        if ($this->options == null) {
            return [];
        }

        $options = json_decode($this->options, true);

        $formattedOption = [];

        foreach ($options as $option) {
            $formattedOption[$option['option']] = $option['option'];
        }

        return $formattedOption;
    }
}
