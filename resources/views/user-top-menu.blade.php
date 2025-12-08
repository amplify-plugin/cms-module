<div {!! $htmlAttributes !!}>
<div id="show-account" class="account">
    <i class="icon-head"></i>
    <ul class="toolbar-dropdown">
        <span class="text-dark toolbar-close">
            <i class="icon-cross"></i>
        </span>
        @if(customer_check())
            <li class="sub-menu-user">
                <div class="user-ava">
                    <img
                        src="{{$contact->profile_image ?? generateUserAvatar($contact->name) }}"
                        alt="{{ $contact->name }}">
                </div>
                <div class="user-info">
                    <h6 class="user-name text">{{ $contact->name }}</h6>
                    <span class="text-xs text-muted">{{ $contact->customer->customer_name }}</span>
                </div>
            </li>
        @endif

        @if(!empty($menuGroup))
            @foreach($menuGroup->menus as $menuItem)
                @if($menuItem->onlyAuth())
                    @if(customer_check())
                        <li @class(['active' => request()->fullUrl() == $menuItem->link(), 'has-children cs-hover' => (count($menuItem->children) > 0)])>
                            <a href="{{ $menuItem->link() }}">
                                {{ $menuItem->displayName() }}
                            </a>
                            @if(count($menuItem->children) > 0)
                                <ul class="sub-menu">
                                    @foreach($menuItem->children as $childIndex => $childItem)
                                        @if($childItem->onlyAuth() && customer_check())
                                            <li @class(['active' => request()->fullUrl() == $childItem->link()])>
                                                <a href="{{ $childItem->link() }}">{{ $childItem->displayName() }}</a>
                                            </li>
                                        @elseif($childItem->onlyPublic())
                                            <li @class(['active' => request()->fullUrl() == $childItem->link()])>
                                                <a href="{{ $childItem->link() }}">{{ $childItem->displayName() }}</a>
                                            </li>
                                        @else
                                            <li @class(['active' => request()->fullUrl() == $childItem->link()])>
                                                <a href="{{ $childItem->link() }}">{{ $childItem->displayName() }}</a>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            @endif
                        </li>

                    @endif
                    @continue
                @endif

                @if($menuItem->onlyPublic())
                    @if(!customer_check())
                            <li @class(['active' => request()->fullUrl() == $menuItem->link(), 'has-children cs-hover' => (count($menuItem->children) > 0)])>
                                <a href="{{ $menuItem->link() }}">
                                    {{ $menuItem->displayName() }}
                                </a>
                                @if(count($menuItem->children) > 0)
                                    <ul class="sub-menu">
                                        @foreach($menuItem->children as $childIndex => $childItem)
                                            @if($childItem->onlyAuth() && customer_check())
                                                <li @class(['active' => request()->fullUrl() == $childItem->link()])>
                                                    <a href="{{ $childItem->link() }}">{{ $childItem->displayName() }}</a>
                                                </li>
                                            @elseif($childItem->onlyPublic())
                                                <li @class(['active' => request()->fullUrl() == $childItem->link()])>
                                                    <a href="{{ $childItem->link() }}">{{ $childItem->displayName() }}</a>
                                                </li>
                                            @else
                                                <li @class(['active' => request()->fullUrl() == $childItem->link()])>
                                                    <a href="{{ $childItem->link() }}">{{ $childItem->displayName() }}</a>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                @endif
                            </li>

                        @endif
                    @continue
                @endif

                    <li @class(['active' => request()->fullUrl() == $menuItem->link(), 'has-children cs-hover' => (count($menuItem->children) > 0)])>
                        <a href="{{ $menuItem->link() }}">
                            {{ $menuItem->displayName() }}
                        </a>
                        @if(count($menuItem->children) > 0)
                            <ul class="sub-menu">
                                @foreach($menuItem->children as $childIndex => $childItem)
                                    @if($childItem->onlyAuth() && customer_check())
                                        <li @class(['active' => request()->fullUrl() == $childItem->link()])>
                                            <a href="{{ $childItem->link() }}">{{ $childItem->displayName() }}</a>
                                        </li>
                                    @elseif($childItem->onlyPublic())
                                        <li @class(['active' => request()->fullUrl() == $childItem->link()])>
                                            <a href="{{ $childItem->link() }}">{{ $childItem->displayName() }}</a>
                                        </li>
                                    @else
                                        <li @class(['active' => request()->fullUrl() == $childItem->link()])>
                                            <a href="{{ $childItem->link() }}">{{ $childItem->displayName() }}</a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        @endif
                    </li>

            @endforeach
        @endif
        @if(customer_check())
            <li class="sub-menu-separator"></li>
            <li><a href="#"> <i class="icon-unlock"></i>Logout</a></li>
        @endif
    </ul>
</div>
</div>
