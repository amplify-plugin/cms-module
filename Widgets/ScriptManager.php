<?php

namespace Amplify\System\Cms\Widgets;

use Amplify\System\Cms\Models\ScriptManager as ModelsScriptManager;
use Amplify\Widget\Abstracts\BaseComponent;
use Closure;
use Illuminate\Contracts\View\View;

/**
 * @class CustomScriptManager
 */
class ScriptManager extends BaseComponent
{
    public const POSITION_HEADER = 'header';

    public const POSITION_FOOTER = 'footer';

    public const POSITION_GOOGLE_EVENT = 'footer';

    /**
     * @var array
     */
    public $options;

    private string $position;

    private string $order;

    /**
     * Create a new component instance.
     *
     * @param  string  $order
     */
    public function __construct(string $position = 'header', $order = 'ASC')
    {
        parent::__construct();
        $this->position = $position;
        $this->order = $order;
    }

    /**
     * Whether the component should be rendered
     */
    public function shouldRender(): bool
    {
        return true;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $scripts = ModelsScriptManager::wherePosition($this->position)
            ->orderBy('priority', $this->order)->get();

        return view('cms::script-manager', compact('scripts'));
    }
}
