@pushonce('off-canvas-menu')
    @pushonce('plugin-style')
        <link href="{{ asset('packages/bootstrap-iconpicker/icon-fonts/font-awesome-5.12.0-1/css/all.min.css') }}" rel="stylesheet" type="text/css">
    @endpushonce
    <div id="mobile-menu" {!! $htmlAttributes !!}>
        <div class="offcanvas-header">
            <h3 class="offcanvas-title text-uppercase font-weight-bolder text-light text-center">Main Menu</h3>
        </div>
        <nav class="offcanvas-menu">
            <ul class="menu">
                @foreach ($menus as $menu)
                    <li
                        class="@if ($menu->has_children) has-children @endif @if ($menu->is_active) active @endif">
                    <span>
                        <a class="{{ $menu->css_class }}"
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
                        @if ($menu->has_children)
                            <ul class="offcanvas-submenu">
                                @foreach ($menu->children as $child)
                                    @if ($menu->type == 'mega-menu')
                                        <li class="col-md-{{ $child->menu_column_size }}">
                                            <x-dynamic-component :component="$child->menu_type" :menu="$child"
                                                                 :submenu="false" />
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
                @endforeach
            </ul>
        </nav>
    </div>
@endpushonce
