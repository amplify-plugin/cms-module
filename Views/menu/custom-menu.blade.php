<section {!! $htmlAttributes !!}>
    @pushonce('plugin-style')
        <link href="{{ asset('packages/bootstrap-iconpicker/icon-fonts/font-awesome-5.12.0-1/css/all.min.css') }}" rel="stylesheet" type="text/css">
    @endpushonce
    @if($is_show_title)
        <h3 class="menu-group-title">{{ $menuGroupTitle() }}</h3>
    @endif

    <ul class="custom-menu-list list-group cs-account-sidebar">
        @foreach($menus as $menu)
            <li class="@if($menu->is_active) active @endif custom-menu-list-item @if($menu->has_children) cs-hover pl-0 @endif">
                <a style="{{ $menu->css_style }}"
                   class="@if($menu->is_active) active @endif {{ $menu->css_class }} custom-menu-list-link"
                   href="{{ $menu->has_children? 'javascript:void(0)' : $menu->url }}"
                   target="{{ $menu->target }}">
                    @if($showIcon && $menu->icon != null)
                        <i @class([$menu->icon, "d-inline-block align-middle"])></i>
                    @endif
                    {{ $menu->title }}
                </a>
                @if(count($menu->children) > 0)
                    <ul class="list-group-sub">
                        @foreach($menu->children as $child)
                            <a style="{{ $child->css_style }}"
                               class="@if($child->is_active) active @endif list-group-sub-item {{ $child->css_class }}"
                               href="{{ $child->url }}"
                               target="{{ $child->target }}">
                                @if($showIcon && $child->icon != null)
                                    <i @class([$child->icon, "d-inline-block align-middle"])></i>
                                @endif
                                {{ $child->title }}
                            </a>
                        @endforeach
                    </ul>
                @endif
            </li>
        @endforeach
    </ul>
    {{ $slot }}
</section>
