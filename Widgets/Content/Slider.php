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
    public function __construct(public string $header )
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
        $contents = \Amplify\System\Cms\Models\Content::all();
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
