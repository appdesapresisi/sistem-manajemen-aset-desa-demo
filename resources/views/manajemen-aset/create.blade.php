<!doctype html>
<html lang="en" data-pc-preset="{{ $pc_preset_theme }}" data-pc-theme="{{ $pc_dark_layout }}">
  <head>
    @include('layouts.head-page-meta', ['title' => 'Tambah Inventaris'])
    @include('layouts.head-css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
      /* Form Styling */
      .form-section {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        border: 1px solid #e2e8f0;
      }
      .form-section-title {
        font-size: 14px;
        font-weight: 600;
        color: #064635;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 10px;
      }
      /* Wrapper kotak ikon di depan judul section */
      .form-section-title .section-icon-box {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        min-width: 28px;
        border-radius: 7px;
        background: linear-gradient(135deg, #064635 0%, #519259 100%);
        flex-shrink: 0;
      }
      .form-section-title .section-icon-box i,
      .form-section-title .section-icon-box svg {
        width: 14px;
        height: 14px;
        color: #fff !important;
        stroke: #fff !important;
        margin: 0;
      }
      .form-section-title-content {
        display: flex;
        flex-direction: column;
      }
      .form-section-desc {
        font-size: 11px;
        color: #6b7280;
        margin-top: 2px;
        font-weight: 400;
        line-height: 1.4;
      }
      .form-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 16px;
      }
      .form-grid-2 {
        display: grid;
        grid-template-columns: 1fr;
        gap: 16px;
      }
      .form-group {
        margin-bottom: 0;
      }
      .form-label {
        font-weight: 500;
        font-size: 13px;
        color: #374151;
        margin-bottom: 6px;
        display: block;
      }
      .form-label .required {
        color: #ef4444;
        margin-left: 2px;
      }
      .form-label .optional {
        color: #9ca3af;
        font-weight: 400;
        font-size: 11px;
        margin-left: 4px;
      }
      .form-control, .form-select {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.2s ease;
        background: #fff;
      }
      .form-control:focus, .form-select:focus {
        outline: none;
        border-color: #064635;
        box-shadow: 0 0 0 3px rgba(6, 70, 53, 0.1);
      }
      .form-control::placeholder {
        color: #9ca3af;
      }
      .form-hint {
        font-size: 12px;
        color: #6b7280;
        margin-top: 4px;
      }
      .flatpickr-calendar {
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        box-shadow: 0 4px 16px rgba(15, 23, 42, 0.08);
        font-family: 'Open Sans', sans-serif;
      }
      .flatpickr-calendar .flatpickr-months {
        padding: 8px 10px 0;
      }
      .flatpickr-calendar .flatpickr-current-month {
        font-size: 14px;
        font-weight: 600;
        color: #1f2937;
      }
      .flatpickr-calendar .flatpickr-current-month .cur-month,
      .flatpickr-calendar .flatpickr-current-month input.cur-year {
        font-weight: 600;
        color: #1f2937;
      }
      .form-control.month-picker,
      .form-control.date-picker,
      .flatpickr-input.month-picker,
      .flatpickr-input.date-picker {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        background-image: none !important;
        padding-right: 14px !important;
      }
      .form-control.month-picker::-webkit-calendar-picker-indicator,
      .form-control.date-picker::-webkit-calendar-picker-indicator,
      .flatpickr-input.month-picker::-webkit-calendar-picker-indicator,
      .flatpickr-input.date-picker::-webkit-calendar-picker-indicator {
        display: none !important;
        opacity: 0 !important;
      }
      .flatpickr-calendar.month-grid-picker {
        width: 236px;
        min-width: 236px;
      }
      .flatpickr-calendar.month-grid-picker .flatpickr-innerContainer,
      .flatpickr-calendar.month-grid-picker .flatpickr-weekdays,
      .flatpickr-calendar.month-grid-picker .flatpickr-monthDropdown-months,
      .flatpickr-calendar.month-grid-picker .flatpickr-prev-month,
      .flatpickr-calendar.month-grid-picker .flatpickr-next-month {
        display: none !important;
      }
      .flatpickr-calendar.month-grid-picker .flatpickr-months {
        display: none !important;
      }
      .flatpickr-calendar.month-grid-picker .month-picker-panel {
        padding: 10px;
      }
      .flatpickr-calendar.month-grid-picker .month-picker-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 10px;
      }
      .flatpickr-calendar.month-grid-picker .month-picker-year {
        font-size: 12px;
        font-weight: 700;
        color: #1f2937;
      }
      .flatpickr-calendar.month-grid-picker .month-picker-nav {
        width: 26px;
        height: 26px;
        border-radius: 7px;
        border: 1px solid #d1d5db;
        background: #fff;
        color: #374151;
        font-size: 16px;
        line-height: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
      }
      .flatpickr-calendar.month-grid-picker .month-picker-nav:hover:not(:disabled) {
        background: #f3f4f6;
      }
      .flatpickr-calendar.month-grid-picker .month-picker-nav:disabled {
        opacity: .45;
        cursor: not-allowed;
      }
      .flatpickr-calendar.month-grid-picker .month-picker-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 8px;
      }
      .flatpickr-calendar.month-grid-picker .month-picker-option {
        border: 1px solid #d1d5db;
        border-radius: 8px;
        background: #fff;
        color: #374151;
        font-size: 12px;
        font-weight: 600;
        padding: 8px 0;
        cursor: pointer;
      }
      .flatpickr-calendar.month-grid-picker .month-picker-option:hover {
        background: #f3f4f6;
      }
      .flatpickr-calendar.month-grid-picker .month-picker-option.active {
        background: #1f2937;
        border-color: #1f2937;
        color: #fff;
      }
      .flatpickr-calendar.date-clean-picker .flatpickr-months {
        padding: 8px 10px 0;
      }
      .flatpickr-calendar.date-clean-picker .flatpickr-prev-month,
      .flatpickr-calendar.date-clean-picker .flatpickr-next-month {
        border-radius: 7px;
      }
      .flatpickr-calendar.date-clean-picker .flatpickr-day {
        border-radius: 8px;
        border-color: transparent;
      }
      .flatpickr-calendar.date-clean-picker .flatpickr-day.selected,
      .flatpickr-calendar.date-clean-picker .flatpickr-day.startRange,
      .flatpickr-calendar.date-clean-picker .flatpickr-day.endRange {
        background: #1f2937;
        border-color: #1f2937;
        color: #fff;
      }
      .flatpickr-calendar.date-clean-picker .flatpickr-day:hover {
        background: #f3f4f6;
      }

      /* Modern date picker: header bulan & tahun bisa diklik */
      /* Sejajarkan header kalender: nav kiri, tengah (bulan+tahun), nav kanan */
      .flatpickr-calendar.date-clean-picker .flatpickr-months {
        display: flex !important;
        align-items: center !important;
        padding: 8px 4px 0 !important;
      }
      .flatpickr-calendar.date-clean-picker .flatpickr-prev-month,
      .flatpickr-calendar.date-clean-picker .flatpickr-next-month {
        position: static !important;
        flex-shrink: 0;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        top: unset !important;
        padding: 0 !important;
        width: 28px !important;
        height: 28px !important;
        border: 1px solid #e5e7eb !important;
        border-radius: 6px !important;
        background: #fff !important;
        color: #374151 !important;
        transition: background 0.15s !important;
      }
      .flatpickr-calendar.date-clean-picker .flatpickr-prev-month:hover,
      .flatpickr-calendar.date-clean-picker .flatpickr-next-month:hover {
        background: #f3f4f6 !important;
      }
      .flatpickr-calendar.date-clean-picker .flatpickr-prev-month svg,
      .flatpickr-calendar.date-clean-picker .flatpickr-next-month svg {
        fill: #374151 !important;
        width: 12px !important;
        height: 12px !important;
      }
      /* Tanggal dari bulan lain: sembunyikan teks, tetap ada tapi transparan */
      .flatpickr-calendar.date-clean-picker .flatpickr-day.prevMonthDay,
      .flatpickr-calendar.date-clean-picker .flatpickr-day.nextMonthDay {
        visibility: hidden !important;
        pointer-events: none !important;
      }
      /* Warna teks nama hari — warna sistem */
      .flatpickr-calendar.date-clean-picker .flatpickr-weekday {
        color: #064635 !important;
        font-weight: 600 !important;
      }
      .flatpickr-calendar.date-clean-picker .flatpickr-month {
        flex: 1 !important;
        height: auto !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
      }
      .flatpickr-calendar.date-clean-picker .flatpickr-current-month {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 2px;
        position: static !important;
        width: auto !important;
        padding: 0 !important;
        font-size: 14px !important;
      }
      .flatpickr-calendar.date-clean-picker .cur-month,
      .flatpickr-calendar.date-clean-picker .cur-year-wrap {
        cursor: pointer;
        padding: 2px 6px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 14px;
        color: #1f2937;
        transition: background 0.15s;
        user-select: none;
      }
      .flatpickr-calendar.date-clean-picker .cur-month:hover,
      .flatpickr-calendar.date-clean-picker .cur-year-wrap:hover {
        background: #f3f4f6;
      }
      /* Sembunyikan wrapper input tahun bawaan flatpickr (termasuk spinner panah) */
      .flatpickr-calendar.date-clean-picker .flatpickr-current-month .numInputWrapper {
        display: none !important;
      }
      /* Span tahun custom yang bisa diklik */
      .flatpickr-calendar.date-clean-picker .fp-cur-year {
        cursor: pointer;
        padding: 2px 8px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 14px;
        color: #1f2937;
        user-select: none;
        transition: background 0.15s;
      }
      .flatpickr-calendar.date-clean-picker .fp-cur-year:hover {
        background: #f3f4f6;
      }
      /* Panel bulan & tahun — mengacu pada tampilan year picker tahun perolehan */
      .fp-month-panel, .fp-year-panel {
        display: none;
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: #fff;
        border-radius: inherit;
        padding: 10px;
        z-index: 10;
        flex-direction: column;
        gap: 8px;
        box-sizing: border-box;
      }
      .fp-month-panel.show, .fp-year-panel.show { display: flex; }
      .fp-panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-shrink: 0;
      }
      .fp-panel-title {
        font-size: 12px;
        font-weight: 700;
        color: #1f2937;
      }
      /* Nav button: kotak dengan border — konsisten dengan acuan year picker */
      .fp-panel-close, .fp-panel-nav {
        width: 26px; height: 26px;
        border: 1px solid #e5e7eb;
        background: #fff;
        font-size: 13px;
        cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        color: #374151;
        border-radius: 6px;
        transition: background 0.15s;
      }
      .fp-panel-close:hover, .fp-panel-nav:hover:not(:disabled) { background: #f3f4f6; }
      .fp-panel-nav:disabled { opacity: 0.45; cursor: not-allowed; }
      /* Grid 12 item — tidak pakai flex:1 agar tinggi baris proporsional */
      .fp-month-grid, .fp-year-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        grid-template-rows: repeat(4, 1fr);
        gap: 8px;
        flex: 1;
      }
      .fp-month-btn, .fp-year-btn {
        border-radius: 8px;
        border: 1px solid #d1d5db;
        background: #fff;
        font-size: 12px;
        font-weight: 600;
        color: #374151;
        cursor: pointer;
        text-align: center;
        transition: all 0.15s;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
      }
      .fp-month-btn:hover, .fp-year-btn:hover { background: #f3f4f6; }
      .fp-month-btn.active, .fp-year-btn.active {
        background: #1f2937;
        border-color: #1f2937;
        color: #fff;
      }
      .flatpickr-calendar.year-only-picker {
        width: 236px;
        min-width: 236px;
      }
      .flatpickr-calendar.year-only-picker .flatpickr-innerContainer,
      .flatpickr-calendar.year-only-picker .flatpickr-weekdays,
      .flatpickr-calendar.year-only-picker .flatpickr-monthDropdown-months,
      .flatpickr-calendar.year-only-picker .flatpickr-prev-month,
      .flatpickr-calendar.year-only-picker .flatpickr-next-month {
        display: none !important;
      }
      .flatpickr-calendar.year-only-picker .flatpickr-months {
        display: none !important;
      }
      .flatpickr-calendar.year-only-picker .year-picker-panel {
        padding: 10px;
      }
      .flatpickr-calendar.year-only-picker .year-picker-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 10px;
      }
      .flatpickr-calendar.year-only-picker .year-picker-range {
        font-size: 12px;
        font-weight: 700;
        color: #1f2937;
      }
      .flatpickr-calendar.year-only-picker .year-picker-nav {
        width: 26px;
        height: 26px;
        border-radius: 7px;
        border: 1px solid #d1d5db;
        background: #fff;
        color: #374151;
        font-size: 16px;
        line-height: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
      }
      .flatpickr-calendar.year-only-picker .year-picker-nav:hover:not(:disabled) {
        background: #f3f4f6;
      }
      .flatpickr-calendar.year-only-picker .year-picker-nav:disabled {
        opacity: .45;
        cursor: not-allowed;
      }
      .flatpickr-calendar.year-only-picker .year-picker-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 8px;
      }
      .flatpickr-calendar.year-only-picker .year-picker-option {
        border: 1px solid #d1d5db;
        border-radius: 8px;
        background: #fff;
        color: #374151;
        font-size: 12px;
        font-weight: 600;
        padding: 8px 0;
        cursor: pointer;
      }
      .flatpickr-calendar.year-only-picker .year-picker-option:hover {
        background: #f3f4f6;
      }
      .flatpickr-calendar.year-only-picker .year-picker-option.active {
        background: #1f2937;
        border-color: #1f2937;
        color: #fff;
      }

      /* Card Header */
      .card-header-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        min-width: 44px;
        border-radius: 12px;
        background: #519259;
        margin-right: 14px;
        position: relative !important;
        flex-shrink: 0;
      }
      .card-header-icon svg,
      .card-header-icon i {
        color: #fff !important;
        stroke: #fff !important;
        width: 22px;
        height: 22px;
        position: relative !important;
        top: unset !important;
        left: unset !important;
      }

      /* Searchable Dropdown Container */
      .searchable-dropdown {
        position: relative;
        width: 100%;
      }

      /* Tombol Trigger Select */
      .dropdown-trigger {
        width: 100% !important;
        text-align: left !important;
        background-color: #fff !important;
        cursor: pointer !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        padding: 10px 16px !important;
        border: 1px solid #d1d5db !important;
        border-radius: 8px !important;
        transition: all 0.2s !important;
      }
      .dropdown-trigger:focus {
        border-color: #064635 !important;
        box-shadow: 0 0 0 3px rgba(6, 70, 53, 0.1) !important;
        outline: none !important;
      }

      /* Panel Menu Dropdown */
      .dropdown-menu-panel {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        z-index: 1000;
        background: #fff;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        width: 100%;
        margin-top: 4px;
        padding: 8px;
        box-sizing: border-box;
      }
      .dropdown-menu-panel.show {
        display: block;
      }

      /* Input Pencarian */
      .dropdown-search-wrapper {
        position: relative;
        margin-bottom: 8px;
      }
      .dropdown-search-wrapper i,
      .dropdown-search-wrapper svg {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        width: 14px;
        height: 14px;
        pointer-events: none;
      }
      .dropdown-search-input {
        width: 100% !important;
        padding: 8px 12px 8px 34px !important;
        font-size: 13px !important;
        border: 1px solid #d1d5db !important;
        border-radius: 6px !important;
        outline: none !important;
        box-sizing: border-box !important;
      }

      /* Opsi Item List */
      .dropdown-options-list {
        max-height: 240px;
        overflow-y: auto;
        padding: 0;
        margin: 0;
        list-style: none;
      }
      .dropdown-option-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 12px;
        border-radius: 6px;
        cursor: pointer;
        transition: background 0.15s;
        user-select: none;
        border-bottom: 1px solid #f3f4f6;
      }
      .dropdown-option-item:last-child {
        border-bottom: none;
      }
      .dropdown-option-item:hover {
        background: #f3f4f6;
      }
      .dropdown-option-item.selected {
        background: #eff6ff;
        color: #2563eb;
      }
      .dropdown-option-icon {
        width: 28px;
        height: 28px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
      }
      .dropdown-option-icon i,
      .dropdown-option-icon svg {
        width: 14px;
        height: 14px;
        color: #fff !important;
        stroke: #fff !important;
      }
      .dropdown-option-name {
        font-size: 13px;
        font-weight: 500;
        color: #374151;
      }

      /* Animasi Chevron */
      .dropdown-trigger-caret {
        transition: transform 0.2s;
        color: #9ca3af;
      }
      .searchable-dropdown.open .dropdown-trigger-caret {
        transform: rotate(180deg);
      }

      .jenis-error-text {
        display: none;
        margin-top: 8px;
        font-size: 12px;
        color: #dc2626;
        font-weight: 500;
      }

      /* Dynamic Fields Section */
      .dynamic-fields-section {
        display: none;
        animation: fadeIn 0.3s ease;
      }
      .dynamic-fields-section.show {
        display: block;
      }
      @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
      }
      .dynamic-fields-header {
        background: linear-gradient(135deg, #064635 0%, #519259 100%);
        color: #fff;
        padding: 12px 16px;
        border-radius: 8px 8px 0 0;
        font-size: 13px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
      }
      .dynamic-fields-header i {
        width: 16px;
        height: 16px;
      }
      .dynamic-fields-body {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-top: none;
        border-radius: 0 0 8px 8px;
        padding: 16px;
      }

      /* Kondisi Radio Group */
      .kondisi-group {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
      }
      .kondisi-option {
        flex: 1;
        min-width: 120px;
      }
      .kondisi-option input {
        position: absolute;
        opacity: 0;
        width: 1px;
        height: 1px;
        margin: 0;
        pointer-events: none;
      }
      .kondisi-option label {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 12px 16px;
        background: #fff;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 13px;
        font-weight: 500;
      }
      .kondisi-option input:checked + label {
        border-color: currentColor;
      }
      .kondisi-option.baik label {
        color: #16a34a;
      }
      .kondisi-option.baik input:checked + label {
        background: #dcfce7;
        border-color: #16a34a;
      }
      .kondisi-option.rusak-ringan label {
        color: #d97706;
      }
      .kondisi-option.rusak-ringan input:checked + label {
        background: #fef3c7;
        border-color: #d97706;
      }
      .kondisi-option.rusak-berat label {
        color: #dc2626;
      }
      .kondisi-option.rusak-berat input:checked + label {
        background: #fee2e2;
        border-color: #dc2626;
      }
      .kondisi-option label i {
        width: 16px;
        height: 16px;
      }

      /* Buku Inventaris Aset Desa Section */
      .buku-inventaris-section {
        background: #f8f9fa;
        border: 1.5px solid #d1d5db;
        border-radius: 10px;
        padding: 16px;
        margin-top: 16px;
      }
      .buku-inventaris-header {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        margin-bottom: 14px;
      }
      .buku-inventaris-header-icon {
        width: 28px;
        height: 28px;
        border-radius: 7px;
        background: transparent;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        margin-top: 2px;
      }
      .buku-inventaris-header-icon i,
      .buku-inventaris-header-icon svg {
        width: 18px;
        height: 18px;
        stroke: #064635;
        color: #064635;
      }
      .buku-inventaris-header-content {
        display: flex;
        flex-direction: column;
        justify-content: center;
      }
      .buku-inventaris-title {
        font-size: 13px;
        font-weight: 600;
        color: #064635;
        line-height: 1.4;
      }
      .buku-inventaris-desc {
        font-size: 11px;
        color: #6b7280;
        margin: 2px 0 0 0;
        line-height: 1.4;
      }
      .asal-usul-group {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
      }
      .asal-usul-option {
        flex: 1;
        min-width: 140px;
      }
      @media (max-width: 480px) {
        .asal-usul-group {
          flex-direction: column;
        }
        .asal-usul-option {
          flex: unset;
          min-width: unset;
          width: 100%;
        }
      }
      .asal-usul-option input {
        position: absolute;
        opacity: 0;
        width: 1px;
        height: 1px;
        margin: 0;
        pointer-events: none;
      }
      .asal-usul-option label {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 10px 14px;
        background: #fff;
        border: 2px solid #d1d5db;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 12px;
        font-weight: 500;
        color: #374151;
        text-align: center;
        line-height: 1.4;
      }
      .asal-usul-option label:hover {
        border-color: #064635;
        background: #f4faf5;
      }
      .asal-usul-option input:checked + label {
        background: #064635;
        border-color: #064635;
        color: #fff;
        font-weight: 600;
      }
      .asal-usul-group.group-invalid .asal-usul-option label {
        border-color: #dc2626;
      }



      /* Info box */
      .info-box {
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        border: 1px solid #93c5fd;
        border-radius: 8px;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 16px;
      }
      .info-box i {
        color: #1e40af;
        stroke: #1e40af;
        flex-shrink: 0;
        width: 18px;
        height: 18px;
      }
      .info-box-text {
        font-size: 13px;
        color: #1e40af;
        line-height: 1.5;
      }

      /* Buttons */
      .form-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        padding-top: 20px;
        border-top: 1px solid #e5e7eb;
        margin-top: 24px;
      }
      .btn-cancel {
        padding: 10px 20px;
        border: 1px solid #d1d5db;
        background: #fff;
        color: #374151;
        border-radius: 8px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
      }
      .btn-cancel:hover {
        background: #f3f4f6;
      }
      .btn-submit {
        padding: 10px 24px;
        border: none;
        background: #064635;
        color: #fff;
        border-radius: 8px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 8px;
      }
      .btn-submit:hover {
        background: #1c5240;
      }
      .btn-submit i {
        width: 18px;
        height: 18px;
      }

      /* Currency Input */
      .currency-input-wrapper {
        position: relative;
      }
      .currency-prefix {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 14px;
        color: #6b7280;
        font-weight: 500;
      }
      .currency-input-wrapper .form-control {
        padding-left: 42px;
      }
      .unit-suffix-input-wrapper {
        position: relative;
      }
      .unit-suffix {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 12px;
        color: #6b7280;
        font-weight: 600;
        pointer-events: none;
      }
      .unit-suffix-input-wrapper .form-control {
        padding-right: 52px;
      }

      /* File upload (seragam dengan form user & pejabat) */
      .file-upload-area {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 12px;
        padding: 30px 20px;
        background: #fff;
        border: 2px dashed #d1d5db;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.2s;
        text-align: center;
      }
      .file-upload-area:hover { border-color: #064635; background: #f4faf5; }
      .file-upload-area.has-file { border-color: #22c55e; background: #f0fdf4; }
      .file-upload-icon {
        width: 48px; height: 48px; border-radius: 12px;
        background: linear-gradient(135deg, #064635 0%, #519259 100%);
        display: flex; align-items: center; justify-content: center;
      }
      .file-upload-icon i, .file-upload-icon svg { width: 24px; height: 24px; color: #fff !important; stroke: #fff !important; }
      .file-upload-text { font-size: 14px; color: #374151; font-weight: 500; }
      .file-upload-hint { font-size: 12px; color: #9ca3af; }
      .foto-preview-thumb {
        position: relative; width: 90px; height: 90px;
        border-radius: 10px; overflow: hidden;
        border: 1px solid #e5e7eb; flex-shrink: 0;
      }
      .foto-preview-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
      .foto-preview-thumb .remove-btn {
        position: absolute; top: 3px; right: 3px;
        width: 20px; height: 20px; border-radius: 50%;
        background: rgba(220,38,38,.8); color: #fff;
        border: none; font-size: 13px; line-height: 1;
        cursor: pointer; display: flex; align-items: center; justify-content: center;
      }

      @media (max-width: 768px) {
        .jenis-selector {
          grid-template-columns: repeat(2, 1fr);
        }
      }
      @media (max-width: 480px) {
        .jenis-selector {
          grid-template-columns: 1fr;
        }
      }

      /* Card header: desktop = justify-between, mobile = stacked */
      .card-header-inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
      }
      .card-status-mobile { display: none; }
      .card-status-desktop { display: flex; align-items: center; gap: 0.5rem; }
      @media (max-width: 640px) {
        .card-header-inner { display: block; }
        .card-status-mobile { display: flex; align-items: center; gap: 0.5rem; margin-top: 4px; }
        .card-status-desktop { display: none; }
      }
      /* Searchable Select Component */
      .searchable-select { position: relative; width: 100%; }
      .ss-trigger { display: flex; align-items: center; justify-content: space-between; padding: 10px 16px; border: 1px solid #e5e7eb; border-radius: 8px; background: #fff; cursor: pointer; font-size: 14px; color: #374151; transition: all 0.2s; }
      .ss-trigger:hover { border-color: #d1d5db; }
      .ss-trigger.ss-open { border-color: #064635; box-shadow: 0 0 0 3px rgba(6, 70, 53,0.1); }
      .ss-value-text { flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
      .ss-dropdown { position: absolute; top: calc(100% + 4px); left: 0; right: 0; background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; box-shadow: 0 10px 40px rgba(0,0,0,0.12); z-index: 1000; max-height: 280px; display: flex; flex-direction: column; }
      .ss-search-wrap { padding: 10px 12px; border-bottom: 1px solid #f3f4f6; flex-shrink: 0; }
      .ss-search { width: 100%; padding: 8px 12px; border: 1px solid #e5e7eb; border-radius: 6px; font-size: 13px; outline: none; }
      .ss-search:focus { border-color: #064635; box-shadow: 0 0 0 3px rgba(6, 70, 53,0.1); }
      .ss-options { overflow-y: auto; max-height: 200px; }
      .ss-option { padding: 9px 14px; font-size: 13px; color: #374151; cursor: pointer; transition: background 0.15s; }
      .ss-option:hover { background: #f4faf5; }
      .ss-option.ss-selected { background: #f0f4f8; font-weight: 600; color: #064635; }
      .ss-option.ss-hidden { display: none; }
      .ss-empty { padding: 16px; text-align: center; color: #9ca3af; font-size: 13px; }
    </style>
  </head>

  <body>
    @include('layouts.modals')
    @include('layouts.layout-vertical')

    <div class="pc-container">
      <div class="pc-content">
        @include('layouts.breadcrumb', ['breadcrumbItemHome' => 'Inventaris', 'breadcrumbItem' => 'Manajemen Aset', 'breadcrumbItemUrl' => route('manajemen-aset.index'), 'breadcrumbItemActive' => 'Tambah Inventaris'])

        @if($errors->any())
        <div style="background:#fee2e2;border:1px solid #fca5a5;border-radius:8px;padding:12px 16px;margin-bottom:16px;">
            <div style="font-weight:600;color:#dc2626;margin-bottom:6px;">Terdapat kesalahan pada data yang dikiri:</div>
            <ul style="margin:0;padding-left:20px;color:#b91c1c;font-size:13px;">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <div class="grid grid-cols-12 gap-x-6">
          <div class="col-span-12">
            <div class="card">
              <div class="card-header border-b border-gray-100">
                <div class="card-header-inner">
                  <div class="flex items-center">
                    <div class="card-header-icon">
                      <i data-feather="package"></i>
                    </div>
                    <div>
                      <h5 class="mb-1">Tambah Inventaris Baru</h5>
                      <p class="text-muted text-sm mb-0">Isi data inventaris sesuai jenis yang dipilih</p>
                      {{-- <div class="items-center gap-2 card-status-mobile">
                        <span class="text-xs text-muted">Status:</span>
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700" id="status-display-mobile">Pending</span>
                      </div> --}}
                    </div>
                  </div>
                  {{-- <div class="items-center gap-2 card-status-desktop">
                    <span class="text-sm text-muted">Status:</span>
                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700" id="status-display">Pending</span>
                  </div> --}}
                </div>
              </div>

              <div class="card-body pt-4">
                @if($errors->any())
                <div class="alert alert-danger mb-4" role="alert">
                    <div class="flex items-center gap-2 mb-2">
                        <i data-feather="alert-circle" style="width:20px;height:20px;"></i>
                        <strong>Terdapat kesalahan pada input:</strong>
                    </div>
                    <ul class="list-disc ml-6 mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ route('manajemen-aset.store') }}" method="POST" id="inventaris-form" enctype="multipart/form-data">
                  @csrf
                  
                  <!-- Step 1: Pilih Jenis Inventaris -->
                  <div class="buku-inventaris-section" style="margin-top: 0;">
                    <div class="buku-inventaris-header" style="margin-bottom: 16px;">
                      <div class="buku-inventaris-header-icon">
                        <i data-feather="layers"></i>
                      </div>
                      <div class="buku-inventaris-header-content">
                        <span class="buku-inventaris-title">Pilih Jenis Inventaris</span>
                        <p class="buku-inventaris-desc">Pilih jenis inventaris untuk menampilkan form isian data yang sesuai.</p>
                      </div>
                    </div>
                    <input type="hidden" name="jenis_inventaris_id" id="jenis_inventaris_id_hidden" value="" />

                    <div class="searchable-dropdown" id="jenis-dropdown-container">
                      <button type="button" class="dropdown-trigger" id="jenis-dropdown-trigger">
                        <span id="jenis-dropdown-selected-label" style="color: #9ca3af; font-size: 13px; font-weight: 500;">-- Pilih Jenis Inventaris --</span>
                        <i data-feather="chevron-down" class="dropdown-trigger-caret" style="width: 16px; height: 16px;"></i>
                      </button>
                      
                      <div class="dropdown-menu-panel" id="jenis-dropdown-menu">
                        <div class="dropdown-search-wrapper">
                          <i data-feather="search"></i>
                          <input type="text" id="jenis-dropdown-search" class="dropdown-search-input" placeholder="Cari jenis inventaris..." autocomplete="off" />
                        </div>
                        <ul class="dropdown-options-list" id="jenis-dropdown-options-list">
                          <!-- Di-render dinamis oleh JavaScript -->
                        </ul>
                      </div>
                    </div>
                    <p id="jenis-error" class="jenis-error-text">Jenis inventaris wajib dipilih.</p>
                    <!-- debug removed -->
                  </div>

                  <!-- Step 2: Field Builder (Fixed & Custom, urut, label, tipe, required) -->
                  <div class="buku-inventaris-section dynamic-fields-section" id="builder-fields-section" style="display:none;margin-top:16px;">
                    <div class="buku-inventaris-header" style="margin-bottom: 16px;">
                      <div class="buku-inventaris-header-icon">
                        <i data-feather="file-text"></i>
                      </div>
                      <div class="buku-inventaris-header-content">
                        <span class="buku-inventaris-title">Data Inventaris</span>
                        <p class="buku-inventaris-desc">Field ini digunakan untuk mengisi informasi utama dan data umum dari inventaris.</p>
                      </div>
                    </div>
                    <div id="builder-fields-message" style="display:none; color:#ef4444; font-size:13px; margin-bottom:12px;"></div>
                    <div class="form-grid" id="builder-fields-container">
                      <!-- Semua field builder akan di-render oleh JavaScript setelah jenis dipilih -->
                    </div>
                    <!-- Field Ruangan — selalu di paling bawah Data Inventaris, muncul jika jenis requires_ruangan = 1 -->
                    <!-- Field Ruangan moved to KIR section -->
                    <!-- Asal Usul Barang & Pengguna — selalu ada di section Data Inventaris -->
                    <div class="form-group" style="margin-top:16px;">
                      <label class="form-label">Asal Usul Barang <span class="required">*</span></label>
                      <select name="asal_usul" id="asal_usul" class="form-control form-select">
                        <option value="" disabled hidden {{ old('asal_usul') ? '' : 'selected' }}>-- Pilih Asal Usul Barang --</option>
                        <option value="apbdes" {{ old('asal_usul') === 'apbdes' ? 'selected' : '' }}>APBDes</option>
                        <option value="perolehan_lain" {{ old('asal_usul') === 'perolehan_lain' ? 'selected' : '' }}>Perolehan Lain yang Sah</option>
                        <option value="kekayaan_asli" {{ old('asal_usul') === 'kekayaan_asli' ? 'selected' : '' }}>Kekayaan Asli Desa</option>
                      </select>
                      @error('asal_usul')
                        <small class="text-danger">{{ $message }}</small>
                      @enderror
                      <small id="asal-usul-client-error" class="text-danger" style="display:none;">Asal Usul Barang wajib dipilih.</small>
                    </div>
                    <div class="form-group" style="margin-top:16px;">
                      <label class="form-label" for="pengguna">Pengguna <span class="required">*</span></label>
                      <input type="hidden" name="pengguna" id="pengguna" value="{{ old('pengguna') }}">
                      <div class="searchable-select" id="ss-pengguna">
                        <div class="ss-trigger" onclick="ssToggle('ss-pengguna')">
                          <span class="ss-value-text" id="ss-pengguna-text">{{ old('pengguna') ?: '-- Pilih Pengguna --' }}</span>
                          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                        </div>
                        <div class="ss-dropdown" style="display:none;">
                          <div class="ss-search-wrap">
                            <input type="text" class="ss-search" placeholder="Cari pengguna..." oninput="ssFilter('ss-pengguna', this.value)">
                          </div>
                          <div class="ss-options">
                            @forelse($userList as $user)
                              @php
                                $roleLabel = $user->role_name_display ?? '';
                                $label = $user->nama . ($roleLabel ? ' ('.$roleLabel.')' : '');
                              @endphp
                              <div class="ss-option" data-value="{{ $user->nama }}" data-search="{{ strtolower($label) }}" onclick="ssSelect('ss-pengguna', '{{ addslashes($user->nama) }}', '{{ addslashes($label) }}')">
                                {{ $label }}
                              </div>
                            @empty
                              <div class="ss-empty">Belum ada data</div>
                            @endforelse
                          </div>
                        </div>
                      </div>
                      @error('pengguna')
                        <small class="text-danger">{{ $message }}</small>
                      @enderror
                    </div>
                  </div>

                  <!-- Step 3: Dynamic Fields (berdasarkan jenis) -->
                  <div class="dynamic-fields-section" id="dynamic-fields-section">
                    <div class="form-section">
                      <div class="dynamic-fields-header">
                        <i data-feather="sliders"></i>
                        <span id="dynamic-fields-title">Field Tambahan</span>
                      </div>
                      <div class="dynamic-fields-body">
                        <div class="form-grid" id="dynamic-fields-container">
                          <!-- Dynamic fields akan di-render oleh JavaScript berdasarkan jenis -->
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Data KIB (Kartu Inventaris Barang) — field statis per jenis, muncul jika jenis punya kib_type -->
                  <div id="kib-section-wrapper" style="display:none;margin-top:16px;">
                  <div class="buku-inventaris-section">
                    <div class="buku-inventaris-header">
                      <div class="buku-inventaris-header-icon">
                        <i data-feather="book-open"></i>
                      </div>
                      <div class="buku-inventaris-header-content">
                        <span class="buku-inventaris-title" id="kib-section-title">Data Buku Inventaris Aset Desa</span>
                        <p class="buku-inventaris-desc">Field ini digunakan untuk mengisi data Buku Inventaris Aset Desa (KIB) sesuai format standar.</p>
                      </div>
                    </div>
                    {{-- Field KIB spesifik per jenis (opsional) --}}
                    <div class="form-grid" style="grid-template-columns:1fr;margin-top:12px;" id="kib-fields-container">
                      <!-- Field KIB akan di-render oleh JavaScript jika jenis punya kib_type -->
                    </div>
                  </div>
                  </div>

                  <!-- Data Laporan Aset — field satuan untuk export laporan per semester/tahunan -->
                  <div id="laporan-aset-section-wrapper" style="display:none;margin-top:16px;">
                  <div class="buku-inventaris-section">
                    <div class="buku-inventaris-header">
                      <div class="buku-inventaris-header-icon">
                        <i data-feather="bar-chart-2"></i>
                      </div>
                      <div class="buku-inventaris-header-content">
                        <span class="buku-inventaris-title">Data Laporan Aset</span>
                        <p class="buku-inventaris-desc">Field ini digunakan untuk export Laporan Aset Per Semester dan Per Tahun.</p>
                      </div>
                    </div>
                    <div class="form-grid" style="margin-top:12px;">
                      <div class="form-group">
                        <label class="form-label" for="satuan">Satuan <span class="required">*</span></label>
                        <input type="text" id="satuan" name="satuan" class="form-control"
                          placeholder="Contoh: Unit, Buah, M², Meter, dll."
                          value="{{ old('satuan') }}"
                          style="width:100%;">
                        @error('satuan')
                          <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
                        @enderror
                      </div>
                      
                      <div class="form-group">
                        <label class="form-label" for="bulan_perolehan">Bulan Perolehan <span class="required">*</span></label>
                        <select id="bulan_perolehan" name="bulan_perolehan" class="form-control form-select" required>
                          <option value="" disabled hidden {{ old('bulan_perolehan') ? '' : 'selected' }}>-- Pilih Bulan --</option>
                          <option value="1" {{ old('bulan_perolehan') == '1' ? 'selected' : '' }}>Januari</option>
                          <option value="2" {{ old('bulan_perolehan') == '2' ? 'selected' : '' }}>Februari</option>
                          <option value="3" {{ old('bulan_perolehan') == '3' ? 'selected' : '' }}>Maret</option>
                          <option value="4" {{ old('bulan_perolehan') == '4' ? 'selected' : '' }}>April</option>
                          <option value="5" {{ old('bulan_perolehan') == '5' ? 'selected' : '' }}>Mei</option>
                          <option value="6" {{ old('bulan_perolehan') == '6' ? 'selected' : '' }}>Juni</option>
                          <option value="7" {{ old('bulan_perolehan') == '7' ? 'selected' : '' }}>Juli</option>
                          <option value="8" {{ old('bulan_perolehan') == '8' ? 'selected' : '' }}>Agustus</option>
                          <option value="9" {{ old('bulan_perolehan') == '9' ? 'selected' : '' }}>September</option>
                          <option value="10" {{ old('bulan_perolehan') == '10' ? 'selected' : '' }}>Oktober</option>
                          <option value="11" {{ old('bulan_perolehan') == '11' ? 'selected' : '' }}>November</option>
                          <option value="12" {{ old('bulan_perolehan') == '12' ? 'selected' : '' }}>Desember</option>
                        </select>
                        <small class="form-hint">Untuk identifikasi aset masuk ke Semester 1 atau 2.</small>
                        @error('bulan_perolehan')
                          <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
                        @enderror
                      </div>
                    </div>
                  </div>
                  </div>

                  <!-- Data KIR (Kartu Inventaris Ruangan) — muncul jika jenis requires_ruangan = 1 -->
                  <div id="kir-section-wrapper" style="display:none;margin-top:16px;">
                    <div class="buku-inventaris-section">
                      <div class="buku-inventaris-header">
                        <div class="buku-inventaris-header-icon">
                          <i data-feather="grid"></i>
                        </div>
                        <div class="buku-inventaris-header-content">
                          <span class="buku-inventaris-title">Data Kartu Inventaris Ruangan (KIR)</span>
                          <p class="buku-inventaris-desc">Field ini digunakan untuk mengisi informasi tambahan yang akan ditampilkan pada hasil export Kartu Inventaris Ruangan (KIR).</p>
                        </div>
                      </div>
                      <div class="form-grid" style="grid-template-columns:1fr;margin-top:12px;">
                        <div class="form-group">
                          <label class="form-label" for="ruangan_id">Ruangan <span class="required">*</span></label>
                          <select name="ruangan_id" id="ruangan_id" class="form-control form-select">
                            <option value="" disabled hidden selected>-- Pilih Ruangan --</option>
                            @foreach($ruanganList as $ruangan)
                              <option value="{{ $ruangan->id }}" {{ old('ruangan_id') == $ruangan->id ? 'selected' : '' }}>
                                {{ $ruangan->nama }}{{ $ruangan->kode ? ' ('.$ruangan->kode.')' : '' }}
                              </option>
                            @endforeach
                          </select>
                          @error('ruangan_id')
                            <small class="text-danger">{{ $message }}</small>
                          @enderror
                        </div>
                        
                        @php
                          $kirFields = [
                            ['key' => 'kir_konstruksi',    'label' => 'Konstruksi',     'placeholder' => 'Masukkan Konstruksi',     'type' => 'text'],
                            ['key' => 'kir_luas',          'label' => 'Luas',            'placeholder' => 'Masukkan Luas',           'type' => 'text'],
                            ['key' => 'kir_merk_tipe',     'label' => 'Merk / Tipe',     'placeholder' => 'Masukkan Merk / Tipe',    'type' => 'text'],
                            ['key' => 'kir_warna',         'label' => 'Warna',           'placeholder' => 'Masukkan Warna',          'type' => 'text'],
                            ['key' => 'kir_judul',         'label' => 'Judul',           'placeholder' => 'Masukkan Judul',          'type' => 'text'],
                            ['key' => 'kir_pencipta',      'label' => 'Pencipta',        'placeholder' => 'Masukkan Pencipta',       'type' => 'text'],
                            ['key' => 'kir_no_pol',        'label' => 'No. Polisi',      'placeholder' => 'Masukkan No. Polisi',     'type' => 'text'],
                            // Field tambahan untuk Format 2 (Desa Demo)
                            ['key' => 'kir_no_seri_pabrik','label' => 'No. Seri Pabrik', 'placeholder' => 'Masukkan No. Seri Pabrik','type' => 'text'],
                            ['key' => 'kir_panjang',       'label' => 'Panjang (P)',     'placeholder' => 'Contoh: 70cm',            'type' => 'text'],
                            ['key' => 'kir_lebar',         'label' => 'Lebar (L)',       'placeholder' => 'Contoh: 20cm',            'type' => 'text'],
                            ['key' => 'kir_tinggi',        'label' => 'Tinggi (T)',      'placeholder' => 'Contoh: 100cm',           'type' => 'text'],
                            ['key' => 'kir_bahan',         'label' => 'Bahan',           'placeholder' => 'Contoh: Kayu, Besi, Plastik', 'type' => 'text'],
                            ['key' => 'kir_kondisi',       'label' => 'Kondisi Aset',    'placeholder' => '',                        'type' => 'select'],
                          ];
                        @endphp
                        @foreach($kirFields as $kf)
                          @php
                            $normKey = strtolower(trim($kf['key']));
                            $normLabel = strtolower(trim(str_replace(['*', '(', ')', '/'], '', $kf['label'])));
                            $dataUmumKeys = ['nama_barang', 'jenis_barang', 'kode_barang', 'nup', 'tahun_perolehan', 'nilai_perolehan', 'keterangan', 'asal_usul', 'pengguna', 'satuan'];
                            $dataUmumLabels = ['nama barang', 'jenis barang / nama barang', 'jenis barang', 'kode barang', 'nup', 'tahun perolehan', 'nilai perolehan', 'keterangan', 'asal usul', 'asal usul barang', 'pengguna', 'satuan'];
                            
                            if (in_array($normKey, $dataUmumKeys) || in_array($normLabel, $dataUmumLabels)) {
                                continue;
                            }
                          @endphp
                          <div class="form-group">
                            <label class="form-label" for="{{ $kf['key'] }}">{{ $kf['label'] }}</label>
                            @if(($kf['type'] ?? 'text') === 'select')
                              <select id="{{ $kf['key'] }}" name="kir[{{ $kf['key'] }}]" class="form-control form-select">
                                <option value="" {{ old('kir.'.$kf['key']) === null ? 'selected' : '' }}>-- Pilih Kondisi --</option>
                                <option value="baik"        {{ old('kir.'.$kf['key']) === 'baik'         ? 'selected' : '' }}>Baik (B)</option>
                                <option value="rusak_ringan"{{ old('kir.'.$kf['key']) === 'rusak_ringan'  ? 'selected' : '' }}>Kurang Baik (KB)</option>
                                <option value="rusak_berat" {{ old('kir.'.$kf['key']) === 'rusak_berat'   ? 'selected' : '' }}>Rusak Berat (RB)</option>
                              </select>
                            @else
                              <input type="text" id="{{ $kf['key'] }}" name="kir[{{ $kf['key'] }}]" class="form-control" placeholder="{{ $kf['placeholder'] }}" value="{{ old('kir.' . $kf['key']) }}" />
                            @endif
                          </div>
                        @endforeach
                      </div>
                    </div>
                  </div>

                  <!-- Upload Foto Aset — hanya tampil setelah jenis dipilih -->
                  <div id="foto-section-wrapper" style="display:none;margin-top:16px;margin-bottom:20px;">
                    <div class="buku-inventaris-section">
                      <div class="buku-inventaris-header" style="margin-bottom: 16px;">
                        <div class="buku-inventaris-header-icon">
                          <i data-feather="image"></i>
                        </div>
                        <div class="buku-inventaris-header-content">
                          <span class="buku-inventaris-title">Foto Aset</span>
                          <p class="buku-inventaris-desc">Unggah foto aset untuk mendokumentasikan kondisi fisik aset (maksimal 5 foto).</p>
                        </div>
                      </div>

                      <input type="file" id="foto-input" name="fotos[]" accept="image/jpeg,image/png,image/webp" multiple style="display:none;" onchange="handleFotoPreview(this)">

                      {{-- Drop area --}}
                      <div class="file-upload-area" id="foto-drop-area" onclick="document.getElementById('foto-input').click()">
                        <div class="file-upload-icon">
                          <i data-feather="image"></i>
                        </div>
                        <div class="file-upload-text">Klik atau drag &amp; drop untuk upload foto</div>
                        <div class="file-upload-hint">Format: JPG, PNG, WEBP &mdash; Maks. 5 foto, setiap foto &le;5MB (total &le;25MB)</div>
                      </div>

                      {{-- Preview grid --}}
                      <div id="foto-preview-grid" style="display:flex;flex-wrap:wrap;gap:10px;margin-top:12px;"></div>
                      <p id="foto-limit-msg" style="color:#dc2626;font-size:12px;margin-top:6px;display:none;">Maksimal 5 foto sudah dipilih.</p>
                    </div>
                  </div>


                  <!-- Section Simpan Beberapa Salinan -->
                  <div class="buku-inventaris-section" id="copy-section" style="display:none;margin-top:16px;">
                    <div class="buku-inventaris-header">
                      <div class="buku-inventaris-header-icon">
                        <i data-feather="copy"></i>
                      </div>
                      <div class="buku-inventaris-header-content">
                        <div class="buku-inventaris-title">Simpan Beberapa Salinan</div>
                        <p class="buku-inventaris-desc">Aktifkan untuk menyimpan aset ini lebih dari satu kali sekaligus dengan data yang sama.</p>
                      </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:0;" id="copy-toggle-row">
                      <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;color:#374151;font-weight:500;user-select:none;">
                        <input type="checkbox" id="copy-enabled" onchange="toggleCopySection(this)"
                          style="width:16px;height:16px;accent-color:#064635;cursor:pointer;">
                        Simpan lebih dari 1 salinan
                      </label>
                    </div>
                    <div id="copy-count-row" style="display:none;margin-top:12px;">
                      <label class="form-label" for="copy-jumlah">Jumlah salinan <span class="required">*</span></label>
                      <input type="text" inputmode="numeric" id="copy-jumlah" name="copy_jumlah" value="2"
                        class="form-control"
                        placeholder="Masukkan jumlah (2–50)"
                        oninput="this.value=this.value.replace(/[^0-9]/g,'');updateCopySubmitBtn()">
                      <p class="form-hint" id="copy-hint">Akan membuat <strong id="copy-hint-count">2</strong> aset dengan data identik.</p>
                    </div>
                  </div>

                  <!-- Form Actions -->
                  <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="confirmCancel()">
                      <i data-feather="x"></i>
                      Batal
                    </button>
                    <button type="button" class="btn-submit" id="btn-submit-main" onclick="submitSimpanAset()">
                      <i data-feather="save"></i>
                      <span id="btn-submit-label">Simpan Aset</span>
                    </button>
                  </div>

                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    @include('layouts.footer-block')
    <script>
    // ── Searchable Select Component ──
    function ssToggle(id) {
      const wrap = document.getElementById(id);
      const dd = wrap.querySelector('.ss-dropdown');
      const trigger = wrap.querySelector('.ss-trigger');
      const isOpen = dd.style.display !== 'none';
      // Close all other open dropdowns first
      document.querySelectorAll('.searchable-select .ss-dropdown').forEach(d => { d.style.display = 'none'; });
      document.querySelectorAll('.ss-trigger').forEach(t => t.classList.remove('ss-open'));
      if (!isOpen) {
        dd.style.display = 'flex';
        trigger.classList.add('ss-open');
        const search = wrap.querySelector('.ss-search');
        if (search) { search.value = ''; ssFilter(id, ''); setTimeout(() => search.focus(), 50); }
      }
    }
    function ssFilter(id, q) {
      const wrap = document.getElementById(id);
      const opts = wrap.querySelectorAll('.ss-option');
      const lower = q.toLowerCase();
      let visible = 0;
      opts.forEach(opt => {
        const match = opt.getAttribute('data-search').includes(lower);
        opt.classList.toggle('ss-hidden', !match);
        if (match) visible++;
      });
      const empty = wrap.querySelector('.ss-empty');
      if (empty) empty.style.display = visible === 0 ? 'block' : 'none';
    }
    function ssSelect(id, value, label) {
      const wrap = document.getElementById(id);
      const hidden = wrap.parentElement.querySelector('input[type="hidden"]');
      const text = wrap.querySelector('.ss-value-text');
      if (hidden) hidden.value = value;
      if (text) text.textContent = label || value;
      // Mark selected
      wrap.querySelectorAll('.ss-option').forEach(opt => {
        opt.classList.toggle('ss-selected', opt.getAttribute('data-value') === value);
      });
      // Close
      wrap.querySelector('.ss-dropdown').style.display = 'none';
      wrap.querySelector('.ss-trigger').classList.remove('ss-open');
    }
    // Close on outside click
    document.addEventListener('click', function(e) {
      if (!e.target.closest('.searchable-select')) {
        document.querySelectorAll('.searchable-select .ss-dropdown').forEach(d => { d.style.display = 'none'; });
        document.querySelectorAll('.ss-trigger').forEach(t => t.classList.remove('ss-open'));
      }
    });
    </script>
    @include('layouts.footer-js')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
  try {
    const thisYear = new Date().getFullYear();
    const jenisInventaris = {!! json_encode($jenisList->toArray(), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!};
    const pejabatByJenis = {!! json_encode((object)($pejabatByJenis ?? []), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!};
    const kibConfigByJenis = {!! json_encode((object)($kibConfigByJenis ?? []), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!};
    const oldInput = @json(old());
    const oldJenisId = {{ old('jenis_inventaris_id') ? (int) old('jenis_inventaris_id') : 'null' }};

    const dynamicFieldsByJenis = {};
    jenisInventaris.forEach(j => { dynamicFieldsByJenis[j.id] = j.fields; });

    let selectedJenis = null;

    function formatRupiah(raw) {
      let parts = raw.split(',');
      let intPart = parts[0].replace(/\./g, '').replace(/\D/g, '');
      let hasComma = parts.length > 1;
      let decPart = hasComma ? parts[1].replace(/\D/g, '').substring(0, 2) : null;
      let formatted = intPart === '' ? '' : new Intl.NumberFormat('id-ID').format(parseInt(intPart) || 0);
      if (hasComma) formatted += ',' + decPart;
      return formatted;
    }

    function isCurrencyField(fieldType, fieldName, fieldLabel) {
      if (fieldType === 'number' && fieldName === 'nilai_perolehan') return true;
      if (fieldType === 'number' || fieldType === 'text') {
        var hay = ((fieldName || '') + ' ' + (fieldLabel || '')).toLowerCase();
        return /\b(nilai|harga|rupiah)\b/.test(hay) && !/\b(luas|tahun|unit)\b/.test(hay);
      }
      return false;
    }

    function attachCurrencyInputListener() {
      document.querySelectorAll('.currency-rupiah-input').forEach(function(input) {
        input.addEventListener('input', function() {
          this.value = formatRupiah(this.value);
        });
      });
    }

    function sanitizeDecimalLocaleInput(raw) {
      var value = String(raw || '').replace(/[^0-9,\.\-]/g, '');
      value = value.replace(/(?!^)-/g, '');
      return value;
    }

    function attachDecimalLocaleInputListener(root) {
      (root || document).querySelectorAll('.decimal-locale-input').forEach(function(input) {
        if (input.dataset.decimalBound === '1') return;
        input.dataset.decimalBound = '1';
        input.addEventListener('input', function() {
          this.value = sanitizeDecimalLocaleInput(this.value);
        });
      });
    }

    function attachNumericOnlyInputListener(root) {
      (root || document).querySelectorAll('.numeric-only-input').forEach(function(input) {
        if (input.dataset.numericBound === '1') return;
        input.dataset.numericBound = '1';
        input.addEventListener('input', function() {
          // Hapus semua karakter selain angka
          const pos = this.selectionStart;
          const before = this.value;
          this.value = before.replace(/[^0-9]/g, '');
          // Pertahankan posisi kursor
          if (this.value.length < before.length) {
            this.setSelectionRange(Math.max(0, pos - 1), Math.max(0, pos - 1));
          }
        });
        input.addEventListener('keydown', function(e) {
          // Izinkan: angka, backspace, delete, tab, enter, arrow keys, home, end
          const allowed = ['Backspace','Delete','Tab','Enter','ArrowLeft','ArrowRight','ArrowUp','ArrowDown','Home','End'];
          if (allowed.includes(e.key)) return;
          // Izinkan Ctrl/Cmd + A/C/V/X
          if ((e.ctrlKey || e.metaKey) && ['a','c','v','x'].includes(e.key.toLowerCase())) return;
          // Blokir semua selain angka
          if (!/^[0-9]$/.test(e.key)) e.preventDefault();
        });
      });
    }

    function clampYearValue(year, minYear, maxYear) {
      const numericYear = Number.parseInt(String(year), 10);
      if (!Number.isFinite(numericYear)) return '';
      return String(Math.min(Math.max(numericYear, minYear), maxYear));
    }

    function attachYearInputGuards(root) {
      (root || document).querySelectorAll('.year-picker').forEach(function(input) {
        if (input.dataset.yearGuardBound === '1') return;
        input.dataset.yearGuardBound = '1';
        input.addEventListener('input', function() {
          this.value = this.value.replace(/\D/g, '').slice(0, 4);
        });
        const normalizeYearValue = function() {
          if (!this.value) return;
          const minYear = Number.parseInt(this.dataset.minYear || '1900', 10);
          const maxYear = Number.parseInt(this.dataset.maxYear || String(new Date().getFullYear() + 1), 10);
          const normalizedYear = clampYearValue(this.value, minYear, maxYear);
          this.value = normalizedYear;
          if (this._flatpickr && normalizedYear) {
            this._flatpickr.setDate(`${normalizedYear}-01-01`, true, 'Y');
          }
        };
        input.addEventListener('blur', normalizeYearValue);
        input.addEventListener('keydown', function(ev) {
          if (ev.key !== 'Enter') return;
          ev.preventDefault();
          normalizeYearValue.call(this);
        });
      });
    }

    function ensureYearPickerPanel(instance, minYear, maxYear) {
      const calendar = instance.calendarContainer;
      if (!calendar) return;

      calendar.classList.add('year-only-picker');

      let state = calendar._yearPickerState;
      if (!state) {
        const panel = document.createElement('div');
        panel.className = 'year-picker-panel';
        panel.innerHTML = `
          <div class="year-picker-header">
            <button type="button" class="year-picker-nav" data-nav="prev" aria-label="Tahun sebelumnya">&lsaquo;</button>
            <span class="year-picker-range"></span>
            <button type="button" class="year-picker-nav" data-nav="next" aria-label="Tahun berikutnya">&rsaquo;</button>
          </div>
          <div class="year-picker-grid"></div>
        `;
        calendar.appendChild(panel);

        state = {
          panel,
          range: panel.querySelector('.year-picker-range'),
          grid: panel.querySelector('.year-picker-grid'),
          prev: panel.querySelector('[data-nav="prev"]'),
          next: panel.querySelector('[data-nav="next"]'),
          pageStart: minYear,
          selectedYear: minYear,
        };
        calendar._yearPickerState = state;

        state.prev.addEventListener('click', function(ev) {
          ev.preventDefault();
          ev.stopPropagation();
          state.pageStart = Math.max(minYear, state.pageStart - 12);
          renderYearPickerPanel(instance, state, minYear, maxYear);
        });

        state.next.addEventListener('click', function(ev) {
          ev.preventDefault();
          ev.stopPropagation();
          const maxPageStart = Math.max(minYear, maxYear - 11);
          state.pageStart = Math.min(maxPageStart, state.pageStart + 12);
          renderYearPickerPanel(instance, state, minYear, maxYear);
        });

        state.grid.addEventListener('click', function(ev) {
          const btn = ev.target.closest('.year-picker-option');
          if (!btn) return;
          const year = Number.parseInt(btn.dataset.year, 10);
          if (!Number.isFinite(year)) return;
          instance.setDate(`${year}-01-01`, true, 'Y');
          instance.input.value = String(year);
          state.selectedYear = year;
          instance.input.dispatchEvent(new Event('input', { bubbles: true }));
          instance.input.dispatchEvent(new Event('change', { bubbles: true }));
          instance.close();
        });
      }

      const rawYear = clampYearValue(instance.input.value, minYear, maxYear);
      const fallbackYear = Math.min(Math.max(new Date().getFullYear(), minYear), maxYear);
      const selectedYear = rawYear ? Number.parseInt(rawYear, 10) : fallbackYear;

      if (!Number.isFinite(state.pageStart) || selectedYear < state.pageStart || selectedYear > (state.pageStart + 11)) {
        const maxPageStart = Math.max(minYear, maxYear - 11);
        state.pageStart = Math.min(maxPageStart, Math.max(minYear, selectedYear - 5));
      }

      state.selectedYear = selectedYear;
      renderYearPickerPanel(instance, state, minYear, maxYear);
    }

    function renderYearPickerPanel(instance, state, minYear, maxYear) {
      const start = state.pageStart;
      const end = Math.min(start + 11, maxYear);

      state.range.textContent = `${start} - ${end}`;
      state.prev.disabled = start <= minYear;
      state.next.disabled = end >= maxYear;

      let html = '';
      for (let year = start; year <= end; year++) {
        const activeClass = year === state.selectedYear ? ' active' : '';
        html += `<button type="button" class="year-picker-option${activeClass}" data-year="${year}">${year}</button>`;
      }
      state.grid.innerHTML = html;
    }

    function initYearPickers(root) {
      const scope = root || document;
      const yearInputs = scope.querySelectorAll('.year-picker');
      if (!yearInputs.length) return;

      const minAllowedYear = 1900;
      const maxAllowedYear = new Date().getFullYear() + 1;

      attachYearInputGuards(scope);

      if (typeof flatpickr === 'undefined') {
        return;
      }

      yearInputs.forEach(function(input) {
        const existingValue = (input.value || '').trim();

        if (input._flatpickr) {
          input._flatpickr.destroy();
        }

        flatpickr(input, {
          dateFormat: 'Y',
          locale: (flatpickr.l10ns && flatpickr.l10ns.id) ? flatpickr.l10ns.id : 'default',
          allowInput: true,
          disableMobile: true,
          minDate: `${minAllowedYear}-01-01`,
          maxDate: `${maxAllowedYear}-12-31`,
          position: 'auto left',
          monthSelectorType: 'static',
          shorthandCurrentMonth: false,
          defaultDate: /^\d{4}$/.test(existingValue) ? `${existingValue}-01-01` : null,
          onReady: function(_, __, instance) {
            ensureYearPickerPanel(instance, minAllowedYear, maxAllowedYear);
          },
          onOpen: function(_, __, instance) {
            ensureYearPickerPanel(instance, minAllowedYear, maxAllowedYear);
          },
          onValueUpdate: function(_, __, instance) {
            const normalizedYear = clampYearValue(instance.input.value, minAllowedYear, maxAllowedYear);
            instance.input.value = normalizedYear;
            ensureYearPickerPanel(instance, minAllowedYear, maxAllowedYear);
          }
        });
      });
    }

    function fetchAndRenderDynamicFields(jenisId) {
      // Cek cache dulu
      if (dynamicFieldsByJenis[jenisId] && dynamicFieldsByJenis[jenisId]._loaded) {
        renderDynamicFields(jenisId);
        return;
      }
      // Fetch dari API untuk dapat dropdown_items
      fetch(`/api/jenis-inventaris/${jenisId}/fields`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
      })
      .then(r => r.json())
      .then(data => {
        if (data.fields) {
          dynamicFieldsByJenis[jenisId] = data.fields;
          dynamicFieldsByJenis[jenisId]._loaded = true;
        }
        renderDynamicFields(jenisId);
      })
      .catch(() => {
        // Fallback ke data yang sudah ada
        renderDynamicFields(jenisId);
      });
    }

    function renderDynamicFields(jenisId) {
      const section = document.getElementById('builder-fields-section');
      const container = document.getElementById('builder-fields-container');
      const message = document.getElementById('builder-fields-message');
      const fields = dynamicFieldsByJenis[jenisId] || [];

      // Tampilkan section copy saat jenis sudah dipilih
      const copySection = document.getElementById('copy-section');
      if (copySection) copySection.style.display = 'block';

      // Update ruangan field visibility
      const jenisData = jenisInventaris.find(j => j.id == jenisId);
      updateRuanganFieldVisibility(jenisData);

      if (fields.length === 0) {
        section.style.display = 'block';
        container.innerHTML = '';
        message.style.display = 'block';
        message.innerHTML = 'Tidak ada data untuk diisi.';
        return;
      } else {
        message.style.display = 'none';
      }

      fields.sort((a, b) => (a.order || 0) - (b.order || 0));

      container.innerHTML = fields.filter(f => {
        // Sembunyikan field jenis_barang — sudah digabung ke nama_barang sebagai "Jenis Barang / Nama Barang"
        if ((f.field_type === 'fixed' && f.fixed_field_name === 'jenis_barang') || f.nama_field === 'jenis_barang') return false;
        return true;
      }).map(f => {
        let inputHtml = '';
        const fieldName = f.nama_field;
        // Ganti label nama_barang menjadi "Jenis Barang / Nama Barang"
        const rawLabel = f.label_field || f.nama_field;
        const fieldLabel = (f.field_type === 'fixed' && f.fixed_field_name === 'nama_barang') || f.nama_field === 'nama_barang'
            ? 'Jenis Barang / Nama Barang'
            : rawLabel;
        const fieldRequired = f.is_required === true || f.is_required === 1;
        const reqMark = fieldRequired ? '<span class="required">*</span>' : '';
        const fieldTypeFixed = f.field_type === 'fixed';
        const isFixedYearField = fieldTypeFixed && (f.fixed_field_name === 'tahun_perolehan' || fieldName === 'tahun_perolehan');
        const fieldType = isFixedYearField ? 'year' : (f.tipe || 'text');

        if (fieldType === 'textarea') {
          inputHtml = `<textarea id="${fieldName}" name="${fieldTypeFixed ? fieldName : `dynamic[${fieldName}]`}" class="form-control" rows="2" placeholder="${fieldLabel}..." ${fieldRequired ? 'required' : ''}></textarea>`;
        } else if (fieldType === 'year') {
          inputHtml = `<input type="text" id="${fieldName}" name="${fieldTypeFixed ? fieldName : `dynamic[${fieldName}]`}" class="form-control year-picker" inputmode="numeric" maxlength="4" data-min-year="1900" data-max-year="${thisYear + 1}" placeholder="Pilih tahun" ${fieldRequired ? 'required' : ''} />`;
        } else if (fieldType === 'month') {
          inputHtml = `<input type="text" id="${fieldName}" name="${fieldTypeFixed ? fieldName : `dynamic[${fieldName}]`}" class="form-control month-picker" data-min-year="1900" data-max-year="${thisYear + 1}" placeholder="Pilih bulan dan tahun" ${fieldRequired ? 'required' : ''} />`;
        } else if (fieldType === 'date') {
          inputHtml = `<input type="text" id="${fieldName}" name="${fieldTypeFixed ? fieldName : `dynamic[${fieldName}]`}" class="form-control date-picker" placeholder="Pilih tanggal" ${fieldRequired ? 'required' : ''} />`;
        } else if (isCurrencyField(fieldType, fieldName, fieldLabel)) {
          const inputName = fieldTypeFixed ? fieldName : `dynamic[${fieldName}]`;
          inputHtml = `<div class="currency-input-wrapper"><span class="currency-prefix">Rp</span><input type="text" id="${fieldName}" name="${inputName}" class="form-control currency-rupiah-input" placeholder="0" ${fieldRequired ? 'required' : ''} /></div>`;
        } else if (fieldType === 'decimal_m2') {
          const inputName = fieldTypeFixed ? fieldName : `dynamic[${fieldName}]`;
          inputHtml = `<div class="unit-suffix-input-wrapper"><input type="text" id="${fieldName}" name="${inputName}" class="form-control decimal-locale-input" inputmode="decimal" placeholder="0" data-kib-sync="luas_m2" ${fieldRequired ? 'required' : ''} /><span class="unit-suffix">m²</span></div>`;
        } else if (fieldType === 'decimal_km') {
          const inputName = fieldTypeFixed ? fieldName : `dynamic[${fieldName}]`;
          inputHtml = `<div class="unit-suffix-input-wrapper"><input type="text" id="${fieldName}" name="${inputName}" class="form-control decimal-locale-input" inputmode="decimal" placeholder="0" data-kib-sync="panjang_km" ${fieldRequired ? 'required' : ''} /><span class="unit-suffix">km</span></div>`;
        } else if (fieldType === 'decimal_m') {
          const inputName = fieldTypeFixed ? fieldName : `dynamic[${fieldName}]`;
          inputHtml = `<div class="unit-suffix-input-wrapper"><input type="text" id="${fieldName}" name="${inputName}" class="form-control decimal-locale-input" inputmode="decimal" placeholder="0" data-kib-sync="lebar_m" ${fieldRequired ? 'required' : ''} /><span class="unit-suffix">m</span></div>`;
        } else if (fieldType === 'number') {
          inputHtml = `<input type="text" id="${fieldName}" name="${fieldTypeFixed ? fieldName : `dynamic[${fieldName}]`}" class="form-control decimal-locale-input" inputmode="decimal" placeholder="0" ${fieldRequired ? 'required' : ''} />`;
        } else if (fieldType === 'select' && fieldName === 'kondisi') {
          inputHtml = `<select id="kondisi" name="kondisi" class="form-control form-select" ${fieldRequired ? 'required' : ''}>
            <option value="" disabled hidden selected>-- Pilih Kondisi --</option>
            <option value="baik">Baik</option>
            <option value="rusak_ringan">Rusak Ringan</option>
            <option value="rusak_berat">Rusak Berat</option>
          </select>`;
        } else if (fieldTypeFixed && f.fixed_field_name === 'kode_barang') {
          // Kode barang: search modal hierarki 5 level
          inputHtml = `<div id="kode-barang-wrapper" style="position:relative;">
  <div style="display:flex;gap:8px;align-items:center;">
    <input type="text" id="kode_barang" name="kode_barang"
      class="form-control"
      placeholder="Pilih kode barang..."
      readonly
      style="flex:1;background:#ffffff;cursor:pointer;"
      onclick="openKodeBarangSearch()"
      value="">
    <button type="button" onclick="openKodeBarangSearch()"
      style="padding:10px 14px;background:linear-gradient(135deg,#064635,#1c5240);color:#fff;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;white-space:nowrap;display:flex;align-items:center;gap:6px;">
      <i data-feather="search" style="width:14px;height:14px;stroke:#fff;"></i> Cari
    </button>
  </div>
  <div id="kode-barang-selected-info" style="display:none;margin-top:6px;padding:8px 12px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:6px;font-size:12px;color:#166534;"></div>
</div>`;
        } else if (fieldTypeFixed && f.fixed_field_name === 'nup') {
          // NUP: hanya angka, tapi disimpan sebagai text (bisa leading zero)
          inputHtml = `<input type="text" id="${fieldName}" name="${fieldName}" class="form-control numeric-only-input" inputmode="numeric" pattern="[0-9]*" placeholder="NUP" ${fieldRequired ? 'required' : ''} />`;
        } else if (fieldType === 'dropdown') {
          const inputName = fieldTypeFixed ? fieldName : `dynamic[${fieldName}]`;
          if (f.dropdown_items && f.dropdown_items.length > 0) {
            const opts = f.dropdown_items.map(item => `<option value="${item.label}">${item.label}</option>`).join('');
            inputHtml = `<select id="${fieldName}" name="${inputName}" class="form-control form-select" ${fieldRequired ? 'required' : ''}>
              <option value="" disabled hidden selected>-- Pilih ${fieldLabel} --</option>
              ${opts}
            </select>`;
          } else {
            // Dropdown belum dikonfigurasi atau tidak ada items
            inputHtml = `<select id="${fieldName}" name="${inputName}" class="form-control form-select" ${fieldRequired ? 'required' : ''} disabled>
              <option value="">-- Dropdown belum dikonfigurasi --</option>
            </select>`;
          }
        } else {
          inputHtml = `<input type="${fieldType}" id="${fieldName}" name="${fieldTypeFixed ? fieldName : `dynamic[${fieldName}]`}" class="form-control" placeholder="${fieldLabel}" ${fieldRequired ? 'required' : ''} />`;
        }

        return `\n              <div class="form-group">\n                <label class="form-label" for="${fieldName}">\n                  ${fieldLabel} ${reqMark}\n                </label>\n                ${inputHtml}\n              </div>\n            `;
      }).join('');

      section.style.display = 'block';
      // Tampilkan toggle foto dan buku inventaris karena jenis sudah dipilih
      document.getElementById('foto-section-wrapper').style.display = 'block';
      document.getElementById('kib-section-wrapper').style.display = 'block';
      document.getElementById('laporan-aset-section-wrapper').style.display = 'block';
      // Render section KIB jika jenis punya kib_type
      renderKibSection(jenisId);
      // Show/hide ruangan field berdasarkan requires_ruangan dari data jenis
      updateRuanganFieldVisibility(jenisData);
      if (jenisData && jenisData.requires_ruangan) {
        setupFieldSync();
      }
      if (typeof feather !== 'undefined') try{feather.replace();}catch(e){}
      attachCurrencyInputListener();
      attachDecimalLocaleInputListener(section);
      attachNumericOnlyInputListener(section);
      initYearPickers(section);
      initMonthPickers(section);
      initDatePickers(document);
      // Auto-fill kode barang SETELAH DOM field sudah dirender
      // (Kode barang sekarang dipilih via modal search hierarki)
    }

    // Populate kode barang dropdown dari semua konfigurasi yang ada
    // Dipanggil SETELAH DOM field kode_barang sudah dirender
    // =========================================================================
    // Render section KIB (Kartu Inventaris Barang) berdasarkan jenis inventaris
    // =========================================================================
    function renderKibSection(jenisId) {
      const wrapper = document.getElementById('kib-section-wrapper');
      const container = document.getElementById('kib-fields-container');
      const titleEl = document.getElementById('kib-section-title');
      if (!wrapper || !container) return;

      const kibConfig = kibConfigByJenis[jenisId];
      if (!kibConfig || !kibConfig.fields || kibConfig.fields.length === 0) {
        wrapper.style.display = 'none';
        container.innerHTML = '';
        return;
      }

      // Update judul section
      if (titleEl) titleEl.textContent = 'Data KIB — ' + (kibConfig.nama || '');

      // Render field-field KIB
      let html = '';
      kibConfig.fields.forEach(function(field) {
        // Cek apakah field ini duplikat dengan Data Umum
        const normKey = (field.key || '').toLowerCase().trim();
        const normLabel = (field.label || '').toLowerCase().trim().replace(/\*/g, '').replace(/\(.*?\)/g, '').trim();
        const dataUmumKeys = ['nama_barang', 'jenis_barang', 'kode_barang', 'nup', 'tahun_perolehan', 'nilai_perolehan', 'keterangan', 'asal_usul', 'pengguna', 'kondisi', 'satuan'];
        const dataUmumLabels = ['nama barang', 'jenis barang / nama barang', 'jenis barang', 'kode barang', 'nup', 'tahun perolehan', 'nilai perolehan', 'keterangan', 'asal usul', 'asal usul barang', 'pengguna', 'kondisi', 'satuan'];
        
        if (dataUmumKeys.includes(normKey) || dataUmumLabels.includes(normLabel)) {
          return; // Lewati rendering jika duplikat dengan Data Umum
        }
        const inputName = 'kib[' + field.key + ']';
        const inputId = 'kib_' + field.key;
        let inputHtml = '';

        if (field.type === 'select' && field.options) {
          inputHtml = '<select id="' + inputId + '" name="' + inputName + '" class="form-control form-select">';
          inputHtml += '<option value="" disabled hidden selected>-- Pilih --</option>';
          field.options.forEach(function(opt) {
            inputHtml += '<option value="' + opt + '">' + opt + '</option>';
          });
          inputHtml += '</select>';
        } else if (field.type === 'textarea') {
          inputHtml = '<textarea id="' + inputId + '" name="' + inputName + '" class="form-control" rows="2" placeholder="' + field.label + '"></textarea>';
        } else if (field.type === 'decimal') {
          inputHtml = '<div class="unit-suffix-input-wrapper"><input type="text" id="' + inputId + '" name="' + inputName + '" class="form-control decimal-locale-input" inputmode="decimal" placeholder="0" />';
          // Tambah suffix satuan berdasarkan key
          let suffix = '';
          if (field.key.endsWith('_m2') || field.key === 'luas_m2') suffix = 'm²';
          else if (field.key.endsWith('_km') || field.key === 'panjang_km') suffix = 'km';
          else if (field.key.endsWith('_m') || field.key === 'lebar_m') suffix = 'm';
          if (suffix) inputHtml += '<span class="unit-suffix">' + suffix + '</span>';
          inputHtml += '</div>';
        } else if (field.type === 'year') {
          inputHtml = '<input type="text" id="' + inputId + '" name="' + inputName + '" class="form-control year-picker" inputmode="numeric" maxlength="4" data-min-year="1900" data-max-year="' + (new Date().getFullYear() + 1) + '" placeholder="Pilih tahun" />';
        } else if (field.type === 'date') {
          inputHtml = '<input type="text" id="' + inputId + '" name="' + inputName + '" class="form-control date-picker" placeholder="Pilih tanggal" />';
        } else {
          inputHtml = '<input type="text" id="' + inputId + '" name="' + inputName + '" class="form-control" placeholder="' + field.label + '" />';
        }

        html += '<div class="form-group">';
        html += '<label class="form-label" for="' + inputId + '">' + field.label + '</label>';
        html += inputHtml;
        html += '</div>';
      });

      container.innerHTML = html;
      wrapper.style.display = 'block';

      // Init pickers untuk field KIB yang baru dirender
      if (typeof feather !== 'undefined') try { feather.replace(); } catch(e) {}
      attachDecimalLocaleInputListener(container);
      initYearPickers(container);
      initDatePickers(container);
    }

    // Tampilkan/sembunyikan form KIR berdasarkan requires_ruangan jenis
    function updateRuanganFieldVisibility(jenisData) {
      const wrapper = document.getElementById('kir-section-wrapper');
      const select  = document.getElementById('ruangan_id');
      if (!wrapper) return;
      const requiresRuangan = jenisData && jenisData.requires_ruangan;
      wrapper.style.display = requiresRuangan ? 'block' : 'none';
      // Jika jenis requires_ruangan = 1 → field ruangan_id wajib diisi
      if (select) select.required = !!requiresRuangan;
    }

    // ── Selektif 2-way real-time sync untuk KIB, KIR, Laporan Aset, dan Data Tambahan ─────────────
    function setupFieldSync() {
      const form = document.getElementById('create-aset-form') || document.getElementById('edit-aset-form');
      if (!form) return;

      function getInputLabel(input) {
        if (input.id) {
          const label = document.querySelector('label[for="' + input.id + '"]');
          if (label) return label;
        }
        const formGroup = input.closest('.form-group');
        if (formGroup) {
          const label = formGroup.querySelector('.form-label');
          if (label) return label;
        }
        return null;
      }

      function getNormalizedLabelText(labelEl) {
        if (!labelEl) return '';
        let text = labelEl.textContent || '';
        text = text.replace(/\*/g, ''); // bersihkan bintang wajib
        text = text.replace(/\(.*?\)/g, ''); // bersihkan catatan kurung seperti (m2)
        return text.toLowerCase().trim();
      }

      const kirGroups = [
        {
          name: 'konstruksi',
          keywords: ['konstruksi'],
          targets: ['kir_konstruksi']
        },
        {
          name: 'luas',
          keywords: ['luas', 'panjang', 'lebar'],
          targets: ['kir_luas']
        },
        {
          name: 'merk_tipe',
          keywords: ['merk', 'merek', 'type', 'tipe'],
          targets: ['kir_merk_tipe']
        },
        {
          name: 'warna',
          keywords: ['warna'],
          targets: ['kir_warna']
        },
        {
          name: 'judul',
          keywords: ['judul'],
          targets: ['kir_judul']
        },
        {
          name: 'pencipta',
          keywords: ['pencipta'],
          targets: ['kir_pencipta']
        },
        {
          name: 'no_pol',
          keywords: ['no pol', 'nopol', 'nomor polisi', 'no. pol', 'polisi'],
          targets: ['kir_no_pol']
        },
        // Group tambahan untuk Format 2 (Desa Demo)
        {
          name: 'bahan',
          keywords: ['bahan'],
          targets: ['kir_bahan']
        },
        {
          name: 'panjang',
          keywords: ['panjang'],
          targets: ['kir_panjang']
        },
        {
          name: 'lebar',
          keywords: ['lebar'],
          targets: ['kir_lebar']
        },
        {
          name: 'tinggi',
          keywords: ['tinggi', 'height'],
          targets: ['kir_tinggi']
        },
        {
          name: 'no_seri',
          keywords: ['no seri', 'no. seri', 'nomor seri', 'seri pabrik', 'serial'],
          targets: ['kir_no_seri_pabrik']
        },
        {
          name: 'kondisi',
          keywords: ['kondisi'],
          targets: ['kir_kondisi']
        }
      ];

      const excludedIds = ['nama_barang', 'kode_barang', 'kode_barang_edit', 'nup', 'tahun_perolehan', 'nilai_perolehan', 'keterangan', 'asal_usul', 'pengguna'];

      function isEligible(input) {
        if (input.type === 'hidden' || input.type === 'file' || input.disabled || input.readOnly) {
          return false;
        }
        if (input.id && excludedIds.includes(input.id)) {
          return false;
        }
        if (input.name && excludedIds.includes(input.name)) {
          return false;
        }
        const name = input.name || '';
        const id = input.id || '';
        return name.startsWith('kib[') || name.startsWith('kir[') || name.startsWith('dynamic[') || name.startsWith('fields[') || id === 'satuan' || name === 'satuan' || id === 'kondisi' || name === 'kondisi' || id === 'kir_kondisi';
      }

      function getInputKeys(input) {
        const keys = new Set();
        const labelEl = getInputLabel(input);
        const labelText = getNormalizedLabelText(labelEl);
        const id = (input.id || '').toLowerCase();
        const name = (input.name || '').toLowerCase();

        if (labelText) {
          keys.add('label_' + labelText);
        }

        kirGroups.forEach(group => {
          const isMatch = group.keywords.some(kw => labelText.includes(kw) || id.includes(kw) || name.includes(kw));
          if (isMatch) {
            keys.add('group_' + group.name);
          }
          if (group.targets.includes(input.id)) {
            keys.add('group_' + group.name);
          }
        });

        return Array.from(keys);
      }

      let isSyncing = false;

      function syncHandler(e) {
        if (isSyncing) return;
        const sourceInput = e.target;
        if (!isEligible(sourceInput)) return;

        const sourceKeys = getInputKeys(sourceInput);
        if (sourceKeys.length === 0) return;

        const allInputs = Array.from(form.querySelectorAll('input, textarea, select')).filter(isEligible);
        isSyncing = true;
        const val = sourceInput.value;

        allInputs.forEach(targetInput => {
          if (targetInput === sourceInput) return;
          const targetKeys = getInputKeys(targetInput);
          const hasMatch = targetKeys.some(k => sourceKeys.includes(k));
          if (hasMatch) {
            if (targetInput.value !== val) {
              targetInput.value = val;
              targetInput.dispatchEvent(new Event('input', { bubbles: true }));
              targetInput.dispatchEvent(new Event('change', { bubbles: true }));
            }
          }
        });
        isSyncing = false;
      }

      form.addEventListener('input', syncHandler);
      form.addEventListener('change', syncHandler);
    }

    function initMonthPickers(root) {
      const scope = root || document;
      const monthInputs = scope.querySelectorAll('.month-picker');
      if (!monthInputs.length || typeof flatpickr === 'undefined') return;

      monthInputs.forEach(function(input) {
        const existingValue = (input.value || '').trim();
        const minAllowedYear = Number.parseInt(input.dataset.minYear || '1900', 10);
        const maxAllowedYear = Number.parseInt(input.dataset.maxYear || String(new Date().getFullYear() + 1), 10);

        if (input._flatpickr) {
          input._flatpickr.destroy();
        }

        flatpickr(input, {
          dateFormat: 'Y-m',
          locale: (flatpickr.l10ns && flatpickr.l10ns.id) ? flatpickr.l10ns.id : 'default',
          allowInput: false,
          disableMobile: true,
          position: 'auto left',
          minDate: `${minAllowedYear}-01-01`,
          maxDate: `${maxAllowedYear}-12-31`,
          defaultDate: /^\d{4}-\d{2}$/.test(existingValue) ? `${existingValue}-01` : null,
          onReady: function(_, __, instance) {
            ensureMonthPickerPanel(instance, minAllowedYear, maxAllowedYear);
          },
          onOpen: function(_, __, instance) {
            ensureMonthPickerPanel(instance, minAllowedYear, maxAllowedYear);
          },
          onValueUpdate: function(_, __, instance) {
            ensureMonthPickerPanel(instance, minAllowedYear, maxAllowedYear);
          }
        });
      });
    }

    function ensureMonthPickerPanel(instance, minYear, maxYear) {
      const calendar = instance.calendarContainer;
      if (!calendar) return;

      calendar.classList.add('month-grid-picker');

      let state = calendar._monthPickerState;
      if (!state) {
        const panel = document.createElement('div');
        panel.className = 'month-picker-panel';
        panel.innerHTML = `
          <div class="month-picker-header">
            <button type="button" class="month-picker-nav" data-nav="prev" aria-label="Tahun sebelumnya">&lsaquo;</button>
            <span class="month-picker-year"></span>
            <button type="button" class="month-picker-nav" data-nav="next" aria-label="Tahun berikutnya">&rsaquo;</button>
          </div>
          <div class="month-picker-grid"></div>
        `;
        calendar.appendChild(panel);

        state = {
          panel,
          yearLabel: panel.querySelector('.month-picker-year'),
          grid: panel.querySelector('.month-picker-grid'),
          prev: panel.querySelector('[data-nav="prev"]'),
          next: panel.querySelector('[data-nav="next"]'),
          selectedYear: minYear,
          selectedMonth: 0,
        };
        calendar._monthPickerState = state;

        state.prev.addEventListener('click', function(ev) {
          ev.preventDefault();
          ev.stopPropagation();
          state.selectedYear = Math.max(minYear, state.selectedYear - 1);
          renderMonthPickerPanel(instance, state, minYear, maxYear);
        });

        state.next.addEventListener('click', function(ev) {
          ev.preventDefault();
          ev.stopPropagation();
          state.selectedYear = Math.min(maxYear, state.selectedYear + 1);
          renderMonthPickerPanel(instance, state, minYear, maxYear);
        });

        state.grid.addEventListener('click', function(ev) {
          const btn = ev.target.closest('.month-picker-option');
          if (!btn) return;
          const month = Number.parseInt(btn.dataset.month, 10);
          if (!Number.isFinite(month)) return;

          const monthValue = `${state.selectedYear}-${String(month + 1).padStart(2, '0')}`;
          instance.setDate(`${monthValue}-01`, true, 'Y-m');
          instance.input.value = monthValue;
          state.selectedMonth = month;
          instance.input.dispatchEvent(new Event('input', { bubbles: true }));
          instance.input.dispatchEvent(new Event('change', { bubbles: true }));
          instance.close();
        });
      }

      const rawValue = String(instance.input.value || '').trim();
      const fallbackYear = Math.min(Math.max(new Date().getFullYear(), minYear), maxYear);
      const fallbackMonth = new Date().getMonth();
      const match = rawValue.match(/^(\d{4})-(\d{2})$/);

      if (match) {
        state.selectedYear = Math.min(Math.max(Number.parseInt(match[1], 10), minYear), maxYear);
        state.selectedMonth = Math.min(Math.max(Number.parseInt(match[2], 10) - 1, 0), 11);
      } else {
        state.selectedYear = fallbackYear;
        state.selectedMonth = fallbackMonth;
      }

      renderMonthPickerPanel(instance, state, minYear, maxYear);
    }

    function renderMonthPickerPanel(instance, state, minYear, maxYear) {
      const monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
      state.yearLabel.textContent = String(state.selectedYear);
      state.prev.disabled = state.selectedYear <= minYear;
      state.next.disabled = state.selectedYear >= maxYear;

      let html = '';
      for (let month = 0; month < 12; month++) {
        const activeClass = month === state.selectedMonth ? ' active' : '';
        html += `<button type="button" class="month-picker-option${activeClass}" data-month="${month}">${monthLabels[month]}</button>`;
      }
      state.grid.innerHTML = html;
    }

    function initDatePickers(root) {
      const scope = root || document;
      const dateInputs = scope.querySelectorAll('.date-picker');
      if (!dateInputs.length || typeof flatpickr === 'undefined') return;

      const BULAN_SHORT = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'];

      // Tambahkan panel bulan & tahun modern ke kalender yang sudah terbuka.
      // Dipanggil dari onReady; dibungkus aman agar tidak pernah memblok flatpickr.
      function enhanceCalendar(instance) {
        const cal = instance.calendarContainer;
        if (!cal || cal._modernBuilt) return;
        cal._modernBuilt = true;

        const minYear = 1900, maxYear = new Date().getFullYear() + 1;

        // Panel bulan
        const monthPanel = document.createElement('div');
        monthPanel.className = 'fp-month-panel';
        monthPanel.innerHTML =
          '<div class="fp-panel-header"><span class="fp-panel-title">Pilih Bulan</span>' +
          '<button type="button" class="fp-panel-close">&#x2715;</button></div>' +
          '<div class="fp-month-grid">' +
          BULAN_SHORT.map(function(m, i){ return '<button type="button" class="fp-month-btn" data-month="'+i+'">'+m+'</button>'; }).join('') +
          '</div>';
        cal.appendChild(monthPanel);

        // Panel tahun
        const yearPanel = document.createElement('div');
        yearPanel.className = 'fp-year-panel';
        yearPanel._pageStart = Math.max(minYear, maxYear - 11);
        yearPanel.innerHTML =
          '<div class="fp-panel-header"><button type="button" class="fp-panel-nav" data-nav="prev">&#10094;</button>' +
          '<span class="fp-panel-title fp-year-range"></span>' +
          '<button type="button" class="fp-panel-nav" data-nav="next">&#10095;</button></div>' +
          '<div class="fp-year-grid"></div>';
        cal.appendChild(yearPanel);

        function renderYearGrid() {
          const ps = yearPanel._pageStart;
          const end = ps + 11; // selalu 12 tahun (4 baris × 3 kolom)
          yearPanel.querySelector('.fp-year-range').textContent = ps + ' - ' + end;
          yearPanel.querySelector('[data-nav="prev"]').disabled = ps <= minYear;
          yearPanel.querySelector('[data-nav="next"]').disabled = ps + 12 > maxYear;
          var html = '';
          for (var y = ps; y <= end; y++) {
            html += '<button type="button" class="fp-year-btn' + (y === instance.currentYear ? ' active' : '') + '" data-year="' + y + '">' + y + '</button>';
          }
          yearPanel.querySelector('.fp-year-grid').innerHTML = html;
        }

        monthPanel.querySelector('.fp-panel-close').addEventListener('click', function(){ monthPanel.classList.remove('show'); });
        monthPanel.querySelector('.fp-month-grid').addEventListener('click', function(e){
          var btn = e.target.closest('.fp-month-btn'); if (!btn) return;
          instance.changeMonth(parseInt(btn.dataset.month, 10) - instance.currentMonth);
          monthPanel.classList.remove('show');
        });
        yearPanel.querySelector('.fp-panel-header').addEventListener('click', function(e){
          var nav = e.target.closest('.fp-panel-nav'); if (!nav) return;
          if (nav.dataset.nav === 'prev') yearPanel._pageStart = Math.max(minYear, yearPanel._pageStart - 12);
          else yearPanel._pageStart = Math.min(Math.max(minYear, maxYear - 11), yearPanel._pageStart + 12);
          renderYearGrid();
        });
        yearPanel.querySelector('.fp-year-grid').addEventListener('click', function(e){
          var btn = e.target.closest('.fp-year-btn'); if (!btn) return;
          instance.changeYear(parseInt(btn.dataset.year, 10));
          yearPanel.classList.remove('show');
        });

        // Header bulan & tahun bisa diklik
        const curMonthEl = cal.querySelector('.flatpickr-current-month');
        if (curMonthEl) {
          const curMonthSpan = curMonthEl.querySelector('.cur-month');
          if (curMonthSpan) {
            curMonthSpan.style.cursor = 'pointer';
            curMonthSpan.style.borderRadius = '6px';
            curMonthSpan.style.padding = '2px 6px';
            curMonthSpan.addEventListener('click', function(e){
              e.stopPropagation();
              monthPanel.querySelectorAll('.fp-month-btn').forEach(function(b){
                b.classList.toggle('active', parseInt(b.dataset.month, 10) === instance.currentMonth);
              });
              monthPanel.classList.add('show');
            });
          }
          const yearInput = curMonthEl.querySelector('input.cur-year');
          const numWrapper = curMonthEl.querySelector('.numInputWrapper');
          if (yearInput) {
            const yearSpan = document.createElement('span');
            yearSpan.className = 'fp-cur-year';
            yearSpan.textContent = instance.currentYear;
            yearSpan.addEventListener('click', function(e){
              e.stopPropagation();
              yearPanel._pageStart = Math.max(minYear, instance.currentYear - 5);
              renderYearGrid();
              yearPanel.classList.add('show');
            });
            // Tempel span tahun setelah nama bulan di header
            curMonthEl.appendChild(yearSpan);
            if (numWrapper) numWrapper.style.display = 'none';

            // Sinkronkan teks tahun saat navigasi bulan/tahun
            cal.addEventListener('click', function(){ setTimeout(function(){ yearSpan.textContent = instance.currentYear; }, 0); });
          }
        }
      }

      function hideOtherMonthRows(instance) {
        var cal = instance && instance.calendarContainer;
        if (!cal) return;
        var rows = cal.querySelectorAll('.flatpickr-days .flatpickr-week');
        if (!rows.length) {
          // Flatpickr tidak pakai .flatpickr-week — hitung manual dari dayContainer
          var days = cal.querySelectorAll('.flatpickr-days .flatpickr-day');
          if (!days.length) return;
          // Grup per 7
          for (var i = 0; i < days.length; i += 7) {
            var allHidden = true;
            for (var j = i; j < i + 7 && j < days.length; j++) {
              var d = days[j];
              if (!d.classList.contains('prevMonthDay') && !d.classList.contains('nextMonthDay')) {
                allHidden = false; break;
              }
            }
            for (var j = i; j < i + 7 && j < days.length; j++) {
              days[j].style.height = allHidden ? '0' : '';
              days[j].style.overflow = allHidden ? 'hidden' : '';
              days[j].style.padding = allHidden ? '0' : '';
              days[j].style.margin = allHidden ? '0' : '';
              days[j].style.minHeight = allHidden ? '0' : '';
              days[j].style.maxHeight = allHidden ? '0' : '';
              days[j].style.lineHeight = allHidden ? '0' : '';
            }
          }
        }
      }

      dateInputs.forEach(function(input) {
        const existingValue = (input.value || '').trim();
        if (input._flatpickr) { input._flatpickr.destroy(); }

        flatpickr(input, {
          dateFormat: 'Y-m-d',
          locale: (flatpickr.l10ns && flatpickr.l10ns.id) ? flatpickr.l10ns.id : 'default',
          allowInput: false,
          disableMobile: true,
          position: 'auto left',
          monthSelectorType: 'static',
          shorthandCurrentMonth: false,
          defaultDate: /^\d{4}-\d{2}-\d{2}$/.test(existingValue) ? existingValue : null,
          onReady: function(_, __, instance) {
            if (instance.calendarContainer) {
              instance.calendarContainer.classList.add('date-clean-picker');
              try { enhanceCalendar(instance); } catch(e) {}
              setTimeout(function(){ hideOtherMonthRows(instance); }, 0);
            }
          },
          onMonthChange: function(_, __, instance) {
            setTimeout(function(){ hideOtherMonthRows(instance); }, 0);
          },
          onYearChange: function(_, __, instance) {
            setTimeout(function(){ hideOtherMonthRows(instance); }, 0);
          },
          onOpen: function(_, __, instance) {
            if (instance.calendarContainer) {
              instance.calendarContainer.classList.add('date-clean-picker');
              try { enhanceCalendar(instance); } catch(e) {}
              setTimeout(function(){ hideOtherMonthRows(instance); }, 0);
            }
          }
        });
      });
    }

    function renderJenisSelector() {
      const trigger = document.getElementById('jenis-dropdown-trigger');
      const menu = document.getElementById('jenis-dropdown-menu');
      const optionsList = document.getElementById('jenis-dropdown-options-list');
      const searchInput = document.getElementById('jenis-dropdown-search');
      const container = document.getElementById('jenis-dropdown-container');
      const hiddenInput = document.getElementById('jenis_inventaris_id_hidden');

      if (!jenisInventaris.length) {
        optionsList.innerHTML = `
          <div style="padding:16px;text-align:center;color:#9ca3af;">
            <div style="font-size:13px;font-weight:600;color:#6b7280;margin:0;">Tidak Ada Data</div>
            <div style="font-size:11px;color:#9ca3af;margin-top:4px;">Belum ada jenis inventaris yang tersedia.</div>
          </div>`;
        return;
      }

      // Render options list
      optionsList.innerHTML = jenisInventaris.map(j => {
        const icon = j.icon || 'box';
        const colors = j.color_gradient || ['#6b7280', '#4b5563'];
        const gradient = `linear-gradient(135deg, ${colors[0]} 0%, ${colors[1]} 100%)`;
        return `
          <li class="dropdown-option-item" data-id="${j.id}" data-search="${j.nama.toLowerCase()}">
            <div class="dropdown-option-icon" style="background: ${gradient};">
              <i data-feather="${icon}"></i>
            </div>
            <span class="dropdown-option-name">${j.nama}</span>
          </li>
        `;
      }).join('');

      if (typeof feather !== 'undefined') try { feather.replace(); } catch (e) {}

      // Click option logic
      optionsList.querySelectorAll('.dropdown-option-item').forEach(item => {
        item.addEventListener('click', function(e) {
          e.stopPropagation();
          optionsList.querySelectorAll('.dropdown-option-item').forEach(o => o.classList.remove('selected'));
          this.classList.add('selected');

          const id = parseInt(this.dataset.id);
          hiddenInput.value = id;
          selectedJenis = id;

          // Update trigger label
          const selectedData = jenisInventaris.find(j => j.id === id);
          if (selectedData) {
            const icon = selectedData.icon || 'box';
            const colors = selectedData.color_gradient || ['#6b7280', '#4b5563'];
            const gradient = `linear-gradient(135deg, ${colors[0]} 0%, ${colors[1]} 100%)`;
            
            document.getElementById('jenis-dropdown-selected-label').innerHTML = `
              <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:24px;height:24px;border-radius:6px;background:${gradient};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                  <i data-feather="${icon}" style="width:12px;height:12px;color:#fff;stroke:#fff;"></i>
                </div>
                <span style="font-size:13px;font-weight:600;color:#1f2937;">${selectedData.nama}</span>
              </div>
            `;
            if (typeof feather !== 'undefined') try { feather.replace(); } catch (e) {}
          }

          const jenisError = document.getElementById('jenis-error');
          if (jenisError) jenisError.style.display = 'none';

          // Fetch dynamic fields
          fetchAndRenderDynamicFields(selectedJenis);

          // Close menu
          menu.classList.remove('show');
          container.classList.remove('open');
        });
      });

      // Toggle dropdown
      trigger.addEventListener('click', function(e) {
        e.stopPropagation();
        const isOpen = menu.classList.contains('show');
        document.querySelectorAll('.dropdown-menu-panel').forEach(m => m.classList.remove('show'));
        document.querySelectorAll('.searchable-dropdown').forEach(c => c.classList.remove('open'));
        
        if (!isOpen) {
          menu.classList.add('show');
          container.classList.add('open');
          searchInput.value = '';
          searchInput.dispatchEvent(new Event('input'));
          // Focus search input
          setTimeout(() => searchInput.focus(), 50);
        }
      });

      // Search input filtering
      searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        let hasVisible = false;
        optionsList.querySelectorAll('.dropdown-option-item').forEach(item => {
          const text = item.dataset.search || '';
          if (text.includes(query)) {
            item.style.display = 'flex';
            hasVisible = true;
          } else {
            item.style.display = 'none';
          }
        });

        // Handle empty state in dropdown list
        let emptyMsg = optionsList.querySelector('.dropdown-empty-search');
        if (!hasVisible) {
          if (!emptyMsg) {
            emptyMsg = document.createElement('div');
            emptyMsg.className = 'dropdown-empty-search';
            emptyMsg.style.cssText = 'padding:16px;text-align:center;color:#9ca3af;font-size:12px;';
            emptyMsg.textContent = 'Tidak ada jenis inventaris yang cocok.';
            optionsList.appendChild(emptyMsg);
          }
        } else if (emptyMsg) {
          emptyMsg.remove();
        }
      });

      // Close dropdown when clicking outside
      document.addEventListener('click', function(e) {
        if (!container.contains(e.target)) {
          menu.classList.remove('show');
          container.classList.remove('open');
        }
      });
    }

    // start
    renderJenisSelector();

    // Restore old input jika ada (setelah validation failure)
    if (oldJenisId) {
      const jenisOption = document.querySelector(`.dropdown-option-item[data-id="${oldJenisId}"]`);
      if (jenisOption) {
        jenisOption.click();
        // Tunggu render selesai lalu isi field values
        setTimeout(function() {
          Object.entries(oldInput).forEach(function([key, value]) {
            if (key === 'jenis_inventaris_id' || key === '_token') return;
            if (key === 'dynamic' && typeof value === 'object') {
              Object.entries(value).forEach(function([dynKey, dynVal]) {
                const el = document.querySelector(`[name="dynamic[${dynKey}]"]`);
                if (el) {
                  if (el.classList.contains('year-picker')) {
                    const yearValue = String(dynVal || '').replace(/\D/g, '').slice(0, 4);
                    if (el._flatpickr && yearValue) {
                      el._flatpickr.setDate(`${yearValue}-01-01`, true, 'Y');
                    } else {
                      el.value = yearValue;
                    }
                    return;
                  }
                  if (el.classList.contains('month-picker')) {
                    const monthValue = String(dynVal || '').trim();
                    if (el._flatpickr && /^\d{4}-\d{2}$/.test(monthValue)) {
                      el._flatpickr.setDate(`${monthValue}-01`, true, 'Y-m');
                    } else {
                      el.value = monthValue;
                    }
                    return;
                  }
                  if (el.classList.contains('date-picker')) {
                    const dateValue = String(dynVal || '').trim();
                    if (el._flatpickr && /^\d{4}-\d{2}-\d{2}$/.test(dateValue)) {
                      el._flatpickr.setDate(dateValue, true, 'Y-m-d');
                    } else {
                      el.value = dateValue;
                    }
                    return;
                  }
                  if (el.classList.contains('currency-rupiah-input') && dynVal) {
                    let dbVal = String(dynVal);
                    let dbParts = dbVal.split('.');
                    let intP = dbParts[0].replace(/\D/g, '');
                    let decP = (dbParts[1] && dbParts[1] !== '00') ? dbParts[1].substring(0, 2) : null;
                    let fmt = intP === '' ? '' : new Intl.NumberFormat('id-ID').format(parseInt(intP) || 0);
                    if (decP) fmt += ',' + decP;
                    el.value = fmt;
                  } else {
                    el.value = dynVal || '';
                  }
                }
              });
              return;
            }
            if (key === 'kondisi') {
              const radio = document.querySelector(`input[name="kondisi"][value="${value}"]`);
              if (radio) radio.checked = true;
              return;
            }
            const el = document.querySelector(`[name="${key}"]`);
            if (!el) return;
            if (el.type === 'number' || el.tagName === 'TEXTAREA' || el.type === 'text' || el.type === 'date' || el.type === 'month' || el.tagName === 'SELECT') {
              // Format currency field (deteksi via class)
              if (el.classList.contains('year-picker')) {
                const yearValue = String(value || '').replace(/\D/g, '').slice(0, 4);
                if (el._flatpickr && yearValue) {
                  el._flatpickr.setDate(`${yearValue}-01-01`, true, 'Y');
                } else {
                  el.value = yearValue;
                }
              } else
              if (el.classList.contains('month-picker')) {
                const monthValue = String(value || '').trim();
                if (el._flatpickr && /^\d{4}-\d{2}$/.test(monthValue)) {
                  el._flatpickr.setDate(`${monthValue}-01`, true, 'Y-m');
                } else {
                  el.value = monthValue;
                }
              } else
              if (el.classList.contains('date-picker')) {
                const dateValue = String(value || '').trim();
                if (el._flatpickr && /^\d{4}-\d{2}-\d{2}$/.test(dateValue)) {
                  el._flatpickr.setDate(dateValue, true, 'Y-m-d');
                } else {
                  el.value = dateValue;
                }
              } else
              if (el.classList.contains('currency-rupiah-input') && value) {
                let dbVal = String(value); // e.g. "150000.59" or "150000"
                let dbParts = dbVal.split('.');
                let intP = dbParts[0].replace(/\D/g, '');
                let decP = (dbParts[1] && dbParts[1] !== '00') ? dbParts[1].substring(0, 2) : null;
                let fmt = intP === '' ? '' : new Intl.NumberFormat('id-ID').format(parseInt(intP) || 0);
                if (decP) fmt += ',' + decP;
                el.value = fmt;
              } else {
                el.value = value || '';
              }
            }
          });
          setupFieldSync();
          if (typeof feather !== 'undefined') try{feather.replace();}catch(e){}
        }, 50);
      }
    }

    // Event delegation global: pastikan field .date-picker selalu bisa membuka kalender,
    // tidak peduli kapan field tersebut dibuat (dinamis/statis).
    document.addEventListener('focusin', function(e) {
      const input = e.target;
      if (input && input.classList && input.classList.contains('date-picker') && !input._flatpickr) {
        initDatePickers(document);
        if (input._flatpickr) { input._flatpickr.open(); }
      }
    });

  } catch (e) { console.error('Page script error', e); }
});
    </script>

    <script>
    // =========================================================================
    // Kode Barang Hierarki — Modal Search (v3)
    // State: _kbLevel, _kbSelected
    // =========================================================================

    function kbCountSegments(kode) {
      if (!kode) return 0;
      return kode.split('.').map(s => s.trim()).filter(s => s !== '').length;
    }

    window._kbLevel    = 5;
    window._kbSelected = {}; // { level: { id, kode, uraian } }

    function openKodeBarangSearch() {
      window._kbLevel    = 5;
      window._kbSelected = {};
      let modal = document.getElementById('kb-search-modal');
      if (!modal) {
        modal = document.createElement('div');
        modal.id = 'kb-search-modal';
        modal.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;display:flex;align-items:center;justify-content:center;';
        modal.innerHTML = `
          <div style="background:#fff;border-radius:16px;width:95%;max-width:780px;max-height:84vh;display:flex;flex-direction:column;box-shadow:0 25px 60px rgba(0,0,0,.28);overflow:hidden;">
            <div style="padding:20px 24px 16px;border-bottom:1px solid #f3f4f6;display:flex;align-items:flex-start;justify-content:space-between;flex-shrink:0;">
              <div>
                <div style="font-size:16px;font-weight:700;color:#1f2937;">Cari Kode Barang</div>
                <div style="font-size:12px;color:#9ca3af;margin-top:3px;">Pilih hingga level Sub-Sub Kelompok untuk mendapatkan kode 5 struktur</div>
              </div>
              <button onclick="closeKbModal()" style="background:none;border:none;cursor:pointer;color:#9ca3af;font-size:22px;line-height:1;padding:0;margin-left:12px;flex-shrink:0;">&times;</button>
            </div>
            <div style="padding:14px 24px 0;flex-shrink:0;">
              <div style="position:relative;">
                <svg style="position:absolute;left:12px;top:50%;transform:translateY(-50%);width:16px;height:16px;stroke:#9ca3af;fill:none;stroke-width:2;" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                <input type="text" id="kb-search-input" placeholder="Cari nama atau kode barang..."
                  style="width:100%;padding:10px 14px 10px 36px;border:1px solid #e5e7eb;border-radius:10px;font-size:13px;outline:none;box-sizing:border-box;"
                  onfocus="this.style.borderColor='#064635';this.style.boxShadow='0 0 0 3px rgba(6, 70, 53,.1)'"
                  onblur="this.style.borderColor='#e5e7eb';this.style.boxShadow='none'"
                  oninput="debounceKbSearch(this.value)">
              </div>
            </div>
            <div style="padding:12px 24px 0;flex-shrink:0;">
              <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
                <span style="font-size:11px;color:#6b7280;white-space:nowrap;font-weight:500;">Cari di struktur:</span>
                <button onclick="setKbLevel(1,this)" class="kb-level-btn" data-level="1" style="padding:5px 11px;border:1px solid #e5e7eb;background:#fff;color:#374151;border-radius:20px;font-size:11px;font-weight:600;cursor:pointer;">Golongan</button>
                <button onclick="setKbLevel(2,this)" class="kb-level-btn" data-level="2" style="padding:5px 11px;border:1px solid #e5e7eb;background:#fff;color:#374151;border-radius:20px;font-size:11px;font-weight:600;cursor:pointer;">Bidang</button>
                <button onclick="setKbLevel(3,this)" class="kb-level-btn" data-level="3" style="padding:5px 11px;border:1px solid #e5e7eb;background:#fff;color:#374151;border-radius:20px;font-size:11px;font-weight:600;cursor:pointer;">Kelompok</button>
                <button onclick="setKbLevel(4,this)" class="kb-level-btn" data-level="4" style="padding:5px 11px;border:1px solid #e5e7eb;background:#fff;color:#374151;border-radius:20px;font-size:11px;font-weight:600;cursor:pointer;">Sub Kelompok</button>
                <button onclick="setKbLevel(5,this)" class="kb-level-btn" data-level="5" style="padding:5px 11px;border:1px solid #064635;background:#064635;color:#fff;border-radius:20px;font-size:11px;font-weight:600;cursor:pointer;">Sub-Sub Kelompok</button>
              </div>
            </div>
            <div id="kb-breadcrumb" style="display:none;padding:8px 24px 0;flex-shrink:0;">
              <div style="display:flex;align-items:center;gap:4px;flex-wrap:wrap;padding:7px 12px;background:#f4faf5;border-radius:6px;border:1px solid #e5e7eb;">
                <span style="font-size:11px;color:#6b7280;font-weight:500;margin-right:2px;">Pilihan:</span>
                <div id="kb-breadcrumb-items" style="display:flex;align-items:center;gap:3px;flex-wrap:wrap;"></div>
                <button onclick="resetKbSelection()" style="margin-left:auto;background:none;border:none;cursor:pointer;color:#9ca3af;font-size:11px;padding:0;white-space:nowrap;">✕ Reset</button>
              </div>
            </div>
            <div style="height:1px;background:#f3f4f6;margin:12px 0 0;flex-shrink:0;"></div>
            <div id="kb-search-results" style="flex:1;overflow-y:auto;">
              <div style="padding:24px;text-align:center;color:#9ca3af;font-size:13px;">Memuat data...</div>
            </div>
          </div>
        `;
        document.body.appendChild(modal);
      } else {
        document.getElementById('kb-search-input').value = '';
        kbUpdateBreadcrumb();
        kbSetActiveTabBtn(5);
      }
      modal.style.display = 'flex';
      setTimeout(() => document.getElementById('kb-search-input')?.focus(), 120);
      doKbSearch('');
    }

    function closeKbModal() {
      const m = document.getElementById('kb-search-modal');
      if (m) m.style.display = 'none';
    }
    document.addEventListener('keydown', function(e){ if(e.key==='Escape') closeKbModal(); });

    function kbSetActiveTabBtn(level) {
      document.querySelectorAll('.kb-level-btn').forEach(b => {
        const lv = parseInt(b.getAttribute('data-level'));
        if (lv === level) { b.style.background='#064635'; b.style.color='#fff'; b.style.borderColor='#064635'; }
        else              { b.style.background='#fff';    b.style.color='#374151'; b.style.borderColor='#e5e7eb'; }
      });
    }

    function setKbLevel(level, btn) {
      window._kbLevel = level;
      kbSetActiveTabBtn(level);
      const inp = document.getElementById('kb-search-input');
      if (inp) inp.value = '';
      doKbSearch('');
    }

    function kbUpdateBreadcrumb() {
      const bc = document.getElementById('kb-breadcrumb');
      const bcItems = document.getElementById('kb-breadcrumb-items');
      if (!bc || !bcItems) return;
      const selected = [1,2,3,4,5].filter(l => window._kbSelected[l]);
      if (!selected.length) { bc.style.display = 'none'; return; }
      bc.style.display = 'block';
      bcItems.innerHTML = selected.map((l, idx) => {
        const s = window._kbSelected[l];
        const isLast = idx === selected.length - 1;
        return `<span onclick="kbBreadcrumbClick(${l})"
          style="font-size:11px;font-weight:${isLast?'700':'500'};color:#064635;cursor:pointer;white-space:nowrap;${!isLast?'opacity:0.6;':''}">
          ${s.kode} — ${s.uraian}
        </span>${!isLast?'<span style="color:#9ca3af;font-size:11px;margin:0 2px;">›</span>':''}`;
      }).join('');
    }

    function kbBreadcrumbClick(level) {
      for (let l = level; l <= 5; l++) delete window._kbSelected[l];
      window._kbLevel = level;
      kbSetActiveTabBtn(level);
      kbUpdateBreadcrumb();
      const inp = document.getElementById('kb-search-input');
      if (inp) inp.value = '';
      doKbSearch('');
    }

    function resetKbSelection() {
      window._kbSelected = {};
      window._kbLevel = 5;
      kbSetActiveTabBtn(5);
      kbUpdateBreadcrumb();
      const inp = document.getElementById('kb-search-input');
      if (inp) inp.value = '';
      doKbSearch('');
    }

    let _kbSearchTimeout = null;
    function debounceKbSearch(q) {
      clearTimeout(_kbSearchTimeout);
      _kbSearchTimeout = setTimeout(() => doKbSearch(q), 280);
    }

    function doKbSearch(q) {
      const resultsEl = document.getElementById('kb-search-results');
      if (!resultsEl) return;
      resultsEl.innerHTML = '<div style="padding:24px;text-align:center;color:#9ca3af;font-size:13px;">Memuat...</div>';
      const level = window._kbLevel || 5;

      // Tentukan parent_id atau ancestor_kode untuk filter
      let parentId     = null;
      let ancestorKode = '';
      if (level > 1 && window._kbSelected[level - 1]) {
        parentId = window._kbSelected[level - 1].id;
      } else if (level > 1) {
        // Cari ancestor tertinggi yang ada
        for (let l = 1; l < level; l++) {
          if (window._kbSelected[l]) ancestorKode = window._kbSelected[l].kode;
        }
      }

      let url = `{{ route('api.kb.search') }}?level=${level}`;
      if (q)                 url += `&q=${encodeURIComponent(q)}`;
      if (parentId !== null) url += `&parent_id=${parentId}`;
      if (ancestorKode)      url += `&ancestor_kode=${encodeURIComponent(ancestorKode)}`;

      fetch(url, {
        credentials: 'same-origin',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
      })
      .then(r => {
        if (!r.ok) {
          resultsEl.innerHTML = `<div style="padding:24px;text-align:center;color:#ef4444;font-size:13px;">Gagal memuat (${r.status}). Coba refresh halaman.</div>`;
          return Promise.reject('http_' + r.status);
        }
        return r.json();
      })
      .then(data => {
        if (!Array.isArray(data) || !data.length) {
          resultsEl.innerHTML = '<div style="padding:24px;text-align:center;color:#9ca3af;font-size:13px;">Tidak ada hasil ditemukan.</div>';
          return;
        }
        const selAtLevel = window._kbSelected[level];
        const pinned = data.filter(i => selAtLevel && i.id === selAtLevel.id);
        const rest   = data.filter(i => !(selAtLevel && i.id === selAtLevel.id));
        const ordered = [...pinned, ...rest];
        const isSelectable = (level === 5);
        resultsEl.innerHTML = ordered.map(item => {
          const kode     = item.kode_lengkap || item.kode || '';
          const uraian   = item.uraian || '';
          const isPinned = selAtLevel && item.id === selAtLevel.id;
          const kodeE    = kode.replace(/\\/g,'\\\\').replace(/'/g,"\\'");
          const uraianE  = uraian.replace(/\\/g,'\\\\').replace(/'/g,"\\'");
          const bg       = isPinned ? '#f0f4f8' : '#fff';
          if (isSelectable) {
            return `<div onclick="selectKodeBarang('${kodeE}','${uraianE}')"
              style="padding:11px 20px;cursor:pointer;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;gap:10px;background:${bg};"
              onmouseover="this.style.background='#f4faf5'" onmouseout="this.style.background='${bg}'">
              <span style="font-size:12px;font-weight:700;color:#064635;white-space:nowrap;">${kode}</span>
              <span style="font-size:13px;color:#374151;margin:0 2px;">—</span>
              <span style="font-size:13px;color:#1f2937;flex:1;">${uraian}</span>
              ${isPinned?'<span style="font-size:10px;color:#064635;font-weight:700;white-space:nowrap;">✓ Terpilih</span>':''}
            </div>`;
          } else {
            return `<div onclick="kbDrillDown(${level},${item.id},'${kodeE}','${uraianE}')"
              style="padding:11px 20px;cursor:pointer;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;gap:10px;background:${bg};"
              onmouseover="this.style.background='#f4faf5'" onmouseout="this.style.background='${bg}'">
              <span style="font-size:12px;font-weight:700;color:#064635;white-space:nowrap;">${kode}</span>
              <span style="font-size:13px;color:#374151;margin:0 2px;">—</span>
              <span style="font-size:13px;color:#1f2937;flex:1;">${uraian}</span>
              <span style="margin-left:auto;font-size:10px;color:#9ca3af;white-space:nowrap;flex-shrink:0;">${isPinned?'✓ Dipilih · ':''}Pilih struktur berikutnya ›</span>
            </div>`;
          }
        }).join('');
      })
      .catch(() => {
        resultsEl.innerHTML = '<div style="padding:24px;text-align:center;color:#ef4444;font-size:13px;">Gagal memuat data. Coba lagi.</div>';
      });
    }

    // Resolve semua ancestor dari kode item yang dipilih, isi _kbSelected untuk level yang dilewati
    function kbResolveAncestors(kode, currentLevel, callback) {
      const needsResolve = [];
      for (let l = 1; l < currentLevel; l++) {
        if (!window._kbSelected[l]) needsResolve.push(l);
      }
      if (!needsResolve.length) { callback(); return; }

      const url = `{{ route('api.kb.resolve-ancestors') }}?kode=${encodeURIComponent(kode)}`;
      fetch(url, {
        credentials: 'same-origin',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
      })
      .then(r => r.json())
      .then(ancestors => {
        Object.keys(ancestors).forEach(lvl => {
          const l = parseInt(lvl);
          if (l < currentLevel && !window._kbSelected[l]) {
            window._kbSelected[l] = ancestors[lvl];
          }
        });
        callback();
      })
      .catch(() => { callback(); });
    }

    function kbDrillDown(level, id, kode, uraian) {
      window._kbSelected[level] = { id, kode, uraian };
      for (let l = level + 1; l <= 5; l++) delete window._kbSelected[l];
      const nextLevel = level + 1;
      window._kbLevel = nextLevel;
      kbSetActiveTabBtn(nextLevel);
      // Resolve ancestor yang mungkin dilewati, lalu update breadcrumb dan search
      kbResolveAncestors(kode, level, function() {
        kbUpdateBreadcrumb();
        const inp = document.getElementById('kb-search-input');
        if (inp) inp.value = '';
        doKbSearch('');
      });
    }

    function selectKodeBarang(kode, uraian) {
      const segCount = kbCountSegments(kode);
      if (segCount !== 5) {
        AppModal.error('Kode Barang Tidak Valid',
          'Kode barang harus memiliki 5 struktur lengkap.<br>Silakan pilih dari level <strong>Sub-Sub Kelompok</strong>.');
        return;
      }
      const input = document.getElementById('kode_barang');
      const info  = document.getElementById('kode-barang-selected-info');
      if (input) input.value = kode;
      if (info) { info.style.display = 'block'; info.textContent = kode + ' — ' + uraian; }
      closeKbModal();
      if (typeof feather !== 'undefined') feather.replace();
    }

    // ── KIB Auto-sync: true 2-way real-time sync ────────────────────────────
    let _kibSyncLock = false;

    document.addEventListener('input', function(e) {
      if (_kibSyncLock) return;
      const input = e.target;

      // Arah 1: field jenis inventaris → KIB
      const kibKey = input.dataset.kibSync;
      if (kibKey) {
        const kibInput = document.getElementById('kib_' + kibKey);
        if (kibInput && kibInput.value !== input.value) {
          _kibSyncLock = true;
          kibInput.value = input.value;
          kibInput.dispatchEvent(new Event('input', { bubbles: true }));
          _kibSyncLock = false;
        }
        return;
      }

      // Arah 2: field KIB → jenis inventaris
      if (input.id && input.id.startsWith('kib_')) {
        const kibKey2 = input.id.replace('kib_', '');
        const sourceInput = document.querySelector('[data-kib-sync="' + kibKey2 + '"]');
        if (sourceInput && sourceInput.value !== input.value) {
          _kibSyncLock = true;
          sourceInput.value = input.value;
          sourceInput.dispatchEvent(new Event('input', { bubbles: true }));
          _kibSyncLock = false;
        }
      }
    });
    </script>

    <script>
      function toggleCopySection(cb) {
        const row = document.getElementById('copy-count-row');
        row.style.display = cb.checked ? 'block' : 'none';
        updateCopySubmitBtn();
      }

      function updateCopySubmitBtn() {
        const cb    = document.getElementById('copy-enabled');
        const input = document.getElementById('copy-jumlah');
        const label = document.getElementById('btn-submit-label');
        const hint  = document.getElementById('copy-hint-count');
        if (!cb || !cb.checked) {
          if (label) label.textContent = 'Simpan Aset';
          return;
        }
        const n = parseInt(input?.value) || 2;
        const safe = Math.max(2, Math.min(50, n));
        if (hint) hint.textContent = safe;
        if (label) label.textContent = 'Simpan ' + safe + ' Salinan';
      }

      function confirmCancel() {
        if (confirm('Apakah Anda yakin ingin membatalkan? Data yang sudah diisi tidak akan tersimpan.')) {
          window.location.href = "{{ route('manajemen-aset.index') }}";
        }
      }

      function submitSimpanAset() {
        const form = document.getElementById('inventaris-form');
        if (!validateForm()) return;

        const copyEnabled = document.getElementById('copy-enabled')?.checked;
        const copyJumlah  = copyEnabled ? (parseInt(document.getElementById('copy-jumlah')?.value) || 2) : 1;
        const safeCopy    = Math.max(1, Math.min(50, copyJumlah));

        // Inject hidden input copy_jumlah ke form
        let hiddenCopy = document.getElementById('_copy_jumlah_hidden');
        if (!hiddenCopy) {
          hiddenCopy = document.createElement('input');
          hiddenCopy.type = 'hidden';
          hiddenCopy.id   = '_copy_jumlah_hidden';
          hiddenCopy.name = 'copy_jumlah';
          form.appendChild(hiddenCopy);
        }
        hiddenCopy.value = safeCopy;

        const confirmMsg = copyEnabled && safeCopy > 1
          ? `Akan membuat ${safeCopy} salinan aset dengan data identik. Lanjutkan?`
          : 'Pastikan data inventaris yang dimasukkan sudah benar sebelum disimpan.';
        const confirmTitle = copyEnabled && safeCopy > 1
          ? `Konfirmasi Simpan ${safeCopy} Salinan`
          : 'Konfirmasi Simpan Aset';

        AppModal.confirm(
          confirmTitle,
          confirmMsg,
          function() {
            // Strip format rupiah sebelum submit agar server menerima angka murni
            form.querySelectorAll('.currency-rupiah-input').forEach(function(input) {
              var raw = input.value;
              var parts = raw.split(',');
              var intP = parts[0].replace(/\./g, '').replace(/\D/g, '');
              var decP = parts.length > 1 ? parts[1].replace(/\D/g, '').substring(0, 2) : '';
              input.value = decP ? intP + '.' + decP : intP;
            });
            form.submit();
          }
        );
      }

      function validateForm() {
        const form = document.getElementById('inventaris-form');
        const jenisError = document.getElementById('jenis-error');
        const bukuWrapper = document.getElementById('kib-section-wrapper');
        const asalGroup = document.querySelector('.asal-usul-group');
        const asalClientError = document.getElementById('asal-usul-client-error');

        if (jenisError) jenisError.style.display = 'none';

        const jenisSelected = document.getElementById('jenis_inventaris_id_hidden').value;
        if (!jenisSelected) {
          if (jenisError) {
            jenisError.style.display = 'block';
            jenisError.scrollIntoView({ behavior: 'smooth', block: 'center' });
          }
          return false;
        }

        const isBukuVisible = !!(bukuWrapper && bukuWrapper.offsetParent !== null && bukuWrapper.style.display !== 'none');
        // Asal usul sekarang pakai <select>, bukan radio
        const asalSelect = document.getElementById('asal_usul');
        const asalSelected = asalSelect && asalSelect.value ? asalSelect : null;
        if (asalGroup) asalGroup.classList.remove('group-invalid');
        if (asalClientError) asalClientError.style.display = 'none';

        if (isBukuVisible && !asalSelected) {
          if (asalSelect) {
            asalSelect.style.borderColor = '#dc2626';
            asalSelect.scrollIntoView({ behavior: 'smooth', block: 'center' });
          }
          if (asalClientError) asalClientError.style.display = 'block';
          return false;
        }
        if (asalSelect) asalSelect.style.borderColor = '';

        if (!form.checkValidity()) {
          form.reportValidity();
          const firstInvalid = form.querySelector(':invalid');
          if (firstInvalid && typeof firstInvalid.scrollIntoView === 'function') {
            firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
          }
          return false;
        }

        return true;
      }

      // Samakan alur validasi ketika submit via Enter keyboard
      document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('inventaris-form');
        if (!form) return;

        // Asal usul sekarang pakai <select> — clear error saat berubah
        const asalSelectEl = document.getElementById('asal_usul');
        if (asalSelectEl) {
          asalSelectEl.addEventListener('change', function() {
            this.style.borderColor = '';
            const asalClientError = document.getElementById('asal-usul-client-error');
            if (asalClientError) asalClientError.style.display = 'none';
          });
        }

        // Sinkronisasi dua arah real-time antara Data Tambahan & KIB
        const syncHandler = function(e) {
          const input = e.target;
          if (!input || !input.id) return;

          // 1. Data Tambahan -> Data KIB
          if (input.name && input.name.startsWith('dynamic[')) {
            const fieldName = input.id;
            const kibInput = document.getElementById('kib_' + fieldName);
            if (kibInput && kibInput.value !== input.value) {
              kibInput.value = input.value;
              kibInput.dispatchEvent(new Event('input', { bubbles: true }));
            }
          }

          // 2. Data KIB -> Data Tambahan
          if (input.name && input.name.startsWith('kib[')) {
            const fieldName = input.id.replace('kib_', '');
            const generalInput = document.getElementById(fieldName);
            if (generalInput && generalInput.value !== input.value) {
              generalInput.value = input.value;
              generalInput.dispatchEvent(new Event('input', { bubbles: true }));
            }
          }
        };

        form.addEventListener('input', syncHandler);
        form.addEventListener('change', syncHandler);

        form.addEventListener('submit', function(e) {
          e.preventDefault();
          submitSimpanAset();
        });
      });

      // ---- Foto preview logic ----
      let selectedFiles = [];
      const FOTO_MAX_COUNT = 5;
      const FOTO_MAX_SIZE_BYTES = 5 * 1024 * 1024;
      const FOTO_MAX_TOTAL_BYTES = 25 * 1024 * 1024;
      const CAN_MUTATE_FILE_INPUT = (() => {
        try {
          return typeof DataTransfer !== 'undefined' && !!(new DataTransfer()).items;
        } catch (e) {
          return false;
        }
      })();

      function syncSelectedFilesToInput() {
        if (!CAN_MUTATE_FILE_INPUT) return false;
        const inputEl = document.getElementById('foto-input');
        if (!inputEl) return false;
        try {
          const dt = new DataTransfer();
          selectedFiles.forEach(f => dt.items.add(f));
          inputEl.files = dt.files;
          return true;
        } catch (e) {
          return false;
        }
      }

      function renderFotoGrid() {
        const grid = document.getElementById('foto-preview-grid');
        const limitMsg = document.getElementById('foto-limit-msg');
        const badge = document.getElementById('foto-count-badge');
        const dropArea = document.getElementById('foto-drop-area');
        grid.innerHTML = '';
        limitMsg.style.display = selectedFiles.length >= FOTO_MAX_COUNT ? 'block' : 'none';
        if (dropArea) dropArea.classList.toggle('has-file', selectedFiles.length > 0);
        if (badge) {
          if (selectedFiles.length > 0) {
            badge.style.display = 'inline-block';
            badge.textContent = selectedFiles.length + '/5';
          } else {
            badge.style.display = 'none';
          }
        }
        selectedFiles.forEach((file, idx) => {
          const reader = new FileReader();
          reader.onload = e => {
            const wrap = document.createElement('div');
            wrap.className = 'foto-preview-thumb';
            wrap.innerHTML = `<img src="${e.target.result}" alt="foto"><button type="button" class="remove-btn" onclick="removeFoto(${idx})">&times;</button>`;
            grid.appendChild(wrap);
          };
          reader.readAsDataURL(file);
        });
        syncSelectedFilesToInput();
      }

      function handleFotoPreview(input) {
        const added = Array.from(input.files || []);
        const isFallbackMode = !CAN_MUTATE_FILE_INPUT;

        if (isFallbackMode) {
          selectedFiles = [];
        }

        let currentTotalBytes = selectedFiles.reduce((total, file) => total + file.size, 0);
        let rejectedBySize = 0;
        let rejectedByCount = 0;
        let rejectedByTotal = 0;

        added.forEach(file => {
          if (file.size > FOTO_MAX_SIZE_BYTES) {
            rejectedBySize++;
            return;
          }

          if (selectedFiles.length >= FOTO_MAX_COUNT) {
            rejectedByCount++;
            return;
          }

          if ((currentTotalBytes + file.size) > FOTO_MAX_TOTAL_BYTES) {
            rejectedByTotal++;
            return;
          }

          selectedFiles.push(file);
          currentTotalBytes += file.size;
        });

        if (rejectedBySize || rejectedByCount || rejectedByTotal) {
          const messages = [];
          if (rejectedBySize) messages.push(`${rejectedBySize} foto melebihi batas 5 MB per foto.`);
          if (rejectedByCount) messages.push(`Maksimal upload adalah ${FOTO_MAX_COUNT} foto.`);
          if (rejectedByTotal) messages.push('Total ukuran seluruh foto maksimal 25 MB.');
          AppModal.error('Validasi Upload Foto', messages.join('<br>'));
        }

        if (isFallbackMode) {
          if (rejectedBySize || rejectedByCount || rejectedByTotal) {
            // Fallback browser mode cannot safely mutate FileList; force reselect so submitted files stay valid.
            input.value = '';
            selectedFiles = [];
          }
        } else {
          input.value = '';
        }

        renderFotoGrid();
      }

      function removeFoto(idx) {
        if (!CAN_MUTATE_FILE_INPUT) {
          AppModal.error('Browser Tidak Mendukung', 'Fitur hapus per foto tidak didukung di browser ini. Silakan pilih ulang foto.');
          const inputEl = document.getElementById('foto-input');
          if (inputEl) inputEl.value = '';
          selectedFiles = [];
          renderFotoGrid();
          return;
        }

        selectedFiles.splice(idx, 1);
        renderFotoGrid();
      }

      function toggleFotoSection() {
        const section  = document.getElementById('foto-upload-section');
        const chevron  = document.getElementById('foto-chevron');
        const btn      = document.getElementById('foto-toggle-btn');
        const isOpen   = section.style.display !== 'none';
        section.style.display = isOpen ? 'none' : 'block';
        chevron.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
        btn.style.borderRadius = isOpen ? '10px' : '10px 10px 0 0';
        btn.style.borderStyle = isOpen ? 'dashed' : 'solid';
        btn.style.borderColor = isOpen ? '#cbd5e1' : '#94a3b8';
        if (!isOpen && typeof feather !== 'undefined') try{feather.replace();}catch(e){}
      }
    </script>
  </body>
</html>
