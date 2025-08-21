<nav {!! $htmlAttributes !!}>
    @pushonce('plugin-style')
        <link href="{{ asset('packages/bootstrap-iconpicker/icon-fonts/font-awesome-5.12.0-1/css/all.min.css') }}" rel="stylesheet" type="text/css">
    @endpushonce
    <ul>
        @foreach ($menus as $menu)
            <li class="@if ($menu->is_active) active @endif @if ($menu->type == 'mega-menu') has-megamenu @endif">
                <a href="{{ $menu->has_children? 'javascript:void(0)' : $menu->url }}"
                   class="{{ $menu->css_class }}"
                   style="{{ $menu->css_style }}"
                   target="{{ $menu->target }}">
                    <span>
                        @if($showIcon && $menu->icon != null)
                            <i @class([$menu->icon, "d-inline-block align-middle"])></i>
                        @endif
                        {{ $menu->title }}
                    </span>
                </a>
                @if ($menu->has_children)
                    <ul class="@if($menu->type == 'mega-menu') mega-menu @else sub-menu @endif">
                        @foreach ($menu->children as $child)
                            @if ($menu->type == 'mega-menu')
                                    <x-dynamic-component :component="$child->menu_type" :menu="$child"/>
                            @elseif($menu->type == 'categories')
                                <x-menu.category-menu :menu="$menu"/>
                            @else
                                <x-menu.nested-menu :menu="$child"/>
                            @endif
                        @endforeach
                    </ul>
                @endif
            </li>
        @endforeach
    </ul>
</nav>
