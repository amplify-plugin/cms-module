<div class="item">
    @if(strlen($button_link) > 0 && strlen($button_title) == 0)
        <a href="{{ $button_link ?? '#' }}">
            @endif
            <img src='{{ $src ?? '' }}' alt="{{ $alt ?? '' }}"
                 class="position-absolute banner-item-img"/>
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="text-white hero-text d-flex justify-content-center flex-column">
                            <h3>
                                {{ $title ?? '' }}
                            </h3>
                            <p>
                                {{ $description ?? '' }}
                            </p>
                            <a href="{{ $button_link ?? '' }}"
                               class="rounded {{ $button_background_color ?? 'bg-white' }} {{ $button_text_color ?? 'text-primary' }}">{{ $button_title ?? '' }}</a>
                        </div>
                    </div>
                </div>
            </div>
            @if(strlen($button_link) > 0 && strlen($button_title) == 0)
        </a>
    @endif
</div>
