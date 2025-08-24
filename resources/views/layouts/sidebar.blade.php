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
            <li class="menu-header">Data Master</li>
            <li>
                <a class="nav-link" href="{{ route('users.index') }}">
                    <i class="fas fa-users"></i> <span>User</span>
                </a>
            </li>
            <li>
                <a class="nav-link" href="{{ route('book-categories.index') }}">
                    <i class="fas fa-th-large"></i> <span>Kategori Buku</span>
                </a>
            </li>
            <li>
                <a class="nav-link" href="{{ route('books.index') }}">
                    <i class="fas fa-book"></i> <span>Buku</span>
                </a>
            </li>
            <li class="menu-header">Transaksi</li>
            <li>
                <a class="nav-link" href="#">
                    <i class="fas fa-thumbs-up"></i> <span>Peminjaman</span>
                </a>
            </li>
            <li>
                <a class="nav-link" href="#">
                    <i class="fas fa-thumbs-down"></i> <span>Pengembalian</span>
                </a>
            </li>
        </ul>
    </aside>
</div>
