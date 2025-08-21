<?php

namespace Amplify\System\Cms\Widgets;

use Amplify\Widget\Abstracts\BaseComponent;
use Closure;
use Illuminate\Contracts\View\View;

/**
 * @class TopBar
 */
class TopBar extends BaseComponent
{
    /**
     * @var array
     */
    public $options;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        parent::__construct();

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
        $class = match (config('amplify.basic.client_code')) {
            'NUX' => \Amplify\Widget\Components\Client\Nudraulix\TopBar::class,
            'DKL' => \Amplify\Widget\Components\Client\DKLOK\TopBar::class,
            default => \Amplify\Widget\Components\Client\Demo\TopBar::class,
        };
        $this->component = new $class;

        $this->component->attributes = $this->attributes;

        return $this->component->render();
    }
}
