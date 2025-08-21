<li {!! $htmlAttributes !!}>
    @if ($showTitle())
        <span class="mega-menu-title">{{ $megaMenuTitle() }}</span>
    @endif
    <div style="max-height: {{ mega_menu_max_height() }} !important; overflow-y: auto;">
        {!! $html !!}
    </div>
</li>
