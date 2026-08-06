@if(!empty($ttdPath) && !empty($previewUrl))
@php
  $ttdExt = strtolower(pathinfo($ttdPath, PATHINFO_EXTENSION));
  $ttdIsPdf = $ttdExt === 'pdf';
@endphp
<div class="ttd-existing-preview" id="ttd-existing-preview">
  @if(!$ttdIsPdf)
  <img src="{{ $previewUrl }}" alt="TTD Saat Ini" class="ttd-existing-img" />
  @else
  <div style="width:80px;height:80px;display:flex;align-items:center;justify-content:center;background:#fee2e2;border-radius:8px;">
    <i data-feather="file-text" style="width:28px;height:28px;color:#ef4444;"></i>
  </div>
  @endif
  <div class="ttd-existing-info">
    <div class="ttd-label">Tanda Tangan Saat Ini</div>
    <div class="ttd-filename">Tanda Tangan Digital</div>
  </div>
</div>
@endif
