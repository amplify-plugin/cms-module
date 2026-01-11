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
    public $categoryId;
    public $items;
    public $order;
    public $header;

    public function __construct(string $categoryId = '1',  string $items = '10',  string $order = 'asc',  string $header = '')
    {
        parent::__construct();

        $this->categoryId = (int) $categoryId;
        $this->items = (int) $items;
        $this->order = strtolower($order) === 'desc' ? 'desc' : 'asc';
        $this->header = $header;
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
        $contents = \Amplify\System\Cms\Models\Content::query()
            ->published()
            ->whereHas('categories', function ($query) {
                $query->where('content_category_id', $this->categoryId);
            })
            ->orderBy('published_at', $this->order)
            ->limit($this->items)
            ->get();

        return view('cms::content.slider', compact('contents'));
    }
    public function carouselOptions(): string
    {
        return json_encode([
            'lazyLoad' => true,
            'animateIn' => 'fadeIn',
            'animateOut' => 'fadeOut',
            'dots' => true,
            'nav' => true,
            'margin' => 30,
            'responsive' => [
                '0' => ['items' => 1],
                '576' => ['items' => 2],
                '768' => ['items' => 2],
                '991' => ['items' => 3],
                '1200' => ['items' => 3],
            ],
        ]);
    }
}
