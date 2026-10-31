<li class="@if ($child->has_children) has-children @endif @if ($child->is_active) active @endif">
    <a href="{{ $child->has_children? 'javascript:void(0)' : $child->url }}"
       class="{{ $child->css_class }}"
       style="{{ $child->css_style }}"
       target="{{ $child->target }}"
       @if($child->target == '_blank') rel="noopener noreferrer nofollow"
       aria-describedby="External {{ $child->title }} Link" @endif
    >
        @if($showIcon && $child->icon != null)
            <i @class([$child->icon, "d-inline-block align-middle"])></i>
        @endif
        {{ $child->title }}
        @includeWhen($child->url_type == 'external' && !$child->has_children, "cms::menu.redirect-icon")
    </a>
    @if ($child->has_children)
        <ul class="sub-menu">
            @foreach ($child->children as $grandchild)
                <li class="@if ($grandchild->is_active) active @endif">
                    <a href="{{ $grandchild->url }}"
                       class="{{ $grandchild->css_class }}"
                       style="{{ $grandchild->css_style }}"
                       target="{{ $grandchild->target }}"
                       @if($grandchild->target == '_blank') rel="noopener noreferrer nofollow"
                       aria-describedby="External {{ $child->title }} Link" @endif
                    >
                        @if($showIcon && $grandchild->icon != null)
                            <i @class([$grandchild->icon, "d-inline-block align-middle"])></i>
                        @endif
                        {{ $grandchild->title }}
                        @includeWhen($grandchild->url_type == 'external', "cms::menu.redirect-icon")
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
</li>
