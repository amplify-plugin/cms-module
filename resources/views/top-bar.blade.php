<div {!! $htmlAttributes !!}>
    <div class="topbar">
        <div class="topbar-column">
            @if(config('amplify.cms.slogan') != null || strlen(config('amplify.cms.slogan')) > 0)
                <div class="topbar-motto">
                    {{ config('amplify.cms.slogan') }}
                </div>
            @endif
        </div>
        <div class="topbar-column">
            @if(config('amplify.cms.email'))
                @if(strlen(config('amplify.cms.email', '')) > 0)
                    <a class="topbar-contact" href="mailto:{{ config('amplify.cms.email') }}"
                       style="white-space: nowrap">
                        <i class="icon-mail"></i>&nbsp; {{ config('amplify.cms.email') }}
                    </a>
                @endif
            @endif
            @if(config('amplify.cms.phone'))
                @if(strlen(config('amplify.cms.phone', '')) > 0)
                    <a class="topbar-contact" href="tel:{{ config('amplify.cms.phone') }}"
                       style="white-space: nowrap">
                        <i class="socicon-viber"></i>&nbsp; {{ config('amplify.cms.phone') }}
                    </a>
                @endif
            @endif
        </div>
        <div class="topbar-column">
            <x-site.language-change/>
        </div>
    </div>
</div>
