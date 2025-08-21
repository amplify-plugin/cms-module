<?php

namespace Amplify\System\Cms\Widgets\Banner;

use Amplify\Widget\Abstracts\BaseComponent;
use Closure;
use Illuminate\Contracts\View\View;

/**
 * @class BannerSliderItem
 */
class BannerItem extends BaseComponent
{
    public $bannersData;

    /**
     * @var array
     */
    public $options;

    public $title = '';

    public $description = '';

    public $button_title = '';

    public $button_link = '#';

    public $alt = '';

    public $src = '';

    public bool $alignment_left = false;

    /**
     * Create a new component instance.
     */
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
        return view('cms::banner.banner-item');
    }
}
