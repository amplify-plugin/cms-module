<div {!! $htmlAttributes !!}>
    <div class="banner-wrapper {{ $bannerClass }}"
         data-background="{{ $backgroundImage }}"
         style="background-image: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0)), url(&quot;{{ $backgroundImage }}&quot;); height: {{$height}}px">
        <div class="container banner-inner">
            <div class="row">
                <div class="col-md-12">
                    <h3 class="text-center inner-text p-2">
                        @foreach ($bannerContent ?? [] as $content)
                            <span class="text-{{ $content['title-color'] }}">{{ $content['title'] }} </span>

                            @if (! empty($content['suffix']))
                                <span class="text-{{ $content['suffix-color'] }}">{{ $content['suffix'] }}</span>
                            @endif
                        @endforeach
                    </h3>
                </div>
            </div>
        </div>
    </div>
</div>
