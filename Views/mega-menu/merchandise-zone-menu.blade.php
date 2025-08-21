<li {!! $htmlAttributes !!}>
    @if ($showTitle())
        <span class="mega-menu-title">{{ $megaMenuTitle() }}</span>
    @endif
    <div class="row"
         style="overflow-y: auto; max-height: {{ mega_menu_max_height() }} !important; margin-left: auto">
        @foreach ($products ?? [] as $product)
            <div class="col-md-{{ $product->column_size }}" style="padding-inline: 8px">
                <div class="mb-3 p-3 product-card mega-menu-card">
                    @if ($product->display_image)
                        <a class="product-thumb" style="height: 100px; width:100%;"
                           href="{{ $product->url }}">
                            <img
                                style="height: 100%; width:100%; object-fit: contain"
                                src="{{ $product->image }}"
                                alt="{{ $product->name }}" />
                        </a>
                    @endif
                    @if ($product->display_name)
                        <a href="{{ $product->url }}" class="text-decoration-none">
                            <h3 class="product-title">
                                {{ $product->name }}
                            </h3>
                        </a>
                    @endif
                    @if ($product->display_price)
                        <h4 class="product-price"> {{ $product->price }}</h4>
                    @endif
                    @if ($product->display_description)
                        <p class="cs-truncate-1">
                            {{ $product->description }}
                        </p>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</li>
