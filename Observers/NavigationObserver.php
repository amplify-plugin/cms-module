<?php

namespace Amplify\System\Cms\Observers;

use Amplify\System\Cms\Models\Navigation;

class NavigationObserver
{
    /**
     * Handle the Navigation "creating" event.
     *
     * @return void
     */
    public function creating(Navigation $navigation)
    {
        $navigation->template_id = template()->id;
    }

    /**
     * Handle the Navigation "created" event.
     *
     * @return void
     */
    public function created(Navigation $navigation)
    {
        if ($navigation->is_enabled) {
            Navigation::where([['id', '!=', $navigation->id], ['template_id', $navigation->template_id], ['is_enabled', true]])->update(['is_enabled' => false]);
        }
    }

    /**
     * Listen to the User updating event.
     *
     * @return void
     */
    public function updating($model)
    {
        if (! empty($model->is_enabled)) {
            Navigation::where([['id', '!=', $model->id], ['template_id', $model->template_id], ['is_enabled', true]])->update(['is_enabled' => false]);
        }
    }
}
