<!DOCTYPE html>
<html lang="en" data-pc-preset="{{ $pc_preset_theme }}" data-pc-theme="{{ $pc_dark_layout }}">
<head>
    @include('layouts.head-page-meta', ['title' => 'Detail Dropdown — ' . ($dropdown->nama ?? 'Dropdown')])
    @include('layouts.head-css')
</head>
<body data-pc-preset="{{ $pc_preset_theme }}" data-pc-sidebar-theme="{{ $pc_sidebar_theme ?? 'light' }}">
    @include('layouts.notification')
    @include('layouts.modals')
    @include('layouts.layout-vertical')
    <div class="pc-container">
        <div class="pc-content">
            @include('layouts.breadcrumb', [
                'breadcrumbItemHome' => 'Master Data',
                'breadcrumbItem' => 'Dropdown Dinamis',
                'breadcrumbItemUrl' => route('dropdown.index'),
                'breadcrumbItemActive' => 'Detail Dropdown'
            ])

            <!-- Top Action Bar -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
                <a href="{{ route('dropdown.index') }}" class="btn-sima-secondary" style="border: none !important; padding-left: 0 !important; background-color: transparent !important;"  onclick="try { sessionStorage.setItem('dropdown_returning', '1'); } catch(e) {} if (window.history.length > 1 && document.referrer && document.referrer.includes('/dropdown')) { window.history.back(); return false; } return true;">
                    <i data-feather="arrow-left" style="width: 14px; height: 14px;"></i>
                    <span>Kembali ke Daftar Dropdown</span>
                </a>
                <div style="display: flex; gap: 10px; align-items: center;">
                    @can('update', $dropdown)
                    <a href="{{ route('dropdown.edit', $dropdown->id) }}" class="btn-sima-primary">
                        <i data-feather="edit-2" style="width: 14px; height: 14px;"></i>
                        <span>Edit Dropdown</span>
                    </a>
                    @endcan
                    @can('delete', $dropdown)
                    <form id="deleteForm" action="{{ route('dropdown.destroy', $dropdown->id) }}" method="POST" style="display:none;">
                        @csrf
                        @method('DELETE')
                    </form>
                    <button type="button" class="btn-sima-danger"
                            onclick="AppModal.confirmDelete('{{ addslashes($dropdown->nama) }}', function(){ document.getElementById('deleteForm').submit(); })">
                        <i data-feather="trash-2" style="width: 14px; height: 14px;"></i>
                        <span>Hapus Dropdown</span>
                    </button>
                    @endcan
                </div>
            </div>

            <!-- ===== MAIN PORTAL CARD ===== -->
            <div class="portal-card">

                <!-- Section: Informasi Dropdown -->
                <div class="portal-section" style="padding-top: 0; border-top: none; margin-top: 32px;">
                    <div class="portal-section-header">
                        <h2 class="portal-section-title">
                            <span>Informasi Dropdown</span>
                        </h2>
                    </div>
                    <div class="portal-stats-grid" style="grid-template-columns: repeat(2, 1fr); gap: 24px 36px;">
                        <div class="portal-stat-item">
                            <span class="portal-stat-label">Nama Dropdown</span>
                            <span class="portal-stat-value">{{ $dropdown->nama ?? '-' }}</span>
                        </div>
                        <div class="portal-stat-item">
                            <span class="portal-stat-label">Total Opsi Pilihan</span>
                            <span class="portal-stat-value">{{ $dropdown->items->count() }} pilihan</span>
                        </div>
                        <div class="portal-stat-item" style="grid-column: 1 / -1;">
                            <span class="portal-stat-label">Deskripsi</span>
                            <span class="portal-stat-value {{ !$dropdown->deskripsi ? 'text-empty-fallback' : '' }}">
                                {{ $dropdown->deskripsi ?: 'Tidak ada deskripsi untuk dropdown ini.' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Section: Daftar Pilihan -->
                <div class="portal-section" style="border-top: none; margin-top: 0; padding-top: 36px;">
                    <div class="portal-section-header">
                        <h2 class="portal-section-title">
                            <span>Daftar Pilihan Opsi ({{ $dropdown->items->count() }})</span>
                        </h2>
                    </div>

                    @if($dropdown->items->isEmpty())
                        <div class="empty-state-sima">
                            <i data-feather="inbox" style="width: 42px; height: 42px; stroke-width: 1.5; color: var(--color-border-dark);"></i>
                            <p>Belum ada opsi pilihan yang ditambahkan.</p>
                        </div>
                    @else
                        <div style="display: flex; flex-direction: column; margin-top: 6px;">
                            @foreach($dropdown->items as $index => $item)
                            <div style="display: flex; align-items: center; font-size: 13.5px; line-height: 1.5; color: var(--color-text); padding: 12px 0; border-bottom: 1px dashed var(--color-border, #cfdcc8);">
                                <span style="color: var(--color-text-muted); font-variant-numeric: tabular-nums; min-width: 24px; font-weight: 500; flex-shrink: 0;">
                                    {{ $index + 1 }}.
                                </span>
                                <span style="font-weight: 600; color: var(--color-text);">
                                    {{ $item->label }}
                                </span>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Section: Audit Data -->
                <div class="portal-section" style="border-top: none; margin-top: 0; padding-top: 36px; margin-bottom: 32px;">
                    <div class="portal-section-header">
                        <h2 class="portal-section-title">
                            <span>Informasi Sistem & Audit Data</span>
                        </h2>
                    </div>
                    <div style="display: flex; justify-content: space-evenly; gap: 36px; font-size: 12.5px; flex-wrap: wrap;">
                        <div class="portal-kv-item" style="flex: 1; min-width: 180px;">
                            <span class="portal-kv-label">ID Dropdown</span>
                            <span class="portal-kv-value" style="font-family: monospace;">#{{ $dropdown->id }}</span>
                        </div>
                        <div class="portal-kv-item" style="flex: 1; min-width: 180px;">
                            <span class="portal-kv-label">Tanggal Dibuat</span>
                            <span class="portal-kv-value">{{ $dropdown->created_at ? $dropdown->created_at->format('d M Y, H:i') : '-' }}</span>
                        </div>
                        <div class="portal-kv-item" style="flex: 1; min-width: 180px;">
                            <span class="portal-kv-label">Terakhir Diperbarui</span>
                            <span class="portal-kv-value">{{ $dropdown->updated_at ? $dropdown->updated_at->format('d M Y, H:i') : '-' }}</span>
                        </div>
                    </div>
                </div>

            </div><!-- /.portal-card -->

        </div>
    </div>
    @include('layouts.footer-block')
    @include('layouts.footer-js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            try { sessionStorage.setItem('dropdown_returning', '1'); } catch(e) {}
        });
    </script>
</body>
</html>
