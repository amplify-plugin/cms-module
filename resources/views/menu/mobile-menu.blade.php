@pushonce('off-canvas-menu')
    @pushonce('plugin-style', 'icon-fonts')
        <link href="{{ asset('packages/bootstrap-iconpicker/icon-fonts/font-awesome-5.12.0-1/css/all.min.css') }}" rel="stylesheet" type="text/css">
    @endpushonce
    <div id="mobile-menu" {!! $htmlAttributes !!}>
        <div class="offcanvas-header">
            <h3 class="offcanvas-title text-uppercase font-weight-bolder text-light text-center">Main Menu</h3>
        </div>
        <nav class="offcanvas-menu">
            <ul class="menu">
                @foreach ($menus as $menu)
                    <x-menu.mobile-menu-item :menu="$menu" :show-icon="$showIcon"/>
                @endforeach
            </ul>
        </nav>
    </div>
@endpushonce
