<li {!! $htmlAttributes !!}>
    @if ($showTitle())
        <span class="mega-menu-title">{{ $megaMenuTitle() }}</span>
    @endif

    <ul class="sub-menu"
        style="overflow-y: auto; max-height: {{ mega_menu_max_height() }} !important;">
        @foreach ($links as $link)
            <li  style="text-align: left;">
                <a href="{{ $link->link }}">
                    <span>{{ $link->name }}</span>
                </a>
            </li>
        @endforeach
    </ul>
</li>
