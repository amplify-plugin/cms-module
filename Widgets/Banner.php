<?php

namespace Amplify\System\Cms\Widgets;

use Amplify\System\Helpers\UtilityHelper;
use Amplify\Widget\Abstracts\BaseComponent;
use Closure;
use Illuminate\Contracts\View\View;

/**
 * @class Banner
 */
class Banner extends BaseComponent
{
    /**
     * @var array
     */
    public $options;

    public $backgroundImage;

    public $bannerContent;

    public $bannerClass;

    public $height;

    /**
     * Create a new component instance.
     */
    public function __construct($backgroundImage, $bannerContent, $bannerClass = '', $height = 250)
    {
        parent::__construct();

        $this->backgroundImage = $backgroundImage;
        $this->height = $height;
        $this->bannerClass = $bannerClass;
        $this->bannerContent = UtilityHelper::typeCast($bannerContent, 'json');
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

        return view('widget::banner');
    }
}
