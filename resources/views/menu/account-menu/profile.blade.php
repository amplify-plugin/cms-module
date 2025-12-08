<li class="sub-menu-user">
    <div class="user-ava">
        <img
            src="{{ $accountProfileImage }}"
            alt="{{ $accountName }}">
    </div>
    <div class="user-info">
        <h6 class="user-name text-dark" title="Account: {{ $accountName }}">{{ $accountName }}</h6>
        <span class="user-name text-xs text-muted" title="Customer: {{ $companyName }}">{{ $companyName }}</span>
    </div>
</li>
