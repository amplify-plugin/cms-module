<li {!! $htmlAttributes !!}>
    @if ($showTitle())
        <span class="mega-menu-title">{{ $megaMenuTitle() }}</span>
    @endif
    <ul class="sub-menu"
        style="display: grid; overflow-y: auto; max-height: {{ mega_menu_max_height() }} !important; grid-template-columns: repeat( {{ $megaMenuColumns() }}, minmax(0, 1fr));">
        @forelse ($subCategories as $category)
            <li class="text-left">
                <a href="{{ frontendShopURL($category->getSEOPath()) }}">
                    <span>
                        {{ $category->getName() }}
                        @if(!empty($category->getProductCount()) && $category->getProductCount() != '-1'  &&  $category->getProductCount() > -1)
                            ({{ $category->getProductCount() }})
                        @endif
                    </span>
                </a>
            </li>
        @empty
            <li style="text-align: left; width: 100%; height: 100%; padding: 1rem">
                <div class="alert alert-danger fade show text-center">
                    <i class="icon-ban"></i>
                    No subcategories exist in this category.
                </div>
            </li>
        @endforelse
    </ul>
</li>
