<div id="sidebar" class="active">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header">
            <div class="d-flex justify-content-center">
                @php
                    // Mendapatkan data user yang sedang login
                    $user = auth()->user();

                    // Fungsi untuk mendapatkan inisial dari nama (contoh: "John Doe" menjadi "JD")
                    $initials = collect(explode(' ', $user->name))
                        ->map(function ($word) {
                            return strtoupper(substr($word, 0, 1));
                        })
                        ->join('');
                @endphp

                <div class="profile" style="text-align: center;">
                    <!-- Elemen lingkaran untuk menampilkan inisial -->
                    <div class="profile-img"
                        style="width: 100px; height: 100px; border-radius: 50%; background: #847513; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                        <span style="font-size: 36px; color: #fff;">{{ $initials }}</span>
                    </div>

                    <!-- Tampilkan nama dan email user -->
                    <div class="profile-info" style="margin-top: 10px;">
                        <p style="margin: 0; font-size: 16px;">{{ $user->name }}</p>
                        <p style="margin: 0; font-size: 14px;">{{ $user->email }}</p>
                    </div>

                </div>

                <div class="toggler">
                    <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                </div>
            </div>
        </div>
        <div class="sidebar-menu">
            <ul class="menu">
                <li class="sidebar-title">Menu</li>

                <li class="sidebar-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}" class='sidebar-link'>
                        <i class="bi bi-grid-fill"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="sidebar-item {{ request()->routeIs('equipment.index') ? 'active' : '' }}">
                    <a href="{{ route('equipment.index') }}" class="sidebar-link">
                        <i class="bi bi-hdd-fill"></i>
                        <span>Equipment</span>
                    </a>
                </li>  
                
                <li class="sidebar-item {{ request()->routeIs('indicator.index') ? 'active' : '' }}">
                    <a href="{{ route('indicator.index') }}" class="sidebar-link">
                        <i class="bi bi-bar-chart"></i>
                        <span>Indicator</span>
                    </a>
                </li>

                <li class="sidebar-item {{ request()->routeIs('problem.index') ? 'active' : '' }}">
                    <a href="{{ route('problem.index') }}" class="sidebar-link">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <span>Problem</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a href="{{ route('logout') }}" class='sidebar-link'
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Logout</span>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </li>
            </ul>
        </div>
        <button class="sidebar-toggler btn x"><i data-feather="x"></i></button>
    </div>
</div>
