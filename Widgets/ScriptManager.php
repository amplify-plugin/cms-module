<?php

namespace Amplify\System\Cms\Widgets;

use Amplify\System\Cms\Models\ScriptManager as ModelsScriptManager;
use Amplify\Widget\Abstracts\BaseComponent;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;

/**
 * @class CustomScriptManager
 */
class ScriptManager extends BaseComponent
{
    public const POSITION_HEADER = 'header';

    public const POSITION_FOOTER = 'footer';

    public const POSITION_GOOGLE_EVENT = 'google_event';

    public Collection $scripts;

    /**
     * Create a new component instance.
     *
     * @param string $position
     * @param string $order
     */
    public function __construct(string $position = 'header', string $order = 'ASC')
    {
        parent::__construct();

        $this->scripts = cache()->rememberForever("site-scripts-{$position}",
            function () use ($position, $order) {
                return ModelsScriptManager::where('position', '=', $position)
                    ->orderBy('priority', $order)->get();
            });
    }

    /**
     * Whether the component should be rendered
     */
    public function shouldRender(): bool
    {
        return $this->scripts->isNotEmpty();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('cms::script-manager');
    }
}
