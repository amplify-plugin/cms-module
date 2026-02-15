<style>
    figcaption {
        opacity: 0.7;
    }

    figure:hover {
        figcaption {
            opacity: 1;
        }
    }
</style>
<div {!! $htmlAttributes !!}>
    @if(!$contents->isEmpty())
        <div class="isotope-grid cols-{{ $itemPerLIne }} mb-2">
            <div class="gutter-sizer"></div>
            <div class="grid-sizer"></div>
            @foreach($contents as $content)
                <div class="grid-item">
                    <figure class="figure mb-3">
                        <img src="{{ $content->cover_image }}" alt="{{ $content->name }}" decoding="async">
                        <figcaption
                                class="figure-caption bg-secondary">
                            <a href="{{ route('contents.show', $content->slug) }}"
                               class="d-block text-decoration-none text-center font-weight-bold">
                                {{ $content->name }}
                            </a>
                        </figcaption>
                    </figure>
                </div>
            @endforeach
        </div>
    @endif

    <div class="d-block mt-3 w-100">
        {{ $contents->withQueryString()->links() }}
    </div>
</div>