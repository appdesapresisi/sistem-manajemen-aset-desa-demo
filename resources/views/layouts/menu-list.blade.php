<li class="pc-item pc-caption" style="margin-top:16px;">
  <label>Navigasi</label>
</li>
<li class="pc-item">
  <a href="{{ route('dashboard') }}" class="pc-link">
    <span class="pc-micon">
      <i data-feather="home"></i>
    </span>
    <span class="pc-mtext">Home</span>
  </a>
</li>
<li class="pc-item pc-caption">
  <label>Inventaris</label>
  <i data-feather="monitor"></i>
</li>
<li class="pc-item pc-hasmenu">
  <a href="{{ route('manajemen-aset.index') }}" class="pc-link">
    <span class="pc-micon"> <i data-feather="book"></i></span>
    <span class="pc-mtext">Manajemen Aset</span>
  </a>
</li>
{{-- DISABLED: Persetujuan menu removed since approval workflow is disabled --}}
<li class="pc-item pc-hasmenu">
  <a href="{{ route('export-data.index') }}" class="pc-link">
    <span class="pc-micon"> <i data-feather="file-text"></i></span>
    <span class="pc-mtext">Export Data</span>
  </a>
</li>
<li class="pc-item pc-caption">
  <label>Master Data</label>
  <i data-feather="feather"></i>
</li>
<li class="pc-item pc-hasmenu">
  <a href="{{ route('jenis-inventaris.index') }}" class="pc-link">
    <span class="pc-micon"> <i data-feather="wind"></i></span>
    <span class="pc-mtext">Jenis Inventaris</span>
  </a>
</li>
<li class="pc-item pc-hasmenu">
  <a href="{{ route('ruangan.index') }}" class="pc-link">
    <span class="pc-micon"> <i data-feather="grid"></i></span>
    <span class="pc-mtext">Ruangan</span>
  </a>
</li>
<li class="pc-item pc-hasmenu">
  <a href="{{ route('dropdown.index') }}" class="pc-link">
    <span class="pc-micon"> <i data-feather="list"></i></span>
    <span class="pc-mtext">Dropdown</span>
  </a>
</li>
<li class="pc-item pc-hasmenu">
  <a href="{{ route('bidangs.index') }}" class="pc-link">
    <span class="pc-micon"> <i data-feather="briefcase"></i></span>
    <span class="pc-mtext">Bidang</span>
  </a>
</li>
<li class="pc-item pc-hasmenu">
  <a href="{{ route('pejabat.index') }}" class="pc-link">
    <span class="pc-micon"> <i data-feather="user-check"></i></span>
    <span class="pc-mtext">Pejabat</span>
  </a>
</li>
<li class="pc-item pc-hasmenu">
  <a href="{{ route('users.index') }}" class="pc-link">
    <span class="pc-micon"> <i data-feather="users"></i></span>
    <span class="pc-mtext">Users</span>
  </a>
</li>