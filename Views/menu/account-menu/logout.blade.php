<li class="sub-menu-separator">
    <a href="#" onclick="document.getElementById('account-logout-form').submit(); return false;">
        <i class="icon-unlock"></i>
        Logout
    </a>
</li>
<form action="{{ route('frontend.logout') }}" method="POST" id="account-logout-form">
    @csrf
</form>
