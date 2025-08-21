<?php

namespace Amplify\System\Cms\Widgets\Banner;

use Amplify\System\Cms\Models\BannerZone;
use Amplify\System\Helpers\UtilityHelper;
use Amplify\Widget\Abstracts\BaseComponent;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * @class BannerSlider
 */
class BannerSlider extends BaseComponent
{
    /**
     * @var array
     */
    public $options;

    public Collection $items;

    private bool $nav;

    private bool $dots;

    private bool $pauseOnHover;

    private bool $showOnMobile;

    public bool $fullWidth;

    private string $backgroundImage;

    public string $height;

    public string $backgroundImgClass;

    private ?BannerZone $bannerZone;

    private bool $loop = false;

    private bool $autoplay = false;

    private int $autoplayTimeout = 500000;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $bannerZone = 'banner-slider',
        string $nav = 'false',
        string $dots = 'true',
        string $loop = 'false',
        string $autoplay = 'false',
        string $pauseOnHover = 'false',
        string $showOnMobile = 'false',
        string $fullWidth = 'true',
        string $height = '200px',
        string $backgroundImage = '',
        string $backgroundImgClass = '',
        string $autoplayTimeout = '500000'
    ) {
        parent::__construct();
        $this->items = collect();
        $this->nav = UtilityHelper::typeCast($nav, 'bool');
        $this->dots = UtilityHelper::typeCast($dots, 'bool');
        $this->pauseOnHover = UtilityHelper::typeCast($pauseOnHover, 'bool');
        $this->showOnMobile = UtilityHelper::typeCast($showOnMobile, 'bool');
        $this->fullWidth = UtilityHelper::typeCast($fullWidth, 'bool');
        $this->backgroundImage = ($backgroundImage != '') ? $backgroundImage : assets_image('img/banner-slider-bg.jpg');
        $this->height = $height;
        $this->backgroundImgClass = $backgroundImgClass;
        $this->bannerZone = BannerZone::whereCode($bannerZone)->first();
        $this->loop = UtilityHelper::typeCast($loop, 'bool');
        $this->autoplay = UtilityHelper::typeCast($autoplay, 'bool');
        $this->autoplayTimeout = UtilityHelper::typeCast($autoplayTimeout, 'integer');
    }

    /**
     * Whether the component should be rendered
     */
    public function shouldRender(): bool
    {
        return (bool) $this->bannerZone;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $this->items = Cache::remember("site-banner-slider-{$this->bannerZone->code}", HOUR, function () {
            $items = collect();
            \Amplify\System\Cms\Models\Banner::where(['enabled' => true, 'banner_zone_id' => $this->bannerZone->getKey()])
                ->orderBy('lft')
                ->get()
                ->each(function ($item) use (&$items) {
                    $this->push($item, $items);
                });

            return $items;
        });

        return view('cms::banner-slider');
    }

    private function push($item, Collection &$items)
    {
        $slide = new \stdClass;

        $slide->id = $item->id;
        $slide->title = $item->name;
        $slide->background_image = $item->background_image;
        $slide->image = $item->image;
        $slide->content = $item->content;
        $slide->display_button = $item->has_button;
        $slide->button_label = $item->button_title;
        $slide->link = $item->button_link;
        $slide->target = $item->open_new_tab ? '_blank' : '_self';
        $slide->alignment = $item->alignment;
        $slide->style = $item->button_style;
        $slide->first_column = $item->slider_ratio ?? 5;
        $slide->last_column = (12 - $item->slider_ratio);
        $slide->text_align = $item->text_alignment;
        $slide->display_image = ($item->image != null);
        $slide->display_title = $item->has_heading;
        $slide->display_content = $item->has_content;
        $slide->background_type = $item->background_type;
        $slide->has_forground = ($slide->display_image || $slide->display_title || $slide->display_content);

        if (! $slide->display_image || ! $slide->image) {
            $slide->first_column = 12;
            $slide->last_column = 0;
        }

        if ($item->image_alignment == 'left') {
            $slide->image_align = 'start';
        } elseif ($item->image_alignment == 'right') {
            $slide->image_align = 'end';
        } else {
            $slide->image_align = 'center';
        }

        $items->push($slide);
    }

    public function sliderControls(): bool|string
    {
        return json_encode([
            'lazyLoad' => true,
            'margin' => 0,
            'animateIn' => 'fadeIn',
            'animateOut' => 'fadeOut',
            'nav' => $this->nav,
            'dots' => $this->dots,
            'autoplayHoverPause' => $this->pauseOnHover,
            'loop' => $this->loop,
            'autoplay' => $this->autoplay,
            'autoplayTimeout' => 3000,
            'items' => 1,
        ]);
    }

    public function displayDots(): bool
    {
        return $this->dots;
    }

    public function displayOnMobile(): bool
    {
        return $this->showOnMobile;
    }

    public function hasAnyVideoItem(): bool
    {
        return $this->items->contains('background_type', '=', 'video');
    }
}
