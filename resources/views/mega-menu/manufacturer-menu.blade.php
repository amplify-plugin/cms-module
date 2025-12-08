<li {!! $htmlAttributes !!}>
    @if ($showTitle())
        <span class="mega-menu-title">{{ $megaMenuTitle() }}</span>
    @endif

    <ul class="sub-menu"
        style="display: grid !important; overflow-y: auto; max-height: {{ mega_menu_max_height() }} !important; grid-template-columns: repeat( {{ $megaMenuColumns() }}, minmax(0, 1fr));">
        @forelse($manufacturers as $manufacturer)
            <li style="text-align: left;">
                <a href="{{ frontendShopURL("-Manufacturer:{$manufacturer->code}") }}">
                <span>
                    {{$manufacturer->name}}
                </span>
                </a>
            </li>
        @empty
            <li style="text-align: left; width: 100%; height: 100%; padding: 1rem">
                <div class="alert alert-danger fade show text-center">
                    <i class="icon-ban"></i>
                    No manufacturer exist in the system.
                </div>
            </li>
        @endforelse
    </ul>
</li>
