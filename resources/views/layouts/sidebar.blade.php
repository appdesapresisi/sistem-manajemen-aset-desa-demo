
<!-- [ Sidebar Menu ] start -->
<nav class="pc-sidebar">
  <div class="navbar-wrapper">
    <div class="m-header flex items-center justify-center px-6" style="padding-top:20px;padding-bottom:14px;">
      <a href="{{ route('dashboard') }}" class="b-brand flex flex-col items-center text-center" style="text-decoration:none;gap:0;">
        <img src="{{ asset('assets/images/logo/logo-demo.png') }}" alt="Logo SIMA" style="width:56px;height:56px;object-fit:contain;flex-shrink:0;margin-bottom:8px;" />
        <span class="text-white font-bold" style="font-size:18px;letter-spacing:3px;line-height:1.2;">SIMA</span>
        <span class="text-white" style="font-size:11px;opacity:0.75;letter-spacing:0.3px;margin-top:2px;">Desa Demo</span>
      </a>
    </div>
    <div class="navbar-content py-2.5">
      <ul class="pc-navbar">
        @include('layouts.menu-list')
      </ul>
    </div>
  </div>
</nav>
<!-- [ Sidebar Menu ] end -->
<!--  -->