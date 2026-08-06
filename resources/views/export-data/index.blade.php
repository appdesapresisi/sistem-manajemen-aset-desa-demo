<!DOCTYPE html>
<html lang="en">
    <head>
        @include('layouts.head-page-meta', ['title' => 'Export Data - Sistem Manajemen Aset Desa Demo'])
        @include('layouts.head-css')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <style>
            /* Gradient Animation */
            @keyframes gradientShift {
                0% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }
            
            /* Page Header Card */
            .page-header-card {
                background: linear-gradient(135deg, #064635 0%, #1c5240 50%, #0b2d21 100%);
                background-size: 200% 200%;
                animation: gradientShift 8s ease infinite;
                border-radius: 20px;
                padding: 32px 40px;
                margin-bottom: 28px;
                position: relative;
                overflow: hidden;
            }
            .page-header-card::before {
                content: '';
                position: absolute;
                top: -50%;
                right: -20%;
                width: 400px;
                height: 400px;
                background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 60%);
                pointer-events: none;
            }
            .page-header-card h1 {
                color: #fff;
                font-size: 28px;
                font-weight: 700;
                margin: 0 0 8px 0;
                position: relative;
                z-index: 1;
            }
            .page-header-card p {
                color: rgba(255,255,255,0.8);
                font-size: 15px;
                margin: 0;
                position: relative;
                z-index: 1;
            }

            /* Container Grid */
            .export-container {
                display: grid !important;
                grid-template-columns: minmax(0, 1fr) 320px !important;
                gap: 20px;
                margin-bottom: 20px;
                width: 100%;
                grid-auto-flow: row !important;
            }

            /* Export Main Section */
            .export-main {
                display: flex;
                flex-direction: column;
                gap: 20px;
                min-height: fit-content;
                min-width: 0;
                grid-column: 1 / 2;
            }

            /* Section Card */
            .section-card {
                background: #fff;
                border-radius: 16px;
                box-shadow: 0 4px 20px rgba(0,0,0,0.05);
                border: 1px solid rgba(0,0,0,0.04);
                overflow: visible;
                transition: margin-top 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .section-header {
                padding: 20px 24px;
                border-bottom: 1px solid #f3f4f6;
                display: flex;
                align-items: center;
                gap: 12px;
            }
            .section-header-icon {
                width: 40px;
                height: 40px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }
            .section-header-icon svg,
            .section-header-icon i { 
                width: 20px; 
                height: 20px; 
                color: #fff !important; 
                stroke: #fff !important;
            }
            .section-header-icon.blue { background: #519259; }
            .section-header-icon.green { background: #519259; }
            .section-header-icon.orange { background: #519259; }
            .section-header-icon.teal { background: #519259; }
            .section-header-title h5 {
                font-size: 16px;
                font-weight: 600;
                color: #064635;
                margin: 0 0 2px 0;
            }
            .section-header-title span {
                font-size: 12px;
                color: #9ca3af;
            }
            .section-body {
                padding: 20px 24px;
            }

            /* Data Selection Section */

            .jenis-selection-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }

            @media (max-width: 480px) {
                .jenis-selection-grid {
                    grid-template-columns: 1fr;
                }
            }

            .jenis-option {
                position: relative;
            }
            .jenis-option input {
                display: none;
            }
            .jenis-option label {
                display: flex;
                align-items: center;
                gap: 14px;
                padding: 14px;
                background: #f8f9fa;
                border: 2px solid #e2e8f0;
                border-radius: 12px;
                cursor: pointer;
                transition: all 0.2s;
            }
            .jenis-option label:hover {
                border-color: #064635;
                background: #f4faf5;
            }
            .jenis-option input:checked + label {
                border-color: #064635;
                background: #e6f4ea;
                box-shadow: 0 4px 12px rgba(6, 70, 53,0.15);
            }
            .jenis-option-icon {
                width: 48px;
                height: 48px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }
            .jenis-option-icon svg,
            .jenis-option-icon i { 
                width: 22px; 
                height: 22px; 
                color: #fff !important; 
                stroke: #fff !important;
            }
            .jenis-option-info {
                flex: 1;
            }
            .jenis-option-name { font-size: 13px; font-weight: 600; color: #1f2937; margin-bottom: 2px; }
            .jenis-option-count { font-size: 11px; color: #9ca3af; }

            .data-controls {
                display: flex;
                align-items: center;
                gap: 12px;
                margin-bottom: 20px;
                flex-wrap: wrap;
            }
            .search-box {
                display: flex;
                align-items: center;
                background: #ffffff;
                border-radius: 10px;
                padding: 0 14px;
                border: 1px solid #d1d5db;
                transition: all 0.2s;
                flex: 1;
                min-width: 200px;
            }
            .search-box:focus-within {
                border-color: #064635;
                box-shadow: 0 0 0 3px rgba(6, 70, 53,0.1);
            }
            .search-box input {
                border: none;
                background: transparent;
                padding: 10px 0;
                font-size: 13px;
                width: 100%;
                outline: none;
            }
            .search-box button {
                border: none;
                background: transparent;
                padding: 0;
                cursor: pointer;
                color: #9ca3af;
            }
            .search-box button i { width: 16px; height: 16px; }

            .filter-select {
                padding: 10px 12px;
                border: 1px solid #d1d5db;
                background: #ffffff;
                border-radius: 10px;
                font-size: 13px;
                color: #374151;
                outline: none;
                transition: all 0.2s;
                min-width: 100px;
            }
            .filter-select:focus {
                border-color: #064635;
                box-shadow: 0 0 0 3px rgba(6, 70, 53,0.1);
            }

            .btn-action {
                padding: 10px 16px;
                border: 1px solid #e5e7eb;
                background: #f3f4f6;
                border-radius: 10px;
                font-size: 12px;
                font-weight: 600;
                color: #374151;
                cursor: pointer;
                transition: all 0.2s;
            }
            .btn-action:hover {
                background: #e5e7eb;
                border-color: #d1d5db;
            }
            .btn-action:disabled {
                opacity: 0.55;
                cursor: not-allowed;
                background: #f3f4f6;
                border-color: #e5e7eb;
            }

            /* Table */
            .data-table {
                width: 100%;
                border-collapse: collapse;
                font-size: 13px;
            }
            .data-table thead th {
                background: #f9fafb;
                padding: 14px 16px;
                text-align: left;
                font-weight: 600;
                color: #6b7280;
                border-bottom: 1px solid #f3f4f6;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                font-size: 11px;
            }
            .data-table thead th:first-child {
                width: 50px;
                text-align: center;
            }
            .data-table tbody tr {
                transition: all 0.2s;
                border-bottom: 1px solid #f3f4f6;
            }
            .data-table tbody tr:hover {
                background: #f9fafb;
            }
            .data-table tbody td {
                padding: 14px 16px;
                vertical-align: middle;
                color: #374151;
            }
            .data-table tbody td:first-child {
                text-align: center;
            }
            .data-table input[type="checkbox"] {
                width: 18px;
                height: 18px;
                accent-color: #064635;
                cursor: pointer;
            }

            /* Badge Styles */
            .jenis-badge {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 4px 10px;
                border-radius: 16px;
                font-size: 11px;
                font-weight: 600;
            }
            .jenis-badge.kendaraan { background: #d1fae5; color: #059669; }
            .jenis-badge.peralatan { background: #fef3c7; color: #b45309; }
            .jenis-badge.bangunan { background: #dbeafe; color: #2563eb; }
            .jenis-badge.tanah { background: #fce7f3; color: #be185d; }
            .jenis-badge.infrastruktur { background: #e9d5ff; color: #7c3aed; }

            .ttd-indicator {
                display: inline-flex;
                align-items: center;
                gap: 4px;
                padding: 4px 10px;
                border-radius: 6px;
                font-size: 11px;
                font-weight: 600;
            }
            .ttd-indicator.default { background: #eff6ff; color: #0369a1; }
            .ttd-indicator.custom { background: #fef3c7; color: #b45309; }

            .jenis-flow-helper {
                display: flex;
                align-items: center;
                gap: 8px;
                margin-bottom: 12px;
                padding: 7px 10px;
                border-radius: 8px;
                background: #eff6ff;
                border: 1px solid #bfdbfe;
                color: #1d4ed8;
                font-size: 11px;
                font-weight: 500;
                line-height: 1.45;
            }
            .jenis-flow-helper i {
                width: 14px;
                height: 14px;
                flex-shrink: 0;
            }

            /* TTD Setup Section */
            .ttd-setup {
                display: flex;
                flex-direction: column;
                gap: 16px;
            }

            .ttd-subsection {
                padding: 16px;
                background: #f8f9fa;
                border-radius: 12px;
                border: 1px solid #e2e8f0;
            }
            .ttd-subsection-title {
                font-size: 13px;
                font-weight: 600;
                color: #064635;
                margin-bottom: 12px;
                display: flex;
                align-items: center;
                gap: 8px;
            }
            .ttd-subsection-title i { width: 16px; height: 16px; }
            .doc-flow-helper {
                display: flex;
                align-items: center;
                gap: 8px;
                margin-bottom: 10px;
                padding: 7px 10px;
                border-radius: 8px;
                background: #eff6ff;
                border: 1px solid #bfdbfe;
                color: #1d4ed8;
                font-size: 11px;
                font-weight: 500;
                line-height: 1.4;
            }
            .doc-flow-helper i {
                width: 14px;
                height: 14px;
                flex-shrink: 0;
            }

            /* Pejabat Display */
            .pejabat-display {
                background: #fff;
                border: 1px solid #e2e8f0;
                border-radius: 10px;
                padding: 12px;
            }
            .pejabat-display-label {
                font-size: 11px;
                font-weight: 600;
                color: #9ca3af;
                text-transform: uppercase;
                margin-bottom: 8px;
                display: block;
            }
            .pejabat-items {
                display: flex;
                flex-direction: column;
                gap: 12px;
            }
            .pejabat-card {
                background: #f8f9fa;
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
            .pejabat-empty {
                font-size: 12px;
                color: #9ca3af;
                text-align: center;
                padding: 12px;
                font-style: italic;
            }

            /* Date Input */
            .date-input-group {
                display: flex;
                flex-direction: column;
                gap: 6px;
            }
            .date-input-label {
                font-size: 11px;
                font-weight: 600;
                color: #6b7280;
                text-transform: uppercase;
            }
            .date-input-field {
                padding: 10px 12px;
                border: 1px solid #e5e7eb;
                border-radius: 8px;
                font-size: 13px;
                outline: none;
                transition: all 0.2s;
                text-align: left;
                cursor: text;
            }
            #doc-tanggal { cursor: pointer; }
            .date-input-field:focus {
                border-color: #064635;
                box-shadow: 0 0 0 3px rgba(6, 70, 53,0.1);
            }

            .date-preview-box {
                background: #eff6ff;
                border: 1px solid #93c5fd;
                border-radius: 8px;
                padding: 10px;
                margin-top: 8px;
            }
            .date-preview-label {
                font-size: 10px;
                font-weight: 600;
                color: #0369a1;
                margin-bottom: 4px;
            }
            .date-preview-text {
                font-size: 13px;
                font-weight: 600;
                color: #0c4a6e;
            }

            /* Info Box (for alerts/helper text) */
            .info-box {
                background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
                border: 1px solid #93c5fd;
                border-radius: 8px;
                padding: 12px 16px;
                display: flex;
                align-items: flex-start;
                gap: 10px;
                margin-top: 16px;
            }
            .info-box i {
                color: #1e40af;
                stroke: #1e40af;
                flex-shrink: 0;
                width: 18px;
                height: 18px;
                margin-top: 2px;
            }
            .info-box-text {
                font-size: 13px;
                color: #1e40af;
                line-height: 1.5;
            }

            /* Preview Box (khusus untuk preview dokumen info) */
            .preview-doc-box {
                background: #eff6ff;
                border: 1px solid #bfdbfe;
                border-radius: 8px;
                padding: 12px 14px;
                margin-top: 12px;
            }
            .preview-doc-box-title {
                font-size: 11px;
                font-weight: 600;
                color: #2563eb;
                margin-bottom: 8px;
                display: flex;
                align-items: center;
                gap: 6px;
            }
            .preview-doc-box-title i {
                width: 14px;
                height: 14px;
                color: #2563eb;
            }
            .preview-doc-box-content {
                font-size: 12px;
                color: #1e40af;
                line-height: 1.7;
            }
            .preview-doc-box-content div {
                margin-bottom: 3px;
            }
            .preview-doc-box-content div:last-child {
                margin-bottom: 0;
            }
            .preview-doc-box-content strong {
                color: #1e3a8a;
                font-weight: 600;
            }

            /* Flatpickr clean compact look (mirip referensi) */
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
                user-select: none;
                -webkit-user-select: none;
                -moz-user-select: none;
            }
            .flatpickr-calendar .flatpickr-current-month .cur-month::selection {
                background: transparent;
            }
            .flatpickr-calendar .flatpickr-weekdays {
                margin-top: 4px;
            }
            .flatpickr-calendar span.flatpickr-weekday {
                color: #6b7280;
                font-size: 12px;
                font-weight: 600;
            }
            .flatpickr-calendar .flatpickr-day {
                border-radius: 50%;
                color: #374151;
                font-size: 13px;
            }
            .flatpickr-calendar .flatpickr-day.selected,
            .flatpickr-calendar .flatpickr-day.startRange,
            .flatpickr-calendar .flatpickr-day.endRange {
                background: #1f2937;
                border-color: #1f2937;
                color: #ffffff;
            }
            .flatpickr-calendar .flatpickr-day:hover {
                background: #f3f4f6;
                border-color: #f3f4f6;
            }
            .flatpickr-calendar .flatpickr-prev-month,
            .flatpickr-calendar .flatpickr-next-month {
                color: #6b7280;
                fill: #6b7280;
            }
            .flatpickr-calendar .flatpickr-prev-month:hover,
            .flatpickr-calendar .flatpickr-next-month:hover {
                color: #111827;
                fill: #111827;
            }

            /* ===== Date Clean Picker Enhancement ===== */
            /* Header sejajar */
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
                fill: #374151 !important;
            }
            .flatpickr-calendar.date-clean-picker .flatpickr-prev-month:hover,
            .flatpickr-calendar.date-clean-picker .flatpickr-next-month:hover { background: #f3f4f6 !important; }
            .flatpickr-calendar.date-clean-picker .flatpickr-prev-month svg,
            .flatpickr-calendar.date-clean-picker .flatpickr-next-month svg { fill: #374151 !important; width: 12px !important; height: 12px !important; }
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
            .flatpickr-calendar.date-clean-picker .cur-month {
                cursor: pointer; border-radius: 6px; padding: 2px 6px;
            }
            .flatpickr-calendar.date-clean-picker .cur-month:hover { background: #f3f4f6; }
            /* Hari nama warna sistem */
            .flatpickr-calendar.date-clean-picker .flatpickr-weekday { color: #064635 !important; font-weight: 600 !important; }
            /* Sembunyikan tanggal bulan lain */
            .flatpickr-calendar.date-clean-picker .flatpickr-day.prevMonthDay,
            .flatpickr-calendar.date-clean-picker .flatpickr-day.nextMonthDay { visibility: hidden !important; pointer-events: none !important; }
            /* Panel bulan & tahun */
            .fp-exp-month-panel, .fp-exp-year-panel {
                display: none; position: absolute; top: 0; left: 0; right: 0; bottom: 0;
                background: #fff; border-radius: inherit; padding: 10px; z-index: 10;
                flex-direction: column; gap: 8px; box-sizing: border-box;
            }
            .fp-exp-month-panel.show, .fp-exp-year-panel.show { display: flex; }
            .fp-exp-panel-header { display: flex; align-items: center; justify-content: space-between; flex-shrink: 0; }
            .fp-exp-panel-title { font-size: 12px; font-weight: 700; color: #1f2937; }
            .fp-exp-panel-close, .fp-exp-panel-nav {
                width: 26px; height: 26px; border: 1px solid #e5e7eb; background: #fff; font-size: 13px; cursor: pointer;
                display: flex; align-items: center; justify-content: center; color: #374151; border-radius: 6px; transition: background 0.15s;
            }
            .fp-exp-panel-close:hover, .fp-exp-panel-nav:hover:not(:disabled) { background: #f3f4f6; }
            .fp-exp-panel-nav:disabled { opacity: 0.45; cursor: not-allowed; }
            .fp-exp-month-grid, .fp-exp-year-grid { display: grid; grid-template-columns: repeat(3, 1fr); grid-template-rows: repeat(4, 1fr); gap: 8px; flex: 1; }
            .fp-exp-month-btn, .fp-exp-year-btn {
                border-radius: 8px; border: 1px solid #d1d5db; background: #fff;
                font-size: 12px; font-weight: 600; color: #374151; cursor: pointer; text-align: center; transition: all 0.15s;
                display: flex; align-items: center; justify-content: center; padding: 0;
            }
            .fp-exp-month-btn:hover, .fp-exp-year-btn:hover { background: #f3f4f6; }
            .fp-exp-month-btn.active, .fp-exp-year-btn.active { background: #1f2937; border-color: #1f2937; color: #fff; }

            /* Sidebar */
            .export-sidebar {
                display: flex;
                flex-direction: column;
                gap: 20px;
            }

            /* Export Options Card */
            .export-options-card {
                background: #fff;
                border-radius: 16px;
                box-shadow: 0 4px 20px rgba(0,0,0,0.05);
                border: 1px solid rgba(0,0,0,0.04);
                overflow: hidden;
            }
            .export-options-header {
                padding: 16px;
                background: linear-gradient(135deg, #064635 0%, #1c5240 100%);
                color: #fff;
            }
            .export-options-header h5 {
                font-size: 14px;
                font-weight: 600;
                margin: 0;
            }
            .export-options-body {
                padding: 16px;
                display: flex;
                flex-direction: column;
                gap: 12px;
            }

            /* Export Format Options */
            .format-options {
                display: flex;
                flex-direction: column;
                gap: 10px;
            }
            .format-option {
                position: relative;
            }
            .format-option input {
                display: none;
            }
            .format-option label {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 12px;
                background: #f8f9fa;
                border: 2px solid #e2e8f0;
                border-radius: 10px;
                cursor: pointer;
                transition: all 0.2s;
            }
            .format-option label:hover {
                border-color: #064635;
                background: #f4faf5;
            }
            .format-option input:checked + label {
                border-color: #064635;
                background: #e6f4ea;
            }
            .format-icon {
                width: 36px;
                height: 36px;
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }
            .format-icon svg,
            .format-icon i { 
                width: 18px; 
                height: 18px; 
                color: #fff !important; 
                stroke: #fff !important;
            }

            .format-icon.excel { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
            .format-icon.pdf { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
            .format-info {
                flex: 1;
                display: flex;
                flex-direction: column;
                gap: 2px;
            }
            .format-label { font-size: 12px; font-weight: 600; color: #064635; }
            .format-desc { font-size: 10px; color: #9ca3af; }

            /* Signature Options */
            .signature-options {
                padding: 12px;
                background: #f8f9fa;
                border: 1px solid #e2e8f0;
                border-radius: 10px;
            }
            .signature-label {
                font-size: 12px;
                font-weight: 600;
                color: #064635;
                margin-bottom: 10px;
                display: flex;
                align-items: center;
                gap: 8px;
            }
            .signature-label i { width: 12px; height: 12px; }
            .signature-options-group {
                display: flex;
                flex-direction: column;
                gap: 8px;
            }
            .signature-option {
                display: flex;
                align-items: center;
                gap: 8px;
            }
            .signature-option input[type="radio"] {
                width: 16px;
                height: 16px;
                accent-color: #064635;
                cursor: pointer;
            }
            .signature-option label {
                font-size: 12px;
                color: #374151;
                cursor: pointer;
                flex: 1;
            }
            .signature-option span {
                font-size: 10px;
                color: #9ca3af;
            }

            /* Locked state sebelum jenis inventaris dipilih */
            .export-options-card.is-locked .format-option label,
            .export-options-card.is-locked .signature-option label,
            .export-options-card.is-locked .signature-option input[type="radio"] {
                cursor: not-allowed;
                opacity: 0.65;
            }
            .export-options-card.is-locked .format-option label:hover {
                border-color: #e2e8f0;
                background: #f8f9fa;
            }
            #section-ttd-setup.is-locked .ttd-subsection {
                opacity: 0.72;
            }
            .ttd-subsection.is-doc-disabled {
                opacity: 0.72;
            }

            /* Export Button */
            .export-action-btn {
                width: 100%;
                padding: 12px;
                background: linear-gradient(135deg, #519259 0%, #064635 100%);
                color: #fff;
                border: none;
                border-radius: 10px;
                font-weight: 600;
                font-size: 13px;
                cursor: pointer;
                transition: all 0.3s;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                box-shadow: 0 4px 12px rgba(6, 70, 53,0.3);
            }
            .export-action-btn:hover:not(:disabled) {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(6, 70, 53,0.4);
            }
            .export-action-btn:disabled {
                opacity: 0.5;
                cursor: not-allowed;
            }
            .export-action-btn i {
                 width: 16px; height: 16px; 
                }

             /* Export Info Grid summary card*/
            .simple-info-list {
                display: flex;
                flex-direction: column;
                gap: 12px;
            }

            .simple-item {
                display: flex;
                align-items: center;
                gap: 14px;
                padding: 14px 16px;
                border: 1px solid #e5e7eb;
                border-radius: 14px;
                background: #fff;
                transition: all 0.2s ease;
            }

            .simple-item:hover {
                background: #f9fafb;
            }

            /* Icon box */
            .simple-icon {
                width: 30px;
                height: 30px;
                border-radius: 12px;
                background: #e6f4ea;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .simple-icon svg {
                width: 20px;
                height: 20px;
                stroke-width: 2;
                color: #334155; 
            }

            /* Text */
            .simple-text {
                display: flex;
                flex-direction: column;
                gap: 2px;
            }

            .simple-title {
                font-size: 13px;
                font-weight: 700;
                color: #111827;
            }

            .simple-sub {
                font-size: 12px;
                color: #6b7280;
            }

            /* Colors */
            /* .simple-icon.blue { background: #3b82f6; }
            .simple-icon.orange { background: #f59e0b; }
            .simple-icon.green { background: #22c55e; }
            .simple-icon.red { background: #ef4444; } */
            /* Export Tab Switcher */
            .export-tab-switcher {
                display: flex;
                gap: 0;
                background: #fff;
                border-radius: 16px;
                box-shadow: 0 4px 20px rgba(0,0,0,0.05);
                border: 1px solid rgba(0,0,0,0.04);
                overflow: hidden;
                margin-bottom: 20px;
            }
            .export-tab-btn {
                flex: 1;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 10px;
                padding: 16px 20px;
                background: #fff;
                border: none;
                cursor: pointer;
                font-size: 14px;
                font-weight: 600;
                color: #6b7280;
                transition: all 0.2s;
                border-bottom: 3px solid transparent;
                position: relative;
            }
            .export-tab-btn:not(:last-child) {
                border-right: 1px solid #f3f4f6;
            }
            .export-tab-btn:hover {
                background: #f9fafb;
                color: #064635;
            }
            .export-tab-btn.active {
                color: #064635;
                background: linear-gradient(135deg, #f0f4f8 0%, #e8ecf0 100%);
                border-bottom-color: #064635;
            }
            .export-tab-btn .tab-icon {
                width: 36px;
                height: 36px;
                border-radius: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
                transition: all 0.2s;
            }
            .export-tab-btn .tab-icon svg,
            .export-tab-btn .tab-icon i {
                width: 18px;
                height: 18px;
                color: #fff !important;
                stroke: #fff !important;
            }
            .export-tab-btn.active .tab-icon,
            .export-tab-btn:hover .tab-icon {
                box-shadow: 0 4px 12px rgba(6, 70, 53,0.25);
            }
            .export-tab-label { text-align: left; }
            .export-tab-label .tab-title { font-size: 14px; font-weight: 600; }
            .export-tab-label .tab-sub { font-size: 11px; color: #9ca3af; font-weight: 400; }
            .export-tab-btn.active .export-tab-label .tab-sub { color: #6b7280; }

            /* Ruangan selection grid - sama persis dengan jenis-selection-grid */
            .ruangan-selection-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }
            @media (max-width: 480px) {
                .ruangan-selection-grid { grid-template-columns: 1fr; }
            }
            .ruangan-option { position: relative; }
            .ruangan-option input { display: none; }
            .ruangan-option label {
                display: flex;
                align-items: center;
                gap: 14px;
                padding: 14px;
                background: #f8f9fa;
                border: 2px solid #e2e8f0;
                border-radius: 12px;
                cursor: pointer;
                transition: all 0.2s;
            }
            .ruangan-option label:hover { border-color: #064635; background: #f4faf5; }
            .ruangan-option input:checked + label {
                border-color: #064635;
                background: #e6f4ea;
                box-shadow: 0 4px 12px rgba(6, 70, 53,0.15);
            }
            .ruangan-option-icon {
                width: 48px; height: 48px; border-radius: 12px;
                display: flex; align-items: center; justify-content: center; flex-shrink: 0;
            }
            .ruangan-option-icon svg, .ruangan-option-icon i {
                width: 22px; height: 22px; color: #fff !important; stroke: #fff !important;
            }
            .ruangan-option-info { flex: 1; }
            .ruangan-option-name { font-size: 13px; font-weight: 600; color: #1f2937; margin-bottom: 2px; }
            .ruangan-option-count { font-size: 11px; color: #9ca3af; }
                background: #fff;
                border-radius: 16px;
                box-shadow: 0 4px 20px rgba(0,0,0,0.05);
                border: 1px solid rgba(0,0,0,0.04);
                overflow: hidden;
                margin-top: 20px;
            }

            .history-table {
                width: 100%;
                border-collapse: collapse;
                font-size: 12px;
            }
            .history-table thead th {
                background: #f9fafb;
                padding: 12px 16px;
                text-align: left;
                font-weight: 600;
                color: #6b7280;
                border-bottom: 1px solid #f3f4f6;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                font-size: 10px;
            }
            .history-table tbody tr {
                border-bottom: 1px solid #f3f4f6;
            }
            .history-table tbody tr:hover {
                background: #f9fafb;
            }
            .history-table tbody td {
                padding: 12px 16px;
                color: #374151;
            }

            .user-info {
                display: flex;
                align-items: flex-start;
                gap: 8px;
                text-align: left;
            }
            /* .user-avatar removed */

            .export-meta {
                display: flex;
                gap: 6px;
                flex-wrap: wrap;
            }
            .meta-badge {
                display: inline-flex;
                align-items: center;
                gap: 4px;
                padding: 3px 8px;
                background: #f0f9ff;
                border: 1px solid #bfdbfe;
                border-radius: 4px;
                font-size: 10px;
                color: #0369a1;
                font-weight: 600;
            }
            .meta-badge i { width: 12px; height: 12px; }

            /* Modal */
            .modal-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0.5);
                align-items: center;
                justify-content: center;
                z-index: 2000;
                backdrop-filter: blur(4px);
            }
            .modal-overlay.show { display: flex; }
            .modal-box {
                background: #fff;
                border-radius: 16px;
                width: 90%;
                max-width: 900px;
                max-height: 90vh;
                overflow: auto;
                box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
                animation: modalSlideIn 0.3s ease;
            }
            @keyframes modalSlideIn {
                from { opacity: 0; transform: translateY(-20px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .modal-header {
                padding: 20px 24px;
                background: linear-gradient(135deg, #064635 0%, #1c5240 100%);
                color: #fff;
                border-bottom: 1px solid #e5e7eb;
            }
            .modal-header h5 {
                color: #fff;
                font-size: 16px;
                font-weight: 600;
                margin: 0;
            }
            .modal-body {
                padding: 24px;
                max-height: 60vh;
                overflow-y: auto;
            }
            .modal-footer {
                padding: 16px 24px;
                background: #f9fafb;
                border-top: 1px solid #e5e7eb;
                display: flex;
                justify-content: flex-end;
                gap: 12px;
            }
            .modal-btn {
                padding: 10px 20px;
                border-radius: 10px;
                font-size: 12px;
                font-weight: 600;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 6px;
                cursor: pointer;
                transition: all 0.2s;
                border: none;
            }
            .modal-btn-cancel {
                background: #f3f4f6;
                color: #374151;
                border: 1px solid #e5e7eb;
            }
            .modal-btn-cancel:hover {
                background: #e5e7eb;
            }
            .modal-btn-confirm {
                background: linear-gradient(135deg, #519259 0%, #064635 100%);
                color: #fff;
            }
            .modal-btn-confirm:hover {
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(6, 70, 53,0.3);
            }

            /* Preview Content */
            .preview-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 16px;
                margin-bottom: 20px;
            }
            .preview-info-box {
                padding: 16px;
                background: #f8f9fa;
                border: 1px solid #e2e8f0;
                border-radius: 10px;
            }
            .preview-info-label {
                font-size: 11px;
                font-weight: 600;
                color: #6b7280;
                text-transform: uppercase;
                margin-bottom: 8px;
            }
            .preview-info-value {
                font-size: 13px;
                font-weight: 600;
                color: #1f2937;
            }

            .preview-signer-panel {
                margin-top: 12px;
                padding: 12px;
                border: 1px solid #e2e8f0;
                border-radius: 10px;
                background: #f8f9fa;
            }
            .preview-signer-title {
                font-size: 11px;
                font-weight: 700;
                color: #475569;
                text-transform: uppercase;
                letter-spacing: 0.4px;
                margin-bottom: 8px;
            }
            .preview-signer-note {
                font-size: 11px;
                color: #64748b;
                margin-bottom: 8px;
            }
            .preview-signer-item {
                font-size: 12px;
                color: #1f2937;
                padding: 4px 0;
            }

            /* Excel Preview */
            .excel-preview-wrapper {
                border: 1px solid #d0d5dd;
                border-radius: 8px;
                overflow: hidden;
                font-family: 'Calibri', 'Segoe UI', sans-serif;
                font-size: 11px;
                background: #fff;
                margin-top: 4px;
            }
            .excel-toolbar {
                background: #217346;
                padding: 4px 10px;
                display: flex;
                align-items: center;
                gap: 8px;
                font-size: 10px;
                color: rgba(255,255,255,0.85);
            }
            .excel-toolbar span { font-weight: 600; color: #fff; font-size: 11px; }
            .excel-col-bar {
                display: flex;
                background: #f2f2f2;
                border-bottom: 1px solid #d0d5dd;
                border-left: 32px solid #e8e8e8;
            }
            .excel-col-cell {
                min-width: 80px;
                max-width: 80px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                padding: 2px 4px;
                text-align: center;
                font-size: 10px;
                color: #595959;
                border-right: 1px solid #d0d5dd;
                border-left: none;
                font-weight: 600;
            }
            .excel-table {
                width: 100%;
                border-collapse: collapse;
                table-layout: fixed;
            }
            .excel-row-num {
                width: 32px;
                min-width: 32px;
                background: #f2f2f2;
                border-right: 1px solid #d0d5dd;
                border-bottom: 1px solid #e8e8e8;
                text-align: center;
                font-size: 10px;
                color: #595959;
                padding: 2px 0;
            }
            .excel-cell {
                min-width: 80px;
                max-width: 80px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                padding: 2px 5px;
                border-right: 1px solid #e2e8f0;
                border-bottom: 1px solid #e8e8e8;
                font-size: 10px;
                color: #1f2937;
            }
            .excel-cell-title {
                background: #fff;
                font-weight: 700;
                font-size: 11px;
                color: #1f2937;
                text-align: center;
            }
            .excel-cell-subtitle {
                background: #fff;
                font-weight: 600;
                font-size: 10px;
                color: #374151;
                text-align: center;
            }
            .excel-cell-header {
                background: #ffffff;
                font-weight: 700;
                font-size: 10px;
                color: #1f2937;
                text-align: center;
            }
            .excel-cell-data {
                background: #fff;
                color: #6b7280;
                font-style: italic;
                font-size: 10px;
            }
            .excel-cell-data.alt { background: #ffffff; }

            /* History Card */
            .history-card {
                background: #fff;
                border-radius: 16px;
                box-shadow: 0 4px 20px rgba(0,0,0,0.05);
                border: 1px solid rgba(0,0,0,0.04);
                overflow: hidden;
                margin-top: 20px;
            }
            .history-empty-state {
                padding: 40px 24px;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                text-align: center;
                color: #9ca3af;
            }
            .history-empty-state i {
                width: 48px;
                height: 48px;
                color: #d1d5db;
                margin-bottom: 12px;
                opacity: 0.5;
            }
            .history-empty-state p {
                font-size: 14px;
                margin: 0 0 8px 0;
                font-weight: 500;
            }
            .history-empty-state small {
                font-size: 12px;
                color: #d1d5db;
            }
            .history-table {
                width: 100%;
                border-collapse: collapse;
                font-size: 13px;
            }
            .history-table thead th {
                background: #f9fafb;
                padding: 14px 16px;
                text-align: left;
                font-weight: 600;
                color: #6b7280;
                border-bottom: 1px solid #f3f4f6;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                font-size: 11px;
            }
            .history-table tbody tr {
                border-bottom: 1px solid #f3f4f6;
                transition: all 0.2s;
            }
            .history-table tbody tr:hover {
                background: #f9fafb;
            }
            .history-table tbody td {
                padding: 14px 16px;
                vertical-align: middle;
                color: #374151;
            }
            .user-info {
                display: flex;
                align-items: center;
                gap: 10px;
            }
            /* .user-avatar removed */
            .meta-badge {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 4px 10px;
                border-radius: 16px;
                font-size: 11px;
                font-weight: 600;
                background: #dbeafe;
                color: #2563eb;
            }
            .meta-badge i { width: 12px; height: 12px; }

            /* RESPONSIVE: Sidebar pindah ke bawah saat layar tidak cukup lebar */
            @media (max-width: 1024px) {
                .export-container {
                    grid-template-columns: 1fr !important;
                }
                .export-main {
                    grid-column: 1 / -1;
                }
                .export-sidebar {
                    width: 100% !important;
                    grid-column: 1 / -1;
                }
                /* Di mobile: Ringkasan Export di atas, Opsi Export di bawah */
                .export-sidebar .export-options-card {
                    order: 2;
                }
                .export-sidebar .section-card {
                    order: 1;
                }
            }

            @media (max-width: 768px) {
                .section-header { flex-wrap: wrap; }
                .data-controls { flex-direction: column; align-items: stretch; }
                .search-box { flex: unset; }
                .filter-select { width: 100%; }
                .btn-action { width: 100%; }
                .preview-grid { grid-template-columns: 1fr; }
                .page-header-card {
                    padding: 24px 20px;
                }
                .page-header-card h1 {
                    font-size: 22px;
                }
                .page-header-card p {
                    font-size: 13px;
                }
            }

            @media (max-width: 480px) {
                .jenis-selection-grid {
                    grid-template-columns: 1fr !important;
                }
                .section-body {
                    padding: 16px;
                }
                .section-header {
                    padding: 16px;
                }
                .export-options-body {
                    padding: 12px;
                }
            }
        </style>
        <style>
            /* Page-specific: hide topbar search icon/dropdown for this page only */
            .me-auto.pc-mob-drp i[data-feather="search"],
            .drp-search {
                display: none !important;
            }
        </style>
        <style>
            .me-auto.pc-mob-drp .pc-head-link.dropdown-toggle svg.feather,
            .me-auto.pc-mob-drp .pc-head-link.dropdown-toggle i[data-feather="search"] {
                display: none !important;
            }
        </style>
        <style>
            /* Searchable Dropdown Container */
            .searchable-dropdown {
                position: relative;
                width: 100%;
            }

            /* Buku Inventaris Section - kotak pembungkus mirip create manajemen aset */
            .buku-inventaris-section {
                background: #f8f9fa;
                border: 1.5px solid #d1d5db;
                border-radius: 10px;
                padding: 16px;
                margin-top: 16px;
                overflow: visible;
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
                background: #064635;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
                margin-top: 2px;
            }
            .buku-inventaris-header-icon i,
            .buku-inventaris-header-icon svg {
                width: 14px;
                height: 14px;
                stroke: #fff;
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
                min-height: 46px !important;
            }
            .dropdown-trigger:hover {
                border-color: #9ca3af !important;
            }
            .dropdown-trigger:focus {
                border-color: #064635 !important;
                box-shadow: 0 0 0 3px rgba(6, 70, 53, 0.1) !important;
                outline: none !important;
            }

            /* Panel Menu Dropdown - push down animation */
            .dropdown-menu-panel {
                position: relative !important;
                z-index: 10 !important;
                background: #fff !important;
                border: 1px solid #d1d5db !important;
                border-radius: 8px !important;
                box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1) !important;
                width: 100% !important;
                margin-top: 8px !important;
                padding: 0 !important;
                box-sizing: border-box !important;
                max-height: 0 !important;
                overflow: hidden !important;
                opacity: 0 !important;
                transform: scaleY(0) !important;
                transform-origin: top !important;
                transition: max-height 0.3s cubic-bezier(0.4, 0, 0.2, 1), 
                            opacity 0.25s ease, 
                            transform 0.3s cubic-bezier(0.4, 0, 0.2, 1),
                            padding 0.3s ease !important;
            }
            .dropdown-menu-panel.show {
                max-height: 400px !important;
                opacity: 1 !important;
                transform: scaleY(1) !important;
                padding: 8px !important;
                overflow-y: auto !important;
            }

            /* Smooth transition untuk section yang turun */
            .section-card {
                transition: margin-top 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            }

            /* Responsive untuk mobile dan tablet */
            @media (max-width: 1024px) {
                .dropdown-menu-panel.show {
                    max-height: 350px !important;
                }
            }

            @media (max-width: 768px) {
                .dropdown-menu-panel.show {
                    max-height: 300px !important;
                }
                .dropdown-options-list {
                    max-height: 240px !important;
                }
            }

            @media (max-width: 480px) {
                .dropdown-menu-panel.show {
                    max-height: 280px !important;
                }
                .dropdown-options-list {
                    max-height: 220px !important;
                }
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
            .dropdown-search-input:focus {
                border-color: #064635 !important;
                box-shadow: 0 0 0 2px rgba(6, 70, 53, 0.1) !important;
            }

            /* Opsi Item List */
            .dropdown-options-list {
                max-height: 280px;
                overflow-y: auto;
                padding: 0;
                margin: 0;
                list-style: none;
            }
            .dropdown-option-item {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 10px 12px;
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
                width: 32px;
                height: 32px;
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }
            .dropdown-option-icon i,
            .dropdown-option-icon svg,
            .dropdown-option-icon svg * {
                color: #fff !important;
                stroke: #fff !important;
            }
            .dropdown-option-icon svg {
                width: 16px;
                height: 16px;
            }
            .dropdown-option-text {
                flex: 1;
            }
            .dropdown-option-name {
                font-size: 13px;
                font-weight: 600;
                color: #374151;
                line-height: 1.4;
            }
            .dropdown-option-desc {
                font-size: 11px;
                color: #9ca3af;
                margin-top: 2px;
                line-height: 1.3;
            }

            /* Animasi Chevron */
            .dropdown-trigger-caret {
                transition: transform 0.2s;
                color: #9ca3af;
                width: 18px !important;
                height: 18px !important;
            }
            .searchable-dropdown.open .dropdown-trigger-caret {
                transform: rotate(180deg);
            }

            /* Year Picker untuk filter-tahun di export-data */
            .flatpickr-calendar.year-only-picker .flatpickr-months,
            .flatpickr-calendar.year-only-picker .flatpickr-weekdays,
            .flatpickr-calendar.year-only-picker .flatpickr-days,
            .flatpickr-calendar.year-only-picker .flatpickr-innerContainer {
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
                border: 1px solid #e5e7eb;
                border-radius: 6px;
                background: #fff;
                font-size: 14px;
                display: flex;
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
                padding: 7px 4px;
                font-size: 12px;
                font-weight: 600;
                text-align: center;
                background: #fff;
                cursor: pointer;
                transition: all 0.15s;
            }
            .flatpickr-calendar.year-only-picker .year-picker-option:hover {
                background: #f3f4f6;
            }
            .flatpickr-calendar.year-only-picker .year-picker-option.active {
                background: #1f2937;
                border-color: #1f2937;
                color: #fff;
            }
        </style>
    </head>

    <body>
        @include('layouts.notification')
        @include('layouts.layout-vertical')

        <div class="pc-container">
            <div class="pc-content">
                @include('layouts.breadcrumb', [
                    'breadcrumbItemHome' => 'Inventaris',
                    'breadcrumbItem' => 'Export Data',
                    'breadcrumbItemActive' => 'Export Jenis'
                ])
 
                <!-- Page Header -->
                <div class="page-header-card">
                    {{-- <h1><i data-feather="download" style="width: 32px; height: 32px; display: inline-block; vertical-align: middle; margin-right: 12px;"></i>Export Data Jenis Inventaris</h1> --}}
                    <h1>Export Data Jenis Inventaris</h1>
                    <p>Ekspor data keseluruhan untuk setiap jenis inventaris dengan kustomisasi pejabat dan tanggal dokumen sesuai kebutuhan.</p>
                </div>

                <!-- Main Content -->
                <div class="export-container">
                    <!-- Left Column: Jenis Selection & TTD Setup -->
                    <div class="export-main">
                        <!-- Section 0: Pilih Tipe Export - selalu tampil -->
                        <div class="section-card" id="section-pilih-tipe">
                            <div class="section-header">
                                <div class="section-header-icon blue">
                                    <i data-feather="download"></i>
                                </div>
                                <div class="section-header-title">
                                    <h5>Pilih Export Data</h5>
                                    <span>Pilih jenis data yang akan diekspor</span>
                                </div>
                            </div>
                            <div class="section-body">
                                <div class="buku-inventaris-section" style="margin-top:0;">
                                    <div class="buku-inventaris-header" style="margin-bottom:16px;">
                                        <div class="buku-inventaris-header-content">
                                            <span class="buku-inventaris-title">Jenis Export</span>
                                            <p class="buku-inventaris-desc">Pilih jenis export data yang akan digunakan.</p>
                                        </div>
                                    </div>
                                    <input type="hidden" name="export_type" id="export_type_hidden" value="">
                                    
                                    <div class="searchable-dropdown" id="export-type-dropdown-container">
                                        <button type="button" class="dropdown-trigger" id="export-type-dropdown-trigger">
                                            <span id="export-type-selected-label" style="color:#9ca3af;font-size:13px;font-weight:500;">-- Pilih Jenis Export Data --</span>
                                            <i data-feather="chevron-down" class="dropdown-trigger-caret" style="width:16px;height:16px;"></i>
                                        </button>
                                        
                                        <div class="dropdown-menu-panel" id="export-type-dropdown-menu">
                                            <ul class="dropdown-options-list" id="export-type-dropdown-options-list"></ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 1: Pilih Jenis Inventaris - hidden sampai tipe dipilih -->
                        <div class="section-card" id="section-pilih-jenis" style="display:none;">
                            <div class="section-header">
                                <div class="section-header-icon" id="section-pilih-icon" style="background:#519259;">
                                    <i data-feather="list" id="section-pilih-icon-el"></i>
                                </div>
                                <div class="section-header-title">
                                    <h5>Pilih Jenis Inventaris</h5>
                                    <span>Pilih jenis inventaris yang akan diekspor</span>
                                </div>
                            </div>
                            <div class="section-body">
                                <!-- Konten Jenis Inventaris - hidden sampai dipilih -->
                                <div id="content-jenis" style="display:none;">
                                @if($jenisInventaris->isEmpty())
                                <div style="width:100%;padding:40px 16px;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;color:#9ca3af;gap:8px;">
                                    <i data-feather="inbox" style="width:44px;height:44px;opacity:0.5;"></i>
                                    <div style="font-size:14px;font-weight:600;color:#6b7280;margin:0;">Tidak Ada Data</div>
                                    <div style="font-size:12px;color:#9ca3af;">Belum ada jenis inventaris yang tersedia.<br>Tambahkan terlebih dahulu di menu <a href="{{ route('jenis-inventaris.index') }}" style="color:#064635;font-weight:600;">Jenis Inventaris</a>.</div>
                                </div>
                                @else
                                <div class="jenis-flow-helper" id="jenis-flow-helper">
                                    <i data-feather="info"></i>
                                    Pilih jenis inventaris terlebih dahulu untuk mengaktifkan opsi export, tanda tangan, pejabat custom, dan tanggal dokumen.
                                </div>
                                <div class="buku-inventaris-section" style="margin-top:0;">
                                    <div class="buku-inventaris-header" style="margin-bottom:16px;">
                                        <div class="buku-inventaris-header-content">
                                            <span class="buku-inventaris-title">Jenis Inventaris</span>
                                            <p class="buku-inventaris-desc">Pilih jenis inventaris untuk menampilkan form isian data yang sesuai.</p>
                                        </div>
                                    </div>
                                    <input type="hidden" name="jenis_inventaris_id" id="jenis_inventaris_id_hidden" value="">
                                    
                                    <div class="searchable-dropdown" id="jenis-dropdown-container">
                                        <button type="button" class="dropdown-trigger" id="jenis-dropdown-trigger">
                                            <span id="jenis-selected-label" style="color:#9ca3af;font-size:13px;font-weight:500;">-- Pilih Jenis Inventaris --</span>
                                            <i data-feather="chevron-down" class="dropdown-trigger-caret" style="width:16px;height:16px;"></i>
                                        </button>
                                        
                                        <div class="dropdown-menu-panel" id="jenis-dropdown-menu">
                                            <div class="dropdown-search-wrapper">
                                                <i data-feather="search"></i>
                                                <input type="text" id="jenis-dropdown-search" class="dropdown-search-input" placeholder="Cari jenis inventaris..." autocomplete="off">
                                            </div>
                                            <ul class="dropdown-options-list" id="jenis-dropdown-options-list"></ul>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                </div>{{-- end content-jenis --}}

                                <!-- Konten Ruangan - hidden sampai dipilih -->
                                <div id="content-ruangan" style="display:none;">
                                @if($ruanganList->isEmpty())
                                <div style="width:100%;padding:40px 16px;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;color:#9ca3af;gap:8px;">
                                    <i data-feather="inbox" style="width:44px;height:44px;opacity:0.5;"></i>
                                    <div style="font-size:14px;font-weight:600;color:#6b7280;margin:0;">Belum Ada Ruangan</div>
                                    <div style="font-size:12px;color:#9ca3af;">Tambahkan data ruangan di menu <a href="{{ route('ruangan.index') }}" style="color:#064635;font-weight:600;">Master Data &raquo; Ruangan</a>.</div>
                                </div>
                                @else
                                <div class="jenis-flow-helper">
                                    <i data-feather="info"></i>
                                    Pilih ruangan untuk mengaktifkan opsi export Kartu Inventaris Ruangan.
                                </div>
                                <div class="buku-inventaris-section" style="margin-top:0;">
                                    <div class="buku-inventaris-header" style="margin-bottom:16px;">
                                        <div class="buku-inventaris-header-content">
                                            <span class="buku-inventaris-title">Ruangan</span>
                                            <p class="buku-inventaris-desc">Pilih ruangan yang akan diekspor sebagai Kartu Inventaris Ruangan.</p>
                                        </div>
                                    </div>
                                    <input type="hidden" name="ruangan_id_selected" id="ruangan_id_hidden" value="">

                                    <div class="searchable-dropdown" id="ruangan-dropdown-container">
                                        <button type="button" class="dropdown-trigger" id="ruangan-dropdown-trigger">
                                            <span id="ruangan-selected-label" style="color:#9ca3af;font-size:13px;font-weight:500;">-- Pilih Ruangan --</span>
                                            <i data-feather="chevron-down" class="dropdown-trigger-caret" style="width:16px;height:16px;"></i>
                                        </button>

                                        <div class="dropdown-menu-panel" id="ruangan-dropdown-menu">
                                            <div class="dropdown-search-wrapper">
                                                <i data-feather="search"></i>
                                                <input type="text" id="ruangan-dropdown-search" class="dropdown-search-input" placeholder="Cari ruangan..." autocomplete="off">
                                            </div>
                                            <ul class="dropdown-options-list" id="ruangan-dropdown-options-list"></ul>
                                        </div>
                                    </div>

                                    {{-- Pilihan format KIR — muncul hanya setelah ruangan dipilih --}}
                                    <div id="kir-format-section" style="display:none;margin-top:14px;">
                                        <div style="font-size:12px;font-weight:600;color:#374151;margin-bottom:8px;">
                                            Template Format KIR
                                        </div>
                                        <div style="display:flex;flex-direction:column;gap:8px;">
                                            <label style="display:flex;align-items:flex-start;gap:10px;padding:10px 12px;border:1.5px solid #064635;border-radius:8px;cursor:pointer;transition:all 0.15s;" id="kir-format-standard-label">
                                                <input type="radio" name="kir-format" value="standard" checked
                                                       style="width:16px;height:16px;accent-color:#064635;flex-shrink:0;margin-top:2px;"
                                                       onchange="onKirFormatChange(this)">
                                                <div>
                                                    <div style="font-size:13px;font-weight:600;color:#374151;">Format Standar (Permendagri)</div>
                                                    <div style="font-size:11px;color:#6b7280;margin-top:1px;">2 TTD: Sekretaris Desa &amp; Petugas Barang</div>
                                                </div>
                                            </label>
                                            <label style="display:flex;align-items:flex-start;gap:10px;padding:10px 12px;border:1.5px solid #e5e7eb;border-radius:8px;cursor:pointer;transition:all 0.15s;" id="kir-format-custom-label">
                                                <input type="radio" name="kir-format" value="custom"
                                                       style="width:16px;height:16px;accent-color:#064635;flex-shrink:0;margin-top:2px;"
                                                       onchange="onKirFormatChange(this)">
                                                <div>
                                                    <div style="font-size:13px;font-weight:600;color:#374151;">Format Desa Demo</div>
                                                    <div style="font-size:11px;color:#6b7280;margin-top:1px;">3 TTD: Perbekel, Penanggung Jawab, &amp; Pengurus Barang</div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                </div>{{-- end content-ruangan --}}

                                {{-- Konten KIB --}}
                                @php
                                $kibColors = [
                                    'all' => ['bg' => 'linear-gradient(135deg,#064635,#1c5240)', 'icon' => 'layers'],
                                    'A'   => ['bg' => 'linear-gradient(135deg,#10b981,#059669)', 'icon' => 'map-pin'],
                                    'B'   => ['bg' => 'linear-gradient(135deg,#3b82f6,#2563eb)', 'icon' => 'tool'],
                                    'C'   => ['bg' => 'linear-gradient(135deg,#8b5cf6,#7c3aed)', 'icon' => 'home'],
                                    'D'   => ['bg' => 'linear-gradient(135deg,#06b6d4,#0891b2)', 'icon' => 'navigation'],
                                    'E'   => ['bg' => 'linear-gradient(135deg,#f59e0b,#d97706)', 'icon' => 'package'],
                                    'F'   => ['bg' => 'linear-gradient(135deg,#ef4444,#dc2626)', 'icon' => 'settings'],
                                ];
                                @endphp
                                <div id="content-kib" style="display:none;">
                                  <div class="jenis-flow-helper" style="margin-bottom:12px;">
                                    <i data-feather="info"></i>
                                    Pilih KIB terlebih dahulu untuk mengaktifkan opsi export. Filter tahun perolehan berlaku untuk semua KIB.
                                  </div>
                                  <div class="buku-inventaris-section" style="margin-top:0;">
                                    <div class="buku-inventaris-header" style="margin-bottom:16px;">
                                        <div class="buku-inventaris-header-content">
                                            <span class="buku-inventaris-title">Buku Inventaris (KIB)</span>
                                            <p class="buku-inventaris-desc">Pilih KIB yang akan diekspor sesuai standar Permendagri.</p>
                                        </div>
                                    </div>
                                    <input type="hidden" name="kib_type_selected" id="kib_type_hidden" value="">
                                    <div class="searchable-dropdown" id="kib-dropdown-container">
                                        <button type="button" class="dropdown-trigger" id="kib-dropdown-trigger">
                                            <span id="kib-selected-label" style="color:#9ca3af;font-size:13px;font-weight:500;">-- Pilih KIB --</span>
                                            <i data-feather="chevron-down" class="dropdown-trigger-caret" style="width:16px;height:16px;"></i>
                                        </button>

                                        <div class="dropdown-menu-panel" id="kib-dropdown-menu">
                                            <div class="dropdown-search-wrapper">
                                                <i data-feather="search"></i>
                                                <input type="text" id="kib-dropdown-search" class="dropdown-search-input" placeholder="Cari KIB..." autocomplete="off">
                                            </div>
                                            <ul class="dropdown-options-list" id="kib-dropdown-options-list"></ul>
                                        </div>
                                    </div>
                                  </div>
                                </div>{{-- end content-kib --}}

                                {{-- Konten Laporan Aset --}}
                                <div id="content-laporan" style="display:none;">
                                    <div class="jenis-flow-helper">
                                        <i data-feather="info"></i>
                                        Pilih periode laporan. Tahun perolehan dipilih melalui Filter Tahun Perolehan di bawah.
                                    </div>

                                    {{-- Pilih Periode --}}
                                    <div style="font-size:12px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:8px;">Periode Laporan</div>
                                    <input type="hidden" name="laporan-periode" id="laporan_periode_hidden" value="tahunan">
                                    <div class="searchable-dropdown" id="laporan-dropdown-container">
                                        <button type="button" class="dropdown-trigger" id="laporan-dropdown-trigger">
                                            <span id="laporan-selected-label" style="color:#9ca3af;font-size:13px;font-weight:500;">-- Pilih Periode Laporan --</span>
                                            <i data-feather="chevron-down" class="dropdown-trigger-caret" style="width:16px;height:16px;"></i>
                                        </button>
                                        <div class="dropdown-menu-panel" id="laporan-dropdown-menu">
                                            <div class="dropdown-search-wrapper">
                                                <i data-feather="search"></i>
                                                <input type="text" id="laporan-dropdown-search" class="dropdown-search-input" placeholder="Cari periode..." autocomplete="off">
                                            </div>
                                            <ul class="dropdown-options-list" id="laporan-dropdown-options-list"></ul>
                                        </div>
                                    </div>
                                </div>{{-- end content-laporan --}}

                            </div>
                        </div>

                        <!-- Section 1.5: Informasi Dokumen (Lampiran & Nomor BA) - Hanya untuk Export Jenis Inventaris -->
                        <div class="section-card" id="section-dokumen-info" style="display:none; margin-top: 20px;">
                            <div class="section-header">
                                <div class="section-header-icon teal">
                                    <i data-feather="file-text"></i>
                                </div>
                                <div class="section-header-title">
                                    <h5>Informasi Dokumen</h5>
                                    <span>Isi informasi lampiran dan berita acara (opsional)</span>
                                </div>
                            </div>
                            <div class="section-body">
                                <div class="ttd-setup">
                                    <!-- Info Helper -->
                                    <div class="info-box" style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border: 1px solid #93c5fd; margin-bottom: 16px;">
                                        <i data-feather="info" style="color: #1e40af; stroke: #1e40af;"></i>
                                        <div class="info-box-text" style="color: #1e40af;">
                                            Informasi ini akan muncul di bagian header laporan export. Boleh dikosongkan jika tidak diperlukan.
                                        </div>
                                    </div>

                                    <!-- Form Input menggunakan form-grid seperti di create.blade.php -->
                                    <div class="ttd-subsection">
                                        <div style="display: grid; grid-template-columns: 1fr; gap: 16px;">
                                            <!-- Lampiran -->
                                            <div class="form-group">
                                                <label class="form-label" for="doc-lampiran">
                                                    Lampiran <span class="optional">(opsional)</span>
                                                </label>
                                                <input type="text" id="doc-lampiran" class="form-control" placeholder="Contoh: 3 / 1 - 2">
                                                <div class="form-hint">Format: [Nomor Urut] / [Halaman] - [Total Halaman]</div>
                                            </div>

                                            <!-- Berita Acara Nomor -->
                                            <div class="form-group">
                                                <label class="form-label" for="doc-ba-nomor">
                                                    Berita Acara Inventarisasi Aset Desa Nomor <span class="optional">(opsional)</span>
                                                </label>
                                                <input type="text" id="doc-ba-nomor" class="form-control" placeholder="Contoh: 029/025/35.09.08.2001/2024">
                                                <div class="form-hint">Nomor surat resmi Berita Acara</div>
                                            </div>

                                            <!-- Tanggal Berita Acara -->
                                            <div class="form-group">
                                                <label class="form-label" for="doc-ba-tanggal">
                                                    Tanggal Berita Acara <span class="optional">(opsional)</span>
                                                </label>
                                                <input type="text" id="doc-ba-tanggal" class="form-control" placeholder="Pilih tanggal" style="cursor:pointer;">
                                                <div class="form-hint">Tanggal penerbitan Berita Acara</div>
                                            </div>
                                        </div>

                                        <!-- Preview Box -->
                                        <div class="preview-doc-box">
                                            <div class="preview-doc-box-title">
                                                <i data-feather="eye"></i>
                                                Preview di Export
                                            </div>
                                            <div class="preview-doc-box-content">
                                                <div><strong>Lampiran:</strong> <span id="preview-lampiran">-</span></div>
                                                <div><strong>Berita Acara Inventarisasi Aset Desa</strong></div>
                                                <div><strong>Nomor:</strong> <span id="preview-ba-nomor">-</span></div>
                                                <div><strong>Tanggal:</strong> <span id="preview-ba-tanggal">-</span></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Atur TTD & Filter Tahun -->
                        <div class="section-card" id="section-ttd-setup" style="display:none; margin-top: 20px;">
                            <div class="section-header">
                                <div class="section-header-icon green">
                                    <i data-feather="users"></i>
                                </div>
                                <div class="section-header-title">
                                    <h5>Atur Pejabat & Tanggal Export</h5>
                                    <span>Konfigurasi pejabat dan filter tahun data untuk export</span>
                                </div>
                            </div>
                            <div class="section-body">
                                <div class="ttd-setup">
                                    <!-- Filter Tahun -->
                                    <div class="ttd-subsection">
                                        <div class="ttd-subsection-title">
                                            <i data-feather="calendar"></i>
                                            Filter Tahun Perolehan
                                        </div>
                                        <div style="display: flex; gap: 12px; align-items: center;">
                                            <input type="text" id="filter-tahun"
                                                class="year-picker-export date-input-field"
                                                placeholder="Pilih tahun"
                                                value="{{ date('Y') }}"
                                                inputmode="numeric" maxlength="4"
                                                data-min-year="2000"
                                                data-max-year="{{ date('Y') + 1 }}"
                                                style="flex:1;padding:10px 12px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;outline:none;cursor:pointer;background:#f9fafb;">
                                            <div class="data-info-badge" style="background: #dbeafe; color: #2563eb; padding: 10px 16px; border-radius: 10px; font-size: 13px; font-weight: 600; white-space: nowrap;">
                                                <span id="tahun-data-count">-</span> aset akan diexport
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Pejabat Default -->
                                    <div class="ttd-subsection" id="ttd-subsec-pejabat-default">
                                        <div class="ttd-subsection-title">
                                            <i data-feather="check"></i>
                                            <span id="default-pejabat-section-title">Penandatangan Saat Ini</span>
                                        </div>
                                        <div class="pejabat-display">
                                            <span class="pejabat-display-label" id="default-pejabat-section-label">Penandatangan yang akan muncul di dokumen:</span>
                                            <div class="pejabat-items" id="default-pejabat-list">
                                                <div class="pejabat-empty">Pilih jenis inventaris untuk melihat penandatangan</div>
                                            </div>
                                        </div>
                                    </div>

                                     <!-- Pejabat Pengganti -->
                                     <div class="ttd-subsection" id="ttd-subsec-pejabat-custom">
                                         <div class="ttd-subsection-title">
                                             <i data-feather="edit-3"></i>
                                             <span id="custom-pejabat-section-title">Ganti Penandatangan (Opsional)</span>
                                         </div>
                                         <div class="pejabat-display">
                                             <div class="pejabat-items" id="custom-pejabat-list">
                                                 <div class="pejabat-empty">Belum diubah — menggunakan penandatangan default dari sistem.</div>
                                             </div>
                                             <div style="display: flex; justify-content: center; align-items: center; gap: 10px; width: 100%; margin-top: 14px; flex-wrap: wrap;">
                                                 <button class="btn-action" id="btn-custom-pejabat" style="min-width: 220px; max-width: 340px; padding: 10px 24px; font-weight: 600; text-align: center; background: linear-gradient(135deg, #519259 0%, #064635 100%); color: #ffffff !important; border: none; box-shadow: 0 4px 12px rgba(6, 70, 53, 0.15); cursor: pointer;" onclick="openCustomPejabatModal()">
                                                     + Ganti Penandatangan
                                                 </button>
                                                 <button class="btn-action" id="btn-reset-custom-pejabat" style="display: none; background: #fee2e2; color: #dc2626; border-color: #fca5a5; padding: 10px 18px; font-weight: 600; cursor: pointer;" onclick="resetCustomPejabat()">
                                                     Reset Default
                                                 </button>
                                             </div>
                                         </div>
                                     </div>

                                    <!-- Tanggal Dokumen -->
                                    <div class="ttd-subsection" id="ttd-subsection-dokumen">
                                        <div class="ttd-subsection-title">
                                            <i data-feather="calendar"></i>
                                            Tanggal Dokumen
                                        </div>
                                        <div class="doc-flow-helper" id="doc-flow-helper-text">
                                            <i data-feather="info"></i>
                                            Tanggal dokumen digunakan untuk mengisi informasi tanggal pada hasil export. Default adalah hari ini.
                                        </div>
                                        <div class="date-input-group">
                                            <label class="date-input-label">Tanggal Dokumen</label>
                                            <input type="text" id="doc-tanggal" class="date-input-field" placeholder="Pilih tanggal dokumen">
                                        </div>
                                        <div class="date-preview-box">
                                            <div class="date-preview-label">Preview Tanggal:</div>
                                            <div class="date-preview-text" id="date-preview">-</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 3: History Export -->
                        <div class="history-card" id="history-section" style="display: block;">
                            <div class="section-header" style="display:flex; justify-content:space-between; align-items:center; gap:16px;">
                                <div style="display:flex; align-items:center; gap:14px; min-width:0;">
                                    <div class="section-header-icon orange">
                                        <i data-feather="server"></i>
                                    </div>
                                    <div class="section-header-title">
                                        <h5 id="history-title">Riwayat Export</h5>
                                        <span id="history-subtitle">Riwayat lengkap export tersedia di halaman khusus dengan filter semua jenis atau per jenis inventaris.</span>
                                    </div>
                                </div>
                                <a href="{{ route('export-data.history', ['from' => 'export_index']) }}" id="history-view-all-btn" class="btn-action" style="background: linear-gradient(135deg, #519259 0%, #064635 100%); color: #ffffff !important; white-space: nowrap;">
                                    Lihat Semua Riwayat
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Right Sidebar: Export Options -->
                    <div class="export-sidebar">
                        <!-- Export Options -->
                        <div class="export-options-card" id="export-options-card">
                            <div class="export-options-header">
                                <h5 class="text-white">Opsi Export</h5>
                            </div>
                            <div class="export-options-body">
                                <!-- Format Selection -->
                                <div>
                                    <div style="font-size: 12px; font-weight: 600; color: #064635; margin-bottom: 10px; display: flex; align-items: center; gap: 6px;">
                                        <i data-feather="download" style="width: 14px; height: 14px;"></i>
                                        Format File
                                    </div>
                                    <div class="format-options">
                                        <div class="format-option">
                                            <input type="radio" name="export-format" id="format-excel" value="excel" checked>
                                            <label for="format-excel">
                                                <div class="format-icon excel">
                                                    <i data-feather="file-minus"></i>
                                                </div>
                                                <div class="format-info">
                                                    <div class="format-label">Excel</div>
                                                    <div class="format-desc">Format .xlsx</div>
                                                </div>
                                            </label>
                                        </div>
                                        <div class="format-option">
                                            <input type="radio" name="export-format" id="format-pdf" value="pdf">
                                            <label for="format-pdf">
                                                <div class="format-icon pdf">
                                                    <i data-feather="file-text"></i>
                                                </div>
                                                <div class="format-info">
                                                    <div class="format-label">PDF</div>
                                                    <div class="format-desc">Format PDF</div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Signature Options -->
                                <div class="signature-label">
                                    <i data-feather="pen-tool"></i>
                                    Opsi Tanda Tangan
                                </div>
                                <div class="signature-options">
                                    <div class="signature-options-group">
                                        <div class="signature-option">
                                            <input type="radio" name="signature-option" id="sig-with" value="with">
                                            <label for="sig-with">Dengan Tanda Tangan</label>
                                        </div>
                                        <div class="signature-option">
                                            <input type="radio" name="signature-option" id="sig-without" value="without" checked>
                                            <label for="sig-without">Tanpa Tanda Tangan</label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Ukuran Kertas - hanya muncul saat format PDF dipilih -->
                                <div id="paper-size-section" style="display:none;">
                                    <div class="signature-label" style="margin-bottom:10px;">
                                        <i data-feather="file" style="width:12px;height:12px;"></i>
                                        Ukuran Kertas
                                    </div>
                                    <div style="display:flex;flex-direction:column;gap:8px;">
                                        <div class="signature-option">
                                            <input type="radio" name="paper-size" id="paper-legal" value="legal" checked
                                                   style="width:16px;height:16px;accent-color:#064635;cursor:pointer;">
                                            <label for="paper-legal" style="cursor:pointer;flex:1;">
                                                <span style="font-size:12px;font-weight:600;color:#1f2937;display:block;">Legal</span>
                                                <span style="font-size:10px;color:#9ca3af;">8.5 &times; 14 in &nbsp;|&nbsp; 21.59 &times; 35.56 cm</span>
                                            </label>
                                        </div>
                                        <div class="signature-option">
                                            <input type="radio" name="paper-size" id="paper-letter" value="letter"
                                                   style="width:16px;height:16px;accent-color:#064635;cursor:pointer;">
                                            <label for="paper-letter" style="cursor:pointer;flex:1;">
                                                <span style="font-size:12px;font-weight:600;color:#1f2937;display:block;">Letter</span>
                                                <span style="font-size:10px;color:#9ca3af;">8.5 &times; 11 in &nbsp;|&nbsp; 21.59 &times; 27.94 cm</span>
                                            </label>
                                        </div>
                                        <div class="signature-option">
                                            <input type="radio" name="paper-size" id="paper-f4" value="f4"
                                                   style="width:16px;height:16px;accent-color:#064635;cursor:pointer;">
                                            <label for="paper-f4" style="cursor:pointer;flex:1;">
                                                <span style="font-size:12px;font-weight:600;color:#1f2937;display:block;">F4 / Folio</span>
                                                <span style="font-size:10px;color:#9ca3af;">8.27 &times; 13 in &nbsp;|&nbsp; 21 &times; 33 cm</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Export Button -->
                                <button class="export-action-btn" id="btn-preview-export" disabled>
                                    <i data-feather="eye"></i>
                                    Preview Export
                                </button>
                            </div>
                        </div>

                        <!-- Summary Card -->
                        <div class="section-card">
                            <div class="section-header">
                                <div class="section-header-icon blue">
                                    <i data-feather="info"></i>
                                </div>
                                <div class="section-header-title">
                                    <h5>Ringkasan Export</h5>
                                </div>
                            </div>
                            {{-- <div class="section-body">
                                <div style="display: flex; flex-direction: column; gap: 12px; font-size: 12px;">
                                    <div style="padding: 0; background: transparent; color: #0369a1;">
                                        <strong style="background: #eff6ff; padding: 4px 8px; border-radius: 4px; display: inline-block;">Jenis:</strong> <span id="info-jenis">Pilih jenis</span>
                                    </div>
                                    <div style="padding: 0; background: transparent; color: #92400e;">
                                        <strong style="background: #fef3c7; padding: 4px 8px; border-radius: 4px; display: inline-block;">Tahun:</strong> <span id="info-tahun">2025</span>
                                    </div>
                                    <div style="padding: 0; background: transparent; color: #15803d;">
                                        <strong style="background: #f0fdf4; padding: 4px 8px; border-radius: 4px; display: inline-block;">Format:</strong> <span id="info-format">Excel (.xlsx)</span>
                                    </div>
                                    <div style="padding: 0; background: transparent; color: #7f1d1d;">
                                        <strong style="background: #fecaca; padding: 4px 8px; border-radius: 4px; display: inline-block;">TTD:</strong> <span id="info-signature">Dengan Tanda Tangan</span>
                                    </div>
                                </div>
                            </div> --}}
                            <div class="section-body">
                                <div class="simple-info-list">

                                    <div class="simple-item">
                                        <div class="simple-icon blue">
                                            <i data-feather="book"></i>
                                        </div>
                                        <div class="simple-text">
                                            <div class="simple-title">Jenis</div>
                                            <div class="simple-sub" id="info-jenis">Buku Inventaris Aset Desa</div>
                                        </div>
                                    </div>

                                    <div class="simple-item">
                                        <div class="simple-icon orange">
                                            <i data-feather="calendar"></i>
                                        </div>
                                        <div class="simple-text">
                                            <div class="simple-title">Tahun</div>
                                            <div class="simple-sub" id="info-tahun">{{ date('Y') }}</div>
                                        </div>
                                    </div>

                                    <div class="simple-item">
                                        <div class="simple-icon green">
                                            <i data-feather="file-text"></i>
                                        </div>
                                        <div class="simple-text">
                                            <div class="simple-title">Format</div>
                                            <div class="simple-sub" id="info-format">Excel (.xlsx)</div>
                                        </div>
                                    </div>

                                    <div class="simple-item">
                                        <div class="simple-icon red">
                                            <i data-feather="edit-3"></i>
                                        </div>
                                        <div class="simple-text">
                                            <div class="simple-title">TTD</div>
                                            <div class="simple-sub" id="info-signature">Tanpa Tanda Tangan</div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Custom Pejabat Modal -->
        <div id="custom-pejabat-modal" class="modal-overlay">
            <div class="modal-box">
                <div class="modal-header">
                    <h5 id="custom-pejabat-modal-title">Pilih Penandatangan</h5>
                </div>
                <div class="modal-body">
                    {{-- Hint khusus Format 2 KIR - muncul/hilang via JS --}}
                    <div id="kir-format2-hint" style="display:none; margin-bottom:14px; padding:14px 16px; background:linear-gradient(135deg,#eff6ff,#dbeafe); border:1px solid #93c5fd; border-radius:10px;">
                        <div style="font-size:12px;font-weight:700;color:#1e40af;margin-bottom:10px;">
                            Cara memilih penandatangan Format Desa Demo
                        </div>
                        <div style="display:flex;flex-direction:column;gap:5px;font-size:12px;color:#1e40af;line-height:1.6;">
                            <div>— Orang pertama yang anda centang menjadi <strong>Penanggung Jawab</strong></div>
                            <div>— Orang kedua yang anda centang menjadi <strong>Pengurus Barang</strong></div>
                            <div>— Pilih tepat <strong>2 orang</strong>. Perbekel sudah otomatis diisi sistem dan tidak perlu dipilih di sini.</div>
                        </div>
                        <div id="kir-format2-hint-count" style="margin-top:10px;font-size:11px;font-weight:600;color:#1e40af;"></div>
                    </div>
                    <div class="pejabat-list" id="modal-pejabat-list" style="border: 1px solid #e2e8f0; border-radius: 10px; max-height: 300px; overflow-y: auto;">
                        <!-- Populated by JS -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="modal-btn modal-btn-cancel" onclick="closeCustomPejabatModal()"><i data-feather="x" style="width:14px;height:14px;"></i> Batal</button>
                    <button class="modal-btn modal-btn-confirm" id="btn-save-custom-pejabat" onclick="saveCustomPejabat()">Simpan</button>
                </div>
            </div>
        </div>

        <!-- Preview Export Modal -->
        <div id="preview-export-modal" class="modal-overlay">
            <div class="modal-box">
                <div class="modal-header">
                    <h5>Preview Export Data</h5>
                </div>
                <div class="modal-body">
                    <div class="preview-grid">
                        <div class="preview-info-box">
                            <div class="preview-info-label">Format File</div>
                            <div class="preview-info-value" id="preview-format">Excel (.xlsx)</div>
                        </div>
                        <div class="preview-info-box">
                            <div class="preview-info-label">Jumlah Data</div>
                            <div class="preview-info-value" id="preview-count">0 aset</div>
                        </div>
                        <div class="preview-info-box">
                            <div class="preview-info-label">Opsi Tanda Tangan</div>
                            <div class="preview-info-value" id="preview-sig">Dengan Tanda Tangan</div>
                        </div>
                        <div class="preview-info-box">
                            <div class="preview-info-label">Tanggal Dokumen</div>
                            <div class="preview-info-value" id="preview-date">Mengwi, -</div>
                        </div>
                    </div>

                    <div style="margin-bottom: 4px;">
                        <h6 style="font-size: 11px; font-weight: 600; color: #6b7280; margin: 0 0 8px 0; text-transform: uppercase; letter-spacing: 0.5px;">Pratinjau Struktur File Export:</h6>

                        {{-- Excel preview --}}
                        <div class="excel-preview-wrapper" id="preview-wrapper-excel">
                            <div class="excel-toolbar">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="white"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8" fill="none" stroke="white" stroke-width="2"/></svg>
                                <span id="excel-filename">export.xlsx</span>
                            </div>
                            <div class="excel-col-bar" id="excel-col-bar"></div>
                            <div style="overflow-x: auto; max-height: 220px; overflow-y: auto;">
                                <table class="excel-table" id="excel-preview-table"></table>
                            </div>
                        </div>

                        {{-- PDF preview --}}
                        <div id="preview-wrapper-pdf" style="display:none; border-radius:6px; overflow:hidden; border:1px solid #e5e7eb;">
                            <div class="excel-toolbar" style="background:#dc2626;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="white"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8" fill="none" stroke="white" stroke-width="2"/></svg>
                                <span id="pdf-filename">export.pdf</span>
                            </div>
                            <div id="pdf-preview-content" style="background:#f9fafb; max-height:260px; overflow-y:auto; padding:12px 16px;"></div>
                        </div>

                        <div class="preview-signer-panel" id="preview-signer-panel" style="display:none !important;">
                            <div class="preview-signer-title">Pratinjau Tim Inventarisasi</div>
                            <div class="preview-signer-note" id="preview-signer-note"></div>
                            <div id="preview-signer-list"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="modal-btn modal-btn-cancel" onclick="closePreviewModal()"><i data-feather="x" style="width:14px;height:14px;"></i> Batal</button>
                    <button class="modal-btn modal-btn-confirm" id="btn-confirm-export" onclick="confirmExport()">
                        <i data-feather="download" style="width: 12px; height: 12px; margin-right: 4px;"></i>
                        <span id="btn-confirm-export-text">Export Sekarang</span>
                    </button>
                </div>
            </div>
        </div>

        @include('layouts.footer-block')
        @include('layouts.footer-js')
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

        <script>
            // ===== DATA DARI PHP =====
            const exportTypeOptions = [
                {
                    id: 'jenis',
                    nama: 'Export Jenis Inventaris',
                    deskripsi: 'Export per jenis atau semua jenis',
                    icon: 'file-text',
                    gradient: 'linear-gradient(135deg,#3b82f6 0%,#2563eb 100%)'
                },
                {
                    id: 'ruangan',
                    nama: 'Export Ruangan',
                    deskripsi: 'Kartu Inventaris per Ruangan',
                    icon: 'grid',
                    gradient: 'linear-gradient(135deg,#10b981 0%,#059669 100%)'
                },
                {
                    id: 'kib',
                    nama: 'Export Buku Inventaris (KIB)',
                    deskripsi: 'KIB A-F sesuai standar Permendagri',
                    icon: 'book-open',
                    gradient: 'linear-gradient(135deg,#f59e0b 0%,#d97706 100%)'
                },
                {
                    id: 'laporan',
                    nama: 'Laporan Aset Per Semester / Tahunan',
                    deskripsi: 'Saldo awal, mutasi, dan saldo akhir',
                    icon: 'bar-chart-2',
                    gradient: 'linear-gradient(135deg,#8b5cf6 0%,#7c3aed 100%)'
                }
            ];

            const jenisInventarisOptions = {!! json_encode($jenisInventaris->map(function($jenis) {
                return [
                    'id' => $jenis->id,
                    'nama' => $jenis->nama,
                    'inventaris_count' => $jenis->inventaris_count,
                    'icon' => $jenis->icon,
                    'color_gradient' => $jenis->color_gradient
                ];
            })->prepend([
                'id' => 'all',
                'nama' => 'Semua Jenis',
                'inventaris_count' => $jenisInventaris->sum('inventaris_count'),
                'icon' => 'layers',
                'color_gradient' => ['#064635', '#2d3a52']
            ])->merge(collect($virtualJenis)->map(function($vJenis) {
                // Handle array format dari virtual jenis
                if (is_array($vJenis)) {
                    preg_match('/linear-gradient\\(135deg,\\s*([^,]+)\\s*0%,\\s*([^)]+)100%\\)/', $vJenis['color'] ?? '', $matches);
                    return [
                        'id' => $vJenis['id'],
                        'nama' => $vJenis['nama'],
                        'inventaris_count' => 0,
                        'icon' => $vJenis['icon'],
                        'color_gradient' => isset($matches[1]) ? [trim($matches[1]), trim($matches[2])] : ['#6b7280', '#4b5563'],
                        'deskripsi' => $vJenis['deskripsi'] ?? '',
                        'is_virtual' => true
                    ];
                }
                return null;
            })->filter())->values()) !!};

            const ruanganOptions = {!! json_encode(
                $ruanganList->map(function($ruangan, $idx) {
                    $palette = [
                        ['gradient' => 'linear-gradient(135deg,#064635 0%,#2d3a52 100%)', 'icon' => 'layers'],
                        ['gradient' => 'linear-gradient(135deg,#3b82f6 0%,#2563eb 100%)', 'icon' => 'home'],
                        ['gradient' => 'linear-gradient(135deg,#8b5cf6 0%,#7c3aed 100%)', 'icon' => 'briefcase'],
                        ['gradient' => 'linear-gradient(135deg,#f59e0b 0%,#d97706 100%)', 'icon' => 'book'],
                        ['gradient' => 'linear-gradient(135deg,#ef4444 0%,#dc2626 100%)', 'icon' => 'monitor'],
                        ['gradient' => 'linear-gradient(135deg,#06b6d4 0%,#0891b2 100%)', 'icon' => 'archive'],
                        ['gradient' => 'linear-gradient(135deg,#ec4899 0%,#db2777 100%)', 'icon' => 'coffee'],
                        ['gradient' => 'linear-gradient(135deg,#14b8a6 0%,#0d9488 100%)', 'icon' => 'box'],
                        ['gradient' => 'linear-gradient(135deg,#f97316 0%,#ea580c 100%)', 'icon' => 'map-pin'],
                    ];
                    $p = $palette[($idx + 1) % count($palette)];
                    return [
                        'id' => $ruangan->id,
                        'nama' => $ruangan->nama,
                        'deskripsi' => $ruangan->inventaris_count . ' aset',
                        'icon' => $p['icon'],
                        'gradient' => $p['gradient'],
                    ];
                })->prepend([
                    'id' => 'all',
                    'nama' => 'Semua Ruangan',
                    'deskripsi' => $ruanganList->count() . ' ruangan, ' . $ruanganList->sum('inventaris_count') . ' total aset',
                    'icon' => 'layers',
                    'gradient' => 'linear-gradient(135deg,#064635 0%,#2d3a52 100%)',
                ])->values()
            ) !!};

            const kibOptions = {!! json_encode(
                collect([
                    ['id' => 'all', 'nama' => 'Semua KIB', 'deskripsi' => 'KIB A - E', 'icon' => 'layers', 'gradient' => 'linear-gradient(135deg,#064635,#1c5240)', 'disabled' => false],
                ])->merge(
                    $kibTypes->map(function($kib) {
                        $colors = [
                            'A' => ['gradient' => 'linear-gradient(135deg,#10b981,#059669)', 'icon' => 'map-pin'],
                            'B' => ['gradient' => 'linear-gradient(135deg,#3b82f6,#2563eb)', 'icon' => 'tool'],
                            'C' => ['gradient' => 'linear-gradient(135deg,#8b5cf6,#7c3aed)', 'icon' => 'home'],
                            'D' => ['gradient' => 'linear-gradient(135deg,#06b6d4,#0891b2)', 'icon' => 'navigation'],
                            'E' => ['gradient' => 'linear-gradient(135deg,#f59e0b,#d97706)', 'icon' => 'package'],
                            'F' => ['gradient' => 'linear-gradient(135deg,#ef4444,#dc2626)', 'icon' => 'settings'],
                        ];
                        $c = $colors[$kib['type']] ?? ['gradient' => 'linear-gradient(135deg,#6b7280,#4b5563)', 'icon' => 'box'];
                        $isDisabled = $kib['type'] === 'F';
                        return [
                            'id'       => $kib['type'],
                            'nama'     => $kib['nama'],
                            'deskripsi'=> $isDisabled ? 'Segera hadir' : ($kib['count'] . ' aset · KIB ' . $kib['type']),
                            'icon'     => $c['icon'],
                            'gradient' => $c['gradient'],
                            'disabled' => $isDisabled,
                        ];
                    })
                )->values()
            ) !!};

            // ===== FORMAT KIR TOGGLE =====
            window.onKirFormatChange = function(radio) {
                const stdLabel = document.getElementById('kir-format-standard-label');
                const cstLabel = document.getElementById('kir-format-custom-label');
                if (stdLabel) stdLabel.style.borderColor = radio.value === 'standard' ? '#064635' : '#e5e7eb';
                if (cstLabel) cstLabel.style.borderColor = radio.value === 'custom'   ? '#064635' : '#e5e7eb';
                // Reset pilihan penandatangan karena format berubah (2 format punya penandatangan berbeda)
                customPejabat = [];
                customPejabatOrder = [];
                // Refresh pejabat default dan label section saat format berubah
                if (typeof activeExportTab !== 'undefined' && activeExportTab === 'ruangan') {
                    loadDefaultPejabat();
                    updateCustomPejabatLabels();
                }
            };

            // ===== TAB SWITCHER =====
            let activeExportTab = 'jenis'; // 'jenis' | 'ruangan' | 'kib'
            let selectedRuangan = null;
            let selectedKib = null;
            let selectedExportType = null;
            let selectedJenisId = null;

            // ===== RENDER DROPDOWN FUNCTIONS =====
            function renderExportTypeDropdown() {
                const optionsList = document.getElementById('export-type-dropdown-options-list');
                const trigger = document.getElementById('export-type-dropdown-trigger');
                const menu = document.getElementById('export-type-dropdown-menu');
                const container = document.getElementById('export-type-dropdown-container');
                const hiddenInput = document.getElementById('export_type_hidden');

                if (!optionsList || exportTypeOptions.length === 0) return;

                // Render options
                optionsList.innerHTML = exportTypeOptions.map(opt => {
                    return `
                        <li class="dropdown-option-item" data-id="${opt.id}" data-search="${opt.nama.toLowerCase()} ${opt.deskripsi.toLowerCase()}">
                            <div class="dropdown-option-icon" style="background: ${opt.gradient};">
                                <i data-feather="${opt.icon}"></i>
                            </div>
                            <div class="dropdown-option-text">
                                <div class="dropdown-option-name">${opt.nama}</div>
                                <div class="dropdown-option-desc">${opt.deskripsi}</div>
                            </div>
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

                        const id = this.dataset.id;
                        hiddenInput.value = id;
                        selectedExportType = id;

                        // Update trigger label
                        const selectedData = exportTypeOptions.find(o => o.id === id);
                        if (selectedData) {
                            document.getElementById('export-type-selected-label').innerHTML = `
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <div style="width:24px;height:24px;border-radius:6px;background:${selectedData.gradient};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                        <i data-feather="${selectedData.icon}" style="width:12px;height:12px;color:#fff;stroke:#fff;"></i>
                                    </div>
                                    <span style="font-size:13px;font-weight:600;color:#1f2937;">${selectedData.nama}</span>
                                </div>
                            `;
                            if (typeof feather !== 'undefined') try { feather.replace(); } catch (e) {}
                        }

                        // Switch tab
                        switchExportTab(id);

                        // Close menu
                        menu.classList.remove('show');
                        container.classList.remove('open');
                    });
                });

                // Toggle dropdown
                trigger.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const isOpen = menu.classList.contains('show');
                    
                    // Close all other dropdowns first
                    document.querySelectorAll('.dropdown-menu-panel').forEach(m => {
                        if (m !== menu) m.classList.remove('show');
                    });
                    document.querySelectorAll('.searchable-dropdown').forEach(c => {
                        if (c !== container) c.classList.remove('open');
                    });

                    if (!isOpen) {
                        menu.classList.add('show');
                        container.classList.add('open');
                    } else {
                        menu.classList.remove('show');
                        container.classList.remove('open');
                    }
                });

                // Close dropdown when clicking outside - use named function to avoid duplicates
                const closeExportTypeDropdown = function(e) {
                    if (!container.contains(e.target)) {
                        menu.classList.remove('show');
                        container.classList.remove('open');
                    }
                };
                
                // Remove old listener if exists and add new one
                document.removeEventListener('click', closeExportTypeDropdown);
                document.addEventListener('click', closeExportTypeDropdown);
            }

            function renderJenisInventarisDropdown() {
                const optionsList = document.getElementById('jenis-dropdown-options-list');
                const trigger = document.getElementById('jenis-dropdown-trigger');
                const menu = document.getElementById('jenis-dropdown-menu');
                const searchInput = document.getElementById('jenis-dropdown-search');
                const container = document.getElementById('jenis-dropdown-container');
                const hiddenInput = document.getElementById('jenis_inventaris_id_hidden');

                if (!optionsList || !jenisInventarisOptions || jenisInventarisOptions.length === 0) return;

                // Render options
                optionsList.innerHTML = jenisInventarisOptions.map(jenis => {
                    const colors = jenis.color_gradient || ['#6b7280', '#4b5563'];
                    const gradient = `linear-gradient(135deg, ${colors[0]} 0%, ${colors[1]} 100%)`;
                    const icon = jenis.icon || 'box';
                    const deskripsi = jenis.deskripsi || `${jenis.inventaris_count} total aset`;
                    
                    return `
                        <li class="dropdown-option-item" data-id="${jenis.id}" data-search="${jenis.nama.toLowerCase()}" data-virtual="${jenis.is_virtual || false}">
                            <div class="dropdown-option-icon" style="background: ${gradient};">
                                <i data-feather="${icon}"></i>
                            </div>
                            <div class="dropdown-option-text">
                                <div class="dropdown-option-name">${jenis.nama}</div>
                                <div class="dropdown-option-desc">${deskripsi}</div>
                            </div>
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

                        const id = this.dataset.id;
                        hiddenInput.value = id;
                        selectedJenisId = id;

                        // Update trigger label
                        const selectedData = jenisInventarisOptions.find(j => String(j.id) === String(id));
                        if (selectedData) {
                            const colors = selectedData.color_gradient || ['#6b7280', '#4b5563'];
                            const gradient = `linear-gradient(135deg, ${colors[0]} 0%, ${colors[1]} 100%)`;
                            const icon = selectedData.icon || 'box';

                            document.getElementById('jenis-selected-label').innerHTML = `
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <div style="width:24px;height:24px;border-radius:6px;background:${gradient};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                        <i data-feather="${icon}" style="width:12px;height:12px;color:#fff;stroke:#fff;"></i>
                                    </div>
                                    <span style="font-size:13px;font-weight:600;color:#1f2937;">${selectedData.nama}</span>
                                </div>
                            `;
                            if (typeof feather !== 'undefined') try { feather.replace(); } catch (e) {}
                        }

                        // Trigger existing logic (sama seperti klik radio button lama)
                        updateUI();
                        updateUIForActiveTab();
                        loadDefaultPejabat();
                        updateDataCount();

                        // Close menu
                        menu.classList.remove('show');
                        container.classList.remove('open');
                    });
                });

                // Toggle dropdown
                trigger.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const isOpen = menu.classList.contains('show');
                    
                    // Close all other dropdowns first
                    document.querySelectorAll('.dropdown-menu-panel').forEach(m => {
                        if (m !== menu) m.classList.remove('show');
                    });
                    document.querySelectorAll('.searchable-dropdown').forEach(c => {
                        if (c !== container) c.classList.remove('open');
                    });

                    if (!isOpen) {
                        menu.classList.add('show');
                        container.classList.add('open');
                        searchInput.value = '';
                        searchInput.dispatchEvent(new Event('input'));
                        setTimeout(() => searchInput.focus(), 50);
                    } else {
                        menu.classList.remove('show');
                        container.classList.remove('open');
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

                // Close dropdown when clicking outside - use named function to avoid duplicates
                const closeJenisDropdown = function(e) {
                    if (!container.contains(e.target)) {
                        menu.classList.remove('show');
                        container.classList.remove('open');
                    }
                };
                
                // Remove old listener if exists and add new one
                document.removeEventListener('click', closeJenisDropdown);
                document.addEventListener('click', closeJenisDropdown);
            }

            function renderRuanganDropdown() {
                const optionsList = document.getElementById('ruangan-dropdown-options-list');
                const trigger     = document.getElementById('ruangan-dropdown-trigger');
                const menu        = document.getElementById('ruangan-dropdown-menu');
                const searchInput = document.getElementById('ruangan-dropdown-search');
                const container   = document.getElementById('ruangan-dropdown-container');
                const hiddenInput = document.getElementById('ruangan_id_hidden');

                if (!optionsList || !ruanganOptions || ruanganOptions.length === 0) return;

                optionsList.innerHTML = ruanganOptions.map(r => `
                    <li class="dropdown-option-item" data-id="${r.id}" data-search="${r.nama.toLowerCase()}">
                        <div class="dropdown-option-icon" style="background: ${r.gradient};">
                            <i data-feather="${r.icon}"></i>
                        </div>
                        <div class="dropdown-option-text">
                            <div class="dropdown-option-name">${r.nama}</div>
                            <div class="dropdown-option-desc">${r.deskripsi}</div>
                        </div>
                    </li>
                `).join('');

                if (typeof feather !== 'undefined') try { feather.replace(); } catch(e) {}

                optionsList.querySelectorAll('.dropdown-option-item').forEach(item => {
                    item.addEventListener('click', function(e) {
                        e.stopPropagation();
                        optionsList.querySelectorAll('.dropdown-option-item').forEach(o => o.classList.remove('selected'));
                        this.classList.add('selected');

                        const id = this.dataset.id;
                        hiddenInput.value = id;
                        selectedRuangan = id;

                        // Tampilkan pilihan format KIR saat ruangan sudah dipilih
                        const kirFormatSection = document.getElementById('kir-format-section');
                        if (kirFormatSection) kirFormatSection.style.display = '';

                        const sel = ruanganOptions.find(r => String(r.id) === String(id));
                        if (sel) {
                            document.getElementById('ruangan-selected-label').innerHTML = `
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <div style="width:24px;height:24px;border-radius:6px;background:${sel.gradient};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                        <i data-feather="${sel.icon}" style="width:12px;height:12px;color:#fff;stroke:#fff;"></i>
                                    </div>
                                    <span style="font-size:13px;font-weight:600;color:#1f2937;">${sel.nama}</span>
                                </div>`;
                            if (typeof feather !== 'undefined') try { feather.replace(); } catch(e) {}
                        }

                        updateUI(); updateUIForActiveTab(); loadDefaultPejabat(); updateDataCount();
                        menu.classList.remove('show');
                        container.classList.remove('open');
                    });
                });

                trigger.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const isOpen = menu.classList.contains('show');
                    document.querySelectorAll('.dropdown-menu-panel').forEach(m => { if (m !== menu) m.classList.remove('show'); });
                    document.querySelectorAll('.searchable-dropdown').forEach(c => { if (c !== container) c.classList.remove('open'); });
                    if (!isOpen) {
                        menu.classList.add('show');
                        container.classList.add('open');
                        if (searchInput) { searchInput.value = ''; searchInput.dispatchEvent(new Event('input')); setTimeout(() => searchInput.focus(), 50); }
                    } else {
                        menu.classList.remove('show');
                        container.classList.remove('open');
                    }
                });

                if (searchInput) {
                    searchInput.addEventListener('input', function() {
                        const q = this.value.toLowerCase().trim();
                        let hasVisible = false;
                        optionsList.querySelectorAll('.dropdown-option-item').forEach(item => {
                            const match = (item.dataset.search || '').includes(q);
                            item.style.display = match ? 'flex' : 'none';
                            if (match) hasVisible = true;
                        });
                        let emptyMsg = optionsList.querySelector('.dropdown-empty-search');
                        if (!hasVisible) {
                            if (!emptyMsg) {
                                emptyMsg = document.createElement('div');
                                emptyMsg.className = 'dropdown-empty-search';
                                emptyMsg.style.cssText = 'padding:16px;text-align:center;color:#9ca3af;font-size:12px;';
                                emptyMsg.textContent = 'Tidak ada ruangan yang cocok.';
                                optionsList.appendChild(emptyMsg);
                            }
                        } else if (emptyMsg) { emptyMsg.remove(); }
                    });
                }

                const closeRuanganDropdown = function(e) {
                    if (!container.contains(e.target)) {
                        menu.classList.remove('show');
                        container.classList.remove('open');
                    }
                };
                document.removeEventListener('click', closeRuanganDropdown);
                document.addEventListener('click', closeRuanganDropdown);
            }

            function renderKibDropdown() {
                const optionsList = document.getElementById('kib-dropdown-options-list');
                const trigger     = document.getElementById('kib-dropdown-trigger');
                const menu        = document.getElementById('kib-dropdown-menu');
                const container   = document.getElementById('kib-dropdown-container');
                const hiddenInput = document.getElementById('kib_type_hidden');
                const searchInput = document.getElementById('kib-dropdown-search');

                if (!optionsList || !kibOptions || kibOptions.length === 0) return;

                optionsList.innerHTML = kibOptions.map(k => `
                    <li class="dropdown-option-item ${k.disabled ? 'is-disabled' : ''}" 
                        data-id="${k.id}" 
                        data-search="${k.nama.toLowerCase()}"
                        data-disabled="${k.disabled}"
                        style="${k.disabled ? 'opacity:0.55;cursor:not-allowed;' : ''}">
                        <div class="dropdown-option-icon" style="background: ${k.gradient};">
                            <i data-feather="${k.icon}"></i>
                        </div>
                        <div class="dropdown-option-text" style="flex:1;">
                            <div class="dropdown-option-name" style="${k.disabled ? 'color:#9ca3af;' : ''}">${k.nama}</div>
                            <div class="dropdown-option-desc">${k.deskripsi}</div>
                        </div>
                        ${k.disabled ? '<span style="font-size:10px;font-weight:600;color:#6b7280;background:#e5e7eb;padding:3px 8px;border-radius:8px;white-space:nowrap;flex-shrink:0;">Segera Hadir</span>' : ''}
                    </li>
                `).join('');

                if (typeof feather !== 'undefined') try { feather.replace(); } catch(e) {}

                optionsList.querySelectorAll('.dropdown-option-item:not(.is-disabled)').forEach(item => {
                    item.addEventListener('click', function(e) {
                        e.stopPropagation();
                        if (this.dataset.disabled === 'true') return;
                        optionsList.querySelectorAll('.dropdown-option-item').forEach(o => o.classList.remove('selected'));
                        this.classList.add('selected');

                        const id = this.dataset.id;
                        hiddenInput.value = id;
                        selectedKib = id;

                        const sel = kibOptions.find(k => String(k.id) === String(id));
                        if (sel) {
                            document.getElementById('kib-selected-label').innerHTML = `
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <div style="width:24px;height:24px;border-radius:6px;background:${sel.gradient};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                        <i data-feather="${sel.icon}" style="width:12px;height:12px;color:#fff;stroke:#fff;"></i>
                                    </div>
                                    <span style="font-size:13px;font-weight:600;color:#1f2937;">${sel.nama}</span>
                                </div>`;
                            if (typeof feather !== 'undefined') try { feather.replace(); } catch(e) {}
                        }

                        updateUI(); updateUIForActiveTab(); loadDefaultPejabat(); updateDataCount();
                        menu.classList.remove('show');
                        container.classList.remove('open');
                    });
                });

                trigger.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const isOpen = menu.classList.contains('show');
                    document.querySelectorAll('.dropdown-menu-panel').forEach(m => { if (m !== menu) m.classList.remove('show'); });
                    document.querySelectorAll('.searchable-dropdown').forEach(c => { if (c !== container) c.classList.remove('open'); });
                    if (!isOpen) {
                        menu.classList.add('show');
                        container.classList.add('open');
                        if (searchInput) { searchInput.value = ''; searchInput.dispatchEvent(new Event('input')); setTimeout(() => searchInput.focus(), 50); }
                    } else {
                        menu.classList.remove('show');
                        container.classList.remove('open');
                    }
                });

                if (searchInput) {
                    searchInput.addEventListener('input', function() {
                        const q = this.value.toLowerCase().trim();
                        let hasVisible = false;
                        optionsList.querySelectorAll('.dropdown-option-item').forEach(item => {
                            const match = (item.dataset.search || '').includes(q);
                            item.style.display = match ? 'flex' : 'none';
                            if (match) hasVisible = true;
                        });
                        let emptyMsg = optionsList.querySelector('.dropdown-empty-search');
                        if (!hasVisible) {
                            if (!emptyMsg) {
                                emptyMsg = document.createElement('div');
                                emptyMsg.className = 'dropdown-empty-search';
                                emptyMsg.style.cssText = 'padding:16px;text-align:center;color:#9ca3af;font-size:12px;';
                                emptyMsg.textContent = 'Tidak ada KIB yang cocok.';
                                optionsList.appendChild(emptyMsg);
                            }
                        } else if (emptyMsg) { emptyMsg.remove(); }
                    });
                }

                const closeKibDropdown = function(e) {
                    if (!container.contains(e.target)) {
                        menu.classList.remove('show');
                        container.classList.remove('open');
                    }
                };
                document.removeEventListener('click', closeKibDropdown);
                document.addEventListener('click', closeKibDropdown);
            }

            function selectKib(kibType) {
                // Fungsi ini sekarang hanya sebagai fallback — dipanggil dari renderKibDropdown
                selectedKib = kibType;
                updateUI();
                updateUIForActiveTab();
                loadDefaultPejabat();
                updateDataCount();
            }

            const laporanPeriodeOptions = [
                {
                    id: 'tahunan',
                    nama: 'Per Tahun',
                    deskripsi: 'Saldo 1 Januari – 31 Desember',
                    icon: 'calendar',
                    gradient: 'linear-gradient(135deg,#8b5cf6,#7c3aed)'
                },
                {
                    id: 'semester_1',
                    nama: 'Semester I',
                    deskripsi: 'Saldo 1 Januari – 30 Juni',
                    icon: 'git-branch',
                    gradient: 'linear-gradient(135deg,#06b6d4,#0891b2)'
                },
                {
                    id: 'semester_2',
                    nama: 'Semester II',
                    deskripsi: 'Saldo 1 Juli – 31 Desember',
                    icon: 'git-commit',
                    gradient: 'linear-gradient(135deg,#eab308,#ca8a04)'
                }
            ];

            function renderLaporanDropdown() {
                const optionsList = document.getElementById('laporan-dropdown-options-list');
                const trigger     = document.getElementById('laporan-dropdown-trigger');
                const menu        = document.getElementById('laporan-dropdown-menu');
                const searchInput = document.getElementById('laporan-dropdown-search');
                const container   = document.getElementById('laporan-dropdown-container');
                const hiddenInput = document.getElementById('laporan_periode_hidden');

                if (!optionsList || !laporanPeriodeOptions || laporanPeriodeOptions.length === 0) return;

                optionsList.innerHTML = laporanPeriodeOptions.map(l => `
                    <li class="dropdown-option-item" data-id="${l.id}" data-search="${l.nama.toLowerCase()}">
                        <div class="dropdown-option-icon" style="background: ${l.gradient};">
                            <i data-feather="${l.icon}"></i>
                        </div>
                        <div class="dropdown-option-text">
                            <div class="dropdown-option-name">${l.nama}</div>
                            <div class="dropdown-option-desc">${l.deskripsi}</div>
                        </div>
                    </li>
                `).join('');

                if (typeof feather !== 'undefined') try { feather.replace(); } catch(e) {}

                // Default selection
                const defaultOption = laporanPeriodeOptions.find(o => o.id === hiddenInput.value);
                if (defaultOption) {
                    optionsList.querySelectorAll('.dropdown-option-item').forEach(o => {
                        if (o.dataset.id === defaultOption.id) o.classList.add('selected');
                    });
                    document.getElementById('laporan-selected-label').innerHTML = `
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:24px;height:24px;border-radius:6px;background:${defaultOption.gradient};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i data-feather="${defaultOption.icon}" style="width:12px;height:12px;color:#fff;stroke:#fff;"></i>
                            </div>
                            <span style="font-size:13px;font-weight:600;color:#1f2937;">${defaultOption.nama}</span>
                        </div>`;
                    if (typeof feather !== 'undefined') try { feather.replace(); } catch(e) {}
                }

                optionsList.querySelectorAll('.dropdown-option-item').forEach(item => {
                    item.addEventListener('click', function(e) {
                        e.stopPropagation();
                        optionsList.querySelectorAll('.dropdown-option-item').forEach(o => o.classList.remove('selected'));
                        this.classList.add('selected');

                        const id = this.dataset.id;
                        hiddenInput.value = id;

                        const sel = laporanPeriodeOptions.find(l => String(l.id) === String(id));
                        if (sel) {
                            document.getElementById('laporan-selected-label').innerHTML = `
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <div style="width:24px;height:24px;border-radius:6px;background:${sel.gradient};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                        <i data-feather="${sel.icon}" style="width:12px;height:12px;color:#fff;stroke:#fff;"></i>
                                    </div>
                                    <span style="font-size:13px;font-weight:600;color:#1f2937;">${sel.nama}</span>
                                </div>`;
                            if (typeof feather !== 'undefined') try { feather.replace(); } catch(e) {}
                        }

                        updateUI(); updateUIForActiveTab(); loadDefaultPejabat(); updateDataCount();
                        menu.classList.remove('show');
                        container.classList.remove('open');
                    });
                });

                trigger.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const isOpen = menu.classList.contains('show');
                    document.querySelectorAll('.dropdown-menu-panel').forEach(m => { if (m !== menu) m.classList.remove('show'); });
                    document.querySelectorAll('.searchable-dropdown').forEach(c => { if (c !== container) c.classList.remove('open'); });
                    if (!isOpen) {
                        menu.classList.add('show');
                        container.classList.add('open');
                        if (searchInput) { searchInput.value = ''; searchInput.dispatchEvent(new Event('input')); setTimeout(() => searchInput.focus(), 50); }
                    } else {
                        menu.classList.remove('show');
                        container.classList.remove('open');
                    }
                });

                if (searchInput) {
                    searchInput.addEventListener('input', function() {
                        const q = this.value.toLowerCase().trim();
                        let hasVisible = false;
                        optionsList.querySelectorAll('.dropdown-option-item').forEach(item => {
                            const match = (item.dataset.search || '').includes(q);
                            item.style.display = match ? 'flex' : 'none';
                            if (match) hasVisible = true;
                        });
                        let emptyMsg = optionsList.querySelector('.dropdown-empty-search');
                        if (!hasVisible) {
                            if (!emptyMsg) {
                                emptyMsg = document.createElement('div');
                                emptyMsg.className = 'dropdown-empty-search';
                                emptyMsg.style.cssText = 'padding:16px;text-align:center;color:#9ca3af;font-size:12px;';
                                emptyMsg.textContent = 'Tidak ada periode yang cocok.';
                                optionsList.appendChild(emptyMsg);
                            }
                        } else if (emptyMsg) { emptyMsg.remove(); }
                    });
                }

                const closeLaporanDropdown = function(e) {
                    if (!container.contains(e.target)) {
                        menu.classList.remove('show');
                        container.classList.remove('open');
                    }
                };
                document.removeEventListener('click', closeLaporanDropdown);
                document.addEventListener('click', closeLaporanDropdown);
            }

            function selectLaporanPeriode(periode) {
                const hiddenInput = document.getElementById('laporan_periode_hidden');
                if (hiddenInput) {
                    hiddenInput.value = periode;
                }
                const sel = laporanPeriodeOptions.find(o => o.id === periode);
                if (sel) {
                    const labelEl = document.getElementById('laporan-selected-label');
                    if (labelEl) {
                        labelEl.innerHTML = `
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="width:24px;height:24px;border-radius:6px;background:${sel.gradient};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i data-feather="${sel.icon}" style="width:12px;height:12px;color:#fff;stroke:#fff;"></i>
                                </div>
                                <span style="font-size:13px;font-weight:600;color:#1f2937;">${sel.nama}</span>
                            </div>`;
                        if (typeof feather !== 'undefined') try { feather.replace(); } catch(e) {}
                    }
                }
                const optionsList = document.getElementById('laporan-dropdown-options-list');
                if (optionsList) {
                    optionsList.querySelectorAll('.dropdown-option-item').forEach(o => {
                        if (o.dataset.id === periode) {
                            o.classList.add('selected');
                        } else {
                            o.classList.remove('selected');
                        }
                    });
                }
                updateDataCount();
                updateUI();
            }

            function switchExportTab(tab) {
                activeExportTab = tab;

                // Reset filter tahun ke default (tahun sekarang) setiap kali pindah tab
                const defaultTahun = '{{ date("Y") }}';
                selectedTahun = defaultTahun;
                const filterTahunInput = document.getElementById('filter-tahun');
                if (filterTahunInput) {
                    filterTahunInput.value = defaultTahun;
                    if (filterTahunInput._flatpickr) {
                        filterTahunInput._flatpickr.setDate(defaultTahun + '-01-01', false);
                    }
                }

                // Tampilkan section pilih jenis/ruangan dan TTD
                const sectionPilih = document.getElementById('section-pilih-jenis');
                const sectionTtd   = document.getElementById('section-ttd-setup');
                const sectionDokumen = document.getElementById('section-dokumen-info');
                if (sectionPilih) sectionPilih.style.display = '';
                // Selalu tampilkan section TTD untuk semua tab
                if (sectionTtd)   sectionTtd.style.display   = '';
                // Section Dokumen Info hanya untuk tab 'jenis'
                if (sectionDokumen) {
                    sectionDokumen.style.display = (tab === 'jenis') ? '' : 'none';
                }

                // Untuk laporan aset: sembunyikan subsection pejabat, tampilkan hanya filter tahun
                const subsecPejabatDefault = document.getElementById('ttd-subsec-pejabat-default');
                const subsecPejabatCustom  = document.getElementById('ttd-subsec-pejabat-custom');
                const subsecTtdOptions     = document.getElementById('ttd-subsec-ttd-options');
                const subsecDocDate        = document.getElementById('ttd-subsec-doc-date');
                if (tab === 'laporan') {
                    if (subsecPejabatDefault) subsecPejabatDefault.style.display = 'none';
                    if (subsecPejabatCustom)  subsecPejabatCustom.style.display  = 'none';
                    if (subsecTtdOptions)     subsecTtdOptions.style.display     = 'none';
                    if (subsecDocDate)        subsecDocDate.style.display        = 'none';
                } else {
                    if (subsecPejabatDefault) subsecPejabatDefault.style.display = '';
                    if (subsecPejabatCustom)  subsecPejabatCustom.style.display  = '';
                    if (subsecTtdOptions)     subsecTtdOptions.style.display     = '';
                    if (subsecDocDate)        subsecDocDate.style.display        = '';
                }

                // Show/hide konten di dalam section yang sama
                const contentJenis   = document.getElementById('content-jenis');
                const contentRuangan = document.getElementById('content-ruangan');
                const contentKib     = document.getElementById('content-kib');
                const contentLaporan = document.getElementById('content-laporan');
                if (contentJenis)   contentJenis.style.display   = tab === 'jenis'   ? '' : 'none';
                if (contentRuangan) contentRuangan.style.display = tab === 'ruangan' ? '' : 'none';
                if (contentKib)     contentKib.style.display     = tab === 'kib'     ? '' : 'none';
                if (contentLaporan) contentLaporan.style.display = tab === 'laporan' ? '' : 'none';

                // Update header section title
                const sectionTitle = document.querySelector('#section-pilih-jenis .section-header-title h5');
                const sectionSub   = document.querySelector('#section-pilih-jenis .section-header-title span');
                const titles = { jenis: 'Pilih Jenis Inventaris', ruangan: 'Pilih Ruangan', kib: 'Pilih KIB', laporan: 'Pilih Periode Laporan' };
                const subs   = { jenis: 'Pilih jenis inventaris yang akan diekspor', ruangan: 'Pilih ruangan yang akan diekspor', kib: 'Pilih Buku Inventaris Aset Desa yang akan diekspor', laporan: 'Pilih tahun dan periode untuk laporan aset' };
                if (sectionTitle) sectionTitle.textContent = titles[tab] || titles.jenis;
                if (sectionSub)   sectionSub.textContent   = subs[tab]   || subs.jenis;

                // Update icon & warna section header - selalu netral (list + gelap), tidak sama dengan option card
                const sectionIcon = document.getElementById('section-pilih-icon');
                const sectionIconEl = document.getElementById('section-pilih-icon-el');
                if (sectionIcon) {
                    sectionIcon.style.background = 'linear-gradient(135deg, #519259 0%, #3f7d47 100%)';
                }
                if (sectionIconEl) {
                    sectionIconEl.setAttribute('data-feather', 'list');
                }

                // Reset selection saat pindah tab
                if (tab === 'jenis') {
                    selectedRuangan = null;
                    selectedKib = null;
                    selectedJenisId = null;
                    document.getElementById('jenis_inventaris_id_hidden').value = '';
                    document.getElementById('jenis-selected-label').innerHTML = '<span style="color:#9ca3af;font-size:13px;font-weight:500;">-- Pilih Jenis Inventaris --</span>';
                    // ruangan-selected radio removed (now using dropdown)
                } else if (tab === 'ruangan') {
                    selectedJenisId = null;
                    selectedKib = null;
                    selectedRuangan = null;
                    document.getElementById('jenis_inventaris_id_hidden').value = '';
                    document.getElementById('jenis-selected-label').innerHTML = '<span style="color:#9ca3af;font-size:13px;font-weight:500;">-- Pilih Jenis Inventaris --</span>';
                    const rHidden = document.getElementById('ruangan_id_hidden');
                    const rLabel  = document.getElementById('ruangan-selected-label');
                    if (rHidden) rHidden.value = '';
                    if (rLabel)  rLabel.innerHTML = '<span style="color:#9ca3af;font-size:13px;font-weight:500;">-- Pilih Ruangan --</span>';
                    document.querySelectorAll('#ruangan-dropdown-options-list .dropdown-option-item').forEach(o => o.classList.remove('selected'));
                    // Sembunyikan format KIR saat ruangan di-reset
                    const kfs = document.getElementById('kir-format-section');
                    if (kfs) kfs.style.display = 'none';
                } else if (tab === 'kib') {
                    selectedJenisId = null;
                    selectedRuangan = null;
                    selectedKib = null;
                    document.getElementById('jenis_inventaris_id_hidden').value = '';
                    document.getElementById('jenis-selected-label').innerHTML = '<span style="color:#9ca3af;font-size:13px;font-weight:500;">-- Pilih Jenis Inventaris --</span>';
                    const kHidden = document.getElementById('kib_type_hidden');
                    const kLabel  = document.getElementById('kib-selected-label');
                    if (kHidden) kHidden.value = '';
                    if (kLabel)  kLabel.innerHTML = '<span style="color:#9ca3af;font-size:13px;font-weight:500;">-- Pilih KIB --</span>';
                    document.querySelectorAll('#kib-dropdown-options-list .dropdown-option-item').forEach(o => o.classList.remove('selected'));
                } else if (tab === 'laporan') {
                    selectedJenisId = null;
                    selectedRuangan = null;
                    selectedKib = null;
                    document.getElementById('jenis_inventaris_id_hidden').value = '';
                    document.getElementById('jenis-selected-label').innerHTML = '<span style="color:#9ca3af;font-size:13px;font-weight:500;">-- Pilih Jenis Inventaris --</span>';
                    // ruangan-selected radio removed (now using dropdown)
                }

                // Scroll ke section pilih data (bukan ke TTD)
                if (sectionPilih) {
                    setTimeout(() => {
                        sectionPilih.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }, 350);
                }

                updateUI();
                loadDefaultPejabat();
                // Update label Pejabat Custom sesuai tab
                updateCustomPejabatLabels();
                // Reset pilihan penandatangan saat pindah tab (konteks berbeda)
                customPejabat = [];
                customPejabatOrder = [];
                renderCustomPejabatList();
                if (typeof feather !== 'undefined') feather.replace();
            }

            function updateUIForActiveTab() {
                const previewBtn = document.getElementById('btn-preview-export');
                if (activeExportTab === 'ruangan') {
                    previewBtn.disabled = !selectedRuangan;
                    previewBtn.innerHTML = '<i data-feather="eye" style="width:16px;height:16px;"></i> Preview Export Ruangan';
                    const ruanganLabel = selectedRuangan === 'all'
                        ? 'Semua Ruangan'
                        : (selectedRuangan ? (ruanganOptions.find(r => String(r.id) === String(selectedRuangan))?.nama || '-') : 'Pilih ruangan');
                    document.getElementById('info-jenis').textContent = 'Ruangan: ' + ruanganLabel;
                    if (typeof feather !== 'undefined') feather.replace();
                } else if (activeExportTab === 'kib') {
                    previewBtn.disabled = !selectedKib;
                    previewBtn.innerHTML = '<i data-feather="eye" style="width:16px;height:16px;"></i> Preview Export KIB';
                    const kibLabel = selectedKib === 'all' ? 'Semua KIB' : (selectedKib ? 'KIB ' + selectedKib : 'Pilih KIB');
                    document.getElementById('info-jenis').textContent = kibLabel;
                    if (typeof feather !== 'undefined') feather.replace();
                } else if (activeExportTab === 'laporan') {
                    previewBtn.disabled = false;
                    previewBtn.innerHTML = '<i data-feather="eye" style="width:16px;height:16px;"></i> Preview Laporan Aset';
                    const tahunLaporan = selectedTahun;
                    const periode = document.getElementById('laporan_periode_hidden')?.value || 'tahunan';
                    const selP = laporanPeriodeOptions.find(o => o.id === periode);
                    const periodeLabel = selP ? selP.nama : 'Per Tahun';
                    document.getElementById('info-jenis').textContent = 'Laporan Aset ' + periodeLabel + ' ' + tahunLaporan;
                    if (typeof feather !== 'undefined') feather.replace();
                } else {
                    previewBtn.disabled = !selectedJenisId;
                    previewBtn.innerHTML = '<i data-feather="eye" style="width:16px;height:16px;"></i> Preview Export';
                    if (typeof feather !== 'undefined') feather.replace();
                }
            }

            // Ruangan selection handler (replaced with dropdown)

            // ===== DATA DARI BACKEND =====
            const jenisData = {
                @foreach($jenisInventaris as $jenis)
                @if($jenis->id !== 'orphan_inventaris')
                @php
                    $fieldsForJs = $jenis->fields->sortBy('order')->filter(function($field) {
                        $fixedName = $field->fixed_field_name ?? null;
                        $namaField = $field->nama_field ?? null;
                        if ($fixedName === 'jenis_barang' || $namaField === 'jenis_barang') return false;
                        if ($field->include_in_export === false) return false;
                        return true;
                    })->values()->map(function($field) {
                        return [
                            'label' => ($field->fixed_field_name === 'nama_barang') ? 'Jenis Barang / Nama Barang' : ($field->label_field ?? ''),
                            'type'  => $field->field_type ?? '',
                            'fixed' => $field->fixed_field_name ?? '',
                            'order' => $field->order ?? 999,
                        ];
                    })->values()->toArray();
                @endphp
                {{ $jenis->id }}: { 
                    nama: '{{ $jenis->nama }}', 
                    count: {{ $jenis->inventaris_count }},
                    fields: {!! json_encode($fieldsForJs) !!},
                    default_pejabat: [
                        @if($jenis->pejabatDefaults)
                        @foreach($jenis->pejabatDefaults as $pejDefault)
                        {{ $pejDefault->pejabat_id }}{{ !$loop->last ? ',' : '' }}
                        @endforeach
                        @endif
                    ] 
                }{{ !$loop->last ? ',' : '' }}
                @else
                {{-- Orphan jenis (Inventaris yang tidak ditemukan) --}}
                'orphan_inventaris': { 
                    nama: '{{ $jenis->nama }}', 
                    count: {{ $jenis->inventaris_count }},
                    fields: [],
                    default_pejabat: [],
                    is_orphan: true
                }{{ !$loop->last ? ',' : '' }}
                @endif
                @endforeach

                {{-- Virtual Jenis data --}}
                @foreach($virtualJenis as $vJenis)
                ,'{{ $vJenis['id'] }}':{
                    nama:'{{ $vJenis['nama'] }}',
                    count:0,
                    fields:[],
                    default_pejabat:[],
                    is_virtual:true
                }{{ !$loop->last ? ',' : '' }}
                @endforeach
            };

            const pejabatData = [
                @foreach($pejabats as $pejabat)
                { 
                    id: {{ $pejabat->id }}, 
                    nama: '{{ addslashes($pejabat->nama) }}', 
                    jabatan: '{{ addslashes($pejabat->jabatan) }}' 
                }{{ !$loop->last ? ',' : '' }}
                @endforeach
            ];

            // ID pejabat yang "tetap" (tidak boleh dipilih user) per konteks export
            const fixedPejabatIds = {!! json_encode($fixedPejabatIds) !!};

            let selectedTahun = '{{ date("Y") }}';
            let customPejabat = [];
            let customPejabatOrder = [];
            let currentDataCount = 0; // cached from AJAX

            const dateInput = document.getElementById('doc-tanggal');
            const sectionCard = document.getElementById('section-ttd-setup');
            const exportOptionsCard = document.getElementById('export-options-card');
            const customPejabatBtn = document.getElementById('btn-custom-pejabat');
            const tahunFilterInput = document.getElementById('filter-tahun');
            const docSubsection = document.getElementById('ttd-subsection-dokumen');
            const jenisFlowHelper = document.getElementById('jenis-flow-helper');
            let docDatePicker = null;

            // Set default date
            function getTodayYmd() {
                const now = new Date();
                const year = now.getFullYear();
                const month = String(now.getMonth() + 1).padStart(2, '0');
                const day = String(now.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            }

            // ===== MODERN DATE PICKER ENHANCEMENT =====
            const _BULAN_SHORT_EXP = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'];

            function hideExpMonthRows(instance) {
                var cal = instance && instance.calendarContainer;
                if (!cal) return;
                var days = cal.querySelectorAll('.flatpickr-days .flatpickr-day');
                if (!days.length) return;
                for (var i = 0; i < days.length; i += 7) {
                    var allHidden = true;
                    for (var j = i; j < i + 7 && j < days.length; j++) {
                        if (!days[j].classList.contains('prevMonthDay') && !days[j].classList.contains('nextMonthDay')) {
                            allHidden = false; break;
                        }
                    }
                    for (var j = i; j < i + 7 && j < days.length; j++) {
                        var st = days[j].style;
                        st.height = allHidden ? '0' : ''; st.overflow = allHidden ? 'hidden' : '';
                        st.padding = allHidden ? '0' : ''; st.margin = allHidden ? '0' : '';
                        st.minHeight = allHidden ? '0' : ''; st.maxHeight = allHidden ? '0' : '';
                        st.lineHeight = allHidden ? '0' : '';
                    }
                }
            }

            function enhanceExportDateCal(instance) {
                var cal = instance.calendarContainer;
                if (!cal || cal._expModernBuilt) return;
                cal._expModernBuilt = true;
                cal.classList.add('date-clean-picker');

                var minYear = 2000, maxYear = new Date().getFullYear() + 1;

                var monthPanel = document.createElement('div');
                monthPanel.className = 'fp-exp-month-panel';
                monthPanel.innerHTML = '<div class="fp-exp-panel-header"><span class="fp-exp-panel-title">Pilih Bulan</span><button type="button" class="fp-exp-panel-close">&#x2715;</button></div><div class="fp-exp-month-grid">' +
                    _BULAN_SHORT_EXP.map(function(m, i){ return '<button type="button" class="fp-exp-month-btn" data-month="'+i+'">'+m+'</button>'; }).join('') + '</div>';
                cal.appendChild(monthPanel);

                var yearPanel = document.createElement('div');
                yearPanel.className = 'fp-exp-year-panel';
                yearPanel._pageStart = Math.max(minYear, maxYear - 11);
                yearPanel.innerHTML = '<div class="fp-exp-panel-header"><button type="button" class="fp-exp-panel-nav" data-nav="prev">&#10094;</button><span class="fp-exp-panel-title fp-exp-year-range"></span><button type="button" class="fp-exp-panel-nav" data-nav="next">&#10095;</button></div><div class="fp-exp-year-grid"></div>';
                cal.appendChild(yearPanel);

                function renderYearGrid() {
                    var ps = yearPanel._pageStart, end = ps + 11;
                    yearPanel.querySelector('.fp-exp-year-range').textContent = ps + ' - ' + end;
                    yearPanel.querySelector('[data-nav="prev"]').disabled = ps <= minYear;
                    yearPanel.querySelector('[data-nav="next"]').disabled = ps + 12 > maxYear;
                    var h = '';
                    for (var y = ps; y <= end; y++) h += '<button type="button" class="fp-exp-year-btn'+(y === instance.currentYear ? ' active' : '')+'" data-year="'+y+'">'+y+'</button>';
                    yearPanel.querySelector('.fp-exp-year-grid').innerHTML = h;
                }

                monthPanel.querySelector('.fp-exp-panel-close').addEventListener('click', function(){ monthPanel.classList.remove('show'); });
                monthPanel.querySelector('.fp-exp-month-grid').addEventListener('click', function(e){
                    var btn = e.target.closest('.fp-exp-month-btn'); if (!btn) return;
                    instance.changeMonth(parseInt(btn.dataset.month, 10) - instance.currentMonth);
                    monthPanel.classList.remove('show');
                });
                yearPanel.querySelector('.fp-exp-panel-header').addEventListener('click', function(e){
                    var nav = e.target.closest('.fp-exp-panel-nav'); if (!nav) return;
                    if (nav.dataset.nav === 'prev') yearPanel._pageStart = Math.max(minYear, yearPanel._pageStart - 12);
                    else yearPanel._pageStart = Math.min(Math.max(minYear, maxYear - 11), yearPanel._pageStart + 12);
                    renderYearGrid();
                });
                yearPanel.querySelector('.fp-exp-year-grid').addEventListener('click', function(e){
                    var btn = e.target.closest('.fp-exp-year-btn'); if (!btn) return;
                    instance.changeYear(parseInt(btn.dataset.year, 10));
                    yearPanel.classList.remove('show');
                });

                var curMonthEl = cal.querySelector('.flatpickr-current-month');
                if (curMonthEl) {
                    var curMonthSpan = curMonthEl.querySelector('.cur-month');
                    if (curMonthSpan) {
                        curMonthSpan.style.cursor = 'pointer'; curMonthSpan.style.borderRadius = '6px'; curMonthSpan.style.padding = '2px 6px';
                        curMonthSpan.addEventListener('click', function(e){
                            e.stopPropagation();
                            monthPanel.querySelectorAll('.fp-exp-month-btn').forEach(function(b){ b.classList.toggle('active', parseInt(b.dataset.month,10) === instance.currentMonth); });
                            monthPanel.classList.add('show');
                        });
                    }
                    var numWrapper = curMonthEl.querySelector('.numInputWrapper');
                    var yearInput = curMonthEl.querySelector('input.cur-year');
                    if (yearInput) {
                        var yearSpan = document.createElement('span');
                        yearSpan.className = 'fp-cur-year';
                        yearSpan.textContent = instance.currentYear;
                        yearSpan.addEventListener('click', function(e){
                            e.stopPropagation();
                            yearPanel._pageStart = Math.max(minYear, instance.currentYear - 5);
                            renderYearGrid(); yearPanel.classList.add('show');
                        });
                        curMonthEl.appendChild(yearSpan);
                        if (numWrapper) numWrapper.style.display = 'none';
                        cal.addEventListener('click', function(){ setTimeout(function(){ if(yearSpan) yearSpan.textContent = instance.currentYear; }, 0); });
                    }
                }
            }

            function initDocumentDatePicker() {
                const defaultDate = getTodayYmd();
                const minAllowedYear = 2000;

                function getCurrentYear() {
                    return new Date().getFullYear();
                }

                function clampYearValue(year) {
                    const numericYear = Number.parseInt(String(year), 10);
                    if (!Number.isFinite(numericYear)) return getCurrentYear();
                    return Math.min(Math.max(numericYear, minAllowedYear), getCurrentYear());
                }

                function bindYearInputGuards(instance) {
                    const yearInput = instance.calendarContainer
                        ? instance.calendarContainer.querySelector('.cur-year')
                        : null;

                    if (!yearInput || yearInput.dataset.yearGuardBound === '1') return;

                    yearInput.dataset.yearGuardBound = '1';
                    yearInput.setAttribute('inputmode', 'numeric');
                    yearInput.setAttribute('maxlength', '4');

                    yearInput.addEventListener('input', function() {
                        const digitsOnly = this.value.replace(/\D/g, '').slice(0, 4);
                        if (this.value !== digitsOnly) {
                            this.value = digitsOnly;
                        }
                    });

                    const applyYear = () => {
                        const normalizedYear = clampYearValue(yearInput.value);
                        yearInput.value = String(normalizedYear);
                        instance.changeYear(normalizedYear);
                    };

                    yearInput.addEventListener('blur', applyYear);
                    yearInput.addEventListener('keydown', function(ev) {
                        if (ev.key !== 'Enter') return;
                        ev.preventDefault();
                        applyYear();
                    });
                }

                // Fallback jika CDN gagal dimuat: tetap pakai input teks tanggal ISO.
                if (typeof flatpickr === 'undefined') {
                    dateInput.value = defaultDate;
                    return;
                }

                docDatePicker = flatpickr(dateInput, {
                    dateFormat: 'Y-m-d',
                    defaultDate,
                    locale: (flatpickr.l10ns && flatpickr.l10ns.id) ? flatpickr.l10ns.id : 'default',
                    allowInput: true,
                    disableMobile: true,
                    minDate: `${minAllowedYear}-01-01`,
                    maxDate: `${getCurrentYear()}-12-31`,
                    position: 'above left',
                    monthSelectorType: 'static',
                    shorthandCurrentMonth: false,
                    onReady: function(_, __, instance) {
                        bindYearInputGuards(instance);
                        try { enhanceExportDateCal(instance); } catch(e) {}
                        setTimeout(function(){ hideExpMonthRows(instance); }, 0);

                        const daysContainer = instance.calendarContainer
                            ? instance.calendarContainer.querySelector('.flatpickr-days')
                            : null;

                        if (daysContainer) {
                            daysContainer.addEventListener('wheel', function(ev) {
                                if (!instance.isOpen) return;
                                ev.preventDefault();
                                instance.changeMonth(ev.deltaY > 0 ? 1 : -1);
                            }, { passive: false });
                        }
                    },
                    onOpen: function(_, __, instance) {
                        bindYearInputGuards(instance);
                        try { enhanceExportDateCal(instance); } catch(e) {}
                        setTimeout(function(){ hideExpMonthRows(instance); }, 0);
                    },
                    onMonthChange: function(_, __, instance) { setTimeout(function(){ hideExpMonthRows(instance); }, 0); },
                    onYearChange: function(_, __, instance) { setTimeout(function(){ hideExpMonthRows(instance); }, 0); },
                });
            }

            initDocumentDatePicker();
            updateDatePreview();

            // ===== DOCUMENT INFO (LAMPIRAN & BA) =====
            let docBaDatePicker = null;

            // ===== LOCALSTORAGE HELPER UNTUK INFO DOKUMEN =====
            const DOC_STORAGE_KEY = 'sima_doc_info_last';

            function docStorageLoad() {
                try {
                    const raw = localStorage.getItem(DOC_STORAGE_KEY);
                    return raw ? JSON.parse(raw) : null;
                } catch(e) { return null; }
            }

            function docStorageSave(lampiran, baNomor, baTanggal) {
                try {
                    const current = docStorageLoad();
                    // Hanya simpan jika ada perubahan
                    if (current &&
                        current.lampiran === lampiran &&
                        current.baNomor === baNomor &&
                        current.baTanggal === baTanggal) return;
                    localStorage.setItem(DOC_STORAGE_KEY, JSON.stringify({ lampiran, baNomor, baTanggal }));
                } catch(e) {}
            }

            function docStorageFill() {
                const saved = docStorageLoad();
                if (!saved) return;
                const lampInput   = document.getElementById('doc-lampiran');
                const baNumInput  = document.getElementById('doc-ba-nomor');
                const baDateInput = document.getElementById('doc-ba-tanggal');
                if (lampInput   && !lampInput.value   && saved.lampiran)  lampInput.value  = saved.lampiran;
                if (baNumInput  && !baNumInput.value  && saved.baNomor)   baNumInput.value = saved.baNomor;
                if (baDateInput && !baDateInput.value && saved.baTanggal) {
                    baDateInput.value = saved.baTanggal;
                    if (docBaDatePicker) docBaDatePicker.setDate(saved.baTanggal, false);
                }
            }

            function initDocumentInfoFeature() {
                const lampInput = document.getElementById('doc-lampiran');
                const baNumInput = document.getElementById('doc-ba-nomor');
                const baDateInput = document.getElementById('doc-ba-tanggal');
                const previewLamp = document.getElementById('preview-lampiran');
                const previewNum = document.getElementById('preview-ba-nomor');
                const previewDate = document.getElementById('preview-ba-tanggal');

                // Update preview realtime
                function updateDocPreview() {
                    const lampVal = (lampInput && lampInput.value.trim()) ? lampInput.value.trim() : '-';
                    const numVal = (baNumInput && baNumInput.value.trim()) ? baNumInput.value.trim() : '-';
                    const dateVal = (baDateInput && baDateInput.value) ? formatDateIndo(baDateInput.value) : '-';

                    if (previewLamp) previewLamp.textContent = lampVal;
                    if (previewNum) previewNum.textContent = numVal;
                    if (previewDate) previewDate.textContent = dateVal;
                }

                // Saat user kosongkan field lalu blur → restore dari storage
                function restoreIfEmpty(input, savedKey) {
                    input.addEventListener('blur', function() {
                        if (this.value.trim() === '') {
                            const saved = docStorageLoad();
                            if (saved && saved[savedKey]) {
                                this.value = saved[savedKey];
                                updateDocPreview();
                            }
                        }
                    });
                }

                if (lampInput) {
                    lampInput.addEventListener('input', updateDocPreview);
                    restoreIfEmpty(lampInput, 'lampiran');
                }
                if (baNumInput) {
                    baNumInput.addEventListener('input', updateDocPreview);
                    restoreIfEmpty(baNumInput, 'baNomor');
                }

                // Initialize BA date picker
                if (baDateInput && typeof flatpickr !== 'undefined') {
                    docBaDatePicker = flatpickr(baDateInput, {
                        dateFormat: 'Y-m-d',
                        defaultDate: 'today',
                        locale: (flatpickr.l10ns && flatpickr.l10ns.id) ? flatpickr.l10ns.id : 'default',
                        allowInput: true,
                        disableMobile: true,
                        minDate: '2000-01-01',
                        maxDate: new Date().getFullYear() + '-12-31',
                        position: 'auto',
                        onChange: function() {
                            updateDocPreview();
                        },
                        onReady: function(selectedDates, dateStr, instance) {
                            // Pre-fill dari storage jika ada, fallback ke today
                            const saved = docStorageLoad();
                            if (saved && saved.baTanggal) {
                                instance.setDate(saved.baTanggal, false);
                            } else if (!baDateInput.value) {
                                instance.setDate('today', false);
                            }
                            updateDocPreview();
                            try { enhanceExportDateCal(instance); } catch(e) {}
                            setTimeout(function(){ hideExpMonthRows(instance); }, 0);
                        },
                        onOpen: function(_, __, instance) {
                            try { enhanceExportDateCal(instance); } catch(e) {}
                            setTimeout(function(){ hideExpMonthRows(instance); }, 0);
                        },
                        onMonthChange: function(_, __, instance) { setTimeout(function(){ hideExpMonthRows(instance); }, 0); },
                        onYearChange: function(_, __, instance) { setTimeout(function(){ hideExpMonthRows(instance); }, 0); },
                    });
                }

                // Pre-fill field teks dari storage
                const saved = docStorageLoad();
                if (saved) {
                    if (lampInput  && !lampInput.value  && saved.lampiran) lampInput.value  = saved.lampiran;
                    if (baNumInput && !baNumInput.value && saved.baNomor)  baNumInput.value = saved.baNomor;
                    // tanggal dihandle di onReady flatpickr
                }

                // Initial preview
                updateDocPreview();
            }

            function formatDateIndo(ymd) {
                if (!ymd) return '-';
                const months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                const parts = ymd.split('-');
                if (parts.length !== 3) return ymd;
                const year = parts[0];
                const month = parseInt(parts[1], 10);
                const day = parseInt(parts[2], 10);
                return `${day} ${months[month-1]} ${year}`;
            }

            initDocumentInfoFeature();

            // -- Year Picker untuk filter-tahun ------------------------------
            (function initExportYearPicker() {
                const input = document.getElementById('filter-tahun');
                if (!input || typeof flatpickr === 'undefined') return;

                const minYear = 2000;
                const maxYear = new Date().getFullYear() + 1;

                function ensureYearPanel(instance) {
                    const cal = instance.calendarContainer;
                    if (!cal) return;
                    cal.classList.add('year-only-picker');

                    if (cal._exportYearState) {
                        renderExportYearGrid(instance, cal._exportYearState, minYear, maxYear);
                        return;
                    }

                    const panel = document.createElement('div');
                    panel.className = 'year-picker-panel';
                    panel.innerHTML = `
                        <div class="year-picker-header">
                            <button type="button" class="year-picker-nav" data-nav="prev">&lsaquo;</button>
                            <span class="year-picker-range"></span>
                            <button type="button" class="year-picker-nav" data-nav="next">&rsaquo;</button>
                        </div>
                        <div class="year-picker-grid"></div>
                    `;
                    cal.appendChild(panel);

                    const state = {
                        panel,
                        range: panel.querySelector('.year-picker-range'),
                        grid:  panel.querySelector('.year-picker-grid'),
                        prev:  panel.querySelector('[data-nav="prev"]'),
                        next:  panel.querySelector('[data-nav="next"]'),
                        pageStart: Math.max(minYear, maxYear - 11),
                        selectedYear: parseInt(input.value) || new Date().getFullYear(),
                    };
                    cal._exportYearState = state;

                    state.prev.addEventListener('click', function(ev) {
                        ev.stopPropagation();
                        state.pageStart = Math.max(minYear, state.pageStart - 12);
                        renderExportYearGrid(instance, state, minYear, maxYear);
                    });
                    state.next.addEventListener('click', function(ev) {
                        ev.stopPropagation();
                        const maxStart = Math.max(minYear, maxYear - 11);
                        state.pageStart = Math.min(maxStart, state.pageStart + 12);
                        renderExportYearGrid(instance, state, minYear, maxYear);
                    });
                    state.grid.addEventListener('click', function(ev) {
                        const btn = ev.target.closest('.year-picker-option');
                        if (!btn) return;
                        const year = parseInt(btn.dataset.year, 10);
                        state.selectedYear = year;
                        input.value = year;
                        instance.close();
                        // Trigger update
                        selectedTahun = year;
                        updateUI();
                        updateDataCount();
                        renderExportYearGrid(instance, state, minYear, maxYear);
                    });

                    renderExportYearGrid(instance, state, minYear, maxYear);
                }

                function renderExportYearGrid(instance, state, minYear, maxYear) {
                    const start = state.pageStart;
                    const end   = Math.min(start + 11, maxYear);
                    state.range.textContent = start + ' - ' + end;
                    state.prev.disabled = start <= minYear;
                    state.next.disabled = end >= maxYear;
                    let html = '';
                    for (let y = start; y <= end; y++) {
                        const active = y === state.selectedYear ? ' active' : '';
                        html += `<button type="button" class="year-picker-option${active}" data-year="${y}">${y}</button>`;
                    }
                    state.grid.innerHTML = html;
                }

                flatpickr(input, {
                    dateFormat: 'Y',
                    defaultDate: input.value ? input.value + '-01-01' : null,
                    disableMobile: true,
                    allowInput: false,
                    onReady: function(_, __, instance) { ensureYearPanel(instance); },
                    onOpen:  function(_, __, instance) { ensureYearPanel(instance); },
                });

                // Sync manual input
                input.addEventListener('change', function() {
                    const y = parseInt(this.value);
                    if (y >= minYear && y <= maxYear) {
                        selectedTahun = y;
                        updateUI();
                        updateDataCount();
                    }
                });
            })();

            function canAccessDocumentOptions() {
                // Tab KIB: tanggal dokumen aktif jika KIB sudah dipilih
                if (activeExportTab === 'kib') return !!selectedKib;

                // Tab ruangan: tanggal dokumen aktif, default hari ini, bisa diubah
                if (activeExportTab === 'ruangan') return !!selectedRuangan;

                if (!selectedJenisId) return false;
                // Tanggal dokumen aktif untuk semua jenis inventaris yang sudah dipilih
                return true;
            }

            function setFlowLockedState(isLocked) {
                const docOptionsLocked = isLocked || !canAccessDocumentOptions();

                if (exportOptionsCard) {
                    exportOptionsCard.classList.toggle('is-locked', isLocked);
                }

                if (jenisFlowHelper) {
                    jenisFlowHelper.style.display = 'flex';
                }

                if (sectionCard) {
                    sectionCard.classList.toggle('is-locked', isLocked);
                }

                if (tahunFilterInput) {
                    tahunFilterInput.disabled = isLocked;
                }

                if (dateInput) {
                    dateInput.disabled = docOptionsLocked;
                }

                if (docSubsection) {
                    docSubsection.classList.toggle('is-doc-disabled', docOptionsLocked);
                }

                if (docOptionsLocked && docDatePicker && typeof docDatePicker.close === 'function') {
                    docDatePicker.close();
                }

                if (customPejabatBtn) {
                    customPejabatBtn.disabled = isLocked;
                }

                document.querySelectorAll('input[name="export-format"], input[name="signature-option"]').forEach(input => {
                    input.disabled = isLocked;
                });
            }

            // Trigger kalender juga saat klik field (bukan hanya icon)
            dateInput.addEventListener('click', function() {
                const hasSelection = activeExportTab === 'ruangan' ? !!selectedRuangan : (activeExportTab === 'kib' ? !!selectedKib : !!selectedJenisId);
                if (!hasSelection || this.disabled) return;
                this.focus();
                if (docDatePicker && typeof docDatePicker.open === 'function') {
                    docDatePicker.open();
                }
            });
            
            dateInput.addEventListener('focus', function() {
                if (sectionCard) {
                    sectionCard.style.overflow = 'visible';
                    sectionCard.style.zIndex = '100';
                }
            });
            
            dateInput.addEventListener('blur', function() {
                if (sectionCard) {
                    sectionCard.style.overflow = 'hidden';
                    sectionCard.style.zIndex = 'auto';
                }
            });

            function updateCustomPejabatLabels() {
                const isRuangan = activeExportTab === 'ruangan';
                const kirFormatEl = document.querySelector('input[name="kir-format"]:checked');
                const isKirFormat2 = isRuangan && kirFormatEl && kirFormatEl.value === 'custom';

                const titleEl   = document.getElementById('custom-pejabat-section-title');
                const btnEl     = document.getElementById('btn-custom-pejabat');

                if (titleEl) titleEl.textContent = 'Ganti Penandatangan (Opsional)';

                if (btnEl) {
                    if (isKirFormat2) {
                        btnEl.textContent = '+ Ganti Penanggung Jawab / Pengurus Barang';
                    } else if (isRuangan) {
                        btnEl.textContent = '+ Ganti Pengurus Barang';
                    } else {
                        btnEl.textContent = '+ Ganti Penandatangan';
                    }
                }

                const emptyTextForRender = isKirFormat2
                    ? 'Belum diubah — menggunakan Penanggung Jawab dan Pengurus Barang yang terdaftar di sistem.'
                    : (isRuangan
                        ? 'Belum diubah — menggunakan Pengurus Barang yang terdaftar di sistem.'
                        : 'Belum diubah — menggunakan penandatangan default dari sistem.');

                renderCustomPejabatList(emptyTextForRender);
            }

            function renderCustomPejabatList(emptyText) {
                const customList = document.getElementById('custom-pejabat-list');
                if (!customList) return;

                const kirFormatEl = document.querySelector('input[name="kir-format"]:checked');
                const isKirFormat2 = activeExportTab === 'ruangan' && kirFormatEl && kirFormatEl.value === 'custom';

                const defaultEmptyText = isKirFormat2
                    ? 'Belum diubah — menggunakan Penanggung Jawab dan Pengurus Barang yang terdaftar di sistem.'
                    : (activeExportTab === 'ruangan'
                        ? 'Belum diubah — menggunakan Pengurus Barang yang terdaftar di sistem.'
                        : 'Belum diubah — menggunakan penandatangan default dari sistem.');

                const finalEmpty = emptyText || defaultEmptyText;
                const resetBtn = document.getElementById('btn-reset-custom-pejabat');
                if (customPejabat.length > 0) {
                    if (resetBtn) resetBtn.style.display = 'inline-flex';
                    customList.innerHTML = customPejabat.map((p, idx) => {
                        // Untuk Format 2: tampilkan label posisi di card
                        let posLabel = '';
                        if (isKirFormat2) {
                            posLabel = idx === 0 ? '<span style="font-size:10px;font-weight:700;color:#064635;background:#e8ecf4;padding:1px 7px;border-radius:6px;margin-left:6px;">Penanggung Jawab</span>'
                                     : idx === 1 ? '<span style="font-size:10px;font-weight:700;color:#064635;background:#e8ecf4;padding:1px 7px;border-radius:6px;margin-left:6px;">Pengurus Barang</span>'
                                     : '';
                        }
                        return `
                            <div class="pejabat-card">
                                <div class="pejabat-order">${idx + 1}</div>
                                <div class="pejabat-info" style="display:flex;align-items:center;flex-wrap:wrap;gap:4px;">
                                    <div>
                                        <div class="pejabat-nama">${p.nama}</div>
                                        <div class="pejabat-jabatan">${p.jabatan}</div>
                                    </div>
                                    ${posLabel}
                                </div>
                            </div>
                        `;
                    }).join('');
                } else {
                    if (resetBtn) resetBtn.style.display = 'none';
                    customList.innerHTML = `<div class="pejabat-empty">${finalEmpty}</div>`;
                }
            }

            function resetCustomPejabat() {
                customPejabat = [];
                customPejabatOrder = [];
                renderCustomPejabatList();
            }

            // Load default pejabat for selected jenis
            function loadDefaultPejabat() {
                // Tab KIB: tampilkan Sekretaris Desa dan Petugas Barang (sama dengan ruangan)
                if (activeExportTab === 'kib') {
                    const listEl = document.getElementById('default-pejabat-list');
                    if (!listEl) return;
                    if (!selectedKib) {
                        listEl.innerHTML = '<div class="pejabat-empty">Pilih jenis inventaris untuk melihat pejabat default</div>';
                        return;
                    }
                    listEl.innerHTML = '<div class="pejabat-empty">Memuat...</div>';
                    fetch('{{ route("export-data.kib-pejabat-info") }}', {
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                    })
                    .then(r => r.json())
                    .then(data => {
                        let html = '';
                        if (data.sekretaris) {
                            html += `<div class="pejabat-card">
                                <div class="pejabat-order">1</div>
                                <div class="pejabat-info">
                                    <div class="pejabat-nama">${data.sekretaris.nama}</div>
                                    <div class="pejabat-jabatan">Sekretaris Desa</div>
                                </div>
                            </div>`;
                        } else {
                            html += '<div class="pejabat-empty" style="color:#dc2626;">Belum ada Sekretaris Desa di sistem.</div>';
                        }
                        if (data.petugas) {
                            const orderNum = data.sekretaris ? 2 : 1;
                            html += `<div class="pejabat-card">
                                <div class="pejabat-order">${orderNum}</div>
                                <div class="pejabat-info">
                                    <div class="pejabat-nama">${data.petugas.nama}</div>
                                    <div class="pejabat-jabatan">Petugas Barang</div>
                                </div>
                            </div>`;
                        } else {
                            html += '<div class="pejabat-empty" style="color:#d97706;">Belum ada Petugas/Pengurus Barang. Centang di form Pejabat.</div>';
                        }
                        listEl.innerHTML = html;
                        if (typeof feather !== 'undefined') feather.replace();
                    })
                    .catch(() => {
                        listEl.innerHTML = '<div class="pejabat-empty">Gagal memuat info pejabat.</div>';
                    });
                    return;
                }

                // Tab ruangan: tampilkan Sekretaris Desa dan Petugas Barang dari sistem
                if (activeExportTab === 'ruangan') {
                    const listEl = document.getElementById('default-pejabat-list');
                    if (!listEl) return;
                    listEl.innerHTML = '<div class="pejabat-empty">Memuat...</div>';

                    const kirFormatEl = document.querySelector('input[name="kir-format"]:checked');
                    const kirFormat   = kirFormatEl ? kirFormatEl.value : 'standard';

                    fetch(`{{ route("export-data.ruangan-pejabat-info") }}?kir_format=${kirFormat}&ruangan_id=${selectedRuangan}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                    })
                    .then(r => r.json())
                    .then(data => {
                        let html = '';

                        if (data.kir_format === 'custom') {
                            // Format 2: Perbekel (fixed, tidak bisa custom) | Penanggung Jawab | Pengurus Barang
                            if (data.perbekel) {
                                html += `<div class="pejabat-card">
                                    <div class="pejabat-order">1</div>
                                    <div class="pejabat-info">
                                        <div class="pejabat-nama">${data.perbekel.nama}</div>
                                        <div class="pejabat-jabatan">${data.perbekel.jabatan} <span style="font-size:10px;color:#9ca3af;">(tetap)</span></div>
                                    </div>
                                </div>`;
                            } else {
                                html += '<div class="pejabat-empty" style="color:#dc2626;">Belum ada Perbekel di sistem (cek jabatan pejabat).</div>';
                            }
                            if (data.kasi) {
                                html += `<div class="pejabat-card">
                                    <div class="pejabat-order">2</div>
                                    <div class="pejabat-info">
                                        <div class="pejabat-nama">${data.kasi.nama}</div>
                                        <div class="pejabat-jabatan">${data.kasi.jabatan}</div>
                                    </div>
                                </div>`;
                            } else {
                                html += '<div class="pejabat-empty" style="color:#d97706;">Belum ada Penanggung Jawab di sistem.</div>';
                            }
                            if (data.petugas) {
                                html += `<div class="pejabat-card">
                                    <div class="pejabat-order">3</div>
                                    <div class="pejabat-info">
                                        <div class="pejabat-nama">${data.petugas.nama}</div>
                                        <div class="pejabat-jabatan">${data.petugas.jabatan}</div>
                                    </div>
                                </div>`;
                            } else {
                                html += '<div class="pejabat-empty" style="color:#d97706;">Belum ada Petugas/Pengurus Barang. Centang di form Pejabat.</div>';
                            }
                        } else {
                            // Format 1: Sekretaris Desa + Petugas Barang
                            if (data.sekretaris) {
                                html += `<div class="pejabat-card">
                                    <div class="pejabat-order">1</div>
                                    <div class="pejabat-info">
                                        <div class="pejabat-nama">${data.sekretaris.nama}</div>
                                        <div class="pejabat-jabatan">Sekretaris Desa</div>
                                    </div>
                                </div>`;
                            } else {
                                html += '<div class="pejabat-empty" style="color:#dc2626;">Belum ada Sekretaris Desa di sistem.</div>';
                            }
                            if (data.petugas) {
                                const orderNum = data.sekretaris ? 2 : 1;
                                html += `<div class="pejabat-card">
                                    <div class="pejabat-order">${orderNum}</div>
                                    <div class="pejabat-info">
                                        <div class="pejabat-nama">${data.petugas.nama}</div>
                                        <div class="pejabat-jabatan">Petugas Barang</div>
                                    </div>
                                </div>`;
                            } else {
                                html += '<div class="pejabat-empty" style="color:#d97706;">Belum ada Petugas/Pengurus Barang. Centang di form Pejabat.</div>';
                            }
                        }

                        listEl.innerHTML = html;
                        if (typeof feather !== 'undefined') feather.replace();
                    })
                    .catch(() => {
                        listEl.innerHTML = '<div class="pejabat-empty">Gagal memuat info pejabat.</div>';
                    });
                    return;
                }

                if (!selectedJenisId) return;

                if (selectedJenisId === 'all') {
                    document.getElementById('default-pejabat-list').innerHTML =
                        '<div class="pejabat-empty">Export semua jenis menggunakan pejabat default masing-masing jenis</div>';
                    return;
                }

                if (!jenisData[selectedJenisId]) return;
                
                // Check if Buku Inventaris Aset Desa
                const isBukuInventaris = jenisData[selectedJenisId].nama === 'Buku Inventaris Aset Desa';
                
                if (isBukuInventaris) {
                    document.getElementById('default-pejabat-list').innerHTML =
                        '<div class="pejabat-empty"><i data-feather="info" style="width:14px;height:14px;display:inline;margin-right:6px;"></i>Buku Inventaris menggunakan pejabat default yang telah ditentukan</div>';
                    
                    // Show pejabat if ada
                    const defaultPejabatIds = jenisData[selectedJenisId].default_pejabat || [];
                    if (defaultPejabatIds.length > 0) {
                        let activeIdx = 1;
                        const pejabatHTML = defaultPejabatIds.map((pejabatId, index) => {
                            const pejabat = pejabatData.find(p => p.id === pejabatId);
                            if (!pejabat) return '';
                            const currentIdx = activeIdx++;
                            return `
                                <div class="pejabat-card">
                                    <div class="pejabat-order">${currentIdx}</div>
                                    <div class="pejabat-info">
                                        <div class="pejabat-nama">${pejabat.nama}</div>
                                        <div class="pejabat-jabatan">${pejabat.jabatan}</div>
                                    </div>
                                </div>
                            `;
                        }).join('');
                        document.getElementById('default-pejabat-list').innerHTML = pejabatHTML;
                    }
                    return;
                }
                
                // Check if other virtual jenis or orphan jenis
                if (jenisData[selectedJenisId].is_virtual) {
                    document.getElementById('default-pejabat-list').innerHTML =
                        '<div class="pejabat-empty"><i data-feather="info" style="width:14px;height:14px;display:inline;margin-right:6px;"></i>' + jenisData[selectedJenisId].nama + ' menggunakan struktur terpadu semua jenis</div>';
                    return;
                }
                
                if (jenisData[selectedJenisId].is_orphan) {
                    document.getElementById('default-pejabat-list').innerHTML =
                        '<div class="pejabat-empty"><i data-feather="info" style="width:14px;height:14px;display:inline;margin-right:6px;"></i>Menggunakan pejabat default dari jenis inventaris</div>';
                    return;
                }
                
                const defaultPejabatIds = jenisData[selectedJenisId].default_pejabat || [];
                const defaultList = document.getElementById('default-pejabat-list');
                
                if (defaultPejabatIds.length > 0) {
                    let activeIdx = 1;
                    const pejabatHTML = defaultPejabatIds.map((pejabatId, index) => {
                        const pejabat = pejabatData.find(p => p.id === pejabatId);
                        if (!pejabat) return '';
                        
                        const currentIdx = activeIdx++;
                        return `
                            <div class="pejabat-card">
                                <div class="pejabat-order">${currentIdx}</div>
                                <div class="pejabat-info">
                                    <div class="pejabat-nama">${pejabat.nama}</div>
                                    <div class="pejabat-jabatan">${pejabat.jabatan}</div>
                                </div>
                            </div>
                        `;
                    }).join('');
                    
                    defaultList.innerHTML = pejabatHTML;
                } else {
                    defaultList.innerHTML = '<div class="pejabat-empty">Tidak ada pejabat default untuk jenis ini</div>';
                }
            }

            // Tahun Filter Handler - dihandle oleh year picker flatpickr di atas
            // (event change tetap sebagai fallback untuk input manual)
            document.getElementById('filter-tahun').addEventListener('input', function() {
                const y = parseInt(this.value);
                if (y >= 2000 && y <= new Date().getFullYear() + 1) {
                    selectedTahun = y;
                    updateDataCount();
                    updateUI();
                }
            });

            // Update data count for selected year and jenis (AJAX)
            function updateDataCount() {
                const el = document.getElementById('tahun-data-count');
                if (activeExportTab === 'ruangan') {
                    if (!selectedRuangan) { el.textContent = '-'; currentDataCount = 0; return; }
                    el.textContent = '...';
                    const param = selectedRuangan === 'all' ? 'all' : selectedRuangan;
                    fetch(`{{ route('export-data.ruangan-count') }}?ruangan_id=${param}&tahun=${selectedTahun}`)
                        .then(r => r.json())
                        .then(data => { currentDataCount = data.count || 0; el.textContent = currentDataCount; })
                        .catch(() => { el.textContent = '-'; currentDataCount = 0; });
                    return;
                }
                if (activeExportTab === 'kib') {
                    if (!selectedKib) { el.textContent = '-'; currentDataCount = 0; return; }
                    // Ambil count dari array kibOptions
                    const kibItem = kibOptions.find(k => String(k.id) === String(selectedKib));
                    if (kibItem) {
                        if (selectedKib === 'all') {
                            let total = 0;
                            kibOptions.forEach(k => {
                                if (k.id !== 'all' && !k.disabled) {
                                    const match = k.deskripsi.match(/\d+/);
                                    if (match) total += parseInt(match[0]);
                                }
                            });
                            currentDataCount = total;
                        } else {
                            const match = kibItem.deskripsi.match(/\d+/);
                            currentDataCount = match ? parseInt(match[0]) : 0;
                        }
                        el.textContent = currentDataCount;
                    } else {
                        el.textContent = '-';
                        currentDataCount = 0;
                    }
                    return;
                }
                if (activeExportTab === 'laporan') {
                    el.textContent = '...';
                    // Fetch count data laporan secara async
                    const periode = document.getElementById('laporan_periode_hidden')?.value || 'tahunan';
                    fetch(`{{ route('export-data.laporan-count') }}?tahun=${selectedTahun}&periode=${periode}`, {
                        credentials: 'same-origin',
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                    })
                    .then(r => r.json())
                    .then(data => {
                        currentDataCount = data.count || 0;
                        el.textContent = currentDataCount;
                    })
                    .catch(() => { el.textContent = '0'; });
                    return;
                }
                if (!selectedJenisId) { el.textContent = '-'; currentDataCount = 0; return; }
                el.textContent = '...';
                fetch(`{{ route('export-data.data-count') }}?jenis_inventaris_id=${selectedJenisId}&tahun=${selectedTahun}`)
                    .then(r => r.json())
                    .then(data => { currentDataCount = data.count || 0; el.textContent = currentDataCount; })
                    .catch(() => { el.textContent = '-'; currentDataCount = 0; });
            }

            // Date preview
            function updateDatePreview() {
                const tanggal = document.getElementById('doc-tanggal').value;
                const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                
                if (tanggal) {
                    const date = new Date(tanggal);
                    document.getElementById('date-preview').textContent = `${date.getDate()} ${months[date.getMonth()]} ${date.getFullYear()}`;
                } else {
                    document.getElementById('date-preview').textContent = '-';
                }
            }
            document.getElementById('doc-tanggal').addEventListener('change', updateDatePreview);

            // Helper: ambil paper size yang dipilih (hanya relevan saat PDF)
            function getSelectedPaperSize() {
                const format = document.querySelector('input[name="export-format"]:checked')?.value;
                if (format !== 'pdf') return 'legal'; // default, tidak dipakai untuk excel
                return document.querySelector('input[name="paper-size"]:checked')?.value || 'legal';
            }

            // Format & Signature update
            document.querySelectorAll('input[name="export-format"], input[name="signature-option"]').forEach(input => {
                input.addEventListener('change', updateUI);
            });

            // Show/hide paper size section berdasarkan format
            function updatePaperSizeVisibility() {
                const format = document.querySelector('input[name="export-format"]:checked')?.value;
                const section = document.getElementById('paper-size-section');
                if (section) section.style.display = format === 'pdf' ? '' : 'none';
            }
            document.querySelectorAll('input[name="export-format"]').forEach(input => {
                input.addEventListener('change', updatePaperSizeVisibility);
            });
            // Init saat load
            updatePaperSizeVisibility();

            // Update UI
            function updateUI() {
                const format = document.querySelector('input[name="export-format"]:checked').value;
                const sig = document.querySelector('input[name="signature-option"]:checked').value;

                // isLocked tergantung tab aktif
                const isLocked = activeExportTab === 'ruangan' ? !selectedRuangan
                    : (activeExportTab === 'kib' ? !selectedKib
                    : (activeExportTab === 'laporan' ? false
                    : !selectedJenisId));

                setFlowLockedState(isLocked);

                // Show/hide section dokumen info (hanya untuk tab jenis DAN sudah pilih jenis)
                const sectionDokumen = document.getElementById('section-dokumen-info');
                if (sectionDokumen) {
                    const shouldShowDokumen = activeExportTab === 'jenis' && selectedJenisId;
                    sectionDokumen.style.display = shouldShowDokumen ? '' : 'none';
                }

                const tahunLaporan = selectedTahun;
                const periode = document.getElementById('laporan_periode_hidden')?.value || 'tahunan';
                const selP = laporanPeriodeOptions.find(o => o.id === periode);
                const periodeLabel = selP ? selP.nama : 'Per Tahun';

                const jenisLabel = activeExportTab === 'ruangan'
                    ? (selectedRuangan === 'all' ? 'Semua Ruangan' : (selectedRuangan ? (ruanganOptions.find(r => String(r.id) === String(selectedRuangan))?.nama || 'Ruangan') : 'Pilih ruangan'))
                    : (activeExportTab === 'kib'
                        ? (selectedKib === 'all' ? 'Semua KIB' : (selectedKib ? 'KIB ' + selectedKib : 'Pilih KIB'))
                        : (activeExportTab === 'laporan'
                            ? ('Laporan Aset ' + periodeLabel + ' ' + tahunLaporan)
                            : (selectedJenisId === 'all'
                                ? 'Semua Jenis (' + {{ $jenisInventaris->count() }} + ' jenis)'
                                : (selectedJenisId && jenisData[selectedJenisId] ? jenisData[selectedJenisId].nama : 'Pilih jenis'))));

                document.getElementById('info-jenis').textContent = jenisLabel;
                document.getElementById('info-tahun').textContent = activeExportTab === 'laporan' ? tahunLaporan : selectedTahun;
                document.getElementById('info-format').textContent = formatLabel(format);
                document.getElementById('info-signature').textContent = sig === 'with' ? 'Dengan Tanda Tangan' : 'Tanpa Tanda Tangan';

                const previewBtn = document.getElementById('btn-preview-export');
                if (activeExportTab === 'ruangan') {
                    previewBtn.disabled = !selectedRuangan;
                    previewBtn.innerHTML = '<i data-feather="eye" style="width:16px;height:16px;"></i> Preview Export Ruangan';
                } else if (activeExportTab === 'kib') {
                    previewBtn.disabled = !selectedKib;
                    previewBtn.innerHTML = '<i data-feather="eye" style="width:16px;height:16px;"></i> Preview Export KIB';
                } else if (activeExportTab === 'laporan') {
                    previewBtn.disabled = false;
                    previewBtn.innerHTML = '<i data-feather="eye" style="width:16px;height:16px;"></i> Preview Laporan Aset';
                } else {
                    previewBtn.disabled = !selectedJenisId;
                    previewBtn.innerHTML = '<i data-feather="eye" style="width:16px;height:16px;"></i> Preview Export';
                }
                if (typeof feather !== 'undefined') feather.replace();

                // Update data count
                updateDataCount();
                updateUIForActiveTab();
            }

            function formatLabel(format) {
                const labels = { excel: 'Excel (.xlsx)', pdf: 'PDF' };
                return labels[format] || format;
            }

            function getActiveSignerList() {
                if (!selectedJenisId || selectedJenisId === 'all') return [];

                if (customPejabat.length > 0) {
                    return customPejabat.filter(Boolean);
                }

                const defaultPejabatIds = (jenisData[selectedJenisId] && jenisData[selectedJenisId].default_pejabat) || [];
                return defaultPejabatIds
                    .map(id => pejabatData.find(p => p.id === id))
                    .filter(Boolean);
            }

            function renderPreviewSignerPanel(signatureOption) {
                const panel = document.getElementById('preview-signer-panel');
                const note = document.getElementById('preview-signer-note');
                const list = document.getElementById('preview-signer-list');
                if (!panel || !note || !list) return false;

                // Panel selalu tersembunyi — hanya logic warning yang dijalankan

                if (selectedJenisId === 'all') {
                    return false;
                }

                const signers = getActiveSignerList();

                if (signers.length === 0 && signatureOption === 'with') {
                    return true; // has warning — tombol export dinonaktifkan
                }

                return false;
            }

            function refreshHistoryTableSafe() {
                if (typeof window.updateHistoryTable === 'function') {
                    window.updateHistoryTable();
                }
            }

            // Custom Pejabat Modal
            function openCustomPejabatModal() {
                // Tab ruangan: tidak perlu cek selectedJenisId
                if (activeExportTab !== 'ruangan' && !selectedJenisId) {
                    showToast('Pilih jenis inventaris terlebih dahulu', 'warning');
                    return;
                }

                const kirFormatElModal = document.querySelector('input[name="kir-format"]:checked');
                const isKirFormat2     = activeExportTab === 'ruangan' && kirFormatElModal?.value === 'custom';
                const isKirFormat1     = activeExportTab === 'ruangan' && !isKirFormat2;
                const isKib            = activeExportTab === 'kib';

                // Tentukan ID pejabat yang harus disembunyikan dari modal (penandatangan tetap)
                let hiddenIds = new Set();
                if (isKirFormat2) {
                    (fixedPejabatIds.kir_format2 || []).forEach(id => hiddenIds.add(Number(id)));
                } else if (isKirFormat1) {
                    (fixedPejabatIds.kir_format1 || []).forEach(id => hiddenIds.add(Number(id)));
                } else if (isKib) {
                    (fixedPejabatIds.kib || []).forEach(id => hiddenIds.add(Number(id)));
                }

                // Update modal title sesuai konteks
                const modalTitle = document.getElementById('custom-pejabat-modal-title');
                if (modalTitle) {
                    modalTitle.textContent = isKirFormat2
                        ? 'Pilih Penandatangan (Format Desa Demo)'
                        : (activeExportTab === 'ruangan'
                            ? 'Pilih Pengurus Barang'
                            : 'Pilih Pejabat Penandatangan');
                }

                // Tampilkan/sembunyikan hint Format 2
                const hintEl = document.getElementById('kir-format2-hint');
                if (hintEl) hintEl.style.display = isKirFormat2 ? '' : 'none';

                const selectedIds = customPejabat
                    .map(p => Number(p.id))
                    .filter(Number.isFinite);

                customPejabatOrder = selectedIds.slice();

                // Filter pejabat tetap dari list yang ditampilkan
                const filteredPejabat = pejabatData.filter(p => !hiddenIds.has(Number(p.id)));

                const list = document.getElementById('modal-pejabat-list');
                list.innerHTML = filteredPejabat.map((p) => {
                    const isChecked = selectedIds.includes(Number(p.id));
                    // Untuk Format 2: tampilkan label urutan (Penanggung Jawab / Pengurus Barang)
                    let orderBadge = '';
                    if (isKirFormat2 && isChecked) {
                        const posIdx = customPejabatOrder.indexOf(Number(p.id));
                        const posLabel = posIdx === 0 ? 'Penanggung Jawab' : (posIdx === 1 ? 'Pengurus Barang' : '');
                        if (posLabel) {
                            orderBadge = `<span style="font-size:10px;font-weight:700;color:#fff;background:#064635;padding:2px 7px;border-radius:8px;margin-left:6px;">${posLabel}</span>`;
                        }
                    }
                    return `
                        <div class="pejabat-row-selectable" data-id="${p.id}" style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-bottom: 1px solid #f1f5f9; cursor: pointer;">
                            <input type="checkbox" class="pejabat-cb" data-id="${p.id}" ${isChecked ? 'checked' : ''} style="width: 16px; height: 16px; accent-color: #064635; flex-shrink:0;">
                            <div style="flex: 1; display:flex; align-items:center; flex-wrap:wrap; gap:4px;">
                                <div>
                                    <div style="font-weight: 600; font-size: 12px; color: #1f2937;">${p.nama}</div>
                                    <div style="font-size: 11px; color: #9ca3af;">${p.jabatan}</div>
                                </div>
                                ${orderBadge}
                            </div>
                        </div>
                    `;
                }).join('');

                if (filteredPejabat.length === 0) {
                    list.innerHTML = '<div style="padding:24px;text-align:center;color:#9ca3af;font-size:13px;">Tidak ada pejabat yang tersedia.</div>';
                }

                // Update counter hint setelah render
                updateFormat2HintCount();

                bindCustomPejabatModalEvents(isKirFormat2);
                document.getElementById('custom-pejabat-modal').classList.add('show');
                if(typeof feather !== 'undefined') feather.replace();
            }

            function updateFormat2HintCount() {
                const hintCount = document.getElementById('kir-format2-hint-count');
                if (!hintCount) return;
                const kirFormatElModal = document.querySelector('input[name="kir-format"]:checked');
                const isKirFormat2 = activeExportTab === 'ruangan' && kirFormatElModal?.value === 'custom';
                if (!isKirFormat2) { hintCount.textContent = ''; return; }
                const checked = document.querySelectorAll('#modal-pejabat-list .pejabat-cb:checked').length;
                if (checked === 0) {
                    hintCount.textContent = 'Belum ada yang dipilih.';
                    hintCount.style.color = '#64748b';
                } else if (checked === 1) {
                    hintCount.textContent = '1 dipilih → posisi Penanggung Jawab. Pilih 1 lagi untuk Pengurus Barang.';
                    hintCount.style.color = '#d97706';
                } else if (checked === 2) {
                    hintCount.textContent = '✓ 2 orang dipilih — sudah lengkap.';
                    hintCount.style.color = '#059669';
                } else {
                    hintCount.textContent = `${checked} dipilih — hanya 2 pertama yang digunakan.`;
                    hintCount.style.color = '#dc2626';
                }
            }

            function closeCustomPejabatModal() {
                document.getElementById('custom-pejabat-modal').classList.remove('show');
            }

            function trackPejabatSelection(checkbox) {
                const id = Number(checkbox.dataset.id);
                if (!Number.isFinite(id)) return;

                const existingIndex = customPejabatOrder.indexOf(id);
                if (checkbox.checked) {
                    if (existingIndex === -1) customPejabatOrder.push(id);
                } else if (existingIndex > -1) {
                    customPejabatOrder.splice(existingIndex, 1);
                }
            }

            function bindCustomPejabatModalEvents(isKirFormat2) {
                const rows = document.querySelectorAll('#modal-pejabat-list .pejabat-row-selectable');
                rows.forEach(row => {
                    row.addEventListener('click', function() {
                        togglePejabatByRow(this);
                        updateFormat2HintCount();
                        // Update label badge di baris yang sudah dicek
                        if (isKirFormat2) refreshFormat2Badges();
                    });
                });

                const checkboxes = document.querySelectorAll('#modal-pejabat-list .pejabat-cb');
                checkboxes.forEach(checkbox => {
                    checkbox.addEventListener('click', function(ev) {
                        ev.stopPropagation();
                    });

                    checkbox.addEventListener('change', function() {
                        trackPejabatSelection(this);
                        updateFormat2HintCount();
                        if (isKirFormat2) refreshFormat2Badges();
                    });
                });
            }

            function refreshFormat2Badges() {
                // Re-render badge label di tiap baris sesuai urutan centang
                const rows = document.querySelectorAll('#modal-pejabat-list .pejabat-row-selectable');
                rows.forEach(row => {
                    const cb = row.querySelector('.pejabat-cb');
                    if (!cb) return;
                    const id = Number(cb.dataset.id);
                    // Hapus badge lama
                    const oldBadge = row.querySelector('.format2-badge');
                    if (oldBadge) oldBadge.remove();
                    // Tambah badge baru jika dicek
                    if (cb.checked) {
                        const posIdx = customPejabatOrder.indexOf(id);
                        const posLabel = posIdx === 0 ? 'Penanggung Jawab' : (posIdx === 1 ? 'Pengurus Barang' : '');
                        if (posLabel) {
                            const badge = document.createElement('span');
                            badge.className = 'format2-badge';
                            badge.style.cssText = 'font-size:10px;font-weight:700;color:#fff;background:#064635;padding:2px 7px;border-radius:8px;margin-left:6px;';
                            badge.textContent = posLabel;
                            const infoDiv = row.querySelector('div > div');
                            if (infoDiv) infoDiv.after(badge);
                        }
                    }
                });
            }

            function togglePejabatByRow(row) {
                const checkbox = row ? row.querySelector('.pejabat-cb') : null;
                if (!checkbox) return;

                checkbox.checked = !checkbox.checked;
                trackPejabatSelection(checkbox);
            }

            function saveCustomPejabat() {
                const selected = document.querySelectorAll('.pejabat-cb:checked');
                const checkedIds = new Set(Array.from(selected).map(cb => Number(cb.dataset.id)));

                customPejabatOrder = customPejabatOrder.filter(id => checkedIds.has(id));

                const kirFormatEl = document.querySelector('input[name="kir-format"]:checked');
                const isKirFormat2 = activeExportTab === 'ruangan' && kirFormatEl?.value === 'custom';

                // Untuk Format 2: batasi hanya 2 orang pertama
                if (isKirFormat2) {
                    if (customPejabatOrder.length > 2) {
                        customPejabatOrder = customPejabatOrder.slice(0, 2);
                        showToast('Hanya 2 penandatangan yang digunakan (Penanggung Jawab & Pengurus Barang)', 'warning');
                    }
                }

                customPejabat = customPejabatOrder
                    .map(id => pejabatData.find(p => Number(p.id) === Number(id)))
                    .filter(Boolean);

                renderCustomPejabatList();

                closeCustomPejabatModal();
                showToast('Penandatangan berhasil disimpan', 'success');
            }

            // Preview & Export
            document.getElementById('btn-preview-export').addEventListener('click', function() {
                // Handle tab ruangan
                if (activeExportTab === 'ruangan') {
                    if (!selectedRuangan) return;
                    const ruanganLabel = selectedRuangan === 'all'
                        ? 'Semua Ruangan'
                        : (ruanganOptions.find(r => String(r.id) === String(selectedRuangan))?.nama || '-');
                    const format = document.querySelector('input[name="export-format"]:checked').value;
                    const sig = document.querySelector('input[name="signature-option"]:checked').value;

                    document.getElementById('preview-format').textContent = formatLabel(format);
                    document.getElementById('preview-count').textContent = currentDataCount + ' aset di ' + ruanganLabel;
                    document.getElementById('preview-sig').textContent = sig === 'with' ? 'Dengan Tanda Tangan' : 'Tanpa Tanda Tangan';
                    document.getElementById('preview-date').textContent = document.getElementById('date-preview').textContent;

                    // Tampilkan preview struktur KIR sesuai format (sama persis dengan jenis inventaris)
                    const wrapExcel = document.getElementById('preview-wrapper-excel');
                    const wrapPdf   = document.getElementById('preview-wrapper-pdf');
                    wrapExcel.style.display = format === 'excel' ? '' : 'none';
                    wrapPdf.style.display   = format === 'pdf'   ? '' : 'none';

                    const baseName = 'kartu_inventaris_ruangan_' + (selectedRuangan === 'all' ? 'semua' : ruanganLabel.toLowerCase().replace(/\s+/g,'_'));

                    if (format === 'excel') {
                        const tbody  = document.getElementById('excel-preview-table');
                        const colBar = document.getElementById('excel-col-bar');
                        document.getElementById('excel-filename').textContent = baseName + '.xlsx';

                        const kirCols = ['NO','JENIS BARANG','KODE BARANG','REG','TAHUN','IDENTITAS','NO.POL','ASAL-USUL','HARGA (Rb)','KET.'];
                        const letters = 'ABCDEFGHIJ';
                        colBar.innerHTML = kirCols.map((_, i) => `<div class="excel-col-cell">${letters[i]}</div>`).join('');

                        const hS = `class="excel-cell excel-cell-header" style="text-align:center;vertical-align:middle;font-size:9px;"`;
                        const rows = [];
                        rows.push(`<tr><td class="excel-row-num">1</td><td class="excel-cell excel-cell-title" colspan="10" style="padding:4px 5px;">KARTU INVENTARIS RUANGAN</td></tr>`);
                        rows.push(`<tr><td class="excel-row-num">2</td>${Array(10).fill('<td class="excel-cell"></td>').join('')}</tr>`);
                        rows.push(`<tr><td class="excel-row-num">3</td>${kirCols.map(h => `<td ${hS}>${h}</td>`).join('')}</tr>`);

                        const placeholders = Math.min(currentDataCount > 0 ? currentDataCount : 3, 3);
                        for (let r = 0; r < placeholders; r++) {
                            const cls = r % 2 === 0 ? 'excel-cell-data' : 'excel-cell-data alt';
                            rows.push(`<tr><td class="excel-row-num">${r+4}</td><td class="excel-cell ${cls}" style="text-align:center;">${r+1}</td>${Array(9).fill(`<td class="excel-cell ${cls}">...</td>`).join('')}</tr>`);
                        }
                        if (currentDataCount > 3) {
                            rows.push(`<tr><td class="excel-row-num" style="color:#9ca3af;">...</td><td class="excel-cell" colspan="10" style="text-align:center;color:#9ca3af;font-size:9px;">... +${currentDataCount - 3} data lainnya</td></tr>`);
                        }
                        rows.push(`<tr><td class="excel-row-num"></td><td class="excel-cell excel-cell-header" colspan="8" style="text-align:center;">JUMLAH HARGA</td><td class="excel-cell excel-cell-header" style="text-align:center;">SUM</td><td class="excel-cell"></td></tr>`);
                        tbody.innerHTML = rows.join('');

                    } else {
                        // PDF preview KIR
                        document.getElementById('pdf-filename').textContent = baseName + '.pdf';
                        const pdfContent = document.getElementById('pdf-preview-content');
                        const placeholders = Math.min(currentDataCount > 0 ? currentDataCount : 3, 3);
                        let pdfHtml = `<div style="font-family:Arial,sans-serif;font-size:9px;">`;
                        pdfHtml += `<div style="text-align:center;font-weight:bold;font-size:11px;margin-bottom:6px;">KARTU INVENTARIS RUANGAN</div>`;
                        pdfHtml += `<table style="width:100%;border-collapse:collapse;font-size:8px;">`;
                        pdfHtml += `<thead><tr style="background:#f9fafb;">`;
                        ['NO','JENIS BARANG / NAMA BARANG','KODE BARANG','REG','TAHUN','IDENTITAS','NO.POL','ASAL-USUL','HARGA (Ribuan) Rp','KET.'].forEach(h => {
                            pdfHtml += `<th style="border:1px solid #aaa;padding:3px 4px;text-align:center;font-weight:bold;">${h}</th>`;
                        });
                        pdfHtml += `</tr></thead><tbody>`;
                        pdfHtml += `<tr><td colspan="10" style="border:1px solid #aaa;padding:3px 6px;background:#e8e8e8;font-weight:bold;font-style:italic;">Nama Ruangan : ${ruanganLabel}</td></tr>`;
                        for (let r = 0; r < placeholders; r++) {
                            pdfHtml += `<tr>${Array(10).fill(`<td style="border:1px solid #aaa;padding:2px 4px;color:#9ca3af;">...</td>`).join('')}</tr>`;
                        }
                        if (currentDataCount > 3) {
                            pdfHtml += `<tr><td colspan="10" style="border:1px solid #aaa;padding:3px;text-align:center;color:#9ca3af;font-size:8px;">... +${currentDataCount - 3} data lainnya</td></tr>`;
                        }
                        pdfHtml += `<tr><td colspan="8" style="border:1px solid #aaa;padding:3px;font-weight:bold;text-align:center;">JUMLAH HARGA</td><td style="border:1px solid #aaa;padding:3px;font-weight:bold;text-align:right;">S</td><td style="border:1px solid #aaa;"></td></tr>`;
                        pdfHtml += `</tbody></table></div>`;
                        pdfContent.innerHTML = pdfHtml;
                    }

                    // Sembunyikan signer panel untuk ruangan
                    const signerPanel = document.getElementById('preview-signer-panel');
                    if (signerPanel) signerPanel.style.display = 'none';

                    const btnConfirm = document.getElementById('btn-confirm-export');
                    const btnText    = document.getElementById('btn-confirm-export-text');
                    if (btnConfirm) { btnConfirm.disabled = false; btnConfirm.style.opacity = ''; btnConfirm.style.cursor = ''; }
                    if (btnText)    { btnText.textContent = 'Export Ruangan'; }

                    document.getElementById('preview-export-modal').classList.add('show');
                    if (typeof feather !== 'undefined') feather.replace();
                    return;
                }

                // Handle tab Laporan Aset
                if (activeExportTab === 'laporan') {
                    const tahunLaporan = selectedTahun;
                    const periode      = document.getElementById('laporan_periode_hidden')?.value || 'tahunan';
                    let periodeLabel;
                    if (periode === 'semester_1') {
                        periodeLabel = 'Semester I (Saldo 1 Januari – 30 Juni)';
                    } else if (periode === 'semester_2') {
                        periodeLabel = 'Semester II (Saldo 1 Juli – 31 Desember)';
                    } else {
                        periodeLabel = 'Per Tahun (Saldo 1 Januari – 31 Desember)';
                    }
                    const format       = document.querySelector('input[name="export-format"]:checked').value;

                    document.getElementById('preview-format').textContent = formatLabel(format);
                    document.getElementById('preview-count').textContent = 'Laporan Aset ' + periodeLabel;
                    document.getElementById('preview-sig').textContent = '-';
                    document.getElementById('preview-date').textContent = tahunLaporan;

                    const wrapExcel = document.getElementById('preview-wrapper-excel');
                    const wrapPdf   = document.getElementById('preview-wrapper-pdf');
                    wrapExcel.style.display = format === 'excel' ? '' : 'none';
                    wrapPdf.style.display   = format === 'pdf'   ? '' : 'none';

                    const periodeSuffix = periode === 'semester_1' ? 'semester_1' : (periode === 'semester_2' ? 'semester_2' : 'tahunan');
                    const baseName = 'laporan_aset_' + periodeSuffix + '_' + tahunLaporan;

                    if (format === 'excel') {
                        const tbody  = document.getElementById('excel-preview-table');
                        const colBar = document.getElementById('excel-col-bar');
                        document.getElementById('excel-filename').textContent = baseName + '.xlsx';
                        const cols = ['KODE','NAMA BARANG','SATUAN','KWANT.','NILAI (RP)','KWANT.','NILAI (RP)','KWANT.','NILAI (RP)','KWANT.','NILAI (RP)'];
                        const letters = 'ABCDEFGHIJK';
                        colBar.innerHTML = cols.map((_, i) => `<div class="excel-col-cell">${letters[i]||'?'}</div>`).join('');
                        const hS = `class="excel-cell excel-cell-header" style="text-align:center;font-size:9px;"`;
                        const rows = [];
                        rows.push(`<tr><td class="excel-row-num">1</td><td class="excel-cell excel-cell-title" colspan="11" style="padding:4px 5px;font-weight:bold;">LAPORAN ASET ${periodeLabel.toUpperCase()} ${tahunLaporan}</td></tr>`);
                        rows.push(`<tr><td class="excel-row-num">2</td><td class="excel-cell excel-cell-subtitle" colspan="11" style="text-align:center;">RINCIAN PENGELOMPOKAN BARANG</td></tr>`);
                        rows.push(`<tr><td class="excel-row-num">3</td>${cols.map(h => `<td ${hS}>${h}</td>`).join('')}</tr>`);
                        for (let r = 0; r < 3; r++) {
                            const cls = r % 2 === 0 ? 'excel-cell-data' : 'excel-cell-data alt';
                            rows.push(`<tr><td class="excel-row-num">${r+4}</td>${Array(11).fill(`<td class="excel-cell ${cls}">...</td>`).join('')}</tr>`);
                        }
                        rows.push(`<tr><td class="excel-row-num"></td><td class="excel-cell excel-cell-header" colspan="3" style="text-align:center;">JUMLAH</td>${Array(8).fill('<td class="excel-cell excel-cell-header" style="text-align:center;">S</td>').join('')}</tr>`);
                        tbody.innerHTML = rows.join('');
                    } else {
                        document.getElementById('pdf-filename').textContent = baseName + '.pdf';
                        const pdfContent = document.getElementById('pdf-preview-content');
                        let pdfHtml = `<div style="font-family:Arial,sans-serif;font-size:9px;">`;
                        pdfHtml += `<div style="text-align:center;font-weight:bold;font-size:11px;margin-bottom:2px;">LAPORAN ASET ${periodeLabel.toUpperCase()} ${tahunLaporan}</div>`;
                        pdfHtml += `<div style="text-align:center;font-size:10px;margin-bottom:6px;">RINCIAN PENGELOMPOKAN BARANG</div>`;
                        pdfHtml += `<table style="width:100%;border-collapse:collapse;font-size:8px;"><thead><tr style="background:#f9fafb;">`;
                        ['KODE','NAMA BARANG','SATUAN','SALDO AWAL KWANT.','SALDO AWAL NILAI','BERTAMBAH KWANT.','BERTAMBAH NILAI','BERKURANG KWANT.','BERKURANG NILAI','SALDO AKHIR KWANT.','SALDO AKHIR NILAI'].forEach(h => {
                            pdfHtml += `<th style="border:1px solid #aaa;padding:2px 3px;text-align:center;font-weight:bold;font-size:7px;">${h}</th>`;
                        });
                        pdfHtml += `</tr></thead><tbody>`;
                        for (let r = 0; r < 3; r++) {
                            pdfHtml += `<tr>${Array(11).fill(`<td style="border:1px solid #aaa;padding:2px 3px;color:#9ca3af;">...</td>`).join('')}</tr>`;
                        }
                        pdfHtml += `<tr><td colspan="3" style="border:1px solid #aaa;padding:3px;font-weight:bold;text-align:center;">JUMLAH</td>${Array(8).fill('<td style="border:1px solid #aaa;padding:3px;font-weight:bold;text-align:right;">S</td>').join('')}</tr>`;
                        pdfHtml += `</tbody></table></div>`;
                        pdfContent.innerHTML = pdfHtml;
                    }

                    const signerPanel = document.getElementById('preview-signer-panel');
                    if (signerPanel) signerPanel.style.display = 'none';

                    const btnConfirm = document.getElementById('btn-confirm-export');
                    const btnText    = document.getElementById('btn-confirm-export-text');
                    if (btnConfirm) { btnConfirm.disabled = false; btnConfirm.style.opacity = ''; btnConfirm.style.cursor = ''; }
                    if (btnText)    { btnText.textContent = 'Export Laporan Aset'; }

                    document.getElementById('preview-export-modal').classList.add('show');
                    if (typeof feather !== 'undefined') feather.replace();
                    return;
                }

                // Handle tab KIB
                if (activeExportTab === 'kib') {
                    const format = document.querySelector('input[name="export-format"]:checked').value;
                    const sig = document.querySelector('input[name="signature-option"]:checked').value;
                    const kibLabel = selectedKib === 'all' ? 'Semua KIB (A-F)' : 'KIB ' + selectedKib;

                    document.getElementById('preview-format').textContent = formatLabel(format);
                    document.getElementById('preview-count').textContent = kibLabel;
                    document.getElementById('preview-sig').textContent = sig === 'with' ? 'Dengan Tanda Tangan' : 'Tanpa Tanda Tangan';
                    document.getElementById('preview-date').textContent = document.getElementById('date-preview').textContent;

                    const wrapExcel = document.getElementById('preview-wrapper-excel');
                    const wrapPdf   = document.getElementById('preview-wrapper-pdf');
                    wrapExcel.style.display = format === 'excel' ? '' : 'none';
                    wrapPdf.style.display   = format === 'pdf'   ? '' : 'none';

                    const baseName = 'kib_' + (selectedKib === 'all' ? 'semua' : selectedKib.toLowerCase());

                    if (format === 'excel') {
                        const tbody  = document.getElementById('excel-preview-table');
                        const colBar = document.getElementById('excel-col-bar');
                        document.getElementById('excel-filename').textContent = baseName + '.xlsx';
                        const kibCols = ['NO','JENIS BARANG / NAMA BARANG','KODE BARANG','REG','... (field KIB)','ASAL-USUL','HARGA Rp','KET.'];
                        const letters = 'ABCDEFGH';
                        colBar.innerHTML = kibCols.map((_, i) => `<div class="excel-col-cell">${letters[i]||'?'}</div>`).join('');
                        const hS = `class="excel-cell excel-cell-header" style="text-align:center;font-size:9px;"`;
                        const rows = [];
                        rows.push(`<tr><td class="excel-row-num">1</td><td class="excel-cell excel-cell-title" colspan="8" style="padding:4px 5px;font-weight:bold;">BUKU INVENTARIS ASET DESA - ${kibLabel}</td></tr>`);
                        rows.push(`<tr><td class="excel-row-num">2</td>${kibCols.map(h => `<td ${hS}>${h}</td>`).join('')}</tr>`);
                        const placeholders = Math.min(currentDataCount > 0 ? currentDataCount : 3, 3);
                        for (let r = 0; r < placeholders; r++) {
                            const cls = r % 2 === 0 ? 'excel-cell-data' : 'excel-cell-data alt';
                            rows.push(`<tr><td class="excel-row-num">${r+3}</td><td class="excel-cell ${cls}" style="text-align:center;">${r+1}</td>${Array(7).fill(`<td class="excel-cell ${cls}">...</td>`).join('')}</tr>`);
                        }
                        if (currentDataCount > 3) {
                            rows.push(`<tr><td class="excel-row-num" style="color:#9ca3af;">...</td><td class="excel-cell" colspan="8" style="text-align:center;color:#9ca3af;font-size:9px;">... +${currentDataCount - 3} data lainnya</td></tr>`);
                        }
                        rows.push(`<tr><td class="excel-row-num"></td><td class="excel-cell excel-cell-header" colspan="6" style="text-align:center;">JUMLAH HARGA</td><td class="excel-cell excel-cell-header" style="text-align:center;">SUM</td><td class="excel-cell"></td></tr>`);
                        tbody.innerHTML = rows.join('');
                    } else {
                        document.getElementById('pdf-filename').textContent = baseName + '.pdf';
                        const pdfContent = document.getElementById('pdf-preview-content');
                        const placeholders = Math.min(currentDataCount > 0 ? currentDataCount : 3, 3);
                        let pdfHtml = `<div style="font-family:Arial,sans-serif;font-size:9px;">`;
                        pdfHtml += `<div style="text-align:center;font-weight:bold;font-size:11px;margin-bottom:2px;">BUKU INVENTARIS ASET DESA</div>`;
                        pdfHtml += `<div style="text-align:center;font-weight:bold;font-size:10px;margin-bottom:6px;">${kibLabel}</div>`;
                        pdfHtml += `<table style="width:100%;border-collapse:collapse;font-size:8px;"><thead><tr style="background:#f9fafb;">`;
                        ['NO','JENIS BARANG / NAMA BARANG','KODE BARANG','REG','... (field KIB)','ASAL-USUL','HARGA Rp'].forEach(h => {
                            pdfHtml += `<th style="border:1px solid #aaa;padding:3px 4px;text-align:center;font-weight:bold;">${h}</th>`;
                        });
                        pdfHtml += `</tr></thead><tbody>`;
                        for (let r = 0; r < placeholders; r++) {
                            pdfHtml += `<tr>${Array(7).fill(`<td style="border:1px solid #aaa;padding:2px 4px;color:#9ca3af;">...</td>`).join('')}</tr>`;
                        }
                        if (currentDataCount > 3) {
                            pdfHtml += `<tr><td colspan="7" style="border:1px solid #aaa;padding:3px;text-align:center;color:#9ca3af;font-size:8px;">... +${currentDataCount - 3} data lainnya</td></tr>`;
                        }
                        pdfHtml += `<tr><td colspan="6" style="border:1px solid #aaa;padding:3px;font-weight:bold;text-align:center;">JUMLAH HARGA</td><td style="border:1px solid #aaa;padding:3px;font-weight:bold;text-align:right;">S</td></tr>`;
                        pdfHtml += `</tbody></table></div>`;
                        pdfContent.innerHTML = pdfHtml;
                    }

                    const signerPanel = document.getElementById('preview-signer-panel');
                    if (signerPanel) signerPanel.style.display = 'none';

                    const btnConfirm = document.getElementById('btn-confirm-export');
                    const btnText    = document.getElementById('btn-confirm-export-text');
                    if (btnConfirm) { btnConfirm.disabled = false; btnConfirm.style.opacity = ''; btnConfirm.style.cursor = ''; }
                    if (btnText)    { btnText.textContent = 'Export KIB Sekarang'; }

                    document.getElementById('preview-export-modal').classList.add('show');
                    if (typeof feather !== 'undefined') feather.replace();
                    return;
                }

                if (!selectedJenisId) return;
                
                const format = document.querySelector('input[name="export-format"]:checked').value;
                const sig = document.querySelector('input[name="signature-option"]:checked').value;

                document.getElementById('preview-format').textContent = formatLabel(format);
                document.getElementById('preview-count').textContent = selectedJenisId === 'all'
                    ? (currentDataCount + ' data semua jenis inventaris')
                    : (currentDataCount + ' data ' + jenisData[selectedJenisId].nama.toLowerCase());
                document.getElementById('preview-sig').textContent = sig === 'with' ? 'Dengan Tanda Tangan' : 'Tanpa Tanda Tangan';
                document.getElementById('preview-date').textContent = document.getElementById('date-preview').textContent;

                // Disable tombol Export jika jenis tunggal + TTD aktif + tidak ada pejabat
                const btnConfirm = document.getElementById('btn-confirm-export');
                const btnText = document.getElementById('btn-confirm-export-text');
                const noPejabatWarning = renderPreviewSignerPanel(sig);
                if (btnConfirm) {
                    btnConfirm.disabled = noPejabatWarning;
                    if (noPejabatWarning) {
                        btnConfirm.style.opacity = '0.5';
                        btnConfirm.style.cursor = 'not-allowed';
                        if (btnText) btnText.textContent = 'Tambahkan Pejabat Dulu';
                    } else {
                        btnConfirm.style.opacity = '';
                        btnConfirm.style.cursor = '';
                        if (btnText) btnText.textContent = 'Export Sekarang';
                    }
                }

                if (selectedJenisId === 'all') {
                    const baseName = 'inventaris_semua_jenis_' + selectedTahun;
                    const jenisList = Object.values(jenisData || {}).filter(j => !j.is_virtual && !j.is_orphan);

                    document.getElementById('preview-wrapper-excel').style.display = format === 'excel' ? '' : 'none';
                    document.getElementById('preview-wrapper-pdf').style.display = format === 'pdf' ? '' : 'none';

                    if (format === 'excel') {
                        const tbody = document.getElementById('excel-preview-table');
                        const colBar = document.getElementById('excel-col-bar');
                        document.getElementById('excel-filename').textContent = baseName + '.xlsx';

                        // Excel preview "Semua Jenis": tampilkan preview jenis pertama dengan format SAMA seperti single jenis
                        const firstJenis = jenisList[0];
                        
                        // Build columns dari fields jenis pertama
                        const cols = ['No'];
                        (firstJenis.fields || []).forEach(f => {
                            if (f.fixed === 'jenis_barang') return;
                            if (f.type === 'fixed' && f.fixed === 'kondisi') {
                                cols.push('Baik', 'Rusak Ringan', 'Rusak Berat');
                            } else if (f.fixed === 'nama_barang') {
                                cols.push('Jenis Barang / Nama Barang');
                            } else {
                                cols.push(f.label);
                            }
                        });

                        const span = cols.length;
                        const letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                        colBar.innerHTML = cols.map((_, i) => `<div class="excel-col-cell">${letters[i] || '?'}</div>`).join('');

                        const rows = [];
                        // Format SAMA dengan single jenis: Row 1 = ASET DESA, Row 2 = BERUPA [JENIS], Row 3 = Empty, Row 4 = Header
                        rows.push(`<tr><td class="excel-row-num">1</td><td class="excel-cell excel-cell-title" colspan="${span}" style="padding:4px 5px;">ASET DESA</td></tr>`);
                        rows.push(`<tr><td class="excel-row-num">2</td><td class="excel-cell excel-cell-subtitle" colspan="${span}" style="padding:3px 5px;">BERUPA ${firstJenis.nama.toUpperCase()}</td></tr>`);
                        rows.push(`<tr><td class="excel-row-num" style="background:#fff;border-right:1px solid #d0d5dd;">3</td>${cols.map(() => '<td class="excel-cell"></td>').join('')}</tr>`);
                        
                        // Row 4: Header
                        rows.push(`<tr><td class="excel-row-num">4</td>${cols.map(c => `<td class="excel-cell excel-cell-header">${c}</td>`).join('')}</tr>`);

                        // Data rows (3 placeholder)
                        const placeholders = 3;
                        for (let r = 0; r < placeholders; r++) {
                            const cls = r % 2 === 0 ? 'excel-cell-data' : 'excel-cell-data alt';
                            rows.push(`<tr><td class="excel-row-num">${r+5}</td><td class="excel-cell ${cls}" style="text-align:center;">${r+1}</td>${cols.slice(1).map(() => `<td class="excel-cell ${cls}">...</td>`).join('')}</tr>`);
                        }

                        // Jumlah row
                        const jumlahRowNum = 8;
                        rows.push(`<tr><td class="excel-row-num">${jumlahRowNum}</td><td class="excel-cell" colspan="${span-1}" style="font-weight:700;font-size:10px;">Jumlah ${'-'.repeat(12)}</td><td class="excel-cell" style="font-weight:700;text-align:right;font-size:10px;color:#217346;">SUM</td></tr>`);
                        rows.push(`<tr><td style="width:32px;min-width:32px;background:#f2f2f2;border-right:1px solid #d0d5dd;"></td><td colspan="${span}" style="border:none;height:6px;"></td></tr>`);

                        // Footer TIM INVENTARISASI
                        const signers = getActiveSignerList();
                        if (signers.length > 0) {
                            const leftSpan = Math.max(1, Math.floor(span * 0.65));
                            const rightSpan = span - leftSpan;
                            rows.push(`<tr><td style="width:32px;min-width:32px;background:#f2f2f2;border-right:1px solid #d0d5dd;"></td><td colspan="${leftSpan}" style="border:none;"></td><td colspan="${rightSpan}" style="border:none;padding:8px 4px 4px;font-size:10px;font-weight:700;text-align:center;">TIM INVENTARISASI</td></tr>`);
                            signers.forEach((p, i) => {
                                const nameSpan = Math.max(1, Math.floor(rightSpan * 0.55));
                                const ttdSpan  = rightSpan - nameSpan;
                                rows.push(`<tr><td style="width:32px;min-width:32px;background:#f2f2f2;border-right:1px solid #d0d5dd;"></td><td colspan="${leftSpan}" style="border:none;"></td><td colspan="${nameSpan}" style="border:none;padding:2px 4px;font-size:10px;">${i+1}. ${p.nama}</td><td colspan="${ttdSpan}" style="border:none;padding:2px 4px;font-size:10px;text-align:center;">(${'\u00a0'.repeat(12)})</td></tr>`);
                            });
                        }

                        // Sheet tabs bar di bawah (tampilkan semua jenis)
                        const displaySheets = jenisList.slice(0, 5);
                        const moreCount = jenisList.length > 5 ? jenisList.length - 5 : 0;
                        const sheetTabsHtml = `
                            <div style="display:flex;align-items:center;gap:0;border-top:2px solid #d0d5dd;margin-top:4px;background:#f2f2f2;overflow-x:auto;flex-wrap:nowrap;">
                                <div style="padding:5px 12px;font-size:10px;font-weight:700;background:#fff;border-right:1px solid #d0d5dd;border-top:2px solid #217346;color:#217346;white-space:nowrap;flex-shrink:0;">${firstJenis.nama}</div>
                                ${displaySheets.slice(1).map(j => `<div style="padding:5px 10px;font-size:10px;color:#6b7280;border-right:1px solid #d0d5dd;white-space:nowrap;flex-shrink:0;">${j.nama}</div>`).join('')}
                                ${moreCount > 0 ? `<div style="padding:5px 8px;font-size:10px;color:#9ca3af;font-style:italic;flex-shrink:0;">+${moreCount} sheets</div>` : ''}
                            </div>`;

                        rows.push(`<tr><td colspan="${span + 1}" style="padding:0;border:none;">${sheetTabsHtml}</td></tr>`);
                        tbody.innerHTML = rows.join('');
                    } else {
                        document.getElementById('pdf-filename').textContent = baseName + '.pdf';
                        const pdfContent = document.getElementById('pdf-preview-content');

                        // PDF preview "Semua Jenis": render semua page secara vertikal tanpa navigation tabs
                        let allPagesHtml = '';
                        
                        jenisList.forEach((jenis, pageIdx) => {
                            // Build columns dari fields jenis ini
                            const cols = ['No'];
                            (jenis.fields || []).forEach(f => {
                                if (f.fixed === 'jenis_barang') return;
                                if (f.type === 'fixed' && f.fixed === 'kondisi') {
                                    cols.push('Baik', 'Rusak Ringan', 'Rusak Berat');
                                } else if (f.fixed === 'nama_barang') {
                                    cols.push('Jenis Barang / Nama Barang');
                                } else {
                                    cols.push(f.label);
                                }
                            });

                            const span = cols.length;
                            const thStyle = 'padding:5px 4px;background:#ffffff;color:#111111;font-size:10px;font-weight:bold;text-align:center;border:1px solid #000000;white-space:nowrap;vertical-align:middle;';
                            const tdStyle = () => 'padding:3px 5px;font-size:10px;border:1px solid #000000;color:#111111;background:#ffffff;vertical-align:middle;';

                            // Header table
                            const headerRow = `<tr>${cols.map(c => `<th style="${thStyle}">${c}</th>`).join('')}</tr>`;
                            
                            // Data rows (3 placeholder per page)
                            const dataRows = [];
                            for (let r = 0; r < 3; r++) {
                                dataRows.push(`<tr><td style="${tdStyle()}text-align:center;">${r+1}</td>${cols.slice(1).map(() => `<td style="${tdStyle()}">...</td>`).join('')}</tr>`);
                            }
                            const jumlahRow = `<tr><td colspan="${span-1}" style="${tdStyle()}font-weight:bold;">Jumlah ${'-'.repeat(20)}</td><td style="${tdStyle()}font-weight:bold;">SUM</td></tr>`;

                            // Footer: TIM INVENTARISASI (sama untuk semua page)
                            const signers = getActiveSignerList();
                            const signerHtml = signers.length > 0
                                ? `<table style="width:100%;border-collapse:collapse;margin-top:12px;"><tr>
                                     <td style="width:55%;border:none;vertical-align:top;"></td>
                                     <td style="width:45%;border:none;vertical-align:top;padding-left:12px;">
                                       <div style="font-size:11px;font-weight:bold;text-align:center;margin-bottom:10px;">TIM INVENTARISASI</div>
                                       ${signers.map((p,i) => `<div style="display:flex;align-items:flex-end;gap:4px;margin-bottom:18px;"><span style="font-size:10px;white-space:nowrap;flex-shrink:0;">${i+1}. ${p.nama}</span><span style="font-size:11px;flex-shrink:0;margin-left:4px;">&nbsp;(</span><span style="flex:1;border-bottom:1px solid #111;min-height:20px;display:inline-block;"></span><span style="font-size:11px;flex-shrink:0;">)</span></div>`).join('')}
                                     </td>
                                   </tr></table>`
                                : '';

                            // Render satu page
                            allPagesHtml += `
                                <div style="background:#fff;padding:14px 18px;box-shadow:0 1px 4px rgba(0,0,0,0.10);border-radius:4px;margin-bottom:${pageIdx < jenisList.length - 1 ? '24px' : '0'};">
                                    <div style="text-align:center;font-size:13px;font-weight:bold;color:#111;margin-bottom:2px;">LAPORAN HASIL INVENTARIS ASET DESA</div>
                                    <div style="text-align:center;font-size:11px;font-weight:bold;color:#111;margin-bottom:10px;">BERUPA ${jenis.nama.toUpperCase()}</div>
                                    <div style="text-align:center;font-size:10px;color:#111;margin-bottom:30px;">Periode Tanggal, 31 Desember ${selectedTahun}</div>
                                    <div style="overflow-x:auto;margin-bottom:4px;">
                                        <table style="width:100%;border-collapse:collapse;">
                                            <thead>${headerRow}</thead>
                                            <tbody>${dataRows.join('')}</tbody>
                                            <tfoot>${jumlahRow}</tfoot>
                                        </table>
                                    </div>
                                    ${signerHtml}
                                    <div style="margin-top:12px;padding-top:8px;border-top:1.2px solid #1a1a1a;font-size:9.5px;color:#1a1a1a;display:flex;justify-content:space-between;">
                                        <span>Laporan Hasil Inventaris Aset Desa - Semua Jenis</span>
                                        <span>Halaman : ${pageIdx + 1} dari ${jenisList.length}</span>
                                    </div>
                                </div>`;
                        });

                        pdfContent.innerHTML = allPagesHtml;
                    }

                    document.getElementById('preview-export-modal').classList.add('show');
                    if (typeof feather !== 'undefined') feather.replace();
                    return;
                }

                const jenis = jenisData[selectedJenisId];

                // Build columns: Buku Inventaris pakai 11 kolom pakem, jenis lain pakai fields
                // Kolom diambil dari fields sesuai urutan export (sudah diurutkan by order dari DB)
                const cols = ['No'];
                if (jenis.is_virtual) {
                    ['Jenis Barang', 'Kode Barang', 'Nama / Identitas Barang', 'APBDes', 'Perolehan Lain yang Sah', 'Kekayaan Asli Desa', 'Tahun Perolehan', 'Nilai Aset (Rp)', 'Pengguna', 'Ket.'].forEach(c => cols.push(c));
                } else {
                    (jenis.fields || []).forEach(f => {
                        // Skip jenis_barang - sudah digabung ke nama_barang
                        if (f.fixed === 'jenis_barang') return;
                        if (f.type === 'fixed' && f.fixed === 'kondisi') {
                            cols.push('Baik', 'Rusak Ringan', 'Rusak Berat');
                        } else if (f.fixed === 'nama_barang') {
                            // Ganti label nama_barang menjadi "Jenis Barang / Nama Barang"
                            cols.push('Jenis Barang / Nama Barang');
                        } else {
                            cols.push(f.label);
                        }
                    });
                }

                const span = cols.length;
                const baseName = jenis.nama.toLowerCase().replace(/\s+/g, '_') + '_' + selectedTahun;
                const nilaiIdx = cols.findIndex((c, i) => i > 0 && c.toLowerCase().includes('nilai'));

                // Show/hide format-specific wrappers
                document.getElementById('preview-wrapper-excel').style.display = format === 'excel' ? '' : 'none';
                document.getElementById('preview-wrapper-pdf').style.display   = format === 'pdf'   ? '' : 'none';

                // -- EXCEL PREVIEW ------------------------------------------
                if (format === 'excel') {
                    const tbody  = document.getElementById('excel-preview-table');
                    const colBar = document.getElementById('excel-col-bar');
                    document.getElementById('excel-filename').textContent = baseName + '.xlsx';

                    const letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                    colBar.innerHTML = cols.map((_, i) =>
                        `<div class="excel-col-cell">${letters[i] || '?'}</div>`
                    ).join('');

                    const rows = [];
                    rows.push(`<tr><td class="excel-row-num">1</td><td class="excel-cell excel-cell-title" colspan="${span}" style="padding:4px 5px;">ASET DESA</td></tr>`);
                    rows.push(`<tr><td class="excel-row-num">2</td><td class="excel-cell excel-cell-subtitle" colspan="${span}" style="padding:3px 5px;">BERUPA ${jenis.nama.toUpperCase()}</td></tr>`);
                    if (jenis.is_virtual) {
                        // 2-row nested header: Asal Usul Barang sebagai group (colspan=3) sama persis seperti Excel asli
                        const hS = `class="excel-cell excel-cell-header" style="text-align:center;vertical-align:middle;font-size:9px;"`;
                        rows.push(`<tr>
                            <td class="excel-row-num">3</td>
                            <td ${hS} rowspan="2">No</td>
                            <td ${hS} rowspan="2">Jenis Barang</td>
                            <td ${hS} rowspan="2">Kode Barang</td>
                            <td ${hS} rowspan="2">Nama / Identitas Barang</td>
                            <td ${hS} colspan="3">Asal Usul Barang</td>
                            <td ${hS} rowspan="2">Tahun Perolehan</td>
                            <td ${hS} rowspan="2">Nilai Aset (Rp)</td>
                            <td ${hS} rowspan="2">Pengguna</td>
                            <td ${hS} rowspan="2">Ket.</td>
                        </tr>`);
                        rows.push(`<tr>
                            <td class="excel-row-num">4</td>
                            <td ${hS}>APBDes</td>
                            <td ${hS}>Perolehan Lain yang Sah</td>
                            <td ${hS}>Kekayaan Asli Desa</td>
                        </tr>`);
                    } else {
                        rows.push(`<tr><td class="excel-row-num" style="background:#fff;border-right:1px solid #d0d5dd;">3</td>${cols.map(() => '<td class="excel-cell"></td>').join('')}</tr>`);
                        rows.push(`<tr><td class="excel-row-num">4</td>${cols.map(c => `<td class="excel-cell excel-cell-header">${c}</td>`).join('')}</tr>`);
                    }

                    const placeholders = currentDataCount > 0 ? Math.min(currentDataCount, 3) : 3;
                    for (let r = 0; r < placeholders; r++) {
                        const cls = r % 2 === 0 ? 'excel-cell-data' : 'excel-cell-data alt';
                        rows.push(`<tr><td class="excel-row-num">${r+5}</td><td class="excel-cell ${cls}" style="text-align:center;">${r+1}</td>${cols.slice(1).map(() => `<td class="excel-cell ${cls}">...</td>`).join('')}</tr>`);
                    }
                    if (currentDataCount > 3) {
                        rows.push(`<tr><td class="excel-row-num" style="color:#9ca3af;">...</td><td class="excel-cell" colspan="${span}" style="text-align:center;color:#9ca3af;font-style:italic;padding:4px;">+ ${currentDataCount - 3} baris data lainnya</td></tr>`);
                    } else if (currentDataCount === 0) {
                        rows[rows.length - 1] = `<tr><td class="excel-row-num">5</td><td class="excel-cell" colspan="${span}" style="text-align:center;color:#9ca3af;font-style:italic;padding:6px;">Tidak ada data untuk tahun ${selectedTahun}</td></tr>`;
                    }

                    const lastDataRow = currentDataCount > 3 ? 8 : (Math.max(currentDataCount, 1) + 4);
                    const jumlahRowNum = lastDataRow + 1;
                    let jumlahCells = '';
                    if (nilaiIdx > 0) {
                        const beforeCount = nilaiIdx;
                        const afterCount  = cols.length - nilaiIdx - 1;
                        jumlahCells = `<td class="excel-cell" colspan="${beforeCount}" style="font-weight:700;font-size:10px;">Jumlah ${'-'.repeat(12)}</td>
                            <td class="excel-cell" style="font-weight:700;text-align:right;font-size:10px;color:#217346;">SUM</td>
                            ${afterCount > 0 ? `<td class="excel-cell" colspan="${afterCount}"></td>` : ''}`;
                    } else {
                        jumlahCells = `<td class="excel-cell" colspan="${span}" style="font-weight:700;font-size:10px;">Jumlah ${'-'.repeat(20)}</td>`;
                    }
                    rows.push(`<tr><td class="excel-row-num">${jumlahRowNum}</td>${jumlahCells}</tr>`);
                    rows.push(`<tr><td style="width:32px;min-width:32px;background:#f2f2f2;border-right:1px solid #d0d5dd;"></td><td colspan="${span}" style="border:none;height:6px;"></td></tr>`);

                    // Tim Inventarisasi di Excel preview — di kanan (~30% kolom terakhir), sesuai export asli
                    const signers = getActiveSignerList();
                    if (signers.length > 0) {
                        const leftSpan = Math.max(1, Math.floor(span * 0.65));
                        const rightSpan = span - leftSpan;
                        rows.push(`<tr><td style="width:32px;min-width:32px;background:#f2f2f2;border-right:1px solid #d0d5dd;"></td><td colspan="${leftSpan}" style="border:none;"></td><td colspan="${rightSpan}" style="border:none;padding:8px 4px 4px;font-size:10px;font-weight:700;text-align:center;">TIM INVENTARISASI</td></tr>`);
                        signers.forEach((p, i) => {
                            const nameSpan = Math.max(1, Math.floor(rightSpan * 0.55));
                            const ttdSpan  = rightSpan - nameSpan;
                            rows.push(`<tr><td style="width:32px;min-width:32px;background:#f2f2f2;border-right:1px solid #d0d5dd;"></td><td colspan="${leftSpan}" style="border:none;"></td><td colspan="${nameSpan}" style="border:none;padding:2px 4px;font-size:10px;">${i+1}. ${p.nama}</td><td colspan="${ttdSpan}" style="border:none;padding:2px 4px;font-size:10px;text-align:center;">(${'\u00a0'.repeat(12)})</td></tr>`);
                        });
                    }
                    tbody.innerHTML = rows.join('');
                }

                // -- PDF PREVIEW --------------------------------------------
                else if (format === 'pdf') {
                    document.getElementById('pdf-filename').textContent = baseName + '.pdf';
                    const pdfContent = document.getElementById('pdf-preview-content');

                    // Style sesuai PDF asli: header putih, border hitam
                    const thStyle = 'padding:5px 4px;background:#ffffff;color:#111111;font-size:10px;font-weight:bold;text-align:center;border:1px solid #000000;white-space:nowrap;vertical-align:middle;';
                    const tdStyle = () => 'padding:3px 5px;font-size:10px;border:1px solid #000000;color:#111111;background:#ffffff;vertical-align:middle;';

                    // Buku Inventaris (virtual): 2-row nested header sama seperti Excel preview
                    let headerRow;
                    if (jenis.is_virtual) {
                        headerRow = `
                            <tr>
                                <th style="${thStyle}" rowspan="2">No</th>
                                <th style="${thStyle}" rowspan="2">Jenis Barang</th>
                                <th style="${thStyle}" rowspan="2">Kode Barang</th>
                                <th style="${thStyle}" rowspan="2">Nama / Identitas Barang</th>
                                <th style="${thStyle}" colspan="3">Asal Usul Barang</th>
                                <th style="${thStyle}" rowspan="2">Tahun Perolehan</th>
                                <th style="${thStyle}" rowspan="2">Nilai Aset (Rp)</th>
                                <th style="${thStyle}" rowspan="2">Pengguna</th>
                                <th style="${thStyle}" rowspan="2">Ket.</th>
                            </tr>
                            <tr>
                                <th style="${thStyle}">APBDes</th>
                                <th style="${thStyle}">Perolehan Lain yang Sah</th>
                                <th style="${thStyle}">Kekayaan Asli Desa</th>
                            </tr>`;
                    } else {
                        headerRow = `<tr>${cols.map(c => `<th style="${thStyle}">${c}</th>`).join('')}</tr>`;
                    }
                    const dataRows = [];
                    const ph = currentDataCount > 0 ? Math.min(currentDataCount, 3) : 3;
                    for (let r = 0; r < ph; r++) {
                        dataRows.push(`<tr><td style="${tdStyle()}text-align:center;">${r+1}</td>${cols.slice(1).map(() => `<td style="${tdStyle()}">...</td>`).join('')}</tr>`);
                    }
                    const extraRow = currentDataCount > 3
                        ? `<tr><td colspan="${span}" style="${tdStyle()}text-align:center;font-style:italic;color:#6b7280;">... +${currentDataCount-3} data lainnya</td></tr>`
                        : (currentDataCount === 0 ? `<tr><td colspan="${span}" style="${tdStyle()}text-align:center;font-style:italic;color:#9ca3af;">Tidak ada data untuk tahun ${selectedTahun}</td></tr>` : '');
                    const jumlahRow = nilaiIdx > 0
                        ? `<tr>${cols.map((c,i) => i===0 ? `<td colspan="${nilaiIdx}" style="${tdStyle()}font-weight:bold;">Jumlah ${'-'.repeat(20)}</td>` : (i===nilaiIdx ? `<td style="${tdStyle()}font-weight:bold;">SUM</td>` : (i > nilaiIdx ? `<td style="${tdStyle()}"></td>` : ''))).filter(Boolean).join('')}</tr>`
                        : `<tr><td colspan="${span}" style="${tdStyle()}font-weight:bold;">Jumlah ${'-'.repeat(20)}</td></tr>`;

                    // Footer: TIM INVENTARISASI di kanan (sesuai export asli PDF)
                    const signers = getActiveSignerList();
                    const signerHtml = signers.length > 0
                        ? `<table style="width:100%;border-collapse:collapse;margin-top:12px;"><tr>
                             <td style="width:55%;border:none;vertical-align:top;"></td>
                             <td style="width:45%;border:none;vertical-align:top;padding-left:12px;">
                               <div style="font-size:11px;font-weight:bold;text-align:center;margin-bottom:10px;">TIM INVENTARISASI</div>
                               ${signers.map((p,i) => `<div style="display:flex;align-items:flex-end;gap:4px;margin-bottom:18px;"><span style="font-size:10px;white-space:nowrap;flex-shrink:0;">${i+1}. ${p.nama}</span><span style="font-size:11px;flex-shrink:0;margin-left:4px;">&nbsp;(</span><span style="flex:1;border-bottom:1px solid #111;min-height:20px;display:inline-block;"></span><span style="font-size:11px;flex-shrink:0;">)</span></div>`).join('')}
                             </td>
                           </tr></table>`
                        : '';

                    pdfContent.innerHTML = `
                        <div style="background:#fff;padding:14px 18px;box-shadow:0 1px 4px rgba(0,0,0,0.10);border-radius:4px;">
                            <div style="text-align:center;font-size:13px;font-weight:bold;color:#111;margin-bottom:2px;">ASET DESA</div>
                            <div style="text-align:center;font-size:11px;font-weight:bold;color:#111;margin-bottom:10px;">BERUPA ${jenis.nama.toUpperCase()}</div>
                            <div style="overflow-x:auto;margin-bottom:4px;">
                                <table style="width:100%;border-collapse:collapse;">
                                    <thead>${headerRow}</thead>
                                    <tbody>${dataRows.join('')}${extraRow}</tbody>
                                    <tfoot>${jumlahRow}</tfoot>
                                </table>
                            </div>
                            ${signerHtml}
                        </div>`;
                }

                document.getElementById('preview-export-modal').classList.add('show');
                if(typeof feather !== 'undefined') feather.replace();
            });

            function closePreviewModal() {
                document.getElementById('preview-export-modal').classList.remove('show');
            }

            // -- Export Semua Jenis -----------------------------------------
            function doExportAll(skipPrompt = false) {
                const format = document.querySelector('input[name="export-format"]:checked').value;
                const signatureOption = document.querySelector('input[name="signature-option"]:checked').value;
                const tahun  = selectedTahun;
                const sidebarBtn = document.getElementById('btn-preview-export');
                const modalBtn = document.getElementById('btn-confirm-export');
                const modalBtnText = document.getElementById('btn-confirm-export-text');
                const sidebarBtnHtml = sidebarBtn ? sidebarBtn.innerHTML : '';

                if (!skipPrompt && !confirm(`Export semua jenis inventaris (tahun ${tahun}) ke 1 file ${format.toUpperCase()}?

Semua data jenis inventaris akan digabung dalam satu file output.

Proses ini mungkin membutuhkan waktu beberapa saat.`)) {
                    return;
                }

                if (skipPrompt) {
                    if (modalBtn) modalBtn.disabled = true;
                } else if (sidebarBtn) {
                    sidebarBtn.disabled = true;
                }

                const formData = new FormData();
                formData.append('format', format);
                formData.append('tahun', tahun);
                formData.append('signature_option', signatureOption);
                formData.append('paper_size', getSelectedPaperSize());
                formData.append('tanggal_dokumen', document.getElementById('doc-tanggal').value || '');
                formData.append('keterangan_dokumen', '');

                // Sertakan informasi dokumen (lampiran & BA) — sama untuk semua sheet
                const _lampAll   = document.getElementById('doc-lampiran');
                const _baNomAll  = document.getElementById('doc-ba-nomor');
                const _baDatAll  = document.getElementById('doc-ba-tanggal');
                if (_lampAll)  formData.append('doc_lampiran',  _lampAll.value  || '');
                if (_baNomAll) formData.append('doc_ba_nomor',  _baNomAll.value || '');
                if (_baDatAll) formData.append('doc_ba_tanggal', _baDatAll.value || '');

                // Sertakan custom pejabat yang dipilih untuk Buku Inventaris sheet
                if (customPejabat.length > 0) {
                    customPejabat.forEach((p, index) => {
                        formData.append(`pejabat_override[${index}]`, p.id);
                    });
                }

                if (typeof AppLoader !== 'undefined') AppLoader.show();
                fetch('{{ route("export-data.store-all") }}', {
                    method: 'POST',
                    body:   formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    },
                })
                .then(async response => {
                    const json = await response.json();
                    if (!response.ok || json.success === false) throw new Error(json.message || 'Export gagal');
                    return json;
                })
                .then(data => {
                    if (skipPrompt) {
                        closePreviewModal();
                    }
                    showToast('Export semua jenis berhasil! File sedang diunduh...', 'success');

                    // Simpan info dokumen ke localStorage hanya jika format PDF
                    if (format === 'pdf') {
                        const _l = document.getElementById('doc-lampiran');
                        const _n = document.getElementById('doc-ba-nomor');
                        const _d = document.getElementById('doc-ba-tanggal');
                        docStorageSave(
                            _l ? _l.value.trim() : '',
                            _n ? _n.value.trim() : '',
                            _d ? _d.value        : ''
                        );
                    }

                    if (data.file_url) {
                        saveLastExport(data.file_url, format);
                        setTimeout(() => {
                            const link = document.createElement('a');
                            link.href = data.file_url;
                            link.download = data.filename;
                            document.body.appendChild(link);
                            link.click();
                            setTimeout(() => link.remove(), 200);
                        }, 500);
                    }

                    refreshHistoryTableSafe();
                })
                .catch(error => {
                    showToast(error.message || 'Terjadi kesalahan saat export semua jenis', 'error');
                })
                .finally(() => {
                    if (typeof AppLoader !== 'undefined') AppLoader.hide();
                    if (skipPrompt) {
                        if (modalBtn) modalBtn.disabled = false;
                        if (modalBtnText) modalBtnText.textContent = 'Export Sekarang';
                    } else if (sidebarBtn) {
                        sidebarBtn.disabled = false;
                        sidebarBtn.innerHTML = sidebarBtnHtml;
                    }
                    if (typeof feather !== 'undefined') feather.replace();
                });
            }

            function confirmExport() {
                // Handle tab Laporan Aset
                if (activeExportTab === 'laporan') {
                    const tahunLaporan = selectedTahun;
                    const periode      = document.getElementById('laporan_periode_hidden')?.value || 'tahunan';
                    const format       = document.querySelector('input[name="export-format"]:checked').value;

                    if (!tahunLaporan) {
                        showToast('Pilih tahun laporan terlebih dahulu', 'warning');
                        return;
                    }

                    const btnConfirm = document.getElementById('btn-confirm-export');
                    const btnText    = document.getElementById('btn-confirm-export-text');
                    if (btnConfirm) { btnConfirm.disabled = true; }
                    if (btnText)    { btnText.textContent = 'Mengexport...'; }

                    const formData = new FormData();
                    formData.append('tahun', tahunLaporan);
                    formData.append('periode', periode);
                    formData.append('format', format);

                    if (typeof AppLoader !== 'undefined') AppLoader.show();
                    fetch('{{ route("export-data.store-laporan-aset") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                        }
                    })
                    .then(async response => {
                        const json = await response.json();
                        if (!response.ok || json.success === false) {
                            throw new Error(json.message || 'Export Laporan Aset gagal');
                        }
                        return json;
                    })
                    .then(data => {
                        closePreviewModal();
                        showToast('Export Laporan Aset berhasil! File sedang diunduh...', 'success');
                        if (data.file_url) {
                            saveLastExport(data.file_url, format);
                            setTimeout(() => {
                                const link = document.createElement('a');
                                link.href = data.file_url;
                                link.download = data.filename;
                                document.body.appendChild(link);
                                link.click();
                                setTimeout(() => link.remove(), 200);
                            }, 500);
                        }
                        refreshHistoryTableSafe();
                    })
                    .catch(error => {
                        console.error('Export Laporan Aset error:', error);
                        showToast(error.message || 'Terjadi kesalahan saat export laporan aset', 'error');
                    })
                    .finally(() => {
                        if (typeof AppLoader !== 'undefined') AppLoader.hide();
                        if (btnConfirm) { btnConfirm.disabled = false; }
                        if (btnText)    { btnText.textContent = 'Export Sekarang'; }
                    });

                    return;
                }

                // Handle tab KIB
                if (activeExportTab === 'kib') {
                    if (!selectedKib) {
                        showToast('Pilih KIB terlebih dahulu', 'warning');
                        return;
                    }

                    const format = document.querySelector('input[name="export-format"]:checked').value;
                    const sig    = document.querySelector('input[name="signature-option"]:checked').value;

                    const btnConfirm = document.getElementById('btn-confirm-export');
                    const btnText    = document.getElementById('btn-confirm-export-text');
                    if (btnConfirm) { btnConfirm.disabled = true; }
                    if (btnText)    { btnText.textContent = 'Mengexport...'; }

                    const formData = new FormData();
                    formData.append('kib_type', selectedKib);
                    formData.append('format', format);
                    formData.append('paper_size', getSelectedPaperSize());
                    formData.append('tahun', selectedTahun);
                    formData.append('signature_option', sig);
                    formData.append('tanggal_dokumen', document.getElementById('doc-tanggal').value || '');
                    formData.append('keterangan_dokumen', '');

                    // Pejabat override untuk KIB: hanya Petugas Barang (kanan) yang bisa diganti user
                    // Sekretaris Desa (kiri) adalah tetap — diisi otomatis oleh server
                    if (customPejabat.length >= 1 && customPejabat[0]) {
                        formData.append('pejabat_kanan', customPejabat[0].id);
                    }

                    if (typeof AppLoader !== 'undefined') AppLoader.show();
                    fetch('{{ route("export-data.store-kib") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                        }
                    })
                    .then(async response => {
                        const json = await response.json();
                        if (!response.ok || json.success === false) {
                            throw new Error(json.message || 'Export KIB gagal');
                        }
                        return json;
                    })
                    .then(data => {
                        closePreviewModal();
                        showToast('Export KIB berhasil! File sedang diunduh...', 'success');
                        if (data.file_url) {
                            saveLastExport(data.file_url, format);
                            setTimeout(() => {
                                const link = document.createElement('a');
                                link.href = data.file_url;
                                link.download = data.filename;
                                document.body.appendChild(link);
                                link.click();
                                setTimeout(() => link.remove(), 200);
                            }, 500);
                        }
                        refreshHistoryTableSafe();
                    })
                    .catch(error => {
                        console.error('Export KIB error:', error);
                        showToast(error.message || 'Terjadi kesalahan saat export KIB', 'error');
                    })
                    .finally(() => {
                        if (typeof AppLoader !== 'undefined') AppLoader.hide();
                        if (btnConfirm) { btnConfirm.disabled = false; }
                        if (btnText)    { btnText.textContent = 'Export Sekarang'; }
                    });

                    return;
                }

                // Handle tab ruangan
                if (activeExportTab === 'ruangan') {
                    if (!selectedRuangan) {
                        showToast('Pilih ruangan terlebih dahulu', 'warning');
                        return;
                    }

                    const format = document.querySelector('input[name="export-format"]:checked').value;
                    const sig    = document.querySelector('input[name="signature-option"]:checked').value;

                    const btnConfirm = document.getElementById('btn-confirm-export');
                    const btnText    = document.getElementById('btn-confirm-export-text');
                    if (btnConfirm) { btnConfirm.disabled = true; }
                    if (btnText)    { btnText.textContent = 'Mengexport...'; }

                    const formData = new FormData();
                    formData.append('ruangan_id', selectedRuangan);
                    formData.append('format', format);
                    formData.append('paper_size', getSelectedPaperSize());
                    formData.append('tahun', selectedTahun);
                    formData.append('signature_option', sig);
                    formData.append('tanggal_dokumen', document.getElementById('doc-tanggal').value || '');
                    formData.append('keterangan_dokumen', '');
                    // Format KIR: standard (Permendagri) atau custom (Desa Demo) — hanya berlaku untuk PDF
                    const kirFormatEl = document.querySelector('input[name="kir-format"]:checked');
                    formData.append('kir_format', kirFormatEl ? kirFormatEl.value : 'standard');

                    if (customPejabat.length > 0) {
                        customPejabat.forEach((p, idx) => {
                            formData.append('pejabat_override[' + idx + ']', p.id);
                        });
                    }

                    if (typeof AppLoader !== 'undefined') AppLoader.show();
                    fetch('{{ route("export-data.store-ruangan") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                        }
                    })
                    .then(async response => {
                        const json = await response.json();
                        if (json.no_sekretaris) {
                            // Modal peringatan khusus: belum ada Sekretaris Desa
                            closePreviewModal();
                            showSekretarisWarningModal(json.message);
                            return;
                        }
                        if (!response.ok || json.success === false) {
                            throw new Error(json.message || 'Export gagal');
                        }
                        return json;
                    })
                    .then(data => {
                        if (!data) return; // sudah ditangani di atas
                        closePreviewModal();
                        showToast('Export Kartu Inventaris Ruangan berhasil! File sedang diunduh...', 'success');
                        if (data.file_url) {
                            saveLastExport(data.file_url, format);
                            setTimeout(() => {
                                const link = document.createElement('a');
                                link.href = data.file_url;
                                link.download = data.filename;
                                document.body.appendChild(link);
                                link.click();
                                setTimeout(() => link.remove(), 200);
                            }, 500);
                        }
                        refreshHistoryTableSafe();
                    })
                    .catch(error => {
                        console.error('Export ruangan error:', error);
                        showToast(error.message || 'Terjadi kesalahan saat export', 'error');
                    })
                    .finally(() => {
                        if (typeof AppLoader !== 'undefined') AppLoader.hide();
                        if (btnConfirm) { btnConfirm.disabled = false; }
                        if (btnText)    { btnText.textContent = 'Export Ruangan'; }
                    });

                    return;
                }

                if (!selectedJenisId) {
                    showToast('Pilih jenis inventaris terlebih dahulu', 'warning');
                    return;
                }

                const format = document.querySelector('input[name="export-format"]:checked').value;
                const signatureOption = document.querySelector('input[name="signature-option"]:checked').value;
                const tahun = selectedTahun;

                // Validasi: data kosong (skip untuk virtual jenis seperti Buku Inventaris)
                const isVirtualJenis = selectedJenisId && jenisData[selectedJenisId] && jenisData[selectedJenisId].is_virtual;
                if (currentDataCount === 0 && !isVirtualJenis) {
                    showToast('Tidak ada data untuk diexport pada tahun ' + tahun, 'warning');
                    return;
                }

                // Validasi: jenis tunggal + "dengan TTD" tapi tidak ada pejabat sama sekali
                if (selectedJenisId !== 'all' && signatureOption === 'with') {
                    const defaultPejabatIds = (jenisData[selectedJenisId] && jenisData[selectedJenisId].default_pejabat) || [];
                    const hasPejabat = customPejabat.length > 0 || defaultPejabatIds.length > 0;
                    if (!hasPejabat) {
                        showToast(
                            'Jenis inventaris ini belum memiliki pejabat penandatangan. ' +
                            'Tambahkan pejabat melalui tombol "Atur Pejabat & Tanda Tangan", atau pilih opsi export tanpa tanda tangan.',
                            'warning'
                        );
                        return;
                    }
                }

                if (selectedJenisId === 'all') {
                    doExportAll(true);
                    return;
                }

                // Loading state pada tombol
                const btnConfirm = document.getElementById('btn-confirm-export');
                const btnText = document.getElementById('btn-confirm-export-text');
                btnConfirm.disabled = true;
                btnText.textContent = 'Mengexport...';

                // Build form data
                const formData = new FormData();
                formData.append('jenis_inventaris_id', selectedJenisId);
                formData.append('format', format);
                formData.append('paper_size', getSelectedPaperSize());
                formData.append('tahun', tahun);
                formData.append('signature_option', signatureOption);
                formData.append('tanggal_dokumen', document.getElementById('doc-tanggal').value || '');
                formData.append('keterangan_dokumen', '');
                
                // Tambahkan informasi dokumen (lampiran & BA)
                const lampInput = document.getElementById('doc-lampiran');
                const baNumInput = document.getElementById('doc-ba-nomor');
                const baDateInput = document.getElementById('doc-ba-tanggal');
                if (lampInput) formData.append('doc_lampiran', lampInput.value || '');
                if (baNumInput) formData.append('doc_ba_nomor', baNumInput.value || '');
                if (baDateInput) formData.append('doc_ba_tanggal', baDateInput.value || '');
                
                // Add custom pejabat override jika ada
                if (customPejabat.length > 0) {
                    customPejabat.forEach((p, index) => {
                        formData.append(`pejabat_override[${index}]`, p.id);
                    });
                }

                // Submit via AJAX
                if (typeof AppLoader !== 'undefined') AppLoader.show();
                fetch('{{ route("export-data.store") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                })
                .then(async response => {
                    const json = await response.json();
                    if (!response.ok || json.success === false) {
                        throw new Error(json.message || 'Export gagal');
                    }
                    return json;
                })
                .then(data => {
                    closePreviewModal();
                    showToast('Export berhasil! File sedang diunduh...', 'success');
                    
                    // Simpan info dokumen ke localStorage hanya jika format PDF dan tab jenis
                    if (format === 'pdf' && activeExportTab === 'jenis') {
                        const lampInput   = document.getElementById('doc-lampiran');
                        const baNumInput  = document.getElementById('doc-ba-nomor');
                        const baDateInput = document.getElementById('doc-ba-tanggal');
                        docStorageSave(
                            lampInput   ? lampInput.value.trim()   : '',
                            baNumInput  ? baNumInput.value.trim()  : '',
                            baDateInput ? baDateInput.value        : ''
                        );
                    }

                    // Download file jika ada
                    if (data.file_url) {
                        saveLastExport(data.file_url, format);
                        setTimeout(() => {
                            const link = document.createElement('a');
                            link.href = data.file_url;
                            link.download = data.filename;
                            document.body.appendChild(link);
                            link.click();
                            setTimeout(() => link.remove(), 200);
                        }, 500);
                    }

                    // Refresh history (opsional jika fungsi tersedia)
                    refreshHistoryTableSafe();
                })
                .catch(error => {
                    console.error('Export error:', error);
                    showToast(error.message || 'Terjadi kesalahan saat export', 'error');
                })
                .finally(() => {
                    if (typeof AppLoader !== 'undefined') AppLoader.hide();
                    btnConfirm.disabled = false;
                    btnText.textContent = 'Export Sekarang';
                });
            }

            // Modal peringatan: belum ada Sekretaris Desa
            function showSekretarisWarningModal(message) {
                // Gunakan AppModal jika tersedia, fallback ke alert
                if (typeof AppModal !== 'undefined' && typeof AppModal.alert === 'function') {
                    AppModal.alert('Sekretaris Desa Belum Diatur', message || 'Belum ada user dengan role Sekretaris Desa. Silakan assign role Sekretaris Desa terlebih dahulu.');
                } else {
                    // Buat modal sederhana
                    const existing = document.getElementById('sekretaris-warning-modal');
                    if (existing) existing.remove();

                    const modal = document.createElement('div');
                    modal.id = 'sekretaris-warning-modal';
                    modal.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;display:flex;align-items:center;justify-content:center;';
                    modal.innerHTML = `
                        <div style="background:#fff;border-radius:16px;padding:28px;max-width:460px;width:90%;box-shadow:0 20px 60px rgba(0,0,0,.2);">
                            <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
                                <div style="width:40px;height:40px;background:#fef3c7;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i data-feather="alert-triangle" style="width:20px;height:20px;color:#d97706;"></i>
                                </div>
                                <h4 style="font-size:16px;font-weight:700;color:#1f2937;margin:0;">Sekretaris Desa Belum Diatur</h4>
                            </div>
                            <p style="font-size:13px;color:#6b7280;margin:0 0 20px;">${message || 'Belum ada user dengan role Sekretaris Desa. Silakan assign role Sekretaris Desa terlebih dahulu sebelum export Kartu Inventaris Ruangan.'}</p>
                            <div style="display:flex;gap:10px;justify-content:flex-end;">
                                <button onclick="document.getElementById('sekretaris-warning-modal').remove()" style="padding:9px 18px;border:1px solid #d1d5db;background:#fff;color:#374151;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">Tutup</button>
                                <a href="{{ route('users.index') }}" style="padding:9px 18px;background:#064635;color:#fff;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
                                    <i data-feather="users" style="width:14px;height:14px;"></i> Kelola Users
                                </a>
                            </div>
                        </div>
                    `;
                    document.body.appendChild(modal);
                    if (typeof feather !== 'undefined') feather.replace();
                }
            }

            // Toast (support type: success | error | warning | info)
            function showToast(message, type = 'success') {
                const configs = {
                    success: { bg: '#10b981', icon: 'check-circle', label: 'Berhasil!' },
                    error:   { bg: '#ef4444', icon: 'x-circle',     label: 'Gagal!' },
                    warning: { bg: '#f59e0b', icon: 'alert-triangle', label: 'Perhatian!' },
                    info:    { bg: '#3b82f6', icon: 'info',           label: 'Info' },
                };
                const cfg = configs[type] || configs.success;

                // Hapus toast sebelumnya yang masih ada
                const existing = document.getElementById('export-toast');
                if (existing) existing.remove();

                const toast = document.createElement('div');
                toast.id = 'export-toast';
                toast.style.cssText = `position:fixed;top:20px;right:20px;z-index:9999;min-width:300px;max-width:420px;
                    background:white;border-radius:12px;border-left:4px solid ${cfg.bg};
                    box-shadow:0 10px 40px rgba(0,0,0,0.15);display:flex;align-items:center;
                    gap:12px;padding:14px 16px;animation:slideInRight .35s ease-out;`;
                toast.innerHTML = `
                    <span style="color:${cfg.bg};flex-shrink:0;width:22px;height:22px;">
                        <i data-feather="${cfg.icon}" style="width:22px;height:22px;"></i>
                    </span>
                    <div style="flex:1;">
                        <div style="font-size:13px;font-weight:600;color:#1f2937;margin-bottom:2px;">${cfg.label}</div>
                        <div style="font-size:12px;color:#6b7280;">${message}</div>
                    </div>
                    <button onclick="document.getElementById('export-toast')?.remove()" style="background:none;border:none;cursor:pointer;color:#9ca3af;padding:2px;">
                        <i data-feather="x" style="width:14px;height:14px;"></i>
                    </button>
                `;
                if (!document.getElementById('export-toast-style')) {
                    const style = document.createElement('style');
                    style.id = 'export-toast-style';
                    style.textContent = '@keyframes slideInRight{from{transform:translateX(100%);opacity:0}to{transform:translateX(0);opacity:1}}';
                    document.head.appendChild(style);
                }
                document.body.appendChild(toast);
                if (typeof feather !== 'undefined') feather.replace();
                setTimeout(() => { if (toast.parentNode) toast.remove(); }, 5000);
            }

            // Floating Widget Functions
            function saveLastExport(url, format) {
                if (!url) return;
                try {
                    // Convert absolute URL to relative path to avoid cross-origin download & filename restrictions
                    const relativeUrl = new URL(url, window.location.origin).pathname;
                    const data = {
                        url: relativeUrl,
                        format: format || (relativeUrl.endsWith('pdf') ? 'pdf' : 'xlsx'),
                        timestamp: Date.now()
                    };
                    localStorage.setItem('last_export_data', JSON.stringify(data));
                    updateFloatingWidget();
                } catch (e) {
                    console.error('Error saving last export:', e);
                }
            }

            function updateFloatingWidget() {
                const widget = document.getElementById('last-export-widget');
                const btn = document.getElementById('last-export-btn');
                const formatLabel = document.getElementById('widget-format-label');
                
                if (!widget || !btn) return;
                
                const lastExportRaw = localStorage.getItem('last_export_data');
                if (!lastExportRaw) {
                    widget.style.display = 'none';
                    return;
                }
                
                try {
                    const data = JSON.parse(lastExportRaw);
                    if (!data || !data.url) {
                        widget.style.display = 'none';
                        return;
                    }
                    
                    btn.href = data.url;
                    
                    const format = (data.format || '').toLowerCase();
                    widget.className = '';
                    if (format === 'pdf') {
                        widget.classList.add('format-pdf');
                        formatLabel.textContent = 'PDF';
                    } else {
                        widget.classList.add('format-excel');
                        formatLabel.textContent = 'Excel';
                    }
                    
                    widget.style.display = 'block';
                } catch(e) {
                    console.error('Error parsing last export data:', e);
                    widget.style.display = 'none';
                }
            }

            function downloadLastExport(event) {
                event.preventDefault();
                const btn = document.getElementById('last-export-btn');
                if (!btn || !btn.href || btn.href === '#' || btn.href.endsWith('#')) return;
                
                const url = btn.href;
                
                // Show standard loader
                if (window.AppLoader) window.AppLoader.show();
                
                // Trigger native download using a programmatic <a> link with download attribute
                const link = document.createElement('a');
                link.href = url;
                link.download = url.split('/').pop() || 'download';
                document.body.appendChild(link);
                link.click();
                setTimeout(() => link.remove(), 200);
                
                // Hide loader and show success toast after a short delay to simulate completion
                setTimeout(() => {
                    if (window.AppLoader) window.AppLoader.hide();
                    if (typeof showToast === 'function') {
                        showToast('Hasil export terakhir berhasil diunduh.', 'success');
                    }
                }, 800);
            }

            // Initialize UI after DOM is fully loaded to ensure all elements (including the widget below) exist
            document.addEventListener('DOMContentLoaded', function() {
                renderExportTypeDropdown();
                renderJenisInventarisDropdown();
                renderRuanganDropdown();
                renderKibDropdown();
                renderLaporanDropdown();
                updateUI();
                updateCustomPejabatLabels();
                updateFloatingWidget();

                // Mobile responsive: pindahkan Riwayat Export ke bawah sidebar
                function repositionHistory() {
                    var historyEl = document.getElementById('history-section');
                    var sidebar = document.querySelector('.export-sidebar');
                    var mainEl = document.querySelector('.export-main');
                    if (!historyEl || !sidebar || !mainEl) return;

                    if (window.innerWidth <= 1024) {
                        if (historyEl.parentElement !== sidebar.parentElement || historyEl.previousElementSibling !== sidebar) {
                            sidebar.parentElement.appendChild(historyEl);
                        }
                    } else {
                        if (historyEl.parentElement !== mainEl) {
                            mainEl.appendChild(historyEl);
                        }
                    }
                }
                repositionHistory();
                window.addEventListener('resize', repositionHistory);
            });
        </script>

        <!-- Floating Last Export Download Widget -->
        <div id="last-export-widget" style="display: none; position: fixed; right: 24px; bottom: 85px; z-index: 9998; animation: slideUpWidget 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;">
            <!-- Tooltip -->
            <div class="widget-tooltip" style="position: absolute; right: 80px; top: 50%; transform: translateY(-50%); background: #1f2937; color: white; padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 500; white-space: nowrap; box-shadow: 0 4px 12px rgba(0,0,0,0.15); opacity: 0; pointer-events: none; transition: opacity 0.2s ease; font-family: 'Inter', sans-serif;">
                Unduh Hasil Export Terakhir (<span id="widget-format-label">PDF</span>)
            </div>
            <!-- Download Button -->
            <a id="last-export-btn" href="#" onclick="downloadLastExport(event)" style="
                display: block;
                width: 68px;
                height: 68px;
                text-decoration: none;
                position: relative;
                background: transparent !important;
                border: none !important;
                box-shadow: none !important;
                transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            " onmouseenter="this.parentNode.querySelector('.widget-tooltip').style.opacity = '1'" onmouseleave="this.parentNode.querySelector('.widget-tooltip').style.opacity = '0'" onmouseover="this.style.transform = 'scale(1.12) translateY(-4px)';" onmouseout="this.style.transform = 'scale(1) translateY(0)';">
                <!-- Custom SVG Icon without Blob Background -->
                <svg viewBox="-102.4 -102.4 1228.80 1228.80" id="widget-svg" version="1.1" xmlns="http://www.w3.org/2000/svg" style="width: 100%; height: 100%; transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);">
                    <g id="SVGRepo_iconCarrier">
                        <path d="M168.8 224h-8.4c-35.3 0-64 28.7-64 64v56.3c0.9-2.2 1.8-4.5 2.8-6.7 17.5-41.4 40.9-79.4 69.6-113.6zM96.5 679.7V736c0 35.3 28.7 64 64 64h8.4c-28.8-34.2-52.1-72.3-69.6-113.7-1-2.1-1.9-4.4-2.8-6.6z" fill="var(--env-accent, #FFEA00)"></path>
                        <path d="M863.5 224H258.3C178.5 294.4 128 397.4 128 512s50.5 217.6 130.3 288h605.1c35.3 0 64-28.7 64-64V288c0.1-35.4-28.5-64-63.9-64z" fill="var(--env-primary, #536DFE)"></path>
                        <path d="M125.1 289.7L512 673.6l382.6-385.5L512 543.9zM717.9 836.2c-14.9 9.5-19.3 29.3-9.8 44.2 6.1 9.6 16.4 14.8 27 14.8 5.9 0 11.8-1.6 17.2-5C791 865.6 825.7 835 855.1 800h-89c-15 13.2-31.1 25.3-48.2 36.2zM927.5 344.1v335.5c21.3-53 32.5-110 32.5-167.6 0-58.4-11.1-115.1-32.5-167.9z" fill="var(--env-accent, #FFEA00)"></path>
                        <path d="M774.3 148.8c-38-27.5-79.9-48.8-124.3-63.1C605.5 71.3 559.1 64 512 64c-60.5 0-119.1 11.8-174.4 35.2-53.4 22.6-101.3 54.9-142.4 96-9.2 9.2-18 18.8-26.4 28.7-28.8 34.2-52.1 72.3-69.6 113.7-0.9 2.2-1.9 4.4-2.8 6.7C74.9 397.6 64 454 64 512s10.9 114.4 32.5 167.7c0.9 2.2 1.8 4.5 2.8 6.7 17.5 41.4 40.8 79.5 69.6 113.7 8.3 9.9 17.1 19.5 26.4 28.7 41.1 41.1 89 73.4 142.4 96C392.9 948.2 451.5 960 512 960c23.1 0 46.3-1.8 69-5.3 17.5-2.7 29.4-19 26.7-36.5s-19.1-29.4-36.5-26.7c-19.5 3-39.4 4.5-59.2 4.5-97.1 0-186-36.3-253.7-96C178.5 729.6 128 626.6 128 512s50.5-217.6 130.3-288c67.7-59.7 156.5-96 253.7-96 40.4 0 80.2 6.2 118.3 18.6 51.5 16.6 97.4 43.3 136.1 77.4h88.7c-23.7-28.3-50.8-53.5-80.8-75.2z" fill="var(--env-accent, #FFEA00)"></path>
                        <path d="M96.5 288v56.3c0.9-2.2 1.8-4.5 2.8-6.7 17.5-41.4 40.8-79.5 69.6-113.7h-8.4c-35.4 0.1-64 28.7-64 64.1zM160.5 800h8.4c-28.8-34.2-52.1-72.3-69.6-113.7-0.9-2.2-1.9-4.4-2.8-6.7V736c0 35.4 28.6 64 64 64zM177.3 324L512 543.9l182.9-122.3c-15.2-74.2-48.8-141.8-95.8-197.7H258.3c-32.3 28.5-59.7 62.3-81 100.1z" fill="var(--env-secondary, #8C9EFF)"></path>
                        <path d="M703 481.2L512 673.6 171.1 335.4C143.6 388.3 128 448.3 128 512c0 114.6 50.5 217.6 130.3 288h340.8C664.6 722.2 704 621.7 704 512c0-10.3-0.4-20.6-1-30.8z" fill="var(--env-secondary, #8C9EFF)"></path>
                        <path d="M177.3 324c-2.1 3.8-4.2 7.6-6.2 11.4L512 673.6l191-192.4c-1.4-20.3-4.1-40.2-8.1-59.6L512 543.9 177.3 324z" fill="var(--env-accent, #FFFF00)"></path>
                        <path d="M128 512c0-63.7 15.6-123.7 43.1-176.6 2-3.8 4-7.6 6.2-11.4 21.3-37.7 48.8-71.6 81.1-100 62.1-54.8 142-89.8 229.8-95.2a446.34 446.34 0 0 0-104.1-46.2c-15.7 4.7-31.2 10.2-46.4 16.7-53.4 22.6-101.3 54.9-142.4 96-9.2 9.2-18 18.8-26.4 28.7-28.8 34.2-52.1 72.3-69.6 113.7-0.9 2.2-1.9 4.4-2.8 6.7C74.9 397.6 64 454 64 512s10.9 114.4 32.5 167.7c0.9 2.2 1.8 4.5 2.8 6.7 17.5 41.4 40.8 79.5 69.6 113.7 8.3 9.9 17.1 19.5 26.4 28.7 41.1 41.1 89 73.4 142.4 96 15.2 6.4 30.7 12 46.4 16.7 36.9-11 71.9-26.6 104.1-46.2-87.8-5.4-167.7-40.4-229.8-95.2C178.5 729.6 128 626.6 128 512z" fill="var(--env-accent, #FFFF00)"></path>
                    </g>
                </svg>
            </a>
        </div>

        <style>
            @keyframes slideUpWidget {
                from {
                    transform: translateY(30px);
                    opacity: 0;
                }
                to {
                    transform: translateY(0);
                    opacity: 1;
                }
            }

            /* Custom dynamic colors based on format class */
            #last-export-widget.format-excel {
                --env-primary: #10b981;      /* Vibrant Emerald Green */
                --env-secondary: #10b981;    /* Vibrant Emerald Green (unified) */
                --env-accent: #047857;       /* Outlines & Contrast Lines (Dark Forest Green) */
            }
            #last-export-widget.format-excel #widget-svg {
                filter: drop-shadow(0 8px 16px rgba(16, 185, 129, 0.35));
            }
            #last-export-widget.format-excel #last-export-btn:hover #widget-svg {
                filter: drop-shadow(0 12px 24px rgba(16, 185, 129, 0.6));
            }

            #last-export-widget.format-pdf {
                --env-primary: #ef4444;      /* Vibrant Red */
                --env-secondary: #ef4444;    /* Vibrant Red (unified) */
                --env-accent: #991b1b;       /* Outlines & Contrast Lines (Dark Crimson Red) */
            }
            #last-export-widget.format-pdf #widget-svg {
                filter: drop-shadow(0 8px 16px rgba(239, 68, 68, 0.35));
            }
            #last-export-widget.format-pdf #last-export-btn:hover #widget-svg {
                filter: drop-shadow(0 12px 24px rgba(239, 68, 68, 0.6));
            }
        </style>
    </body>
</html>

