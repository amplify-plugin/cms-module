<li class="@if ($child->has_children) has-children @endif @if ($child->is_active) active @endif">
    <a href="{{ $child->has_children? 'javascript:void(0)' : $child->url }}"
       class="{{ $child->css_class }}"
       style="{{ $child->css_style }}"
       target="{{ $child->target }}">
        @if($showIcon && $child->icon != null)
            <i @class([$child->icon, "d-inline-block align-middle"])></i>
        @endif
        {{ $child->title }}
    </a>
    @if ($child->has_children)
        <ul class="sub-menu">
            @foreach ($child->children as $grandchild)
                <li class="@if ($grandchild->is_active) active @endif">
                    <a href="{{ $grandchild->url }}"
                       class="{{ $grandchild->css_class }}"
                       style="{{ $grandchild->css_style }}"
                       target="{{ $grandchild->target }}">
                        @if($showIcon && $grandchild->icon != null)
                            <i @class([$grandchild->icon, "d-inline-block align-middle"])></i>
                        @endif
                        {{ $grandchild->title }}
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
</li>
