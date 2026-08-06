<!doctype html>
<html lang="en" data-pc-preset="{{ $pc_preset_theme }}" data-pc-theme="{{ $pc_dark_layout }}">
  <head>
    @include('layouts.head-page-meta', ['title' => 'Detail Jenis Inventaris'])
    @include('layouts.head-css')
    <style>
      /* ===== LAYOUT CENTER ===== */
      .page-center { max-width:720px; margin:0 auto; }

      /* Detail Section Styling */
      .detail-section {
        background: #ffffff;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
      }
      .detail-section-title {
        font-size: 14px;
        font-weight: 600;
        color: #064635;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
        padding-bottom: 12px;
        border-bottom: 2px solid #f1f5f9;
      }
      .detail-section-title i {
        width: 18px;
        height: 18px;
      }
      .detail-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
      }
      .detail-item {
        display: flex;
        flex-direction: column;
        gap: 6px;
      }
      .detail-label {
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
      }
      .detail-value {
        font-size: 15px;
        color: #1e293b;
        font-weight: 500;
      }
      .detail-value.empty {
        color: #94a3b8;
        font-style: italic;
      }
      .detail-full {
        grid-column: 1 / -1;
      }

      /* Vertical Detail List (replaces 2-col grid for info section) */
      .detail-list { list-style:none; margin:0; padding:0; }
      .detail-list li { display:flex; align-items:flex-start; padding:11px 0; border-bottom:1px solid #f1f5f9; gap:12px; }
      .detail-list li:last-child { border-bottom:none; }
      .dl-label { width:150px; flex-shrink:0; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:.4px; padding-top:3px; }
      .dl-value { flex:1; font-size:14px; color:#1e293b; font-weight:500; word-break:break-word; }
      .dl-value.empty { color:#cbd5e1; font-style:italic; font-weight:400; }

      /* Card Header */
      .card-header-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: #519259;
        margin-right: 12px;
      }
      .card-header-icon i {
        color: #fff !important;
        stroke: #fff !important;
        width: 20px;
        height: 20px;
      }
      .card-header-icon svg {
        width: 20px;
        height: 20px;
        stroke: #fff !important;
        color: #fff !important;
      }

      /* Pejabat List */
      .pejabat-list-container {
        display: flex;
        flex-direction: column;
        gap: 12px;
      }
      .pejabat-card {
        background: #f4faf5;
        border: 1px solid #e2e8f0;
        border-left: 3px solid #064635;
        border-radius: 8px;
        padding: 14px 16px;
        display: flex;
        align-items: center;
        gap: 12px;
      }
      .pejabat-order {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: #064635;
        color: #fff;
        font-size: 14px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
      }
      .pejabat-info {
        flex: 1;
      }
      .pejabat-nama {
        font-size: 14px;
        font-weight: 600;
        color: #1e293b;
      }
      .pejabat-jabatan {
        font-size: 12px;
        color: #64748b;
      }

      /* Fields Table */
      .fields-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        overflow: hidden;
      }
      .fields-table thead {
        background: #f4faf5;
      }
      .fields-table th {
        padding: 12px 16px;
        text-align: left;
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid #e2e8f0;
      }
      .fields-table td {
        padding: 14px 16px;
        font-size: 14px;
        color: #1e293b;
        border-bottom: 1px solid #f1f5f9;
      }
      .fields-table tbody tr:last-child td {
        border-bottom: none;
      }
      .fields-table tbody tr:hover {
        background: #f4faf5;
      }
      .field-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
      }
      .badge-text { background: #dbeafe; color: #1e40af; }
      .badge-number { background: #dcfce7; color: #166534; }
      .badge-date { background: #fef3c7; color: #92400e; }
      .badge-textarea { background: #e0e7ff; color: #3730a3; }
      .badge-required { background: #fee2e2; color: #991b1b; }
      .badge-optional { background: #f1f5f9; color: #64748b; }

      /* Action Bar */
      .action-bar { background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:14px 20px; display:flex; gap:10px; align-items:center; flex-wrap:wrap; margin-bottom:14px; box-shadow:0 1px 3px rgba(0,0,0,.05); justify-content:space-between; }
      .action-bar-right { display:flex; gap:10px; align-items:center; }

      /* Action Buttons */
      .action-buttons {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-top: 24px;
      }
      .action-buttons-right {
        display: flex;
        gap: 12px;
      }
      .btn {
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
      }
      .btn i {
        width: 16px;
        height: 16px;
      }
      .btn-back {
        border: 1px solid #d1d5db;
        background: #fff;
        color: #374151;
      }
      .btn-back:hover {
        background: #f4faf5;
      }
      .btn-edit {
        border: none;
        background: #064635;
        color: #fff;
      }
      .btn-edit:hover {
        background: #1c5240;
      }
      .btn-delete {
        border: none;
        background: #dc2626;
        color: #fff;
      }
      .btn-delete:hover {
        background: #b91c1c;
      }

      /* Stats Card */
      .stats-card {
        background: linear-gradient(135deg, #064635 0%, #519259 100%);
        color: #fff;
        border-radius: 12px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
      }
      .stats-icon {
        width: 50px;
        height: 50px;
        background: rgba(255,255,255,0.2);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
      }
      .stats-icon i {
        width: 26px;
        height: 26px;
        color: #fff;
      }
      .stats-info {
        flex: 1;
      }
      .stats-label {
        font-size: 13px;
        opacity: 0.9;
        margin-bottom: 4px;
      }
      .stats-value {
        font-size: 28px;
        font-weight: 700;
      }

      .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 2rem;
        color: #94a3b8;
      }
      .empty-state i {
        width: 48px;
        height: 48px;
        margin-bottom: 1rem;
        opacity: 0.5;
      }

      @media (max-width: 768px) {
        .detail-grid {
          grid-template-columns: 1fr;
        }
        .action-buttons {
          flex-direction: column;
          align-items: stretch;
        }
        .action-buttons-right {
          flex-direction: column;
        }
        .detail-list li {
          gap: 8px;
        }
        .dl-label {
          width: 92px;
          font-size: 10px;
          letter-spacing: 0.3px;
        }
        .dl-value {
          font-size: 13px;
        }
        .card-header-icon {
          width: 44px;
          height: 44px;
          min-width: 44px;
          position: relative !important;
          flex-shrink: 0;
        }
        .card-header-icon i,
        .card-header-icon svg {
          position: relative !important;
          top: auto !important;
          left: auto !important;
        }
      }
    </style>
  </head>

  <body>
    @include('layouts.layout-vertical')

    <div class="pc-container">
      <div class="pc-content">
        @include('layouts.breadcrumb', ['breadcrumbItemHome' => 'Master Data', 'breadcrumbItem' => 'Jenis Inventaris', 'breadcrumbItemUrl' => route('jenis-inventaris.index'), 'breadcrumbItemActive' => 'Detail'])

        <div class="page-center">

          <!-- Action Bar -->
          <div class="action-bar">
            <a href="{{ route('jenis-inventaris.index') }}" class="btn btn-back">
              <i data-feather="arrow-left"></i>
              Kembali
            </a>
            @canany(['update', 'delete'], $jenis)
            <div class="action-bar-right">
              @can('update', $jenis)
              <a href="{{ route('jenis-inventaris.edit', $jenis->id) }}" class="btn btn-edit">
                <i data-feather="edit-2"></i>
                Edit Jenis
              </a>
              @endcan
              @can('delete', $jenis)
              @if(!($isVirtual ?? false))
              <button type="button" onclick="confirmDelete()" class="btn btn-delete">
                <i data-feather="trash-2"></i>
                Hapus
              </button>
              @endif
              @endcan
            </div>
            @endcanany
          </div>

            <!-- Basic Information -->
            <div class="card mb-4">
              <div class="card-header border-b border-gray-100">
                <div class="flex items-center justify-between">
                  <div class="flex items-center">
                    <div class="card-header-icon">
                      <i data-feather="info"></i>
                    </div>
                    <div>
                      <h5 class="mb-1">
                        {{ $jenis->nama ?? 'Jenis tidak diketahui' }}
                      </h5>
                      <p class="text-muted text-sm mb-0">Detail informasi dan pengaturan jenis inventaris</p>
                    </div>
                  </div>
                  <!-- Edit button removed - using bottom action buttons instead -->
                </div>
              </div>

              <div class="card-body">
                <div class="detail-section">

                  <ul class="detail-list">
                    <li>
                      <span class="dl-label">Total Aset</span>
                      <span class="dl-value">{{ $approvedCount ?? 0 }} aset</span>
                    </li>
                    <li>
                      <span class="dl-label">Nama Jenis</span>
                      <span class="dl-value">{{ $jenis->nama ?? 'Tidak ada nama' }}</span>
                    </li>
                    <li>
                      <span class="dl-label">Deskripsi</span>
                      <span class="dl-value {{ !($jenis->deskripsi ?? null) ? 'empty' : '' }}">{{ $jenis->deskripsi ?? 'Tidak ada deskripsi' }}</span>
                    </li>
                    <li>
                      <span class="dl-label">Dibuat Pada</span>
                      <span class="dl-value">{{ $jenis->created_at ? $jenis->created_at->format('d M Y, H:i') . ' WITA' : '-' }}</span>
                    </li>
                    <li>
                      <span class="dl-label">Diperbarui</span>
                      <span class="dl-value">{{ $jenis->updated_at ? $jenis->updated_at->format('d M Y, H:i') . ' WITA' : '-' }}</span>
                    </li>
                  </ul>
                </div>
              </div>
            </div>

            <!-- Pejabat Defaults -->
            <div class="card mb-4">
              <div class="card-header border-b border-gray-100">
                <div class="flex items-center">
                  <div class="card-header-icon">
                    <i data-feather="users"></i>
                  </div>
                  <div>
                    <h5 class="mb-0">Pejabat yang Bertanda Tangan</h5>
                    <p class="text-muted text-sm mb-0">{{ $jenis->pejabatDefaults ? $jenis->pejabatDefaults->count() : 0 }} pejabat yang terdaftar untuk bertanda tangan</p>
                  </div>
                </div>
              </div>

              <div class="card-body">
                @if($jenis->pejabatDefaults && $jenis->pejabatDefaults->count() > 0)
                <div class="pejabat-list-container">
                  @foreach(($jenis->pejabatDefaults ? $jenis->pejabatDefaults->sortBy('order') : collect()) as $default)
                  <div class="pejabat-card">
                    <div class="pejabat-order">{{ $default->order }}</div>
                    <div class="pejabat-info">
                      <div class="pejabat-nama">{{ optional($default->pejabat)->nama ?? 'Pejabat tidak ditemukan' }}</div>
                      <div class="pejabat-jabatan">{{ optional($default->pejabat)->jabatan ?? '-' }}</div>
                    </div>
                  </div>
                  @endforeach
                </div>
                @else
                <div class="empty-state">
                  <i data-feather="user-x"></i>
                  <p>Belum ada pejabat default yang ditentukan</p>
                </div>
                @endif
              </div>
            </div>

            <!-- Data Umum Inventaris (Gabungan Fixed + Custom Fields) -->
            <div class="card mb-4">
              <div class="card-header border-b border-gray-100">
                <div class="flex items-center">
                  <div class="card-header-icon">
                    <i data-feather="file-text"></i>
                  </div>
                  <div>
                    <h5 class="mb-0">Data Umum Inventaris</h5>
                    <p class="text-muted text-sm mb-0">Struktur data untuk inventaris jenis ini (terurut sesuai pengaturan)</p>
                  </div>
                </div>
              </div>

              <div class="card-body">
                <table class="fields-table">
                  <thead>
                    <tr>
                      <th style="width:80px;">URUTAN</th>
                      <th>LABEL</th>
                      <th style="width:180px;">KATEGORI</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php
                      $counter = 1;
                    @endphp
                    @foreach(($fields ?? collect())->sortBy('order') as $field)
                    <tr>
                      <td>{{ $counter++ }}</td>
                      <td>
                        <span style="font-weight:500;">{{ $field->label_field }}</span>
                        @if(isset($field->description) && $field->description)
                        <span style="display:block;font-size:11px;color:#6b7280;">{{ $field->description }}</span>
                        @endif
                      </td>
                      <td class="text-muted">
                        Data Umum
                      </td>
                    </tr>
                    @endforeach

                    @if(!($isVirtual ?? false))
                    {{-- Append Buku Inventaris & Laporan fields to Data Umum --}}
                    <tr>
                      <td>{{ $counter++ }}</td>
                      <td>
                        <span style="font-weight:500;">Asal Usul Barang</span>
                        <span style="display:block;font-size:11px;color:#6b7280;">APBDes / Perolehan Lain yang Sah / Kekayaan Asli Desa</span>
                      </td>
                      <td class="text-muted" style="font-size:13px;">Data Khusus (data template sistem)</td>
                    </tr>
                    <tr>
                      <td>{{ $counter++ }}</td>
                      <td>
                        <span style="font-weight:500;">Pengguna</span>
                        <span style="display:block;font-size:11px;color:#6b7280;">Nama orang atau bidang yang menggunakan aset ini</span>
                      </td>
                      <td class="text-muted" style="font-size:13px;">Data Khusus (data template sistem)</td>
                    </tr>
                    <tr>
                      <td>{{ $counter++ }}</td>
                      <td>
                        <span style="font-weight:500;">Satuan</span>
                        <span style="display:block;font-size:11px;color:#6b7280;">Satuan pengukur barang (misal: Unit, Buah, Set, dll.)</span>
                      </td>
                      <td class="text-muted" style="font-size:13px;">Data Laporan Aset Pertahun</td>
                    </tr>
                    @endif

                    @if(($fields ?? collect())->count() === 0 && ($isVirtual ?? false))
                    <tr>
                      <td colspan="3" class="text-center py-4 text-muted">
                        <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;gap:8px;">
                          <i data-feather="inbox" style="width:40px;height:40px;opacity:0.5;"></i>
                          <p style="margin:0;">Belum ada field yang ditambahkan</p>
                        </div>
                      </td>
                    </tr>
                    @endif
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Data KIB (Kartu Inventaris Barang) — Khusus Jenis yang mendapatkan KIB saja -->
            @if(!($isVirtual ?? false) && $jenis->kib_type && ($kibConfig = config('kib.' . $jenis->kib_type)))
            <div class="card mb-4" style="border:1px solid #e2e8f0;">
              <div class="card-header border-b border-gray-100">
                <div class="flex items-center">
                  <div class="card-header-icon" style="background:#519259;">
                    <i data-feather="clipboard"></i>
                  </div>
                  <div>
                    <h5 class="mb-0">Data KIB (KIB {{ $jenis->kib_type }} - {{ $kibConfig['nama'] }})</h5>
                    <p class="text-muted text-sm mb-0">Struktur data Kartu Inventaris Barang untuk jenis inventaris ini</p>
                  </div>
                </div>
              </div>
              <div class="card-body" style="padding-top:12px;">
                <div style="background:#f4faf5;border:1px dashed #d1d5db;border-radius:8px;padding:10px 14px;display:flex;align-items:flex-start;gap:8px;margin-bottom:14px;">
                  <i data-feather="info" style="width:15px;height:15px;stroke:#6b7280;flex-shrink:0;margin-top:2px;"></i>
                  <span style="font-size:12px;color:#374151;line-height:1.5;">Field di bawah ini adalah data wajib KIB yang otomatis dikelola sistem berdasarkan format standar Permendagri.</span>
                </div>
                <table class="fields-table">
                  <thead>
                    <tr>
                      <th style="width:80px;">URUTAN</th>
                      <th>LABEL</th>
                      <th style="width:180px;">KATEGORI</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach(($kibConfig['fields'] ?? []) as $index => $kibField)
                    <tr>
                      <td>{{ $index + 1 }}</td>
                      <td>
                        <span style="font-weight:500;">{{ $kibField['label'] }}</span>
                      </td>
                      <td>
                        <span style="font-size:13px;color:#374151;font-weight:500;">KIB {{ $jenis->kib_type }}</span>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
            @endif

            <!-- Data KIR (Kartu Inventaris Ruangan) — Khusus Jenis yang memerlukan Ruangan saja -->
            @if($jenis->requires_ruangan)
            <div class="card mb-4" style="border:1px solid #e2e8f0;">
              <div class="card-header border-b border-gray-100">
                <div class="flex items-center">
                  <div class="card-header-icon" style="background:#519259;">
                    <i data-feather="grid"></i>
                  </div>
                  <div>
                    <h5 class="mb-0">Data KIR (Kartu Inventaris Ruangan)</h5>
                    <p class="text-muted text-sm mb-0">Struktur data Kartu Inventaris Ruangan untuk jenis inventaris ini</p>
                  </div>
                </div>
              </div>
              <div class="card-body" style="padding-top:12px;">
                <div style="background:#f4faf5;border:1px dashed #d1d5db;border-radius:8px;padding:10px 14px;display:flex;align-items:flex-start;gap:8px;margin-bottom:14px;">
                  <i data-feather="info" style="width:15px;height:15px;stroke:#6b7280;flex-shrink:0;margin-top:2px;"></i>
                  <span style="font-size:12px;color:#374151;line-height:1.5;">Field di bawah ini adalah data wajib KIR yang otomatis dikelola sistem.</span>
                </div>
                <table class="fields-table">
                  <thead>
                    <tr>
                      <th style="width:80px;">URUTAN</th>
                      <th>LABEL</th>
                      <th style="width:180px;">KATEGORI</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php
                      $kirFieldsShow = [
                        'Ruangan',
                        'Konstruksi',
                        'Luas',
                        'Merk / Tipe',
                        'Warna',
                        'Judul',
                        'Pencipta',
                        'No. Polisi'
                      ];
                    @endphp
                    @foreach($kirFieldsShow as $index => $label)
                    <tr>
                      <td>{{ $index + 1 }}</td>
                      <td>
                        <span style="font-weight:500;">{{ $label }}</span>
                      </td>
                      <td>
                        <span style="font-size:13px;color:#374151;font-weight:500;">KIR</span>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
            @endif

            <!-- Delete Form (hidden) -->
            @can('delete', $jenis)
            @if(!($isVirtual ?? false))
            <form id="deleteForm" action="{{ route('jenis-inventaris.destroy', $jenis->id) }}" method="POST" style="display:none;">
              @csrf
              @method('DELETE')
            </form>
            @endif
            @endcan

          </div><!-- /.page-center -->
      </div>
    </div>

    @include('layouts.footer-block')
    @include('layouts.footer-js')

    <script>
      function confirmDelete() {
        if (!document.getElementById('deleteForm')) return;
        if (confirm('Apakah Anda yakin ingin menghapus jenis inventaris "{{ $jenis->nama }}"?\n\nSemua data inventaris yang terkait akan ikut terhapus!')) {
          document.getElementById('deleteForm').submit();
        }
      }
    </script>
  </body>
</html>
