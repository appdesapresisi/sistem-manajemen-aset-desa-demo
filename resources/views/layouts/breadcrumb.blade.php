<!-- [ breadcrumb ] start -->
<div class="page-header">
  <div class="page-block">
    <div class="page-header-title">
      <h5 class="mb-0 font-medium">{{ $breadcrumbItemActive ?? 'Page' }}</h5>
    </div>
    <ul class="breadcrumb">
      <li class="breadcrumb-item">{{ $breadcrumbItemHome ?? 'Home' }}</li>
      @if(isset($breadcrumbItem) && $breadcrumbItem)
        @if(isset($breadcrumbItemUrl) && $breadcrumbItemUrl)
          <li class="breadcrumb-item"><a href="{{ $breadcrumbItemUrl }}">{{ $breadcrumbItem }}</a></li>
        @else
          <li class="breadcrumb-item">{{ $breadcrumbItem }}</li>
        @endif
      @endif
      @if(isset($breadcrumbItemActive) && $breadcrumbItemActive)
        <li class="breadcrumb-item" aria-current="page">{{ $breadcrumbItemActive }}</li>
      @endif
    </ul>
  </div>
</div>
<!-- [ breadcrumb ] end -->
