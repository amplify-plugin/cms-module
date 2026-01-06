<div {!! $htmlAttributes !!}>
    @pushonce('plugin-style', 'icon-fonts')
        <link href="{{ asset('packages/bootstrap-iconpicker/icon-fonts/font-awesome-5.12.0-1/css/all.min.css') }}" rel="stylesheet" type="text/css">
    @endpushonce
    <div class="sidebar-toggle position-left">
        <i class="icon-filter pe-7s-user" style="font-size: 2rem;"></i>
    </div>
    <aside class="sidebar sidebar-offcanvas position-left">
    <span class="sidebar-close">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
             class="feather feather-x">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
    </span>
        <x-menu.account-sidebar.profile :account="$account"/>
        <nav class="list-group cs-account-sidebar">
            @foreach ($menus as $menu)
                <div
                    class="@if ($menu->has_children) cs-hover @endif user-sidebar">
                    <a style="{{ $menu->css_style }}"
                       class="@if ($menu->is_active) active @endif list-group-item {{ $menu->css_class }}"
                       href="{{ $menu->has_children? 'javascript:void(0)' : $menu->url }}" target="{{ $menu->target }}">
                        @if($showIcon && $menu->icon != null)
                            <i @class([$menu->icon, "d-inline-block align-middle"])></i>
                        @endif
                        {{ $menu->title }}
                    </a>
                    @if (count($menu->children) > 0)
                        <nav class="list-group list-group-sub">
                            @foreach ($menu->children as $child)
                                <a style="{{ $child->css_style }}"
                                   class="@if ($child->is_active) active @endif list-group-item list-group-sub-item {{ $child->css_class }}"
                                   href="{{ $child->url }}" target="{{ $child->target }}">
                                    @if($showIcon && $child->icon != null)
                                        <i @class([$child->icon, "d-inline-block align-middle"])></i>
                                    @endif
                                    {{ $child->title }}
                                </a>
                            @endforeach
                        </nav>
                    @endif
                </div>
            @endforeach
        </nav>
    </aside>
</div>
<style>
    .cs-account-sidebar .cs-hover:has(.active)::after {
        transform: rotate(90deg);
    }

    .cs-account-sidebar .cs-hover .list-group-sub:has(.active) {
        max-height: 500px;
        opacity: 1;
        visibility: visible;
    }
</style>
