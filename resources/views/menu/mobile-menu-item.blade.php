<li @class(['has-children' => $menu->has_children, 'active' => $menu->is_active])>
    <span>
        <a @class([$menu->css_class]) target="{{ $menu->target ?? '_self' }}"
           href="{{ $menu->has_children ? 'javascript:void(0)' : $menu->url }}"
           @if($menu->target == '_blank') rel="noopener noreferrer nofollow"
           aria-describedby="External {{ $menu->title }} Link" @endif
           style="{{ $menu->css_style }}">
            <span>
                @if($showIcon && $menu->icon != null)
                    <i @class([$menu->icon, "d-inline-block align-middle"])></i>
                @endif
                {{ $menu->title }}
                    @includeWhen($menu->url_type == 'external' && !$menu->has_children, "cms::menu.redirect-icon")
            </span>
        </a>
        @if ($menu->has_children)
            <span class="sub-menu-toggle"></span>
        @endif
    </span>
    @if($menu->type == 'categories')
        <x-menu.category-menu
                :menu="$menu"
                :max-items="$menu->category_limit"
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
                    <li @class(['active' => $child->is_active])>
                        <a @class([$child->css_class])
                           target="{{ $child->target ?? '_self' }}"
                           href="{{ $child->url }}"
                           @if($child->target == '_blank') rel="noopener noreferrer nofollow"
                           aria-describedby="External {{ $child->title }} Link" @endif
                           style="{{ $child->css_style }}">
                            @if($showIcon && $child->icon != null)
                                <i @class([$child->icon, "d-inline-block align-middle"])></i>
                            @endif
                            {{ $child->title }}
                            @includeWhen($child->url_type == 'external', "cms::menu.redirect-icon")
                        </a>
                    </li>
                @endif
            @endforeach
        </ul>
    @endif
</li>