<div {!! $htmlAttributes !!}>
    <div class="d-flex justify-content-between gap-3">
        <div class="text-left">
            <a class="btn btn-outline-secondary btn-sm @if($previous == null) disabled @endif"
               @if($previous == null) disabled @endif
               href="{{ $previous != null ? route('frontend.contents.show', $previous->slug) : '#' }}"
            >
                <i class="icon-arrow-left"></i>&nbsp;Prev
            </a>
        </div>
        {!!  $slot ?? '' !!}
        <div class="text-right">
            <a class="btn btn-outline-secondary btn-sm @if($next == null) disabled @endif"
               @if($next == null) disabled @endif
               href="{{ $next != null ? route('frontend.contents.show', $next->slug) : '#' }}">
                Next&nbsp;<i class="icon-arrow-right"></i>
            </a>
        </div>
    </div>
</div>
