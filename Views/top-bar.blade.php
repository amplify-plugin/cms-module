<div {!! $htmlAttributes !!}>
<div class="row">
    <div class="col-12">
        <div class="topbar border-0">
            <div class="topbar-column">
                @if(config('amplify.cms.slogan') != null || strlen(config('amplify.cms.slogan')) > 0)
                    <div class="social-button topbar-motto">
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
                <x-site.language-change/>
            </div>
        </div>
    </div>
</div>
</div>


<style>
    @media (max-width: 660px) {
        .topbar.border-0 {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }
        .topbar {
             height: auto;
        }
        .topbar .topbar-column {
            width: 100% !important;
        }
        .topbar .topbar-column:last-child {
            display: flex;
            justify-content: space-between;
        }
    }
</style>
