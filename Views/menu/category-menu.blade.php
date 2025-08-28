<ul {!! $htmlAttributes !!}>
    @foreach($categories as $category)
        <li @class(['has-children' => $category->hasSubCategories()])>
            <a href="{{ frontendShopURL($category->getSEOPath()) }}">
                {{ $category->getName() }}
                @if($showProductCount)
                    <span class="text-muted">
                        ({{ $category->getProductCount() }})
                    </span>
                @endif
            </a>
            @if($category->hasSubCategories())
                <x-menu.category-menu :menu="null" :category="$category" :show-product-count="$showProductCount"/>
            @endif
        </li>
    @endforeach
</ul>
