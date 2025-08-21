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
{{--                                <li class="col-md-{{ $child->menu_column_size }} mega-li">--}}
                                    <x-dynamic-component :component="$child->menu_type" :menu="$child"/>
{{--                                </li>--}}
                            @else
                                <li class="@if ($child->has_children) has-children @endif @if ($child->is_active) active @endif">
                                    <a href="{{ $child->has_children? 'javascript:void(0)' : $child->url }}"
                                       class="{{ $child->css_class }}"
                                       style="{{ $child->css_style }}"
                                       target="{{ $child->target }}">
                                        @if($showIcon && $child->icon != null)
                                            <i @class([$child->icon, "d-inline-block align-middle"])></i>
                                        @endif
                                        {{ $child->title }}
                                    </a>
                                    @if ($child->has_children)
                                        <ul class="sub-menu">
                                            @foreach ($child->children as $grandchild)
                                                <li class="@if ($grandchild->is_active) active @endif">
                                                    <a href="{{ $grandchild->url }}"
                                                       class="{{ $grandchild->css_class }}"
                                                       style="{{ $grandchild->css_style }}"
                                                       target="{{ $grandchild->target }}">
                                                        @if($showIcon && $grandchild->icon != null)
                                                            <i @class([$grandchild->icon, "d-inline-block align-middle"])></i>
                                                        @endif
                                                        {{ $grandchild->title }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </li>
                            @endif
                        @endforeach
                    </ul>
                @endif
            </li>
        @endforeach
    </ul>
</nav>
