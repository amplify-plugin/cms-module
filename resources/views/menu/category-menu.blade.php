<ul {!! $htmlAttributes !!}>
    @foreach($categories as $category)
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
                                      :show-product-count="$showProductCount"
                                      :sub-menu-class="$onMobileMenu() ? 'offcanvas-submenu' : 'sub-menu'"/>
            @endif
        </li>
    @endforeach
</ul>
