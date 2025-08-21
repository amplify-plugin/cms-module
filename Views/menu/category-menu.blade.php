<ul {!! $htmlAttributes !!}>
    @foreach($categories as $category)
        <li>
            <a href="{{ frontendShopURL($category->getSEOPath()) }}">
                {{ $category->getName() }}
                <span class="text-muted">
                    ({{ $category->getProductCount() }})
                </span>
            </a>
            @if($category->hasSubCategories())
                <x-menu.category-menu :menu="null" :category="$category" />
            @endif
        </li>
    @endforeach
</ul>
