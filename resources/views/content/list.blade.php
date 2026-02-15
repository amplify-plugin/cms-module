<style>
    figcaption {
        opacity: 0.7;
        transition: transform 0.4s ease;
    }

    figure > img {
        transform: scale(0.75);
    }

    figure:hover > figcaption {
        opacity: 1;
    }

    figure:hover > img {
        transform: scale(1);
    }
</style>
<div {!! $htmlAttributes !!}>
    @if(!$contents->isEmpty())
        <div class="isotope-grid grid-no-gap cols-{{ $itemPerLIne }} mb-2">
            <div class="gutter-sizer"></div>
            <div class="grid-sizer"></div>
            @foreach($contents as $content)
                <div class="grid-item p-2">
                    <figure class="figure text-center">
                        <img src="{{ $content->cover_image }}" alt="{{ $content->name }}" decoding="async">
                        <figcaption
                                class="figure-caption bg-secondary">
                            <a href="{{ route('frontend.contents.show', $content->slug) }}"
                               class="d-block text-decoration-none text-center">
                                {{ $content->name }}
                            </a>
                        </figcaption>
                    </figure>
                </div>
            @endforeach
        </div>
    @endif

    {{ $contents->links() }}
</div>