@pushonce("footer-script")
    <script>
            const heroSlider = document.querySelectorAll('.banner-slider .item');
            const innerImage = document.querySelectorAll('.inner-image-div');

            function updateBannerItemHeight() {
                let heroHeight = []
                let itemsHeight = []

                innerImage.forEach((item, index) => {
                    itemsHeight.push(item.offsetHeight)
                })

                const imageHeight = Math.max(...itemsHeight);

                innerImage.forEach((item, index) => {
                    item.style.height = `${imageHeight}px`;
                })

                heroSlider.forEach((item, index) => {
                    heroHeight.push(item.offsetHeight)
                })

                const mainHeight = Math.max(...heroHeight);

                heroSlider.forEach((item, index) => {
                    item.style.height = `${mainHeight}px`;
                })

            }

            window.addEventListener("load", (event) => { updateBannerItemHeight(); });

            window.addEventListener("resize", (event) => { updateBannerItemHeight(); });
    </script>
@endpushonce

@if ($hasAnyVideoItem())
    @pushonce("plugin-script")
        <script src="//vjs.zencdn.net/8.6.1/video.min.js"></script>
    @endpushonce
@endif
<div {!! $htmlAttributes !!}>
    <section class="hero-slider @if ($displayOnMobile()) d-block @else d-none d-sm-block @endif">
        <div class="owl-carousel large-controls @if ($displayDots()) dots-inside @endif"
            data-owl-carousel="{{ $sliderControls() }}">
            @foreach ($items as $item)
                <div class="item" style="background-color:#eeeeee; background-size: @if ($item->has_forground) cover @else contain @endif; background-repeat: no-repeat; background-position:bottom;
                     @if ($item->has_forground) background-image: url('{{ $item->background_image }}'); @endif">
                    @if (!$item->display_button && $item->link != null)
                            <a href="{{ $item->link }}" target="{{ $item->target }}">
                    @endif
                    @if($item->background_image)
                    <div class="w-100">
                        @if ($item->background_type == 'image')
                            <img class="d-block img-fluid h-100 {{ $backgroundImgClass ?? '' }}" src="{{ $item->background_image }}"
                                alt="{{ $item->title }}">
                        @elseif($item->background_type == 'video')
                            <video class="video-js" id="background-video-{{ $item->id ?? '' }}" data-setup="{}" preload="auto" muted autoplay loop>
                                <source src="{{ $item->background_image }}" type="video/mp4" />
                                <p class="vjs-no-js">To view this video please enable JavaScript, and consider upgrading to a web browser that <a href="https://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a></p>
                            </video>
                        @endif
                    </div>
                    @endif
                    @if($item->has_forground)
                    <div class="{{ $fullWidth ? 'container-fluid' : 'container' }} h-100" style="position: absolute; top: 0; left: 0; right: 0;">
                        <div class="row h-100 align-items-center slider-foreground {{ $item->alignment == 'right' ? 'flex-row-reverse' : '' }}"
                            style="row-gap: 24px">
                            <div class="col-md-{{ $item->first_column }}">
                                <div
                                    class="slider-content text-{{ $item->text_align }} @if (!$item->display_title && !$item->display_content) d-none @endif">
                                    <div class="from-bottom">
                                        <h1
                                            class="text-body text-bold @if (!$item->display_title) d-none @endif mb-3">
                                            {{ $item->title }}
                                        </h1>
                                        <div
                                            class="text-body text-normal @if (!$item->display_content) d-none @endif mb-3 pb-1">
                                            {!! $item->content !!}
                                        </div>
                                    </div>
                                    @if ($item->display_button)
                                        <a class="action-button d-md-inline-block btn btn-{{ $item->style }} scale-up"
                                            href="{{ $item->link }}" title="{{ $item->button_label }}"
                                            style="white-space: normal" target="{{ $item->target }}">
                                            {{ $item->button_label }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                            @if ($item->last_column != 0)
                                <div class="col-md-{{ $item->last_column }}">
                                    <div
                                        class="d-flex inner-image-div justify-content-center justify-content-md-{{ $item->image_align }} overflow-hidden">
                                        <img class="d-block img-fluid h-100" src="{{ $item->image }}"
                                            alt="{{ $item->title }}">
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    @endif
                    @if (!$item->display_button && $item->link != null)
                        </a>
                   @endif
                </div>
            @endforeach
        </div>
    </section>
</div>
<style>
    .hero-slider .item {
        height: {{ $height }} !important;
        min-height: {{ $height }} !important;
    }

    @media (max-width: 769px) {
        .hero-slider .item {
            min-height: auto !important;
        }

    }

    @media (max-width: 426px) {
        .hero-slider .slider-foreground {
            padding-top: 3rem !important;
            padding-bottom: 3rem !important;
        }
    }
</style>
