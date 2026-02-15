<?php

namespace Amplify\System\Cms\Widgets\Content;

use Amplify\System\Cms\Models\ContentCategory;
use Amplify\Widget\Abstracts\BaseComponent;
use Closure;
use Illuminate\Contracts\View\View;

/**
 * @class Navigation
 * @package Amplify\System\Cms\Widgets\Content
 *
 */
class Navigation extends BaseComponent
{
    public ?\Amplify\System\Cms\Models\Content $entry;

    public ?ContentCategory $category;

    public function __construct()
    {
        parent::__construct();

        $this->entry = store('contentModel', null);

        $this->category = $this->entry->categories->first();
    }

    /**
     * Whether the component should be rendered
     */
    public function shouldRender(): bool
    {
        return !empty($this->category);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $previous = $this->category->contents()
            ->published()
            ->where('contents.id', '<', $this->entry->id)
            ->orderBy('contents.id', 'desc')
            ->first();

        $next = $this->category->contents()
            ->published()
            ->where('contents.id', '>', $this->entry->id)
            ->orderBy('contents.id', 'asc')
            ->first();

        return view('cms::content.navigation', compact('previous', 'next'));
    }
}
