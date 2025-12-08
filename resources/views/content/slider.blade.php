<div {!! $htmlAttributes !!}>
    @if(!$contents->isEmpty())
        @if(strlen($header) > 0)
            <h3 class="content-slider-title">
                {{ __($header) }}
            </h3>
        @endif

        <div class="owl-carousel" data-owl-carousel="{{ $carouselOptions() }}">
            @foreach ($contents as $content)
                <div class="grid-item">
                    <img class="card-img-top" src="{{ $content->cover_image }}" alt="Card image">
                    <div class="card-body">
                        <h4 class="card-title">{{ $content->name }}</h4>
                        <p class="card-text">{{ $content->summary }}</p>
                        <a class="btn btn-primary btn-block"
                           href="{{ route('frontend.contents.show', $content->slug) }}">Read more</a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
