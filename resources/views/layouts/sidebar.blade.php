<!-- [ Sidebar Menu ] start -->
<nav class="pc-sidebar">
  <div class="navbar-wrapper">
    <!-- Brand / Logo Header -->
    <div class="m-header" style="display: flex; align-items: center; justify-content: flex-start;">
      <a href="{{ route('dashboard') }}" class="b-brand">
        <img src="{{ asset('assets/images/logo/logo-sima-desa-demo.png') }}" alt="Logo SIMA" style="width:38px;height:38px;object-fit:contain;flex-shrink:0;" />
        <div style="display:flex;flex-direction:column;line-height:1.2;">
          <span style="font-size:16px;font-weight:700;color:var(--color-primary);letter-spacing:0.5px;">SIMA</span>
          <span style="font-size:11px;font-weight:500;color:var(--color-text-muted);">Desa {{ config('desa.nama_lokasi', 'Demo') }}</span>
        </div>
      </a>
    </div>

    <!-- Navigation Items -->
    <div class="navbar-content">
      <ul class="pc-navbar">
        @include('layouts.menu-list')
      </ul>
    </div>
  </div>
</nav>
<!-- [ Sidebar Menu ] end -->