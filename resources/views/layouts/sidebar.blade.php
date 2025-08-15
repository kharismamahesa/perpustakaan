<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="{{ route('dashboard') }}">Perpus App</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="{{ route('dashboard') }}">PA</a>
        </div>
        <ul class="sidebar-menu">
            <li class="menu-header">Menu Utama</li>
            <li>
                <a class="nav-link" href="{{ route('dashboard') }}">
                    <i class="fas fa-fire"></i> <span>Dashboard</span>
                </a>
            </li>
            <li class="dropdown">
                <a href="#" class="nav-link has-dropdown">
                    <i class="fas fa-th-large"></i> <span>Data Master</span>
                </a>
                <ul class="dropdown-menu">
                    @auth
                        @if (auth()->user()->role === 'admin')
                            <li><a class="nav-link" href="{{ route('users.index') }}">User</a></li>
                        @endif
                    @endauth
                    <li><a class="nav-link" href="#">Buku</a></li>
                </ul>
            </li>
        </ul>
    </aside>
</div>
