<div {!! $htmlAttributes !!}>
<img src="{{ asset('img/account/exchange_sm.png') }}" class="img-fluid d-none d-md-block"/>
<img src="{{ asset('img/account/exchange.svg') }}" class="img-fluid d-md-none h-100"/>
    <ul class="toolbar-dropdown">
        <span class="text-dark toolbar-close exchange-close">
            <i class="icon-cross"></i>
        </span>
        @if(customer_check())
            @if($getMemberStatus())
                <li class="sub-menu-user">
                    <div class="user-info">
                        <h6 class="user-name text">My EXCHANGE Rewards</h6>
                        @if(isset($getMemberBalance))
                            <span class="text-xs text-muted"><strong>Balance:</strong> {{$getMemberBalance}} </span>
                        @else
                           <span class="text-xs text-muted"><strong>Balance:</strong> </span>
                        @endif
                        <div class="exchange_redeem_cont">
                            <a id="exRedeemLnk" href="{{$getSsoRequest}}" target="_blank">Redeem My Points</a>
                        </div>
                    </div>
                </li>
            @else
            <li class="sub-menu-user">
                <div class="user-info">
                    <h6>Activate your free EXCHANGE Rewards program by signing into your Safety Products Inc online account and accepting Terms and Conditions in EXCHANGE.</h6>
                    <a id="exRedeemLnk" href="{{$getSsoRequest}}" target="_blank">Accept</a>
                </div>  
            </li>
            @endif
        @else
        <li class="sub-menu-user">
            <div class="user-info">
                <h6>Activate your free EXCHANGE Rewards program by signing into your Safety Products Inc online account and accepting Terms and Conditions in EXCHANGE.</h6>
                <a href="{{ route('frontend.login') }}" class="" style="" target="_self">Login</a>
            </div>
        </li>
        @endif
    </ul>
</div>
