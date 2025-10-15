<?php

namespace Amplify\System\Cms\Widgets\Content;

use Amplify\Widget\Abstracts\BaseComponent;
use Closure;
use Illuminate\Contracts\View\View;

/**
 * @class Slider
 * @package Amplify\System\Cms\Widgets\Content
 *
 */
class Slider extends BaseComponent
{
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

        return view('cms::content.slider');
    }
}
