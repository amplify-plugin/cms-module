<?php

namespace Amplify\System\Cms\Widgets\Menu;

use Amplify\System\Cms\Models\Menu;
use Amplify\Widget\Abstracts\BaseComponent;
use Closure;
use Illuminate\Contracts\View\View;

/**
 * @class NestedMenu
 * @package Amplify\Widget\Components\Menu
 *
 */
class NestedMenu extends BaseComponent
{
    public function __construct(public ?\stdClass $child = null,  public bool $showIcon = false)
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

        return view('cms::menu.nested-menu');
    }
}
