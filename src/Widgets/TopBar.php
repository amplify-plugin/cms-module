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
        $defaultClasses = ['topbar-section', 'container-collapse-p0'];
        $defaultClasses[] = theme_option('full_screen_header') ? 'container-fluid' : 'container';

        $this->attributes = $this->attributes->class($defaultClasses);
        return parent::htmlAttributes();
    }
}
