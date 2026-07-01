<li @class(['has-children' => $menu->has_children, 'active' => $menu->is_active])>
    <span>
        <a @class([$menu->css_class])
           href="{{ $menu->has_children ? 'javascript:void(0)' : $menu->url }}"
           style="{{ $menu->css_style }}">
            <span>
                @if($showIcon && $menu->icon != null)
                    <i @class([$menu->icon, "d-inline-block align-middle"])></i>
                @endif
                {{ $menu->title }}
            </span>
        </a>
        @if ($menu->has_children)
            <span class="sub-menu-toggle"></span>
        @endif
    </span>
    @if($menu->type == 'categories')
        <x-menu.category-menu
                :menu="$menu"
                :show-product-count="$menu->display_product_count ?: false"
                sub-menu-class="offcanvas-submenu"/>

    @elseif ($menu->has_children)
        <ul class="offcanvas-submenu">
            @foreach ($menu->children as $child)
                @if ($menu->type == 'mega-menu')
                    <li class="col-md-{{ $child->menu_column_size }}">
                        <x-dynamic-component :component="$child->menu_type" :menu="$child"
                                             :submenu="false"/>
                    </li>
                @else
                    <li class="@if ($child->is_active) active @endif">
                        <a class="{{ $child->css_class }}" href="{{ $child->url }}"
                           style="{{ $child->css_style }}">
                            @if($showIcon && $child->icon != null)
                                <i @class([$child->icon, "d-inline-block align-middle"])></i>
                            @endif
                            {{ $child->title }}
                        </a>
                    </li>
                @endif
            @endforeach
        </ul>
    @endif
</li>