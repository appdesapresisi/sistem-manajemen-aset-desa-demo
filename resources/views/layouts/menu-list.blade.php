<!-- GRUP: UTAMA -->
<li class="pc-item pc-caption">
  <label>Utama</label>
</li>
<li class="pc-item {{ request()->routeIs('dashboard') && !request()->routeIs('dashboard.activities') ? 'active' : '' }}">
  <a href="{{ route('dashboard') }}" class="pc-link">
    <span class="pc-micon"><i data-feather="grid"></i></span>
    <span class="pc-mtext">Dashboard</span>
  </a>
</li>

<!-- GRUP: INVENTARIS DESA -->
<li class="pc-item pc-caption">
  <label>Inventaris Desa</label>
</li>
<li class="pc-item {{ request()->routeIs('manajemen-aset.*') ? 'active' : '' }}">
  <a href="{{ route('manajemen-aset.index') }}" class="pc-link">
    <span class="pc-micon"><i data-feather="book"></i></span>
    <span class="pc-mtext">Buku Inventaris</span>
  </a>
</li>
<li class="pc-item {{ request()->routeIs('penghapusan-aset.*') ? 'active' : '' }}">
  <a href="{{ route('penghapusan-aset.index') }}" class="pc-link">
    <span class="pc-micon"><i data-feather="trash-2"></i></span>
    <span class="pc-mtext">Penghapusan Aset</span>
  </a>
</li>
<li class="pc-item {{ request()->routeIs('ruangan.*') ? 'active' : '' }}">
  <a href="{{ route('ruangan.index') }}" class="pc-link">
    <span class="pc-micon"><i data-feather="map-pin"></i></span>
    <span class="pc-mtext">Ruangan</span>
  </a>
</li>

<!-- GRUP: PELAPORAN & DOKUMEN -->
<li class="pc-item pc-caption">
  <label>Ekspor Data</label>
</li>
<li class="pc-item {{ (request()->routeIs('export-data.*') && !request()->routeIs('export-data.history*')) ? 'active' : '' }}">
  <a href="{{ route('export-data.index') }}" class="pc-link">
    <span class="pc-micon"><i data-feather="file-text"></i></span>
    <span class="pc-mtext">Pusat Ekspor</span>
  </a>
</li>
<li class="pc-item {{ request()->routeIs('export-data.history*') ? 'active' : '' }}">
  <a href="{{ route('export-data.history') }}" class="pc-link">
    <span class="pc-micon"><i data-feather="clock"></i></span>
    <span class="pc-mtext">Riwayat Ekspor</span>
  </a>
</li>

<!-- GRUP: PENGATURAN & MASTER -->
<li class="pc-item pc-caption">
  <label>Master Data</label>
</li>
<li class="pc-item {{ request()->routeIs('jenis-inventaris.*') ? 'active' : '' }}">
  <a href="{{ route('jenis-inventaris.index') }}" class="pc-link">
    <span class="pc-micon"><i data-feather="layers"></i></span>
    <span class="pc-mtext">Jenis Inventaris</span>
  </a>
</li>
<li class="pc-item {{ request()->routeIs('pejabat.*') ? 'active' : '' }}">
  <a href="{{ route('pejabat.index') }}" class="pc-link">
    <span class="pc-micon"><i data-feather="user-check"></i></span>
    <span class="pc-mtext">Manajemen Pejabat</span>
  </a>
</li>
<li class="pc-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
  <a href="{{ route('users.index') }}" class="pc-link">
    <span class="pc-micon"><i data-feather="users"></i></span>
    <span class="pc-mtext">Manajemen Pengguna</span>
  </a>
</li>
<li class="pc-item {{ request()->routeIs('bidangs.*') ? 'active' : '' }}">
  <a href="{{ route('bidangs.index') }}" class="pc-link">
    <span class="pc-micon"><i data-feather="briefcase"></i></span>
    <span class="pc-mtext">Manajemen Bidang</span>
  </a>
</li>
<li class="pc-item {{ request()->routeIs('dropdown.*') ? 'active' : '' }}">
  <a href="{{ route('dropdown.index') }}" class="pc-link">
    <span class="pc-micon"><i data-feather="list"></i></span>
    <span class="pc-mtext">Dropdown Dinamis</span>
  </a>
</li>
<li class="pc-item pc-caption">
  <label>Log Sistem</label>
</li>
<li class="pc-item {{ request()->routeIs('dashboard.activities') ? 'active' : '' }}">
  <a href="{{ route('dashboard.activities') }}" class="pc-link">
    <span class="pc-micon"><i data-feather="activity"></i></span>
    <span class="pc-mtext">Aktivitas Sistem</span>
  </a>
</li>
@if(auth()->check() && auth()->user()->isSuperDeveloper())
<li class="pc-item pc-caption">
  <label>Keamanan & Cadangan</label>
</li>
<li class="pc-item {{ request()->routeIs('backup.*') ? 'active' : '' }}">
  <a href="{{ route('backup.index') }}" class="pc-link">
    <span class="pc-micon"><i data-feather="database"></i></span>
    <span class="pc-mtext">Cadangan Data</span>
  </a>
</li>
@endif