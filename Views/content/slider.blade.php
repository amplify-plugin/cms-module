@props (['header','carouselOptions' => json_encode([
            'lazyLoad' => true,
            'animateIn' => 'fadeIn',
            'animateOut' => 'fadeOut',
            'dots' => true,
            'nav' => true,
            'margin' => 30,
            'responsive' => [
                '0' => ['items' => 1],
                '576' => ['items' => 2],
                '768' => ['items' => 3],
                '991' => ['items' => 4],
                '1200' => ['items' => 4],
            ],
        ])
        ])
@php
    $products = collect([[]]);
@endphp
<div {!! $htmlAttributes !!}>
    @if(!$products->isEmpty())
        @if(strlen($header) > 0)
            <h3 class="product-slider-title">
                {{ __($header) }}
            </h3>
        @endif

        <div class="owl-carousel" data-owl-carousel="{{ $carouselOptions }}">
            <!-- Product-->
            @for ($i = 0; $i < 18; $i++)
                <div class="grid-item">
                    <div class="card"><img class="card-img-top" src="{{ asset('assets/img/01.png') }}" alt="Card image">
                        <div class="card-body">
                            <h4 class="card-title">Card title</h4>
                            <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                            <a class="btn btn-primary btn-sm" href="#">Go somewhere</a>
                        </div>
                    </div>
                </div>
            @endfor
        </div>
    @endif
</div>
