<?php

namespace Amplify\System\Cms\Widgets\Banner;

use Amplify\System\Cms\Models\BannerZone;
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

    public Collection $items;
    private string $backgroundImage;
    private ?BannerZone $bannerZone;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string        $bannerZone = 'banner-slider',
        public bool   $nav = false,
        public bool   $dots = true,
        public bool   $loop = false,
        public bool   $autoplay = false,
        public bool   $pauseOnHover = false,
        public bool   $showOnMobile = false,
        public bool   $fullWidth = true,
        public string $height = '200px',
        string        $backgroundImage = '',
        public string $backgroundImgClass = '',
        public int    $autoplayTimeout = 500000
    )
    {
        parent::__construct();
        $this->items = collect();
        $this->backgroundImage = ($backgroundImage != '') ? $backgroundImage : assets_image('img/banner-slider-bg.jpg');
        $this->bannerZone = BannerZone::whereCode($bannerZone)->first();

    }

    /**
     * Whether the component should be rendered
     */
    public function shouldRender(): bool
    {
        return (bool)$this->bannerZone;
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

        return view('cms::banner.banner-slider');
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

        if (!$slide->display_image || !$slide->image) {
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
