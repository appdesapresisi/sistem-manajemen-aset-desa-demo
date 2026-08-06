@php
  $required = $required ?? false;
  $optionalNote = $optionalNote ?? null;
  $defaultMethod = $defaultMethod ?? 'draw';
  $uploadHint = $uploadHint ?? 'Format: PNG dan JPG (Max 5MB)';
  $uploadAreaClass = trim((string) ($uploadAreaClass ?? ''));
  $guideItems = $guideItems ?? [
    'Pilih <strong>Gambar Tanda Tangan</strong> untuk menggambar langsung di sistem',
    'Pilih <strong>Upload Tanda Tangan Elektronik</strong> untuk tanda tangan berkode (PNG/JPG)',
    'Ukuran file upload maksimal 5MB',
  ];

  $selectedMethod = old('signature_method', $defaultMethod);
  $isDrawSelected = $selectedMethod === 'draw';
  // ttd_type: 'gambar' jika draw, 'elektronik' jika upload
  $initialTtdType = $isDrawSelected ? 'gambar' : 'elektronik';
@endphp

{{-- Hidden input ttd_type — diupdate otomatis via JS saat pilihan berubah --}}
<input type="hidden" name="ttd_type" id="ttd_type_input" value="{{ old('ttd_type', $initialTtdType) }}">

<label class="form-label">
  Metode Tanda Tangan
  @if($required)
  <span class="required">*</span>
  @elseif($optionalNote)
  <span class="optional">({{ $optionalNote }})</span>
  @endif
</label>

<div class="signature-method-toggle">
  <label class="signature-method-option">
    <input type="radio" name="signature_method" value="draw" {{ $isDrawSelected ? 'checked' : '' }}>
    <span>
      <i data-feather="edit-3"></i>
      Gambar Tanda Tangan
    </span>
  </label>
  <label class="signature-method-option">
    <input type="radio" name="signature_method" value="upload" {{ !$isDrawSelected ? 'checked' : '' }}>
    <span>
      <i data-feather="upload-cloud"></i>
      Upload Tanda Tangan Elektronik
    </span>
  </label>
</div>

@if(!empty($currentPreviewView))
  @include($currentPreviewView, $currentPreviewData ?? [])
@endif

<div id="draw-signature-section" class="signature-draw-section" style="{{ $isDrawSelected ? '' : 'display:none;' }}">
  <div class="signature-draw-wrapper">
    <canvas id="signature-canvas" class="signature-canvas"></canvas>
  </div>
  <div class="signature-draw-actions">
    <button type="button" id="clear-signature" class="btn-clear-signature">Bersihkan Gambar</button>
    <div class="signature-draw-hint">Gambar tanda tangan menggunakan mouse, touch, atau stylus.</div>
  </div>
</div>

<div id="upload-signature-section" class="file-upload-wrapper" style="{{ $isDrawSelected ? 'display:none;' : '' }}">
  <label for="ttd_path" class="file-upload-area{{ $uploadAreaClass ? ' ' . $uploadAreaClass : '' }}" id="upload-area" style="cursor: pointer;">
    <div class="file-upload-icon">
      <i data-feather="upload"></i>
    </div>
    <div class="file-upload-text">Klik atau drag &amp; drop untuk upload</div>
    <div class="file-upload-hint">{{ $uploadHint }}</div>
  </label>
  <input type="file" id="ttd_path" name="ttd_path" accept="image/png,image/jpeg,image/jpg" style="display: none;" />
</div>

<div id="signature-method-error" class="text-danger text-sm mt-1" style="display:none;"></div>

@error('ttd_path')
<div class="text-danger text-sm mt-1">{{ $message }}</div>
@enderror

<div class="file-preview" id="file-preview">
  <img src="" alt="Preview TTD" class="file-preview-img" id="preview-img" />
  <div class="file-preview-info">
    <div class="file-preview-name" id="preview-name">filename.png</div>
    <div class="file-preview-size" id="preview-size">120 KB</div>
  </div>
  <div style="display: flex; gap: 8px;">
    <label for="ttd_path" class="file-preview-change" title="Ganti file" style="width: 32px; height: 32px; border-radius: 8px; border: none; background: #3b82f6; color: white; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s;" onmouseover="this.style.background='#2563eb'" onmouseout="this.style.background='#3b82f6'">
      <i data-feather="refresh-cw" style="width: 16px; height: 16px;"></i>
    </label>
    <button type="button" class="file-preview-remove" id="remove-file" title="Hapus file">
      <i data-feather="x"></i>
    </button>
  </div>
</div>

<div class="signature-guide">
  <div class="signature-guide-title">
    <i data-feather="info"></i>
    Panduan Tanda Tangan
  </div>
  <ul class="signature-guide-list">
    @foreach($guideItems as $guideItem)
    <li>{!! $guideItem !!}</li>
    @endforeach
  </ul>
</div>
