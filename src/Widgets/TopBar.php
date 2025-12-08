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
    public function __construct(public bool $render = true)
    {
        parent::__construct();
    }

    /**
     * Whether the component should be rendered
     */
    public function shouldRender(): bool
    {
        return $this->render;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return \view('cms::top-bar');
    }

    public function htmlAttributes(): string
    {
        $this->attributes = $this->attributes->class(['topbar-section']);

        return parent::htmlAttributes();
    }
}
