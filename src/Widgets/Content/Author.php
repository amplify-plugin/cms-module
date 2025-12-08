<?php

namespace Amplify\System\Cms\Widgets\Content;

use Amplify\Widget\Abstracts\BaseComponent;
use Closure;
use Illuminate\Contracts\View\View;

/**
 * @class Author
 * @package Amplify\System\Cms\Widgets\Content
 *
 */
class Author extends BaseComponent
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
        $author = $this->entry->author;

        return view('cms::content.author', compact('author'));
    }
}
