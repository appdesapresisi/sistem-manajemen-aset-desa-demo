@if(!empty($previewUrl))
@php
  $ttdExt = strtolower(pathinfo((string) ($ttdPath ?? ''), PATHINFO_EXTENSION));
  $ttdIsPdf = $ttdExt === 'pdf';
@endphp
<div style="margin-bottom: 12px; padding: 12px; background: var(--color-surface); border: 1px solid var(--color-border); border-radius: var(--radius-sm); display: flex; align-items: center; gap: 12px;">
  @if(!$ttdIsPdf)
  <img src="{{ $previewUrl }}" alt="TTD Saat Ini" onclick="if(window.openSimaImageViewer) openSimaImageViewer('{{ $previewUrl }}', 0, 'Tanda Tangan Digital')" title="Klik untuk memperbesar (Layar Penuh)" style="width: 80px; height: 80px; border-radius: var(--radius-sm); object-fit: contain; background: var(--color-surface, #f8faf7); border: 1px solid var(--color-border); padding: 4px; cursor: zoom-in;" />
  @else
  <div style="width:80px;height:80px;display:flex;align-items:center;justify-content:center;background:var(--color-danger-soft);border-radius:var(--radius-sm);">
    <i data-feather="file-text" style="width:28px;height:28px;color:var(--color-danger);"></i>
  </div>
  @endif
  <div style="flex: 1;">
    <div style="font-size: 11px; font-weight: 600; color: var(--color-primary); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Tanda Tangan Saat Ini</div>
    <div style="font-size: 13px; font-weight: 500; color: var(--color-text);">Tanda Tangan Digital</div>
  </div>
</div>
@endif
