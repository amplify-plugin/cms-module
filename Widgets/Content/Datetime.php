<?php

namespace Amplify\System\Cms\Widgets\Content;

use Amplify\Widget\Abstracts\BaseComponent;
use Closure;
use Illuminate\Contracts\View\View;

/**
 * @class Datetime
 * @package Amplify\System\Cms\Widgets\Content
 *
 */
class Datetime extends BaseComponent
{
    public ?\Amplify\System\Cms\Models\Content $entry;

    public function __construct(public string $column = 'published_at')
    {
        parent::__construct();

        $this->entry = store('ContentModel', null);
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

        return view('cms::content.datetime');
    }
}
