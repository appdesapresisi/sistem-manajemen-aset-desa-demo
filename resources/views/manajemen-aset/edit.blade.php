<!doctype html>
<html lang="en" data-pc-preset="{{ $pc_preset_theme }}" data-pc-theme="{{ $pc_dark_layout }}">
<head>
    @include('layouts.head-page-meta', ['title' => 'Edit Inventaris'])
    @include('layouts.head-css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
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
            align-items: flex-start;
            gap: 8px;
        }
        .form-section-title i,
        .form-section-title svg {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
            margin-top: 1px;
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
            flex-shrink: 0;
            position: relative !important;
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
        .currency-input-wrapper,
        .unit-suffix-input-wrapper {
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
        .flatpickr-calendar.date-clean-picker .flatpickr-months {
            display: flex !important; align-items: center !important; padding: 8px 4px 0 !important;
        }
        .flatpickr-calendar.date-clean-picker .flatpickr-prev-month,
        .flatpickr-calendar.date-clean-picker .flatpickr-next-month {
            position: static !important; flex-shrink: 0;
            display: flex !important; align-items: center !important; justify-content: center !important;
            top: unset !important; padding: 0 !important; width: 28px !important; height: 28px !important;
            border: 1px solid #e5e7eb !important; border-radius: 6px !important;
            background: #fff !important; color: #374151 !important; transition: background 0.15s !important;
        }
        .flatpickr-calendar.date-clean-picker .flatpickr-prev-month:hover,
        .flatpickr-calendar.date-clean-picker .flatpickr-next-month:hover { background: #f3f4f6 !important; }
        .flatpickr-calendar.date-clean-picker .flatpickr-prev-month svg,
        .flatpickr-calendar.date-clean-picker .flatpickr-next-month svg { fill: #374151 !important; width: 12px !important; height: 12px !important; }
        /* Warna teks nama hari */
        .flatpickr-calendar.date-clean-picker .flatpickr-weekday { color: #064635 !important; font-weight: 600 !important; }
        /* Sembunyikan tanggal bulan lain */
        .flatpickr-calendar.date-clean-picker .flatpickr-day.prevMonthDay,
        .flatpickr-calendar.date-clean-picker .flatpickr-day.nextMonthDay {
            visibility: hidden !important; pointer-events: none !important;
        }
        .flatpickr-calendar.date-clean-picker .flatpickr-month {
            flex: 1 !important; height: auto !important;
            display: flex !important; align-items: center !important; justify-content: center !important;
        }
        .flatpickr-calendar.date-clean-picker .flatpickr-current-month {
            display: flex !important; align-items: center !important; justify-content: center !important;
            gap: 2px; position: static !important; width: auto !important;
            padding: 0 !important; font-size: 14px !important;
        }
        .flatpickr-calendar.date-clean-picker .flatpickr-current-month .numInputWrapper { display: none !important; }
        .flatpickr-calendar.date-clean-picker .fp-cur-year {
            cursor: pointer; padding: 2px 8px; border-radius: 6px; font-weight: 700;
            font-size: 14px; color: #1f2937; user-select: none; transition: background 0.15s;
        }
        .flatpickr-calendar.date-clean-picker .fp-cur-year:hover { background: #f3f4f6; }
        .fp-month-panel, .fp-year-panel {
            display: none; position: absolute; top: 0; left: 0; right: 0; bottom: 0;
            background: #fff; border-radius: inherit; padding: 10px; z-index: 10;
            flex-direction: column; gap: 8px; box-sizing: border-box;
        }
        .fp-month-panel.show, .fp-year-panel.show { display: flex; }
        .fp-panel-header { display: flex; align-items: center; justify-content: space-between; flex-shrink: 0; }
        .fp-panel-title { font-size: 12px; font-weight: 700; color: #1f2937; }
        .fp-panel-close, .fp-panel-nav {
            width: 26px; height: 26px; border: 1px solid #e5e7eb; background: #fff; font-size: 13px; cursor: pointer;
            display: flex; align-items: center; justify-content: center; color: #374151; border-radius: 6px; transition: background 0.15s;
        }
        .fp-panel-close:hover, .fp-panel-nav:hover:not(:disabled) { background: #f3f4f6; }
        .fp-panel-nav:disabled { opacity: 0.45; cursor: not-allowed; }
        .fp-month-grid, .fp-year-grid { display: grid; grid-template-columns: repeat(3, 1fr); grid-template-rows: repeat(4, 1fr); gap: 8px; flex: 1; }
        .fp-month-btn, .fp-year-btn {
            border-radius: 8px; border: 1px solid #d1d5db; background: #fff;
            font-size: 12px; font-weight: 600; color: #374151; cursor: pointer; text-align: center; transition: all 0.15s;
            display: flex; align-items: center; justify-content: center; padding: 0;
        }
        .fp-month-btn:hover, .fp-year-btn:hover { background: #f3f4f6; }
        .fp-month-btn.active, .fp-year-btn.active { background: #1f2937; border-color: #1f2937; color: #fff; }
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
        .form-full {
            grid-column: 1 / -1;
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

        .form-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            margin-top: 24px;
        }
        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }
        .btn i {
            width: 16px;
            height: 16px;
        }
        .btn-secondary {
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #d1d5db;
        }
        .btn-secondary:hover {
            background: #e5e7eb;
        }
        .btn-primary {
            background: #064635;
            color: #fff;
            border: none;
        }
        .btn-primary:hover {
            background: #1c5240;
        }
        /* File upload (seragam) */
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
        @media (max-width: 768px) {
            .form-grid { grid-template-columns: 1fr; }
        }

        /* Card header: desktop = justify-between, mobile = stacked */
        .card-header-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .card-jenis-mobile { display: none; }
        .card-jenis-desktop { display: flex; align-items: center; gap: 0.5rem; }
        @media (max-width: 640px) {
            .card-header-inner { display: block; }
            .card-jenis-mobile { display: flex; align-items: center; gap: 0.5rem; margin-top: 4px; }
            .card-jenis-desktop { display: none; }
        }
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
            @include('layouts.breadcrumb', [
                'breadcrumbItemHome' => 'Inventaris',
                'breadcrumbItem' => 'Manajemen Aset',
                'breadcrumbItemUrl' => route('manajemen-aset.index'),
                'breadcrumbItemActive' => 'Edit Inventaris'
            ])

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i data-feather="check-circle" style="width:18px;height:18px;"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i data-feather="alert-circle" style="width:18px;height:18px;"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <div class="flex items-center gap-2 mb-2">
                    <i data-feather="alert-circle" style="width:18px;height:18px;"></i>
                    <strong>Terdapat kesalahan pada input:</strong>
                </div>
                <ul class="list-disc ml-6 mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="card">
                <div class="card-header border-b border-gray-100">
                    <div class="card-header-inner">
                        <div class="flex items-center">
                            <div class="card-header-icon">
                                <i data-feather="edit-2"></i>
                            </div>
                            <div>
                                <h5 class="mb-1">Edit Inventaris</h5>
                                <p class="text-muted text-sm mb-0">Perbarui detail informasi barang inventaris</p>
                                <div class="items-center gap-2 card-jenis-mobile" style="margin-top:2px;">
                                    <span class="text-xs text-muted">Jenis:</span>
                                    <span class="text-xs font-semibold" style="color:#064635;font-weight:600;">{{ $inventaris->jenis->nama ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="items-center gap-2 card-jenis-desktop">
                            <span class="text-sm text-muted">Jenis:</span>
                            <span class="text-sm font-semibold" style="color:#064635;font-weight:600;">{{ $inventaris->jenis->nama ?? '-' }}</span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form id="edit-aset-form" action="{{ route('manajemen-aset.update', $inventaris->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Info Alert -->
                        <div style="background: #f0f9ff; border: 1px solid #bfdbfe; border-radius: 8px; padding:12px 16px; margin-bottom:20px; font-size:13px; color:#1e40af;">
                            <i data-feather="info" style="width:16px;height:16px; display:inline-block; margin-right:8px; vertical-align:middle; color:#1e40af; stroke:#1e40af;"></i>
                            <strong>Informasi:</strong> Jenis Inventaris tidak dapat diubah. Anda dapat mengubah semua data termasuk kode barang.
                        </div>

                        <!-- Data Umum & Custom Fields (Urut sesuai Order) -->
                        @if($fieldsWithData && $fieldsWithData->count() > 0)
                            <div class="buku-inventaris-section">
                                <div class="buku-inventaris-header" style="margin-bottom: 16px;">
                                    <div class="buku-inventaris-header-icon">
                                        <i data-feather="file-text"></i>
                                    </div>
                                    <div class="buku-inventaris-header-content">
                                        <span class="buku-inventaris-title">Data Inventaris</span>
                                        <p class="buku-inventaris-desc">Field ini digunakan untuk mengisi informasi utama dan data umum dari inventaris.</p>
                                    </div>
                                </div>
                                <div class="form-grid">
                                    
                                    <!-- Jenis Inventaris (Read-only, selalu di atas) -->

                                    <div class="form-group">
                                        <label class="form-label">Jenis Inventaris</label>
                                        <input type="text" class="form-control" value="{{ $inventaris->jenis->nama ?? '-' }}" disabled readonly />
                                        <input type="hidden" name="jenis_inventaris_id" value="{{ $inventaris->jenis_inventaris_id }}" />
                                        <small class="text-muted">Tidak dapat diubah</small>
                                    </div>

                                    <!-- Fields urut by ORDER -->
                                    @foreach($fieldsWithData as $item)
                                        @php
                                            $field = $item['field'];
                                            $value = $item['value'];
                                            $fieldType = $item['type']; // 'fixed' atau 'dynamic'

                                            // Skip field jenis_barang — sudah digabung ke nama_barang
                                            if ($fieldType === 'fixed' && ($field->fixed_field_name ?? null) === 'jenis_barang') continue;
                                            if (($field->nama_field ?? null) === 'jenis_barang') continue;

                                            $formName = $fieldType === 'fixed' 
                                                ? $field->nama_field 
                                                : "fields[{$field->id}]";
                                            $isFixedYearField = $fieldType === 'fixed' && (($field->fixed_field_name ?? null) === 'tahun_perolehan' || ($field->nama_field ?? null) === 'tahun_perolehan');
                                            $effectiveInputType = $isFixedYearField ? 'year' : ($field->tipe ?? 'text');
                                            $isFullWidth = in_array($effectiveInputType, ['textarea']);

                                            // Ganti label nama_barang menjadi "Jenis Barang / Nama Barang"
                                            $displayLabel = ($fieldType === 'fixed' && ($field->fixed_field_name ?? null) === 'nama_barang') || ($field->nama_field ?? null) === 'nama_barang'
                                                ? 'Jenis Barang / Nama Barang'
                                                : ($field->label_field ?? $field->nama_field);
                                        @endphp
                                        
                                        <div class="form-group{{ $isFullWidth ? ' form-full' : '' }}">
                                            <label class="form-label">
                                                {{ $displayLabel }}
                                                @if($field->is_required)<span class="required">*</span>@endif
                                            </label>

                                            @php
                                                $req = $field->is_required ? 'required' : '';
                                                $oldKey = str_replace(['[', ']'], ['.', ''], $formName);
                                                // Cek currency sebelum switch agar juga berlaku untuk tipe=text
                                                $isCurrencyField = \App\Models\JenisInventarisData::isCurrencyField(
                                                    $field->fixed_field_name ?? null,
                                                    $field->tipe ?? null,
                                                    $field->nama_field ?? null,
                                                    $field->label_field ?? null
                                                );
                                                if ($isCurrencyField) {
                                                    $rawNilai = old($oldKey, $value);
                                                    if ($rawNilai !== '' && $rawNilai !== null) {
                                                        $fNilai = (float) str_replace(['.', ','], ['', '.'], preg_replace('/[^0-9,.]/', '', $rawNilai));
                                                        $npDec = fmod($fNilai, 1) != 0 ? 2 : 0;
                                                        $displayNilai = number_format($fNilai, $npDec, ',', '.');
                                                    } else {
                                                        $displayNilai = '';
                                                    }
                                                }

                                                $isDecimalNumericField = in_array($effectiveInputType, ['number', 'decimal_m2', 'decimal_km', 'decimal_m'], true) && !$isCurrencyField;
                                                if ($isDecimalNumericField) {
                                                    $rawDecimal = old($oldKey, $value);
                                                    if ($rawDecimal !== '' && $rawDecimal !== null) {
                                                        $displayDecimal = preg_replace('/[^0-9,\.\-]/u', '', (string) $rawDecimal) ?? '';
                                                        if (str_contains($displayDecimal, '.') && !str_contains($displayDecimal, ',')) {
                                                            $displayDecimal = str_replace('.', ',', $displayDecimal);
                                                        }
                                                    } else {
                                                        $displayDecimal = '';
                                                    }
                                                }
                                            @endphp
                                            @if($isCurrencyField)
                                                <div class="currency-input-wrapper">
                                                    <span class="currency-prefix">Rp</span>
                                                    <input type="text" name="{{ $formName }}" id="{{ $field->nama_field }}"
                                                           class="form-control currency-rupiah-input"
                                                           value="{{ $displayNilai }}"
                                                           placeholder="0" {{ $req }} />
                                                </div>
                                            @else
                                            @switch($effectiveInputType)
                                                @case('textarea')
                                                    <textarea name="{{ $formName }}" id="{{ $field->nama_field }}" class="form-control" rows="2" {{ $req }}>{{ old($oldKey, $value) }}</textarea>
                                                    @break

                                                @case('select')
                                                    @php
                                                        $selectedVal = old($oldKey, $value);
                                                    @endphp
                                                    <select name="{{ $formName }}" id="{{ $field->nama_field }}" class="form-control form-select" {{ $req }}>
                                                        <option value="" disabled hidden {{ $selectedVal ? '' : 'selected' }}>-- Pilih --</option>
                                                    @if($field->nama_field === 'kondisi')
                                                            <option value="baik" {{ $selectedVal === 'baik' ? 'selected' : '' }}>Baik</option>
                                                            <option value="rusak_ringan" {{ $selectedVal === 'rusak_ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                                                            <option value="rusak_berat" {{ $selectedVal === 'rusak_berat' ? 'selected' : '' }}>Rusak Berat</option>
                                                        @endif
                                                    </select>
                                                    @break

                                                @case('year')
                                                    <input type="text" name="{{ $formName }}" id="{{ $field->nama_field }}" class="form-control year-picker"
                                                           value="{{ old($oldKey, $value) }}"
                                                           inputmode="numeric" maxlength="4"
                                                           data-min-year="1900" data-max-year="{{ now()->year + 1 }}"
                                                           placeholder="Pilih tahun" {{ $req }} />
                                                    @break

                                                @case('month')
                                                       <input type="text" name="{{ $formName }}" id="{{ $field->nama_field }}" class="form-control month-picker"
                                                           value="{{ old($oldKey, $value) }}"
                                                           data-min-year="1900" data-max-year="{{ now()->year + 1 }}"
                                                           placeholder="Pilih bulan dan tahun"
                                                           {{ $req }} />
                                                    @break

                                                @case('number')
                                                    <input type="text" name="{{ $formName }}" id="{{ $field->nama_field }}" class="form-control decimal-locale-input"
                                                           value="{{ $displayDecimal ?? old($oldKey, $value) }}"
                                                           inputmode="decimal"
                                                           placeholder="0" {{ $req }} />
                                                    @break

                                                @case('decimal_m2')
                                                    <div class="unit-suffix-input-wrapper">
                                                        <input type="text" name="{{ $formName }}" id="{{ $field->nama_field }}" class="form-control decimal-locale-input"
                                                               value="{{ $displayDecimal ?? old($oldKey, $value) }}"
                                                               inputmode="decimal"
                                                               placeholder="0" {{ $req }} data-kib-sync="luas_m2" />
                                                        <span class="unit-suffix">m²</span>
                                                    </div>
                                                    @break

                                                @case('decimal_km')
                                                    <div class="unit-suffix-input-wrapper">
                                                        <input type="text" name="{{ $formName }}" id="{{ $field->nama_field }}" class="form-control decimal-locale-input"
                                                               value="{{ $displayDecimal ?? old($oldKey, $value) }}"
                                                               inputmode="decimal"
                                                               placeholder="0" {{ $req }} data-kib-sync="panjang_km" />
                                                        <span class="unit-suffix">km</span>
                                                    </div>
                                                    @break

                                                @case('decimal_m')
                                                    <div class="unit-suffix-input-wrapper">
                                                        <input type="text" name="{{ $formName }}" id="{{ $field->nama_field }}" class="form-control decimal-locale-input"
                                                               value="{{ $displayDecimal ?? old($oldKey, $value) }}"
                                                               inputmode="decimal"
                                                               placeholder="0" {{ $req }} data-kib-sync="lebar_m" />
                                                        <span class="unit-suffix">m</span>
                                                    </div>
                                                    @break

                                                @case('date')
                                                       <input type="text" name="{{ $formName }}" id="{{ $field->nama_field }}" class="form-control date-picker"
                                                           value="{{ old($oldKey, $value) }}"
                                                           placeholder="Pilih tanggal"
                                                           {{ $req }} />
                                                    @break

                                                @default
                                                    @if($field->tipe === 'dropdown' && $field->dropdown_id)
                                                        @php
                                                            $ddItems = \App\Models\DropdownItem::where('dropdown_id', $field->dropdown_id)->orderBy('order')->get();
                                                            $selectedDd = old($oldKey, $value);
                                                        @endphp
                                                        <select name="{{ $formName }}" id="{{ $field->nama_field }}" class="form-control form-select" {{ $req }}>
                                                            <option value="" disabled hidden {{ $selectedDd ? '' : 'selected' }}>-- Pilih {{ $field->label_field }} --</option>
                                                            @foreach($ddItems as $ddItem)
                                                                <option value="{{ $ddItem->label }}" {{ $selectedDd === $ddItem->label ? 'selected' : '' }}>{{ $ddItem->label }}</option>
                                                            @endforeach
                                                        </select>
                                                    @elseif($field->field_type === 'fixed' && ($field->fixed_field_name ?? null) === 'kode_barang')
                                                        {{-- Kode barang: editable via modal search hierarki --}}
                                                        @php
                                                            $kbInfo = ($value ?? null)
                                                                ? \App\Models\KbSubSubKelompok::where('kode_lengkap', $value)->first()
                                                                : null;
                                                        @endphp
                                                        <div id="kode-barang-wrapper-edit" style="position:relative;">
                                                            <div style="display:flex;gap:8px;align-items:center;">
                                                                <input type="text" id="kode_barang_edit" name="{{ $formName }}"
                                                                    class="form-control"
                                                                    readonly
                                                                    style="flex:1;background:#ffffff;cursor:pointer;"
                                                                    onclick="openKodeBarangSearchEdit()"
                                                                    value="{{ old($oldKey, $value) }}">
                                                                <button type="button" onclick="openKodeBarangSearchEdit()"
                                                                    style="padding:10px 14px;background:linear-gradient(135deg,#064635,#1c5240);color:#fff;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;white-space:nowrap;display:flex;align-items:center;gap:6px;">
                                                                    Cari
                                                                </button>
                                                            </div>
                                                            <div id="kode-barang-selected-info-edit" style="{{ $kbInfo ? '' : 'display:none;' }}margin-top:6px;padding:8px 12px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:6px;font-size:12px;color:#166534;">
                                                                {{ $kbInfo ? $kbInfo->kode_lengkap . ' — ' . $kbInfo->uraian : '' }}
                                                            </div>
                                                        </div>
                                                    @else
                                                        @if($field->field_type === 'fixed' && ($field->fixed_field_name ?? null) === 'nup')
                                                            {{-- NUP: hanya angka --}}
                                                            <input type="text" name="{{ $formName }}" id="{{ $field->nama_field }}" class="form-control numeric-only-input"
                                                                   value="{{ old($oldKey, $value) }}"
                                                                   inputmode="numeric" pattern="[0-9]*"
                                                                   placeholder="NUP"
                                                                   {{ $req }} />
                                                        @else
                                                            <input type="text" name="{{ $formName }}" id="{{ $field->nama_field }}" class="form-control"
                                                                   value="{{ old($oldKey, $value) }}"
                                                                   {{ $req }} />
                                                        @endif
                                                    @endif
                                            @endswitch
                                            @endif

                                            @error($formName)
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    @endforeach

                                    <!-- Field Ruangan dipindahkan ke KIR section di luar -->

                                    <!-- Asal Usul Barang & Pengguna — selalu ada di section Data Inventaris -->
                                    <div class="form-group form-full" style="margin-top:4px;">
                                        <label class="form-label">Asal Usul Barang <span class="required">*</span></label>
                                        <select name="asal_usul" id="asal_usul" class="form-control form-select" required>
                                            <option value="" disabled hidden {{ old('asal_usul', $inventaris->asal_usul) ? '' : 'selected' }}>-- Pilih Asal Usul Barang --</option>
                                            <option value="apbdes" {{ old('asal_usul', $inventaris->asal_usul) === 'apbdes' ? 'selected' : '' }}>APBDes</option>
                                            <option value="perolehan_lain" {{ old('asal_usul', $inventaris->asal_usul) === 'perolehan_lain' ? 'selected' : '' }}>Perolehan Lain yang Sah</option>
                                            <option value="kekayaan_asli" {{ old('asal_usul', $inventaris->asal_usul) === 'kekayaan_asli' ? 'selected' : '' }}>Kekayaan Asli Desa</option>
                                        </select>
                                        @error('asal_usul')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="form-group form-full" style="margin-top:4px;">
                                        <label class="form-label" for="pengguna">Pengguna <span class="required">*</span></label>
                                        <input type="hidden" name="pengguna" id="pengguna" value="{{ old('pengguna', $inventaris->pengguna) }}">
                                        <div class="searchable-select" id="ss-pengguna">
                                          <div class="ss-trigger" onclick="ssToggle('ss-pengguna')">
                                            <span class="ss-value-text" id="ss-pengguna-text">{{ old('pengguna', $inventaris->pengguna) ?: '-- Pilih Pengguna --' }}</span>
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
                                                  $isSelected = old('pengguna', $inventaris->pengguna) === $user->nama;
                                                @endphp
                                                <div class="ss-option {{ $isSelected ? 'ss-selected' : '' }}" data-value="{{ $user->nama }}" data-search="{{ strtolower($label) }}" onclick="ssSelect('ss-pengguna', '{{ addslashes($user->nama) }}', '{{ addslashes($label) }}')">
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
                            </div>
                        @endif

                        <!-- Data KIB — field spesifik per jenis, muncul jika jenis punya kib_type -->
                        @if(!empty($kibConfig['fields']))
                        <div class="buku-inventaris-section">
                          <div class="buku-inventaris-header">
                            <div class="buku-inventaris-header-icon">
                              <i data-feather="book-open"></i>
                            </div>
                            <div class="buku-inventaris-header-content">
                              <span class="buku-inventaris-title">Data Buku Inventaris Aset Desa{{ !empty($kibConfig['nama']) ? ' — ' . $kibConfig['nama'] : '' }}</span>
                              <p class="buku-inventaris-desc">Field ini digunakan untuk mengisi data Buku Inventaris Aset Desa (KIB) sesuai format standar. Semua field bersifat opsional.</p>
                            </div>
                          </div>
                          <div style="display:grid;grid-template-columns:1fr;gap:16px;">
                            @foreach($kibConfig['fields'] as $kibField)
                            @php
                              $kibKey = $kibField['key'];
                              $kibLabel = $kibField['label'];
                              
                              $normKey = strtolower(trim($kibKey));
                              $normLabel = strtolower(trim(str_replace(['*', '(', ')'], '', $kibLabel)));
                              $dataUmumKeys = ['nama_barang', 'jenis_barang', 'kode_barang', 'nup', 'tahun_perolehan', 'nilai_perolehan', 'keterangan', 'asal_usul', 'pengguna', 'kondisi', 'satuan'];
                              $dataUmumLabels = ['nama barang', 'jenis barang / nama barang', 'jenis barang', 'kode barang', 'nup', 'tahun perolehan', 'nilai perolehan', 'keterangan', 'asal usul', 'asal usul barang', 'pengguna', 'kondisi', 'satuan'];
                              
                              if (in_array($normKey, $dataUmumKeys) || in_array($normLabel, $dataUmumLabels)) {
                                  continue;
                              }
                              
                              $kibType = $kibField['type'];
                              $kibOldVal = old('kib.' . $kibKey, $kibValues[$kibKey] ?? '');
                            @endphp
                              <div class="form-group">
                                <label class="form-label" for="kib_{{ $kibKey }}">{{ $kibLabel }}</label>
                                @if($kibType === 'select')
                                  <select id="kib_{{ $kibKey }}" name="kib[{{ $kibKey }}]" class="form-control form-select">
                                    <option value="" disabled hidden selected>-- Pilih --</option>
                                    @foreach($kibField['options'] as $opt)
                                      <option value="{{ $opt }}" {{ $kibOldVal === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                    @endforeach
                                  </select>
                                @elseif($kibType === 'textarea')
                                  <textarea id="kib_{{ $kibKey }}" name="kib[{{ $kibKey }}]" class="form-control" rows="2" placeholder="{{ $kibLabel }}">{{ $kibOldVal }}</textarea>
                                @elseif($kibType === 'decimal')
                                  @php
                                    $dRaw = $kibOldVal;
                                    $dDisplay = '';
                                    if ($dRaw !== '' && $dRaw !== null) {
                                        $dDisplay = str_contains((string)$dRaw, '.') && !str_contains((string)$dRaw, ',')
                                            ? str_replace('.', ',', $dRaw) : $dRaw;
                                    }
                                    $suffix = '';
                                    if (str_ends_with($kibKey, '_m2') || $kibKey === 'luas_m2') $suffix = 'm²';
                                    elseif (str_ends_with($kibKey, '_km') || $kibKey === 'panjang_km') $suffix = 'km';
                                    elseif (str_ends_with($kibKey, '_m') || $kibKey === 'lebar_m') $suffix = 'm';
                                  @endphp
                                  <div class="unit-suffix-input-wrapper">
                                    <input type="text" id="kib_{{ $kibKey }}" name="kib[{{ $kibKey }}]"
                                           class="form-control decimal-locale-input"
                                           value="{{ $dDisplay }}" inputmode="decimal" placeholder="0" />
                                    @if($suffix)<span class="unit-suffix">{{ $suffix }}</span>@endif
                                  </div>
                                @elseif($kibType === 'year')
                                  <input type="text" id="kib_{{ $kibKey }}" name="kib[{{ $kibKey }}]"
                                         class="form-control year-picker"
                                         value="{{ $kibOldVal }}"
                                         inputmode="numeric" maxlength="4"
                                         data-min-year="1900" data-max-year="{{ now()->year + 1 }}"
                                         placeholder="Pilih tahun" />
                                @elseif($kibType === 'date')
                                  <input type="text" id="kib_{{ $kibKey }}" name="kib[{{ $kibKey }}]"
                                         class="form-control date-picker"
                                         value="{{ $kibOldVal }}"
                                         placeholder="Pilih tanggal" />
                                @else
                                  <input type="text" id="kib_{{ $kibKey }}" name="kib[{{ $kibKey }}]"
                                         class="form-control"
                                         value="{{ $kibOldVal }}"
                                         placeholder="{{ $kibLabel }}" />
                                @endif
                              </div>
                            @endforeach
                          </div>
                        </div>
                        @endif

                        <!-- Data Laporan Aset — field satuan untuk export laporan per semester/tahunan -->
                        <div class="buku-inventaris-section" style="margin-top:16px;">
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
                                value="{{ old('satuan', $inventaris->satuan ?? '') }}"
                                style="width:100%;">
                              @error('satuan')
                                <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
                              @enderror
                            </div>
                            
                            <div class="form-group">
                              <label class="form-label" for="bulan_perolehan">Bulan Perolehan <span class="required">*</span></label>
                              <select id="bulan_perolehan" name="bulan_perolehan" class="form-control form-select" required>
                                <option value="" disabled hidden {{ old('bulan_perolehan', $inventaris->getBulanPerolehan()) ? '' : 'selected' }}>-- Pilih Bulan --</option>
                                <option value="1" {{ old('bulan_perolehan', $inventaris->getBulanPerolehan()) == '1' ? 'selected' : '' }}>Januari</option>
                                <option value="2" {{ old('bulan_perolehan', $inventaris->getBulanPerolehan()) == '2' ? 'selected' : '' }}>Februari</option>
                                <option value="3" {{ old('bulan_perolehan', $inventaris->getBulanPerolehan()) == '3' ? 'selected' : '' }}>Maret</option>
                                <option value="4" {{ old('bulan_perolehan', $inventaris->getBulanPerolehan()) == '4' ? 'selected' : '' }}>April</option>
                                <option value="5" {{ old('bulan_perolehan', $inventaris->getBulanPerolehan()) == '5' ? 'selected' : '' }}>Mei</option>
                                <option value="6" {{ old('bulan_perolehan', $inventaris->getBulanPerolehan()) == '6' ? 'selected' : '' }}>Juni</option>
                                <option value="7" {{ old('bulan_perolehan', $inventaris->getBulanPerolehan()) == '7' ? 'selected' : '' }}>Juli</option>
                                <option value="8" {{ old('bulan_perolehan', $inventaris->getBulanPerolehan()) == '8' ? 'selected' : '' }}>Agustus</option>
                                <option value="9" {{ old('bulan_perolehan', $inventaris->getBulanPerolehan()) == '9' ? 'selected' : '' }}>September</option>
                                <option value="10" {{ old('bulan_perolehan', $inventaris->getBulanPerolehan()) == '10' ? 'selected' : '' }}>Oktober</option>
                                <option value="11" {{ old('bulan_perolehan', $inventaris->getBulanPerolehan()) == '11' ? 'selected' : '' }}>November</option>
                                <option value="12" {{ old('bulan_perolehan', $inventaris->getBulanPerolehan()) == '12' ? 'selected' : '' }}>Desember</option>
                              </select>
                              <small class="form-hint">Untuk identifikasi aset masuk ke Semester 1 atau 2.</small>
                              @error('bulan_perolehan')
                                <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
                              @enderror
                            </div>
                          </div>
                        </div>

                        <!-- Data KIR (Kartu Inventaris Ruangan) — muncul jika jenis requires_ruangan = 1 -->
                        @if($inventaris->jenis && $inventaris->jenis->requires_ruangan)
                        <div id="kir-section-wrapper" class="buku-inventaris-section" style="margin-top:16px;">
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
                              <select name="ruangan_id" id="ruangan_id" class="form-control form-select" required>
                                <option value="" disabled hidden {{ old('ruangan_id', $inventaris->ruangan_id) ? '' : 'selected' }}>-- Pilih Ruangan --</option>
                                @foreach($ruanganList as $ruangan)
                                  <option value="{{ $ruangan->id }}" {{ old('ruangan_id', $inventaris->ruangan_id) == $ruangan->id ? 'selected' : '' }}>
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
                            $savedVal = old('kir.' . $kf['key'], $inventaris->getKirValue($kf['key']));
                          @endphp
                          <div class="form-group">
                            <label class="form-label" for="{{ $kf['key'] }}">{{ $kf['label'] }}</label>
                            @if(($kf['type'] ?? 'text') === 'select')
                              <select id="{{ $kf['key'] }}" name="kir[{{ $kf['key'] }}]" class="form-control form-select">
                                <option value="" {{ $savedVal === null || $savedVal === '' ? 'selected' : '' }}>-- Pilih Kondisi --</option>
                                <option value="baik"        {{ $savedVal === 'baik'         ? 'selected' : '' }}>Baik (B)</option>
                                <option value="rusak_ringan"{{ $savedVal === 'rusak_ringan'  ? 'selected' : '' }}>Kurang Baik (KB)</option>
                                <option value="rusak_berat" {{ $savedVal === 'rusak_berat'   ? 'selected' : '' }}>Rusak Berat (RB)</option>
                              </select>
                            @else
                              <input type="text" id="{{ $kf['key'] }}" name="kir[{{ $kf['key'] }}]" class="form-control" placeholder="{{ $kf['placeholder'] }}" value="{{ $savedVal }}" />
                            @endif
                          </div>
                        @endforeach
                          </div>
                        </div>
                        @endif

                        <!-- Foto Aset -->
                        <div class="buku-inventaris-section" style="margin-top:16px;">
                            <div class="buku-inventaris-header" style="margin-bottom: 16px;">
                                <div class="buku-inventaris-header-icon">
                                    <i data-feather="image"></i>
                                </div>
                                <div class="buku-inventaris-header-content">
                                    <span class="buku-inventaris-title">Foto Aset</span>
                                    <p class="buku-inventaris-desc">Unggah foto aset untuk mendokumentasikan kondisi fisik aset (maksimal 5 foto).</p>
                                </div>
                            </div>

                            {{-- Existing fotos --}}
                            @if($inventaris->fotos->isNotEmpty())
                            <div style="display:flex;flex-wrap:wrap;gap:10px;margin-bottom:14px;" id="existing-foto-grid">
                                @foreach($inventaris->fotos as $foto)
                                <div id="foto-wrap-{{ $foto->id }}" style="position:relative;width:90px;height:90px;border-radius:8px;overflow:hidden;border:1px solid #e5e7eb;">
                                    <img src="{{ Storage::url($foto->path) }}" style="width:100%;height:100%;object-fit:cover;" loading="lazy">
                                    <button type="button"
                                        onclick="deleteFotoExisting({{ $foto->id }})"
                                        style="position:absolute;top:2px;right:2px;background:rgba(220,38,38,.8);color:#fff;border:none;border-radius:50%;width:20px;height:20px;font-size:12px;cursor:pointer;line-height:1;">&times;</button>
                                    <input type="hidden" name="keep_fotos[]" value="{{ $foto->id }}">
                                </div>
                                @endforeach
                            </div>
                            @endif

                            {{-- New foto upload --}}
                            <input type="file" id="new-foto-input" name="fotos[]" accept="image/jpeg,image/png,image/webp" multiple
                                style="display:none;" onchange="handleNewFotoPreview(this)">
                            <div class="file-upload-area" id="new-foto-drop-area" onclick="document.getElementById('new-foto-input').click()">
                                <div class="file-upload-icon">
                                    <i data-feather="image"></i>
                                </div>
                                <div class="file-upload-text">Klik atau drag &amp; drop untuk tambah foto baru</div>
                                <div class="file-upload-hint">Format: JPG, PNG, WEBP &mdash; Setiap foto &le;5MB (total &le;25MB)</div>
                            </div>
                            <div id="new-foto-grid" style="display:flex;flex-wrap:wrap;gap:10px;margin-top:12px;"></div>
                            <p id="new-foto-limit-msg" style="color:#dc2626;font-size:12px;margin-top:6px;display:none;">Maksimal 5 foto total.</p>
                        </div>

                        <div class="info-box" style="margin-top:16px;">
                            <i data-feather="info" style="color:#1e40af;stroke:#1e40af;"></i>
                            <div style="font-size:13px;color:#1e40af;line-height:1.5;">
                                Perubahan data inventaris akan langsung tersimpan setelah Anda klik tombol <strong>Update</strong>.
                            </div>
                        </div>

                        <div class="form-actions">
                            <a href="{{ request('from') === 'show' ? route('manajemen-aset.show', $inventaris->id) : route('manajemen-aset.index') }}" class="btn btn-secondary" onclick="return confirm('Apakah Anda yakin ingin membatalkan? Data yang sudah diisi tidak akan tersimpan.');">
                                <i data-feather="x"></i>
                                Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i data-feather="save"></i>
                                Update Aset
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.footer-block')
    @include('layouts.footer-js')
    <script>
    // ── Searchable Select Component ──
    function ssToggle(id) {
      const wrap = document.getElementById(id);
      const dd = wrap.querySelector('.ss-dropdown');
      const trigger = wrap.querySelector('.ss-trigger');
      const isOpen = dd.style.display !== 'none';
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
      wrap.querySelectorAll('.ss-option').forEach(opt => {
        opt.classList.toggle('ss-selected', opt.getAttribute('data-value') === value);
      });
      wrap.querySelector('.ss-dropdown').style.display = 'none';
      wrap.querySelector('.ss-trigger').classList.remove('ss-open');
    }
    document.addEventListener('click', function(e) {
      if (!e.target.closest('.searchable-select')) {
        document.querySelectorAll('.searchable-select .ss-dropdown').forEach(d => { d.style.display = 'none'; });
        document.querySelectorAll('.ss-trigger').forEach(t => t.classList.remove('ss-open'));
      }
    });
    </script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
    <script>
        if(typeof feather !== 'undefined') {
            feather.replace();
        }

        // Populate dropdown kode barang saat halaman edit dimuat — DIHAPUS
        // Kode barang read-only di edit form, tidak perlu populate dropdown

        function attachNumericOnlyInputListener(root) {
            (root || document).querySelectorAll('.numeric-only-input').forEach(function(input) {
                if (input.dataset.numericBound === '1') return;
                input.dataset.numericBound = '1';
                input.addEventListener('input', function() {
                    const pos = this.selectionStart;
                    const before = this.value;
                    this.value = before.replace(/[^0-9]/g, '');
                    if (this.value.length < before.length) {
                        this.setSelectionRange(Math.max(0, pos - 1), Math.max(0, pos - 1));
                    }
                });
                input.addEventListener('keydown', function(e) {
                    const allowed = ['Backspace','Delete','Tab','Enter','ArrowLeft','ArrowRight','ArrowUp','ArrowDown','Home','End'];
                    if (allowed.includes(e.key)) return;
                    if ((e.ctrlKey || e.metaKey) && ['a','c','v','x'].includes(e.key.toLowerCase())) return;
                    if (!/^[0-9]$/.test(e.key)) e.preventDefault();
                });
            });
        }

        // Attach saat halaman load (untuk field yang sudah ada di DOM)
        attachNumericOnlyInputListener(document);

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

                    function enhanceCalendar(instance) {
                        const cal = instance.calendarContainer;
                        if (!cal || cal._modernBuilt) return;
                        cal._modernBuilt = true;

                        const minYear = 1900, maxYear = new Date().getFullYear() + 1;

                        const monthPanel = document.createElement('div');
                        monthPanel.className = 'fp-month-panel';
                        monthPanel.innerHTML =
                            '<div class="fp-panel-header"><span class="fp-panel-title">Pilih Bulan</span>' +
                            '<button type="button" class="fp-panel-close">&#x2715;</button></div>' +
                            '<div class="fp-month-grid">' +
                            BULAN_SHORT.map(function(m, i){ return '<button type="button" class="fp-month-btn" data-month="'+i+'">'+m+'</button>'; }).join('') +
                            '</div>';
                        cal.appendChild(monthPanel);

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
                            const end = ps + 11; // selalu 12 tahun
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
                                curMonthEl.appendChild(yearSpan);
                                if (numWrapper) numWrapper.style.display = 'none';
                                cal.addEventListener('click', function(){ setTimeout(function(){ yearSpan.textContent = instance.currentYear; }, 0); });
                            }
                        }
                    }

                    function hideOtherMonthRows(instance) {
                        var cal = instance && instance.calendarContainer;
                        if (!cal) return;
                        var days = cal.querySelectorAll('.flatpickr-days .flatpickr-day');
                        if (!days.length) return;
                        for (var i = 0; i < days.length; i += 7) {
                            var allHidden = true;
                            for (var j = i; j < i + 7 && j < days.length; j++) {
                                var d = days[j];
                                if (!d.classList.contains('prevMonthDay') && !d.classList.contains('nextMonthDay')) {
                                    allHidden = false; break;
                                }
                            }
                            for (var j = i; j < i + 7 && j < days.length; j++) {
                                var st = days[j].style;
                                st.height = allHidden ? '0' : '';
                                st.overflow = allHidden ? 'hidden' : '';
                                st.padding = allHidden ? '0' : '';
                                st.margin = allHidden ? '0' : '';
                                st.minHeight = allHidden ? '0' : '';
                                st.maxHeight = allHidden ? '0' : '';
                                st.lineHeight = allHidden ? '0' : '';
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

                initYearPickers(document);
                initMonthPickers(document);
                initDatePickers(document);

                // Event delegation global: pastikan field .date-picker selalu bisa membuka kalender
                document.addEventListener('focusin', function(e) {
                    const input = e.target;
                    if (input && input.classList && input.classList.contains('date-picker') && !input._flatpickr) {
                        initDatePickers(document);
                        if (input._flatpickr) { input._flatpickr.open(); }
                    }
                });

        // Confirm sebelum update
        (function() {
          const form = document.getElementById('edit-aset-form');
          if (form) {
            form.addEventListener('submit', function(e) {
              e.preventDefault();

                            if (!form.checkValidity()) {
                                form.reportValidity();
                                return;
                            }

              AppModal.confirm(
                'Konfirmasi Update Aset',
                'Apakah Anda yakin ingin menyimpan perubahan data aset ini?',
                function() {
                  form.submit();
                }
              );
            });
          }
        })();

        // ---- Currency format (semua .currency-rupiah-input) ----
        (function() {
                    function sanitizeDecimalLocaleInput(raw) {
                        var value = String(raw || '').replace(/[^0-9,\.\-]/g, '');
                        value = value.replace(/(?!^)-/g, '');
                        return value;
                    }

                    document.querySelectorAll('.decimal-locale-input').forEach(function(input) {
                        input.addEventListener('input', function() {
                            this.value = sanitizeDecimalLocaleInput(this.value);
                        });
                    });

          function formatRupiah(raw) {
            var parts = raw.split(',');
            var intPart = parts[0].replace(/\./g, '').replace(/\D/g, '');
            var hasComma = parts.length > 1;
            var decPart = hasComma ? parts[1].replace(/\D/g, '').substring(0, 2) : null;
            var formatted = intPart === '' ? '' : new Intl.NumberFormat('id-ID').format(parseInt(intPart) || 0);
            if (hasComma) formatted += ',' + decPart;
            return formatted;
          }

          document.querySelectorAll('.currency-rupiah-input').forEach(function(input) {
            input.addEventListener('input', function() {
              this.value = formatRupiah(this.value);
            });
          });

          // Strip format before submit so server receives plain number e.g. "1000.59"
          var form = document.getElementById('edit-aset-form');
          if (form) {
            form.addEventListener('submit', function() {
              form.querySelectorAll('.currency-rupiah-input').forEach(function(input) {
                var raw = input.value;
                var parts = raw.split(',');
                var intP = parts[0].replace(/\./g, '').replace(/\D/g, '');
                var decP = parts.length > 1 ? parts[1].replace(/\D/g, '').substring(0, 2) : '';
                input.value = decP ? intP + '.' + decP : intP;
              });
            }, true); // capture phase so it runs before confirm dialog blocks
          }
        })();

        // ---- Foto edit logic ----
        let newSelectedFiles = [];
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
        const existingCount = () => document.querySelectorAll('#existing-foto-grid [id^="foto-wrap-"]').length;

        function syncNewFotoInput() {
            if (!CAN_MUTATE_FILE_INPUT) return false;
            const inputEl = document.getElementById('new-foto-input');
            if (!inputEl) return false;
            try {
                const dt = new DataTransfer();
                newSelectedFiles.forEach(f => dt.items.add(f));
                inputEl.files = dt.files;
                return true;
            } catch (e) {
                return false;
            }
        }

        function handleNewFotoPreview(input) {
            const added = Array.from(input.files || []);
            const isFallbackMode = !CAN_MUTATE_FILE_INPUT;

            if (isFallbackMode) {
                newSelectedFiles = [];
            }

            let currentTotalBytes = newSelectedFiles.reduce((total, file) => total + file.size, 0);
            let rejectedBySize = 0;
            let rejectedByCount = 0;
            let rejectedByTotal = 0;

            added.forEach(file => {
                if (file.size > FOTO_MAX_SIZE_BYTES) {
                    rejectedBySize++;
                    return;
                }

                if ((existingCount() + newSelectedFiles.length) >= FOTO_MAX_COUNT) {
                    rejectedByCount++;
                    return;
                }

                if ((currentTotalBytes + file.size) > FOTO_MAX_TOTAL_BYTES) {
                    rejectedByTotal++;
                    return;
                }

                newSelectedFiles.push(file);
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
                    newSelectedFiles = [];
                }
            } else {
                input.value = '';
            }

            const grid = document.getElementById('new-foto-grid');
            grid.innerHTML = '';
            const limitMsg = document.getElementById('new-foto-limit-msg');
            limitMsg.style.display = (existingCount() + newSelectedFiles.length) >= FOTO_MAX_COUNT ? 'block' : 'none';

            newSelectedFiles.forEach((file, idx) => {
                const reader = new FileReader();
                reader.onload = e => {
                    const wrap = document.createElement('div');
                    wrap.className = 'foto-preview-thumb';
                    wrap.innerHTML = `<img src="${e.target.result}" alt="foto"><button type="button" class="remove-btn" onclick="removeNewFoto(${idx})">&times;</button>`;
                    grid.appendChild(wrap);
                };
                reader.readAsDataURL(file);
            });
            const dropArea = document.getElementById('new-foto-drop-area');
            if (dropArea) dropArea.classList.toggle('has-file', newSelectedFiles.length > 0);
            syncNewFotoInput();
        }

        function removeNewFoto(idx) {
            if (!CAN_MUTATE_FILE_INPUT) {
                AppModal.error('Browser Tidak Mendukung', 'Fitur hapus per foto tidak didukung di browser ini. Silakan pilih ulang foto.');
                const inputEl = document.getElementById('new-foto-input');
                if (inputEl) inputEl.value = '';
                newSelectedFiles = [];
                handleNewFotoPreview({ files: [] });
                return;
            }

            newSelectedFiles.splice(idx, 1);
            handleNewFotoPreview({ files: [] });
        }

        function deleteFotoExisting(id) {
            if (!confirm('Hapus foto ini?')) return;
            fetch(`/manajemen-aset/foto/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                }
            }).then(r => r.json()).then(data => {
                if (data.success) {
                    const wrap = document.getElementById('foto-wrap-' + id);
                    if (wrap) wrap.remove();
                }
            }).catch(() => alert('Gagal menghapus foto.'));
        }
    </script>

    <script>
    // =========================================================================
    // Kode Barang Hierarki — Modal Search untuk Edit (sama persis dengan create)
    // =========================================================================
    function kbCountSegmentsEdit(kode) {
      if (!kode) return 0;
      return kode.split('.').map(s => s.trim()).filter(s => s !== '').length;
    }

    window._kbLevelEdit    = 5;
    window._kbSelectedEdit = {};

    function openKodeBarangSearchEdit() {
      window._kbLevelEdit    = 5;
      window._kbSelectedEdit = {};
      let modal = document.getElementById('kb-search-modal-edit');
      if (!modal) {
        modal = document.createElement('div');
        modal.id = 'kb-search-modal-edit';
        modal.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;display:flex;align-items:center;justify-content:center;';
        modal.innerHTML = `
          <div style="background:#fff;border-radius:16px;width:95%;max-width:780px;max-height:84vh;display:flex;flex-direction:column;box-shadow:0 25px 60px rgba(0,0,0,.28);overflow:hidden;">
            <div style="padding:20px 24px 16px;border-bottom:1px solid #f3f4f6;display:flex;align-items:flex-start;justify-content:space-between;flex-shrink:0;">
              <div>
                <div style="font-size:16px;font-weight:700;color:#1f2937;">Cari Kode Barang</div>
                <div style="font-size:12px;color:#9ca3af;margin-top:3px;">Pilih hingga level Sub-Sub Kelompok untuk mendapatkan kode 5 struktur</div>
              </div>
              <button onclick="closeKbModalEdit()" style="background:none;border:none;cursor:pointer;color:#9ca3af;font-size:22px;line-height:1;padding:0;margin-left:12px;flex-shrink:0;">&times;</button>
            </div>
            <div style="padding:14px 24px 0;flex-shrink:0;">
              <div style="position:relative;">
                <svg style="position:absolute;left:12px;top:50%;transform:translateY(-50%);width:16px;height:16px;stroke:#9ca3af;fill:none;stroke-width:2;" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                <input type="text" id="kb-search-input-edit" placeholder="Cari nama atau kode barang..."
                  style="width:100%;padding:10px 14px 10px 36px;border:1px solid #e5e7eb;border-radius:10px;font-size:13px;outline:none;box-sizing:border-box;"
                  onfocus="this.style.borderColor='#064635';this.style.boxShadow='0 0 0 3px rgba(6, 70, 53,.1)'"
                  onblur="this.style.borderColor='#e5e7eb';this.style.boxShadow='none'"
                  oninput="debounceKbSearchEdit(this.value)">
              </div>
            </div>
            <div style="padding:12px 24px 0;flex-shrink:0;">
              <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
                <span style="font-size:11px;color:#6b7280;white-space:nowrap;font-weight:500;">Cari di struktur:</span>
                <button onclick="setKbLevelEdit(1,this)" class="kb-level-btn-edit" data-level="1" style="padding:5px 11px;border:1px solid #e5e7eb;background:#fff;color:#374151;border-radius:20px;font-size:11px;font-weight:600;cursor:pointer;">Golongan</button>
                <button onclick="setKbLevelEdit(2,this)" class="kb-level-btn-edit" data-level="2" style="padding:5px 11px;border:1px solid #e5e7eb;background:#fff;color:#374151;border-radius:20px;font-size:11px;font-weight:600;cursor:pointer;">Bidang</button>
                <button onclick="setKbLevelEdit(3,this)" class="kb-level-btn-edit" data-level="3" style="padding:5px 11px;border:1px solid #e5e7eb;background:#fff;color:#374151;border-radius:20px;font-size:11px;font-weight:600;cursor:pointer;">Kelompok</button>
                <button onclick="setKbLevelEdit(4,this)" class="kb-level-btn-edit" data-level="4" style="padding:5px 11px;border:1px solid #e5e7eb;background:#fff;color:#374151;border-radius:20px;font-size:11px;font-weight:600;cursor:pointer;">Sub Kelompok</button>
                <button onclick="setKbLevelEdit(5,this)" class="kb-level-btn-edit" data-level="5" style="padding:5px 11px;border:1px solid #064635;background:#064635;color:#fff;border-radius:20px;font-size:11px;font-weight:600;cursor:pointer;">Sub-Sub Kelompok</button>
              </div>
            </div>
            <div id="kb-breadcrumb-edit" style="display:none;padding:8px 24px 0;flex-shrink:0;">
              <div style="display:flex;align-items:center;gap:4px;flex-wrap:wrap;padding:7px 12px;background:#f4faf5;border-radius:6px;border:1px solid #e5e7eb;">
                <span style="font-size:11px;color:#6b7280;font-weight:500;margin-right:2px;">Pilihan:</span>
                <div id="kb-breadcrumb-items-edit" style="display:flex;align-items:center;gap:3px;flex-wrap:wrap;"></div>
                <button onclick="resetKbSelectionEdit()" style="margin-left:auto;background:none;border:none;cursor:pointer;color:#9ca3af;font-size:11px;padding:0;white-space:nowrap;">✕ Reset</button>
              </div>
            </div>
            <div style="height:1px;background:#f3f4f6;margin:12px 0 0;flex-shrink:0;"></div>
            <div id="kb-search-results-edit" style="flex:1;overflow-y:auto;">
              <div style="padding:24px;text-align:center;color:#9ca3af;font-size:13px;">Memuat data...</div>
            </div>
          </div>
        `;
        document.body.appendChild(modal);
      } else {
        document.getElementById('kb-search-input-edit').value = '';
        kbUpdateBreadcrumbEdit();
        kbSetActiveTabBtnEdit(5);
      }
      modal.style.display = 'flex';
      setTimeout(() => document.getElementById('kb-search-input-edit')?.focus(), 120);
      doKbSearchEdit('');
    }

    function closeKbModalEdit() { const m = document.getElementById('kb-search-modal-edit'); if (m) m.style.display = 'none'; }
    document.addEventListener('keydown', function(e){ if(e.key==='Escape') closeKbModalEdit(); });

    function kbSetActiveTabBtnEdit(level) {
      document.querySelectorAll('.kb-level-btn-edit').forEach(b => {
        const lv = parseInt(b.getAttribute('data-level'));
        if (lv === level) { b.style.background='#064635'; b.style.color='#fff'; b.style.borderColor='#064635'; }
        else              { b.style.background='#fff';    b.style.color='#374151'; b.style.borderColor='#e5e7eb'; }
      });
    }

    function setKbLevelEdit(level, btn) {
      window._kbLevelEdit = level;
      kbSetActiveTabBtnEdit(level);
      const inp = document.getElementById('kb-search-input-edit');
      if (inp) inp.value = '';
      doKbSearchEdit('');
    }

    function kbUpdateBreadcrumbEdit() {
      const bc = document.getElementById('kb-breadcrumb-edit');
      const bcItems = document.getElementById('kb-breadcrumb-items-edit');
      if (!bc || !bcItems) return;
      const selected = [1,2,3,4,5].filter(l => window._kbSelectedEdit[l]);
      if (!selected.length) { bc.style.display = 'none'; return; }
      bc.style.display = 'block';
      bcItems.innerHTML = selected.map((l, idx) => {
        const s = window._kbSelectedEdit[l];
        const isLast = idx === selected.length - 1;
        return `<span onclick="kbBreadcrumbClickEdit(${l})"
          style="font-size:11px;font-weight:${isLast?'700':'500'};color:#064635;cursor:pointer;white-space:nowrap;${!isLast?'opacity:0.6;':''}">
          ${s.kode} — ${s.uraian}
        </span>${!isLast?'<span style="color:#9ca3af;font-size:11px;margin:0 2px;">›</span>':''}`;
      }).join('');
    }

    function kbBreadcrumbClickEdit(level) {
      for (let l = level; l <= 5; l++) delete window._kbSelectedEdit[l];
      window._kbLevelEdit = level;
      kbSetActiveTabBtnEdit(level);
      kbUpdateBreadcrumbEdit();
      const inp = document.getElementById('kb-search-input-edit');
      if (inp) inp.value = '';
      doKbSearchEdit('');
    }

    function resetKbSelectionEdit() {
      window._kbSelectedEdit = {};
      window._kbLevelEdit = 5;
      kbSetActiveTabBtnEdit(5);
      kbUpdateBreadcrumbEdit();
      const inp = document.getElementById('kb-search-input-edit');
      if (inp) inp.value = '';
      doKbSearchEdit('');
    }

    let _kbSearchTimeoutEdit = null;
    function debounceKbSearchEdit(q) {
      clearTimeout(_kbSearchTimeoutEdit);
      _kbSearchTimeoutEdit = setTimeout(() => doKbSearchEdit(q), 280);
    }

    function doKbSearchEdit(q) {
      const resultsEl = document.getElementById('kb-search-results-edit');
      if (!resultsEl) return;
      resultsEl.innerHTML = '<div style="padding:24px;text-align:center;color:#9ca3af;font-size:13px;">Memuat...</div>';
      const level = window._kbLevelEdit || 5;
      let parentId = null;
      let ancestorKode = '';
      if (level > 1 && window._kbSelectedEdit[level - 1]) {
        parentId = window._kbSelectedEdit[level - 1].id;
      } else if (level > 1) {
        for (let l = 1; l < level; l++) {
          if (window._kbSelectedEdit[l]) ancestorKode = window._kbSelectedEdit[l].kode;
        }
      }
      let url = `{{ route('api.kb.search') }}?level=${level}`;
      if (q)                 url += `&q=${encodeURIComponent(q)}`;
      if (parentId !== null) url += `&parent_id=${parentId}`;
      if (ancestorKode)      url += `&ancestor_kode=${encodeURIComponent(ancestorKode)}`;
      fetch(url, { credentials:'same-origin', headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')||''} })
      .then(r => { if (!r.ok) { resultsEl.innerHTML = `<div style="padding:24px;text-align:center;color:#ef4444;font-size:13px;">Gagal memuat (${r.status}).</div>`; return Promise.reject(); } return r.json(); })
      .then(data => {
        if (!Array.isArray(data) || !data.length) { resultsEl.innerHTML = '<div style="padding:24px;text-align:center;color:#9ca3af;font-size:13px;">Tidak ada hasil ditemukan.</div>'; return; }
        const selAtLevel = window._kbSelectedEdit[level];
        const pinned = data.filter(i => selAtLevel && i.id === selAtLevel.id);
        const rest   = data.filter(i => !(selAtLevel && i.id === selAtLevel.id));
        const ordered = [...pinned, ...rest];
        const isSelectable = (level === 5);
        resultsEl.innerHTML = ordered.map(item => {
          const kode = item.kode_lengkap || item.kode || '';
          const uraian = item.uraian || '';
          const isPinned = selAtLevel && item.id === selAtLevel.id;
          const kodeE = kode.replace(/\\/g,'\\\\').replace(/'/g,"\\'");
          const uraianE = uraian.replace(/\\/g,'\\\\').replace(/'/g,"\\'");
          const bg = isPinned ? '#f0f4f8' : '#fff';
          if (isSelectable) {
            return `<div onclick="selectKodeBarangEdit('${kodeE}','${uraianE}')"
              style="padding:11px 20px;cursor:pointer;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;gap:10px;background:${bg};"
              onmouseover="this.style.background='#f4faf5'" onmouseout="this.style.background='${bg}'">
              <span style="font-size:12px;font-weight:700;color:#064635;white-space:nowrap;">${kode}</span>
              <span style="font-size:13px;color:#374151;margin:0 2px;">—</span>
              <span style="font-size:13px;color:#1f2937;flex:1;">${uraian}</span>
              ${isPinned?'<span style="font-size:10px;color:#064635;font-weight:700;white-space:nowrap;">✓ Terpilih</span>':''}
            </div>`;
          } else {
            return `<div onclick="kbDrillDownEdit(${level},${item.id},'${kodeE}','${uraianE}')"
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
      .catch(() => { resultsEl.innerHTML = '<div style="padding:24px;text-align:center;color:#ef4444;font-size:13px;">Gagal memuat data.</div>'; });
    }

    function kbResolveAncestorsEdit(kode, currentLevel, callback) {
      const needsResolve = [];
      for (let l = 1; l < currentLevel; l++) { if (!window._kbSelectedEdit[l]) needsResolve.push(l); }
      if (!needsResolve.length) { callback(); return; }
      fetch(`{{ route('api.kb.resolve-ancestors') }}?kode=${encodeURIComponent(kode)}`, { credentials:'same-origin', headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'} })
      .then(r => r.json())
      .then(ancestors => {
        Object.keys(ancestors).forEach(lvl => {
          const l = parseInt(lvl);
          if (l < currentLevel && !window._kbSelectedEdit[l]) window._kbSelectedEdit[l] = ancestors[lvl];
        });
        callback();
      })
      .catch(() => { callback(); });
    }

    function kbDrillDownEdit(level, id, kode, uraian) {
      window._kbSelectedEdit[level] = { id, kode, uraian };
      for (let l = level + 1; l <= 5; l++) delete window._kbSelectedEdit[l];
      const nextLevel = level + 1;
      window._kbLevelEdit = nextLevel;
      kbSetActiveTabBtnEdit(nextLevel);
      kbResolveAncestorsEdit(kode, level, function() {
        kbUpdateBreadcrumbEdit();
        const inp = document.getElementById('kb-search-input-edit');
        if (inp) inp.value = '';
        doKbSearchEdit('');
      });
    }

    function selectKodeBarangEdit(kode, uraian) {
      const segCount = kbCountSegmentsEdit(kode);
      if (segCount !== 5) {
        AppModal.error('Kode Barang Tidak Valid', 'Kode barang harus memiliki 5 struktur lengkap.<br>Silakan pilih dari level <strong>Sub-Sub Kelompok</strong>.');
        return;
      }
      const input = document.getElementById('kode_barang_edit');
      const info  = document.getElementById('kode-barang-selected-info-edit');
      if (input) input.value = kode;
      if (info) { info.style.display = 'block'; info.textContent = kode + ' — ' + uraian; }
      closeKbModalEdit();
      if (typeof feather !== 'undefined') feather.replace();
    }

    // ── KIB Auto-sync: true 2-way real-time sync ────────────────────────────
    // Ketik di field jenis inventaris → KIB ikut, dan sebaliknya.
    const editForm = document.getElementById('edit-aset-form');
    if (editForm) {
      const syncHandler = function(e) {
        const input = e.target;
        if (!input || !input.id) return;

        // 1. Data Tambahan / Dynamic Field -> KIB
        if (input.name && (input.name.startsWith('fields[') || input.name.startsWith('dynamic['))) {
          // Sync via ID match
          const fieldName = input.id;
          const kibInput = document.getElementById('kib_' + fieldName);
          if (kibInput && kibInput.value !== input.value) {
            kibInput.value = input.value;
            kibInput.dispatchEvent(new Event('input', { bubbles: true }));
          }

          // Sync via data-kib-sync attribute
          const kibKey = input.dataset.kibSync;
          if (kibKey) {
            const kibInputAttr = document.getElementById('kib_' + kibKey);
            if (kibInputAttr && kibInputAttr.value !== input.value) {
              kibInputAttr.value = input.value;
              kibInputAttr.dispatchEvent(new Event('input', { bubbles: true }));
            }
          }
        }

        // 2. KIB -> Data Tambahan / Dynamic Field
        if (input.name && input.name.startsWith('kib[')) {
          const fieldName = input.id.replace('kib_', '');
          
          // Sync via ID match
          const generalInput = document.getElementById(fieldName);
          if (generalInput && generalInput.value !== input.value) {
            generalInput.value = input.value;
            generalInput.dispatchEvent(new Event('input', { bubbles: true }));
          }

          // Sync via data-kib-sync attribute match
          const generalInputAttr = document.querySelector('[data-kib-sync="' + fieldName + '"]');
          if (generalInputAttr && generalInputAttr.value !== input.value) {
            generalInputAttr.value = input.value;
            generalInputAttr.dispatchEvent(new Event('input', { bubbles: true }));
          }
        }
      };

      editForm.addEventListener('input', syncHandler);
      editForm.addEventListener('change', syncHandler);
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

    // Pre-fill KIB saat halaman edit dimuat (jika KIB kosong tapi field jenis inventaris sudah ada nilai)
    document.addEventListener('DOMContentLoaded', function() {
      // Pre-fill via data-kib-sync
      document.querySelectorAll('[data-kib-sync]').forEach(function(sourceInput) {
        if (!sourceInput.value) return;
        const kibKey = sourceInput.dataset.kibSync;
        const kibInput = document.getElementById('kib_' + kibKey);
        if (kibInput && kibInput.value === '') {
          kibInput.value = sourceInput.value;
        }
      });
      // Pre-fill via ID match
      const editForm = document.getElementById('edit-aset-form');
      if (editForm) {
        editForm.querySelectorAll('[name^="fields["]').forEach(function(sourceInput) {
          if (!sourceInput.value || !sourceInput.id) return;
          const kibInput = document.getElementById('kib_' + sourceInput.id);
          if (kibInput && kibInput.value === '') {
            kibInput.value = sourceInput.value;
          }
        });
      }

      // Initialize KIR sync if the page has room select
      const ruanganSelect = document.getElementById('ruangan_id');
      if (ruanganSelect) {
        setupFieldSync();
      }
    });
    </script>
</body>
</html>
