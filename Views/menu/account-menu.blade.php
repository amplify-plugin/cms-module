<div id="show-account" data-title="{{ !empty($contact?->name) ? $contact?->name : 'Sign In' }}" {!! $htmlAttributes !!}>
    @pushonce('plugin-style')
        <link href="{{ asset('packages/bootstrap-iconpicker/icon-fonts/font-awesome-5.12.0-1/css/all.min.css') }}" rel="stylesheet" type="text/css">
    @endpushonce

    @if(! empty($avatar))
        <img class="header-avatar" src="{{$avatar}}" alt="{{ $contact->name }}">
    @else
        <i class="icon-head"></i>
    @endif

    <ul class="toolbar-dropdown">
        <span class="text-dark toolbar-close">
            <i class="icon-cross"></i>
        </span>
        <x-menu.account-menu.profile />
        @foreach($menus as $menu)
            <li class="@if($menu->is_active) active @endif @if($menu->has_children) has-children cs-hover @endif">
                <a href="{{ $menu->has_children ? 'javascript:void(0)' : $menu->url }}"
                   class="{{ $menu->css_class }}"
                   style="{{ $menu->css_style }}"
                   target="{{ $menu->target }}">
                    @if($showIcon && $menu->icon != null)
                        <i @class([$menu->icon, "d-inline-block align-middle"])></i>
                    @endif
                    {{ $menu->title }}
                </a>
                @if($menu->has_children)
                    <ul class="sub-menu">
                        @foreach($menu->children as $child)
                            <li class="@if($child->is_active) active @endif">
                                <a href="{{ $child->url }}"
                                   class="{{ $child->css_class }}"
                                   style="{{ $child->css_style }}"
                                   target="{{ $child->target }}">
                                    @if($showIcon && $child->icon != null)
                                        <i @class([$child->icon, "d-inline-block align-middle"])></i>
                                    @endif
                                    {{ $child->title }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </li>
        @endforeach
        <x-menu.account-menu.logout/>
    </ul>
</div>
