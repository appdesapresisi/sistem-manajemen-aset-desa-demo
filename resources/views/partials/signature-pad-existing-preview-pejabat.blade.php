@if(!empty($previewUrl))
@php
  $ttdExt = strtolower(pathinfo((string) ($ttdPath ?? ''), PATHINFO_EXTENSION));
  $ttdIsPdf = $ttdExt === 'pdf';
@endphp
<div style="margin-bottom: 12px; padding: 12px; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; display: flex; align-items: center; gap: 12px;">
  @if(!$ttdIsPdf)
  <img src="{{ $previewUrl }}" alt="TTD Saat Ini" style="width: 80px; height: 80px; border-radius: 8px; object-fit: contain; background: #fff; border: 1px solid #e5e7eb; padding: 4px;" />
  @else
  <div style="width:80px;height:80px;display:flex;align-items:center;justify-content:center;background:#fee2e2;border-radius:8px;">
    <i data-feather="file-text" style="width:28px;height:28px;color:#ef4444;"></i>
  </div>
  @endif
  <div style="flex: 1;">
    <div style="font-size: 11px; font-weight: 600; color: #1e40af; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Tanda Tangan Saat Ini</div>
    <div style="font-size: 13px; font-weight: 500; color: #374151;">Tanda Tangan Digital</div>
  </div>
</div>
@endif
