<?php

namespace Amplify\System\Cms\Models;

use App\Models\Event;
use App\Models\EventRecipent;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Form extends Model
{
    use CrudTrait;
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'forms';

    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];

    // protected $fillable = [];
    // protected $hidden = [];
    protected $casts = [
        'enabled' => 'boolean',
        'allow_reset' => 'boolean',
        'allow_captcha' => 'boolean',
    ];

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    protected static function booted(): void
    {
        static::saved(function (self $form) {
            $form->syncEventForForm($form);
        });

        static::deleted(function (self $form) {
            $form->deleteEventForm($form);
        });
    }

    private function syncEventForForm(self $form): void
    {
        Event::updateOrCreate([
            'code' => $form->event_code,
        ], [
            'name' => $form->name.' Form Submitted',
            'code' => $form->event_code,
            'enabled' => true,
            'description' => $form->name,
            'eventRecipents' => [
                new EventRecipent(['name' => 'Salesperson', 'event_action_field' => 'is_get_salesperson', 'description' => 'Send email to salesperson', 'enabled' => true]),
            ],
        ]);
    }

    private function deleteEventForm(self $form): void
    {
        $event = Event::whereCode($form->event_code)->first();
        $event->delete();
        $event->eventActions()->delete();
        $event->eventTemplate()->delete();
        $event->eventVariables()->delete();
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function formFields(): HasMany
    {
        return $this->hasMany(FormField::class);
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

    public function getTargetedEventAttribute()
    {

        return Event::whereCode($this->event_code)->first();
    }

    public function getEventCodeAttribute()
    {
        return str_replace('-', '_', ($this->code.'_form_submitted'));
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
