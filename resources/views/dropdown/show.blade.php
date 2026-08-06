<!DOCTYPE html>
<html lang="en" data-pc-preset="{{ $pc_preset_theme }}" data-pc-theme="{{ $pc_dark_layout }}">
<head>
    @include('layouts.head-page-meta', ['title' => 'Detail Dropdown - '.$dropdown->nama])
    @include('layouts.head-css')
    <style>
        .page-center { max-width:680px; margin:0 auto; }
        .d-card { background:#fff; border-radius:12px; border:1px solid #e2e8f0; box-shadow:0 1px 3px rgba(0,0,0,.05); margin-bottom:14px; overflow:hidden; }
        .d-card-header { padding:13px 20px; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; gap:8px; font-size:12px; font-weight:700; color:#064635; text-transform:uppercase; letter-spacing:.5px; }
        .d-card-header i { width:15px; height:15px; }
        .detail-list { list-style:none; margin:0; padding:0; }
        .detail-list li { display:flex; align-items:flex-start; padding:12px 20px; border-bottom:1px solid #f1f5f9; gap:12px; }
        .detail-list li:last-child { border-bottom:none; }
        .dl-label { width:130px; flex-shrink:0; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:.4px; padding-top:3px; }
        .dl-value { flex:1; font-size:14px; color:#1e293b; font-weight:500; }
        .dl-value.empty { color:#cbd5e1; font-style:italic; font-weight:400; }
        .action-bar { background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:14px 20px; display:flex; gap:10px; align-items:center; flex-wrap:wrap; margin-bottom:14px; box-shadow:0 1px 3px rgba(0,0,0,.05); justify-content:space-between; }
        .action-bar-right { display:flex; gap:10px; align-items:center; }
        .btn { padding:9px 18px; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; transition:all .2s; display:inline-flex; align-items:center; gap:7px; text-decoration:none; border:none; white-space:nowrap; }
        .btn i { width:15px; height:15px; }
        .btn-back   { border:1px solid #d1d5db !important; background:#fff; color:#374151; }
        .btn-back:hover { background:#f4faf5; }
        .btn-edit   { background:#064635; color:#fff; }
        .btn-edit:hover { background:#1c5240; }
        .btn-delete { background:#dc2626; color:#fff; }
        .btn-delete:hover { background:#b91c1c; }
        .item-pill { display:inline-flex; align-items:center; gap:6px; padding:5px 12px; background:#ffffff; color:#334155; border-radius:8px; font-size:13px; font-weight:500; margin:3px; border:1px solid #e2e8f0; }
        .item-num-badge { font-size:12px; font-weight:600; color:#64748b; }
    </style>
</head>
<body data-pc-preset="{{ $pc_preset_theme }}" data-pc-sidebar-theme="{{ $pc_sidebar_theme ?? 'light' }}">
    @include('layouts.notification')
    @include('layouts.modals')
    @include('layouts.layout-vertical')
    <div class="pc-container">
        <div class="pc-content">
            @include('layouts.breadcrumb', ['breadcrumbItemHome'=>'Master Data','breadcrumbItem'=>'Dropdown','breadcrumbItemUrl'=>route('dropdown.index'),'breadcrumbItemActive'=>'Detail Dropdown'])

            <div class="page-center">
                <div class="action-bar">
                    <a href="{{ route('dropdown.index') }}" class="btn btn-back">
                        <i data-feather="arrow-left"></i> Kembali
                    </a>
                    <div class="action-bar-right">
                        @can('update', $dropdown)
                        <a href="{{ route('dropdown.edit', $dropdown->id) }}" class="btn btn-edit">
                            <i data-feather="edit-2"></i> Edit
                        </a>
                        @endcan
                        @can('delete', $dropdown)
                        <form id="deleteForm" action="{{ route('dropdown.destroy', $dropdown->id) }}" method="POST" style="display:none;">
                            @csrf @method('DELETE')
                        </form>
                        <button type="button" class="btn btn-delete"
                                onclick="AppModal.confirmDelete('{{ addslashes($dropdown->nama) }}', function(){ document.getElementById('deleteForm').submit(); })">
                            <i data-feather="trash-2"></i> Hapus
                        </button>
                        @endcan
                    </div>
                </div>

                <div class="d-card">
                    <div class="d-card-header"><i data-feather="info"></i> Informasi Dropdown</div>
                    <ul class="detail-list">
                        <li>
                            <span class="dl-label">Nama</span>
                            <span class="dl-value">{{ $dropdown->nama }}</span>
                        </li>
                        <li>
                            <span class="dl-label">Deskripsi</span>
                            <span class="dl-value {{ $dropdown->deskripsi ? '' : 'empty' }}">
                                {{ $dropdown->deskripsi ?: 'Tidak ada deskripsi' }}
                            </span>
                        </li>
                        <li>
                            <span class="dl-label">Dibuat</span>
                            <span class="dl-value">{{ $dropdown->created_at?->format('d M Y') }}</span>
                        </li>
                    </ul>
                </div>

                <div class="d-card">
                    <div class="d-card-header">
                        <i data-feather="chevron-down"></i>
                        Daftar Pilihan
                        <span style="margin-left:auto;background:#064635;color:#fff;padding:2px 10px;border-radius:12px;font-size:11px;">
                            {{ $dropdown->items->count() }} pilihan
                        </span>
                    </div>
                    @if($dropdown->items->isEmpty())
                    <div style="text-align:center;padding:32px;color:#9ca3af;font-size:13px;">
                        Belum ada pilihan
                    </div>
                    @else
                    <div style="padding:16px 20px;display:flex;flex-direction:column;gap:4px;">
                        @foreach($dropdown->items as $index => $item)
                        <span class="item-pill">
                            <span class="item-num-badge">{{ $index + 1 }}.</span>
                            {{ $item->label }}
                        </span>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @include('layouts.footer-block')
    @include('layouts.footer-js')
</body>
</html>
