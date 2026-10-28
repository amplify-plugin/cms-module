<ul {!! $htmlAttributes !!}>
    @foreach($categories as $index => $category)
        @if(!$maxItems!= null && $index == $maxItems)
            <li class="browse">
                @if($onMobileMenu())
                    <span>
            @endif
            <a href="{{ $menuHeader['link'] }}">
                See All {{ $menuHeader['label'] }}
            </a>

            @if($onMobileMenu())
                </span>
                @endif
            </li>
            @break
        @endif

        <li @class(['has-children' => $category->hasSubCategories()])>
            @if($onMobileMenu())
                <span>
            @endif
            <a href="{{ frontendShopURL($category->getSEOPath()) }}">
                {{ $category->getName() }}

                @if($showProductCount)
                    <span class="text-muted">
                        ({{ $category->getProductCount() }})
                    </span>
                @endif

            </a>

            @if($onMobileMenu() && $category->hasSubCategories())
                <span class="sub-menu-toggle"></span>
            @endif

            @if($onMobileMenu())
                </span>
            @endif

            @if($category->hasSubCategories())
                <x-menu.category-menu :menu="null" :category="$category"
                                      :max-items="$maxItems"
                                      :show-product-count="$showProductCount"
                                      :sub-menu-class="$onMobileMenu() ? 'offcanvas-submenu' : 'sub-menu'"/>
            @endif
        </li>
    @endforeach
</ul>
