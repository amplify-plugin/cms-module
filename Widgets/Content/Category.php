<?php

namespace Amplify\System\Cms\Widgets\Content;

use Amplify\Widget\Abstracts\BaseComponent;
use Closure;
use Illuminate\Contracts\View\View;

/**
 * @class Category
 * @package Amplify\System\Cms\Widgets\Content
 *
 */
class Category extends BaseComponent
{
    public ?\Amplify\System\Cms\Models\Content $entry;

    public function __construct()
    {
        parent::__construct();

        $this->entry = store('contentModel', null);
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
        $categories = $this->entry->categories()
            ->pluck('name')
            ->map(fn($name) => '#'.ucwords($name))
            ->implode(', ');

        return view('cms::content.category', compact('categories'));
    }
}
