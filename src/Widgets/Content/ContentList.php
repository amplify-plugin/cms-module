<?php

namespace Amplify\System\Cms\Widgets\Content;

use Closure;
use Illuminate\Support\Facades\Config;
use Illuminate\Contracts\View\View;
use Amplify\Widget\Abstracts\BaseComponent;

/**
 * @class ContentList
 * @package Amplify\System\Cms\Widgets\Content
 *
 */
class ContentList extends BaseComponent
{
    public function __construct(public string $category = '', public int $perPage = 10, public string $order = 'asc', public int $itemPerLIne = 4)
    {
        parent::__construct();
    }

    public function shouldRender(): bool
    {
        return !empty($this->category);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $contents = \Amplify\System\Cms\Models\Content::whereHas('categories', function ($query) {
            return $query->where('content_categories.slug', $this->category);
        })->orderBy('contents.updated_at', $this->order)
            ->paginate($this->perPage);

        return view('cms::content.list', compact('contents'));
    }
}
