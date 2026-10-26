<!DOCTYPE html>
<html lang="en">
    <head>
        @include('layouts.head-page-meta', ['title' => 'Export Data - Sistem Manajemen Aset Desa'])
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
                background: var(--color-surface);
                border: 1px solid var(--color-border);
                border-radius: var(--radius-lg);
                padding: 24px 28px;
                margin-bottom: 24px;
                position: relative;
            }
            .page-header-card h1 {
                color: var(--color-text);
                font-size: 22px;
                font-weight: 700;
                margin: 0 0 6px 0;
            }
            .page-header-card p {
                color: var(--color-text-muted);
                font-size: 13.5px;
                margin: 0;
            }

            /* Container Grid */
            .export-container {
                display: grid !important;
                grid-template-columns: minmax(0, 1fr) 320px !important;
                gap: 20px;
                margin-bottom: 40px;
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
            .section-card{
                background: var(--color-surface);
                border-radius: var(--radius-lg);
                box-shadow: none;
                border: 1px solid var(--color-border);
                overflow: visible;
            }
            .section-header {
                padding: 24px 28px 0;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 16px;
            }
            .section-header-icon {
                width: 36px;
                height: 36px;
                border-radius: var(--radius-sm);
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
                background: var(--color-primary-tint) !important;
            }
            .section-header-icon svg,
            .section-header-icon i { 
                width: 18px; 
                height: 18px; 
                color: var(--color-primary) !important; 
                stroke: var(--color-primary) !important;
            }
            .section-header-icon.blue,
            .section-header-icon.green,
            .section-header-icon.orange,
            .section-header-icon.teal { background: var(--color-primary-tint) !important; }
            .section-header-title h5 {
                font-size: 15px;
                font-weight: 600;
                color: var(--color-text);
                margin: 0 0 2px 0;
            }
            .section-header-title span {
                font-size: 12.5px;
                color: var(--color-text-muted);
            }
            .section-body {
                padding: 20px 28px 28px;
            }
            .export-summary-card .section-header {
                padding: 20px 20px 0;
            }
            .export-summary-card .section-body {
                padding: 16px 20px 24px;
            }
            @media (max-width: 768px) {
                .section-header {
                    padding: 16px 16px 0;
                }
                .section-body {
                    padding: 16px 16px 20px;
                }
            }

            /* Visual Export Type Cards Grid (Modern Enterprise Report Catalog) */
            .export-type-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 12px;
                margin-top: 8px;
                margin-bottom: 4px;
            }
            .export-type-card {
                background: var(--color-surface);
                border: 1.5px solid var(--color-border);
                border-radius: var(--radius-md);
                padding: 18px 14px 16px;
                cursor: pointer;
                transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
                position: relative;
                display: flex;
                flex-direction: column;
                align-items: center;
                text-align: center;
                gap: 8px;
                user-select: none;
            }
            .export-type-card:hover {
                border-color: var(--color-primary);
                background: var(--color-row-hover);
                transform: translateY(-2px);
            }
            .export-type-card.active {
                border-color: var(--color-primary) !important;
                background: var(--color-primary-tint) !important;
                box-shadow: 0 4px 14px rgba(31, 58, 42, 0.1) !important;
            }
            .export-type-card-badge {
                display: none !important;
            }
            .export-type-card-icon {
                width: 40px;
                height: 40px;
                border-radius: var(--radius-sm);
                display: flex;
                align-items: center;
                justify-content: center;
                background: transparent !important;
                color: var(--color-primary) !important;
                transition: all 0.2s;
            }
            .export-type-card-icon i,
            .export-type-card-icon svg {
                width: 20px;
                height: 20px;
                color: var(--color-primary) !important;
                stroke: var(--color-primary) !important;
            }
            .export-type-card.active .export-type-card-icon {
                background: transparent !important;
                color: var(--color-primary) !important;
            }
            .export-type-card.active .export-type-card-icon i,
            .export-type-card.active .export-type-card-icon svg {
                color: var(--color-primary) !important;
                stroke: var(--color-primary) !important;
            }
            .export-type-card-title {
                font-size: 13.5px;
                font-weight: 600;
                color: var(--color-text);
                margin-top: 4px;
                line-height: 1.3;
                text-align: center;
            }
            .export-type-card.active .export-type-card-title {
                color: var(--color-primary);
            }
            .export-type-card-desc {
                font-size: 11.5px;
                color: var(--color-text-muted);
                line-height: 1.4;
                text-align: center;
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
                gap: 12px;
                padding: 12px 14px;
                background: var(--color-surface);
                border: 1.5px solid var(--color-border);
                border-radius: var(--radius-md);
                cursor: pointer;
                transition: all 0.2s;
            }
            .jenis-option label:hover {
                border-color: var(--color-primary);
                background: var(--color-row-hover);
            }
            .jenis-option input:checked + label {
                border-color: var(--color-primary);
                background: var(--color-primary-tint);
            }
            .jenis-option-icon {
                width: 40px;
                height: 40px;
                border-radius: var(--radius-sm);
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
                background: var(--color-primary-tint);
            }
            .jenis-option-icon svg,
            .jenis-option-icon i { 
                width: 20px; 
                height: 20px; 
                color: var(--color-primary) !important; 
                stroke: var(--color-primary) !important;
            }
            .jenis-option-info {
                flex: 1;
            }
            .jenis-option-name { font-size: 13px; font-weight: 600; color: var(--color-text); margin-bottom: 2px; }
            .jenis-option-count { font-size: 11.5px; color: var(--color-text-muted); }

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
                background: var(--color-surface);
                border-radius: var(--radius-sm);
                padding: 0 14px;
                border: 1px solid var(--color-border);
                transition: all 0.2s;
                flex: 1;
                min-width: 200px;
            }
            .search-box:focus-within {
                border-color: var(--color-primary);
                box-shadow: none;
            }
            .search-box input {
                border: none;
                background: transparent;
                padding: 10px 0;
                font-size: 13px;
                width: 100%;
                outline: none;
                color: var(--color-text);
            }
            .search-box button {
                border: none;
                background: transparent;
                padding: 0;
                cursor: pointer;
                color: var(--color-text-muted);
            }
            .search-box button i { width: 16px; height: 16px; }

            .filter-select {
                padding: 8px 12px;
                border: 1px solid var(--color-border);
                background: var(--color-surface);
                border-radius: var(--radius-sm);
                font-size: 13px;
                color: var(--color-text);
                outline: none;
                transition: all 0.2s;
                min-width: 100px;
            }
            .filter-select:focus {
                border-color: var(--color-primary);
                box-shadow: none;
            }

            .btn-action{
                padding: 8px 16px;
                border: 1px solid var(--color-border);
                background: var(--color-surface);
                border-radius: var(--radius-full) !important;
                font-size: 12px;
                font-weight: 600;
                color: var(--color-text);
                cursor: pointer;
                transition: all 0.2s;
            }
            .btn-action:hover {
                background: var(--color-row-hover);
                border-color: var(--color-primary);
            }
            .btn-action:disabled {
                opacity: 0.55;
                cursor: not-allowed;
                background: var(--color-surface);
                border-color: var(--color-border);
            }

            #history-view-all-btn,
            #btn-custom-pejabat {
                background: var(--color-primary) !important;
                color: #eaf1e7 !important;
                border: 1px solid var(--color-primary) !important;
                border-radius: var(--radius-full) !important;
                box-shadow: none !important;
                transition: background 0.2s ease;
                font-weight: 600;
                cursor: pointer;
            }
            #history-view-all-btn:hover,
            #btn-custom-pejabat:hover {
                background: var(--color-primary-hover) !important;
                color: #eaf1e7 !important;
            }

            /* Table (Prodex Style) */
            .data-table {
                width: 100%;
                border-collapse: collapse;
                font-size: 13px;
            }
            .data-table thead th {
                background: var(--color-surface);
                padding: 12px 16px;
                text-align: left;
                font-weight: 700;
                color: var(--color-text-muted);
                border-bottom: 1px solid var(--color-border);
                text-transform: uppercase;
                letter-spacing: 0.05em;
                font-size: 11px;
            }
            .data-table thead th:first-child {
                width: 50px;
                text-align: center;
            }
            .data-table tbody tr {
                transition: all 0.15s ease;
                border-bottom: 1px solid var(--color-border);
            }
            .data-table tbody tr:hover {
                background: var(--color-row-hover);
            }
            .data-table tbody td {
                padding: 12px 16px;
                vertical-align: middle;
                color: var(--color-text);
            }
            .data-table tbody td:first-child {
                text-align: center;
            }
            .data-table input[type="checkbox"] {
                width: 16px;
                height: 16px;
                accent-color: var(--color-primary);
                cursor: pointer;
            }

            /* Badge Styles */
            .jenis-badge {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 4px 10px;
                border-radius: var(--radius-sm);
                font-size: 11px;
                font-weight: 600;
            }
            .jenis-badge.kendaraan { background: var(--color-success-soft); color: var(--color-success); }
            .jenis-badge.peralatan { background: var(--color-warning-soft); color: var(--color-warning); }
            .jenis-badge.bangunan { background: var(--color-info-soft); color: var(--color-info); }
            .jenis-badge.tanah { background: var(--color-primary-tint); color: var(--color-primary); }
            .jenis-badge.infrastruktur { background: var(--color-primary-tint); color: var(--color-primary); }

            .ttd-indicator {
                display: inline-flex;
                align-items: center;
                gap: 4px;
                padding: 4px 10px;
                border-radius: var(--radius-sm);
                font-size: 11px;
                font-weight: 600;
            }
            .ttd-indicator.default { background: var(--color-primary-tint); color: var(--color-primary); }
            .ttd-indicator.custom { background: var(--color-warning-soft); color: var(--color-warning); }

            .jenis-flow-helper {
                display: flex;
                width: 100%;
                box-sizing: border-box;
                align-items: center;
                gap: 8px;
                margin-bottom: 16px;
                padding: 8px 14px;
                border-radius: var(--radius-sm);
                background: var(--color-primary-tint);
                border: 1px solid var(--color-border);
                color: var(--color-text);
                font-size: 12px;
                font-weight: 500;
                line-height: 1.4;
            }
            .jenis-flow-helper i,
            .jenis-flow-helper svg {
                width: 14px;
                height: 14px;
                min-width: 14px;
                min-height: 14px;
                flex-shrink: 0;
                color: var(--color-primary);
                stroke: var(--color-primary);
                display: block;
                margin: 0;
            }
            .jenis-flow-helper span {
                font-size: 12px;
                font-weight: 500;
                color: var(--color-text);
                line-height: 1.4;
                flex: 1;
            }

            /* TTD Setup Section */
            .ttd-setup {
                display: flex;
                flex-direction: column;
                gap: 20px;
            }

            .ttd-subsection {
                padding: 0;
                background: transparent;
                border: none;
                border-radius: 0;
            }
            .ttd-subsection-title {
                font-size: 13px;
                font-weight: 600;
                color: var(--color-text);
                margin-bottom: 10px;
                display: flex;
                align-items: center;
                gap: 8px;
            }
            .ttd-subsection-title i { width: 16px; height: 16px; }

            .filter-tahun-wrap {
                display: flex;
                gap: 12px;
                align-items: center;
                flex-wrap: wrap;
            }
            .filter-tahun-wrap input.year-picker-export {
                flex: 1 1 160px;
                min-width: 0;
                width: 100%;
                padding: 10px 14px;
                border: 1px solid var(--color-border);
                border-radius: var(--radius-sm);
                font-size: 13px;
                outline: none;
                cursor: pointer;
                background: var(--color-surface);
                color: var(--color-text);
                box-sizing: border-box;
                transition: border-color 0.2s ease;
            }
            .filter-tahun-wrap input.year-picker-export:focus {
                border-color: var(--color-primary);
            }
            .filter-tahun-wrap .data-info-badge {
                background: var(--color-primary-tint);
                color: var(--color-primary);
                padding: 9px 14px;
                border-radius: var(--radius-sm);
                font-size: 12.5px;
                font-weight: 600;
                border: 1px solid var(--color-border);
                white-space: nowrap;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }
            @media (max-width: 640px) {
                .filter-tahun-wrap {
                    flex-direction: column;
                    align-items: stretch;
                    gap: 10px;
                }
                .filter-tahun-wrap input.year-picker-export {
                    width: 100%;
                    flex: 1 1 100%;
                }
                .filter-tahun-wrap .data-info-badge {
                    width: 100%;
                    text-align: center;
                    justify-content: center;
                    box-sizing: border-box;
                    white-space: normal;
                }
            }
            .doc-flow-helper {
                display: flex;
                width: 100%;
                box-sizing: border-box;
                align-items: center;
                gap: 8px;
                margin-bottom: 14px;
                padding: 8px 14px;
                border-radius: var(--radius-sm);
                background: var(--color-primary-tint);
                border: 1px solid var(--color-border);
                color: var(--color-text);
                font-size: 12px;
                font-weight: 500;
                line-height: 1.4;
            }
            .doc-flow-helper i,
            .doc-flow-helper svg {
                width: 14px;
                height: 14px;
                min-width: 14px;
                min-height: 14px;
                flex-shrink: 0;
                color: var(--color-primary);
                stroke: var(--color-primary);
                display: block;
                margin: 0;
            }
            .doc-flow-helper span {
                font-size: 12px;
                font-weight: 500;
                color: var(--color-text);
                line-height: 1.4;
                flex: 1;
            }

            /* Pejabat Display */
            .pejabat-display {
                background: var(--color-surface);
                border: 1px solid var(--color-border);
                border-radius: var(--radius-md);
                padding: 16px 18px;
                min-height: 105px;
                box-sizing: border-box;
                display: flex;
                flex-direction: column;
            }
            .pejabat-display-label {
                font-size: 12px;
                font-weight: 600;
                color: var(--color-text-muted);
                text-transform: none;
                margin-bottom: 10px;
                display: block;
            }
            .pejabat-items {
                display: flex;
                flex-direction: column;
                gap: 10px;
                flex: 1;
                justify-content: center;
            }
            .pejabat-card {
                background: var(--color-bg);
                border: 1px solid var(--color-border);
                border-radius: var(--radius-sm);
                padding: 12px 14px;
                display: flex;
                align-items: center;
                gap: 12px;
            }
            .pejabat-order {
                width: 30px;
                height: 30px;
                border-radius: var(--radius-sm);
                background: var(--color-primary);
                color: var(--color-text-on-primary);
                font-size: 13px;
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
                font-size: 13.5px;
                font-weight: 600;
                color: var(--color-text);
            }
            .pejabat-jabatan {
                font-size: 12px;
                color: var(--color-text-muted);
            }
            .pejabat-empty {
                font-size: 12.5px;
                color: var(--color-text-muted);
                text-align: center;
                padding: 16px 12px;
                font-style: italic;
            }

            /* Date Input */
            .date-input-group {
                display: flex;
                flex-direction: column;
                gap: 6px;
            }
            .date-input-label {
                font-size: 12px;
                font-weight: 600;
                color: var(--color-text-muted);
                text-transform: none;
            }
            .date-input-field {
                padding: 10px 14px;
                border: 1px solid var(--color-border);
                border-radius: var(--radius-sm);
                background: var(--color-surface);
                color: var(--color-text);
                font-size: 13px;
                outline: none;
                transition: border-color 0.2s;
                text-align: left;
                cursor: text;
            }
            #doc-tanggal { cursor: pointer; }
            .date-input-field:focus {
                border-color: var(--color-primary);
                box-shadow: none;
            }

            .date-preview-box {
                background: var(--color-primary-tint);
                border: 1px solid var(--color-border);
                border-radius: var(--radius-sm);
                padding: 10px 14px;
                margin-top: 10px;
            }
            .preview-doc-box {
                background: var(--color-primary-tint);
                border: 1px solid var(--color-border);
                border-radius: var(--radius-sm);
                padding: 10px 14px;
                margin-top: 18px;
            }
            .date-preview-label,
            .preview-doc-box-title {
                font-size: 11px;
                font-weight: 600;
                color: var(--color-primary);
                margin-bottom: 4px;
                display: block;
            }
            .date-preview-text,
            .preview-doc-box-content {
                font-size: 13px;
                font-weight: 600;
                color: var(--color-text);
                line-height: 1.5;
            }
            .preview-doc-box-content div {
                margin-bottom: 2px;
            }
            .preview-doc-box-content div:last-child {
                margin-bottom: 0;
            }
            .preview-doc-box-content strong {
                color: var(--color-text);
                font-weight: 600;
            }
            .preview-doc-box-content span {
                color: var(--color-text);
                font-weight: 600;
            }

            /* Info Box (for alerts/helper text) */
            .info-box {
                background: var(--color-primary-tint);
                border: 1px solid var(--color-border);
                border-radius: var(--radius-sm);
                padding: 8px 14px;
                display: flex;
                width: 100%;
                box-sizing: border-box;
                align-items: center;
                gap: 8px;
                margin-top: 16px;
            }
            .info-box i,
            .info-box svg {
                color: var(--color-primary);
                stroke: var(--color-primary);
                flex-shrink: 0;
                width: 14px;
                height: 14px;
                min-width: 14px;
                min-height: 14px;
                display: block;
                margin: 0;
            }
            .info-box-text,
            .info-box span {
                font-size: 12px;
                color: var(--color-text);
                line-height: 1.4;
                font-weight: 500;
                flex: 1;
            }

            /* Flatpickr clean compact look */
            .flatpickr-calendar {
                border: 1px solid var(--color-border);
                border-radius: var(--radius-md);
                background: var(--color-surface);
                box-shadow: var(--shadow-float);
                font-family: var(--font-main);
            }
            .flatpickr-calendar .flatpickr-months {
                padding: 8px 10px 0;
                background: var(--color-surface);
            }
            .flatpickr-calendar .flatpickr-current-month {
                font-size: 14px;
                font-weight: 600;
                color: var(--color-text);
            }
            .flatpickr-calendar .flatpickr-current-month .cur-month,
            .flatpickr-calendar .flatpickr-current-month input.cur-year {
                font-weight: 600;
                color: var(--color-text);
                user-select: none;
                -webkit-user-select: none;
                -moz-user-select: none;
            }
            .flatpickr-calendar .flatpickr-current-month .cur-month::selection {
                background: transparent;
            }
            .flatpickr-calendar .flatpickr-weekdays {
                margin-top: 4px;
                background: var(--color-surface);
            }
            .flatpickr-calendar span.flatpickr-weekday {
                color: var(--color-text-muted);
                font-size: 12px;
                font-weight: 600;
            }
            .flatpickr-calendar .flatpickr-day {
                border-radius: var(--radius-sm);
                color: var(--color-text);
                font-size: 13px;
            }
            .flatpickr-calendar .flatpickr-day.selected,
            .flatpickr-calendar .flatpickr-day.startRange,
            .flatpickr-calendar .flatpickr-day.endRange {
                background: var(--color-primary);
                border-color: var(--color-primary);
                color: var(--color-text-on-primary);
            }
            .flatpickr-calendar .flatpickr-day:hover {
                background: var(--color-row-hover);
                border-color: var(--color-border);
            }
            .flatpickr-calendar .flatpickr-prev-month,
            .flatpickr-calendar .flatpickr-next-month {
                color: var(--color-text-muted);
                fill: var(--color-text-muted);
            }
            .flatpickr-calendar .flatpickr-prev-month:hover,
            .flatpickr-calendar .flatpickr-next-month:hover {
                color: var(--color-text);
                fill: var(--color-text);
            }

            /* ===== Date Clean Picker Enhancement ===== */
            .flatpickr-calendar.date-clean-picker .flatpickr-months {
                display: flex !important; align-items: center !important; padding: 8px 4px 0 !important;
            }
            .flatpickr-calendar.date-clean-picker .flatpickr-prev-month,
            .flatpickr-calendar.date-clean-picker .flatpickr-next-month {
                position: static !important; flex-shrink: 0;
                display: flex !important; align-items: center !important; justify-content: center !important;
                top: unset !important; padding: 0 !important; width: 28px !important; height: 28px !important;
                border: none !important; border-radius: var(--radius-sm) !important;
                background: transparent !important; color: var(--color-text) !important; transition: background 0.15s !important;
                fill: var(--color-text) !important;
                box-shadow: none !important;
            }
            .flatpickr-calendar.date-clean-picker .flatpickr-prev-month:hover,
            .flatpickr-calendar.date-clean-picker .flatpickr-next-month:hover { background: var(--color-row-hover) !important; }
            .flatpickr-calendar.date-clean-picker .flatpickr-prev-month svg,
            .flatpickr-calendar.date-clean-picker .flatpickr-next-month svg { fill: var(--color-text) !important; width: 12px !important; height: 12px !important; }
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
                cursor: pointer; padding: 2px 8px; border-radius: var(--radius-sm); font-weight: 700;
                font-size: 14px; color: var(--color-primary); user-select: none; transition: background 0.15s;
            }
            .flatpickr-calendar.date-clean-picker .fp-cur-year:hover { background: var(--color-row-hover); }
            .flatpickr-calendar.date-clean-picker .cur-month {
                cursor: pointer; border-radius: var(--radius-sm); padding: 2px 6px; font-weight: 700; color: var(--color-primary);
            }
            .flatpickr-calendar.date-clean-picker .cur-month:hover { background: var(--color-row-hover); }
            /* Hari nama warna sistem */
            .flatpickr-calendar.date-clean-picker .flatpickr-weekday { color: var(--color-primary) !important; font-weight: 600 !important; }
            /* Sembunyikan tanggal bulan lain */
            .flatpickr-calendar.date-clean-picker .flatpickr-day.prevMonthDay,
            .flatpickr-calendar.date-clean-picker .flatpickr-day.nextMonthDay { visibility: hidden !important; pointer-events: none !important; }
            /* Panel bulan & tahun */
            .fp-exp-month-panel, .fp-exp-year-panel {
                display: none; position: absolute; top: 0; left: 0; right: 0; bottom: 0;
                background: var(--color-surface); border-radius: inherit; padding: 10px; z-index: 10;
                flex-direction: column; gap: 8px; box-sizing: border-box;
            }
            .fp-exp-month-panel.show, .fp-exp-year-panel.show { display: flex; }
            .fp-exp-panel-header { display: flex; align-items: center; justify-content: space-between; flex-shrink: 0; }
            .fp-exp-panel-title { font-size: 12px; font-weight: 700; color: var(--color-primary); }
            .fp-exp-panel-close, .fp-exp-panel-nav {
                width: 26px; height: 26px; border: none; background: transparent; font-size: 13px; cursor: pointer;
                display: flex; align-items: center; justify-content: center; color: var(--color-text); border-radius: var(--radius-sm); transition: background 0.15s;
            }
            .fp-exp-panel-close:hover, .fp-exp-panel-nav:hover:not(:disabled) { background: var(--color-row-hover); }
            .fp-exp-panel-nav:disabled { opacity: 0.45; cursor: not-allowed; }
            .fp-exp-month-grid, .fp-exp-year-grid { display: grid; grid-template-columns: repeat(3, 1fr); grid-template-rows: repeat(4, 1fr); gap: 8px; flex: 1; }
            .fp-exp-month-btn, .fp-exp-year-btn {
                border-radius: var(--radius-sm); border: 1px solid var(--color-border); background: var(--color-surface);
                font-size: 12px; font-weight: 600; color: var(--color-text); cursor: pointer; text-align: center; transition: all 0.15s;
                display: flex; align-items: center; justify-content: center; padding: 0;
            }
            .fp-exp-month-btn:hover, .fp-exp-year-btn:hover { background: var(--color-row-hover); }
            .fp-exp-month-btn.active, .fp-exp-year-btn.active { background: var(--color-primary); border-color: var(--color-primary); color: var(--color-text-on-primary); }

            /* Sidebar */
            .export-sidebar {
                display: flex;
                flex-direction: column;
                gap: 20px;
            }

            /* Export Options Card */
            .export-options-card{
                background: var(--color-surface);
                border-radius: var(--radius-lg);
                box-shadow: none;
                border: 1px solid var(--color-border);
                overflow: hidden;
            }
            /* Summary Card Smooth Transitions */
            .export-summary-card {
                background: var(--color-surface);
                border-radius: var(--radius-lg);
                box-shadow: none;
                border: 1px solid var(--color-border);
                overflow: hidden;
                transition: opacity 0.35s cubic-bezier(0.16, 1, 0.3, 1), transform 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            }
            .export-summary-card.fade-in {
                animation: summarySlideDown 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            }
            .export-summary-card.fade-out {
                animation: summarySlideUp 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            }
            @keyframes summarySlideDown {
                from {
                    opacity: 0;
                    transform: translateY(-8px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            @keyframes summarySlideUp {
                from {
                    opacity: 1;
                    transform: translateY(0);
                }
                to {
                    opacity: 0;
                    transform: translateY(-8px);
                }
            }
            .export-options-header {
                padding: 16px 20px;
                background: var(--color-surface);
                border-bottom: 1px solid var(--color-border);
                color: var(--color-text);
            }
            .export-options-header h5 {
                font-size: 14px;
                font-weight: 600;
                color: var(--color-text);
                margin: 0;
            }
            .export-options-body {
                padding: 20px 20px 24px 20px;
                display: flex;
                flex-direction: column;
                gap: 16px;
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
                display: none !important;
                position: absolute !important;
                opacity: 0 !important;
                width: 0 !important;
                height: 0 !important;
                pointer-events: none !important;
                visibility: hidden !important;
            }
            .format-option label {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 12px;
                background: var(--color-surface);
                border: 1.5px solid var(--color-border);
                border-radius: var(--radius-md);
                cursor: pointer;
                transition: all 0.2s;
            }
            .format-option label:hover {
                border-color: var(--color-primary);
                background: var(--color-row-hover);
            }
            .format-option input:checked + label {
                border-color: var(--color-primary);
                background: var(--color-primary-tint);
            }
            .format-icon {
                display: none !important;
            }
            .format-info {
                flex: 1;
                display: flex;
                flex-direction: column;
                gap: 2px;
            }
            .format-label { font-size: 12.5px; font-weight: 600; color: var(--color-text); }
            .format-desc { font-size: 11px; color: var(--color-text-muted); }

            /* Signature Options */
            .signature-options {
                padding: 0;
                background: transparent;
                border: none;
            }
            .signature-label {
                font-size: 13.5px;
                font-weight: 600;
                color: var(--color-text);
                margin-top: 10px;
                margin-bottom: 8px;
                display: flex;
                align-items: center;
                gap: 8px;
            }
            .signature-label.no-margin-top {
                margin-top: 0;
            }
            .signature-label i { width: 12px; height: 12px; }
            .signature-options-group {
                display: flex;
                flex-direction: column;
                gap: 8px;
            }
            #paper-size-section {
                display: flex;
                flex-direction: column;
                gap: 12px;
            }
            .signature-option {
                display: flex;
                align-items: center;
                gap: 8px;
            }
            .signature-option input[type="radio"] {
                width: 16px;
                height: 16px;
                accent-color: var(--color-primary);
                cursor: pointer;
            }
            .signature-option label {
                font-size: 12.5px;
                color: var(--color-text);
                cursor: pointer;
                flex: 1;
            }
            .signature-option span {
                font-size: 10.5px;
                color: var(--color-text-muted);
            }

            /* Locked state sebelum jenis inventaris dipilih */
            .export-options-card.is-locked .format-option label,
            .export-options-card.is-locked .signature-option label,
            .export-options-card.is-locked .signature-option input[type="radio"] {
                cursor: not-allowed;
                opacity: 0.65;
            }
            .export-options-card.is-locked .format-option label:hover {
                border-color: var(--color-border);
                background: var(--color-surface);
            }
            #section-ttd-setup.is-locked .ttd-subsection {
                opacity: 0.72;
            }
            .ttd-subsection.is-doc-disabled {
                opacity: 0.72;
            }

            /* Export Button */
            .export-action-btn{
                width: 100%;
                margin-top: 10px;
                padding: 12px 18px;
                background: var(--color-primary);
                color: var(--color-text-on-primary);
                border: 1px solid var(--color-primary);
                border-radius: var(--radius-full);
                font-weight: 600;
                font-size: 13.5px;
                cursor: pointer;
                transition: all 0.2s ease;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                box-shadow: none;
            }
            .export-action-btn:hover:not(:disabled) {
                background: var(--color-primary-hover);
                color: var(--color-text-on-primary);
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
                border: 1px solid var(--color-border);
                border-radius: var(--radius-md);
                background: var(--color-surface);
                transition: all 0.2s ease;
            }

            .simple-item:hover {
                background: var(--color-row-hover);
            }

            /* Icon box */
            .simple-icon {
                width: 32px;
                height: 32px;
                border-radius: var(--radius-sm);
                background: var(--color-primary-tint);
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .simple-icon svg {
                width: 18px;
                height: 18px;
                color: var(--color-primary); 
                stroke: var(--color-primary);
            }

            /* Text */
            .simple-text {
                display: flex;
                flex-direction: column;
                gap: 2px;
            }

            .simple-title {
                font-size: 13px;
                font-weight: 600;
                color: var(--color-text);
            }

            .simple-sub {
                font-size: 12px;
                color: var(--color-text-muted);
            }

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
                background: var(--color-surface);
                border: 1.5px solid var(--color-border);
                border-radius: var(--radius-md);
                cursor: pointer;
                transition: all 0.2s;
            }
            .ruangan-option label:hover { border-color: var(--color-primary); background: var(--color-row-hover); }
            .ruangan-option input:checked + label {
                border-color: var(--color-primary);
                background: var(--color-primary-tint);
            }
            .ruangan-option-icon {
                width: 40px; height: 40px; border-radius: var(--radius-sm);
                display: flex; align-items: center; justify-content: center; flex-shrink: 0;
            }
            .ruangan-option-icon svg, .ruangan-option-icon i {
                width: 20px; height: 20px; color: var(--color-primary) !important; stroke: var(--color-primary) !important;
            }
            .ruangan-option-info { flex: 1; }
            .ruangan-option-name { font-size: 13px; font-weight: 600; color: var(--color-text); margin-bottom: 2px; }
            .ruangan-option-count { font-size: 11px; color: var(--color-text-muted); }

            .history-table {
                width: 100%;
                border-collapse: collapse;
                font-size: 12.5px;
            }
            .history-table thead th {
                background: var(--color-surface);
                padding: 12px 16px;
                text-align: left;
                font-weight: 600;
                color: var(--color-text-muted);
                border-bottom: 1px solid var(--color-border);
                text-transform: uppercase;
                letter-spacing: 0.5px;
                font-size: 11px;
            }
            .history-table tbody tr {
                border-bottom: 1px solid var(--color-border);
            }
            .history-table tbody tr:hover {
                background: var(--color-row-hover);
            }
            .history-table tbody td {
                padding: 12px 16px;
                color: var(--color-text);
            }

            .user-info {
                display: flex;
                align-items: flex-start;
                gap: 8px;
                text-align: left;
            }

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
                background: var(--color-primary-tint);
                border: 1px solid var(--color-border);
                border-radius: var(--radius-sm);
                font-size: 11px;
                color: var(--color-primary);
                font-weight: 600;
            }
            .meta-badge i { width: 12px; height: 12px; }

            /* Modal */
            .modal-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(31, 58, 42, 0.4);
                align-items: center;
                justify-content: center;
                z-index: 2000;
                backdrop-filter: blur(4px);
            }
            .modal-overlay.show { display: flex; }
            .modal-box {
                background: var(--color-surface);
                border-radius: var(--radius-xl);
                border: 1px solid var(--color-border);
                width: 90%;
                max-width: 900px;
                max-height: 90vh;
                overflow: auto;
                box-shadow: var(--shadow-float);
                animation: modalSlideIn 0.3s ease;
            }
            @keyframes modalSlideIn {
                from { opacity: 0; transform: translateY(-20px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .modal-header {
                padding: 20px 24px;
                background: var(--color-surface);
                color: var(--color-text);
                border-bottom: none !important;
            }
            .modal-header h5 {
                color: var(--color-text);
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
                background: var(--color-surface);
                border-top: none !important;
                display: flex;
                justify-content: flex-end;
                gap: 12px;
            }
            .modal-btn{
                padding: 10px 20px;
                border-radius: var(--radius-full);
                font-size: 13px;
                font-weight: 600;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 6px;
                cursor: pointer;
                transition: all 0.2s;
                border: none;
            }
            .modal-btn-cancel{
                background: var(--color-surface);
                color: var(--color-text);
                border: 1px solid var(--color-border);
            }
            .modal-btn-cancel:hover {
                background: var(--color-row-hover);
            }
            .modal-btn-confirm{
                background: var(--color-primary);
                color: var(--color-text-on-primary);
                border: 1px solid var(--color-primary);
                border-radius: var(--radius-full);
                box-shadow: none;
                transition: background 0.2s ease;
            }
            .modal-btn-confirm:hover {
                background: var(--color-primary-hover);
                color: var(--color-text-on-primary);
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
                border-radius: 8px;
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
                border-radius: 8px;
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

            /* PDF Preview Styles - strict overrides to match actual PDF export files and eliminate system green & black background */
            #pdf-preview-content {
                background: #ffffff !important;
                padding: 16px 20px !important;
                font-family: Arial, sans-serif !important;
                color: #000000 !important;
            }
            #pdf-preview-content * {
                box-sizing: border-box;
            }
            #pdf-preview-content table {
                border-collapse: collapse !important;
                background: #ffffff !important;
                font-family: Arial, sans-serif !important;
            }
            #pdf-preview-content table thead th,
            #pdf-preview-content table th {
                background: #ffffff !important;
                color: #000000 !important;
                border: 1px solid #000000 !important;
                font-weight: bold !important;
            }
            #pdf-preview-content table.pdf-blue-table thead th,
            #pdf-preview-content table.pdf-blue-table th,
            #pdf-preview-content table.pdf-blue-header thead th,
            #pdf-preview-content table.pdf-blue-header th {
                background: #9bc2e6 !important;
                color: #000000 !important;
                border: 1px solid #000000 !important;
                font-weight: bold !important;
            }
            #pdf-preview-content table tbody td,
            #pdf-preview-content table tbody tr td,
            #pdf-preview-content table tbody tr:hover td {
                background: #ffffff !important;
                color: #000000 !important;
                border: 1px solid #000000 !important;
            }
            #pdf-preview-content table tr.pdf-blue-total td,
            #pdf-preview-content table tr.pdf-blue-total:hover td,
            #pdf-preview-content table td.pdf-blue-bg {
                background: #9bc2e6 !important;
                color: #000000 !important;
                font-weight: bold !important;
            }
            #pdf-preview-content table tr.pdf-gray-subheader td,
            #pdf-preview-content table tr.pdf-gray-subheader:hover td,
            #pdf-preview-content table td.pdf-gray-bg {
                background: #d9d9d9 !important;
                color: #000000 !important;
                font-style: italic !important;
                font-weight: bold !important;
            }

            /* History Card */
            .history-card{
                background: #fff;
                border-radius: 8px;
                box-shadow: none;
                border: 1px solid #e5e7eb;
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
                border-radius: 8px;
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
                .export-sidebar .export-options-card{
                    order: 2;
                }
                .export-sidebar .section-card{
                    order: 1;
                }
            }

            @media (max-width: 768px) {
                .section-header { flex-wrap: wrap; }
                .data-controls { flex-direction: column; align-items: stretch; }
                .search-box { flex: unset; }
                .filter-select { width: 100%; }
                .btn-action{ width: 100%; }
                .preview-grid { grid-template-columns: 1fr; }
                .page-header-card{
                    padding: 24px 20px;
                }
                .page-header-card h1 {
                    font-size: 22px;
                }
                .page-header-card p {
                    font-size: 13px;
                }
                .jenis-flow-helper,
                .doc-flow-helper {
                    flex-direction: column !important;
                    align-items: center !important;
                    justify-content: center !important;
                    text-align: center !important;
                    gap: 6px !important;
                    padding: 10px 14px !important;
                }
                .jenis-flow-helper i,
                .jenis-flow-helper svg,
                .doc-flow-helper i,
                .doc-flow-helper svg {
                    margin: 0 auto !important;
                    align-self: center !important;
                }
                .jenis-flow-helper span,
                .jenis-flow-helper div,
                .doc-flow-helper span,
                .doc-flow-helper div {
                    text-align: center !important;
                    width: 100% !important;
                    flex: none !important;
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
                    padding: 16px 16px 0;
                }
                .history-card .section-header {
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

            /* Buku Inventaris Section */
            .buku-inventaris-section {
                background: transparent;
                border: none;
                border-radius: 0;
                padding: 0;
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
                border-radius: var(--radius-sm);
                background: var(--color-primary);
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
                stroke: var(--color-text-on-primary);
            }
            .buku-inventaris-header-content {
                display: flex;
                flex-direction: column;
                justify-content: center;
            }
            .buku-inventaris-title {
                font-size: 14px;
                font-weight: 600;
                color: var(--color-text);
                line-height: 1.4;
            }
            .buku-inventaris-desc {
                font-size: 12px;
                color: var(--color-text-muted);
                margin: 3px 0 0 0;
                line-height: 1.4;
            }

            /* Tombol Trigger Select */
            .dropdown-trigger {
                width: 100% !important;
                text-align: left !important;
                background-color: var(--color-surface) !important;
                cursor: pointer !important;
                display: flex !important;
                align-items: center !important;
                justify-content: space-between !important;
                padding: 12px 18px !important;
                border: 1px solid var(--color-border) !important;
                border-radius: var(--radius-sm) !important;
                color: var(--color-text) !important;
                transition: all 0.2s !important;
                min-height: 48px !important;
            }
            .dropdown-trigger:hover {
                border-color: var(--color-primary) !important;
                background-color: var(--color-row-hover) !important;
            }
            .dropdown-trigger:focus,
            .dropdown-trigger.open,
            .searchable-dropdown.open .dropdown-trigger {
                border-color: var(--color-primary) !important;
                background-color: var(--color-surface) !important;
                box-shadow: none !important;
                outline: none !important;
            }

            /* Panel Menu Dropdown - push down animation */
            .dropdown-menu-panel {
                position: relative !important;
                z-index: 10 !important;
                background: var(--color-surface) !important;
                border: 1px solid var(--color-border) !important;
                border-radius: var(--radius-md) !important;
                box-shadow: var(--shadow-float) !important;
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
                color: var(--color-text-muted);
                width: 14px;
                height: 14px;
                pointer-events: none;
            }
            .dropdown-search-input {
                width: 100% !important;
                padding: 8px 12px 8px 34px !important;
                font-size: 13px !important;
                border: 1px solid var(--color-border) !important;
                border-radius: var(--radius-sm) !important;
                background: var(--color-surface) !important;
                color: var(--color-text) !important;
                outline: none !important;
                box-sizing: border-box !important;
                box-shadow: none !important;
                transition: border-color 0.2s ease;
            }
            .dropdown-search-input:hover,
            .dropdown-search-input:focus {
                border-color: var(--color-primary) !important;
                box-shadow: none !important;
                outline: none !important;
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
                border-radius: var(--radius-sm);
                cursor: pointer;
                transition: background 0.15s;
                user-select: none;
                border-bottom: 1px solid var(--color-border);
            }
            .dropdown-option-item:last-child {
                border-bottom: none;
            }
            .dropdown-option-item:hover {
                background: var(--color-row-hover);
            }
            .dropdown-option-item.selected {
                background: var(--color-primary-tint) !important;
                color: var(--color-primary) !important;
            }
            .dropdown-option-icon {
                display: none !important;
            }
            .dropdown-option-text {
                flex: 1;
            }
            .dropdown-option-name {
                font-size: 13px;
                font-weight: 600;
                color: var(--color-text);
                line-height: 1.4;
            }
            .dropdown-option-desc {
                font-size: 11px;
                color: var(--color-text-muted);
                margin-top: 2px;
                line-height: 1.3;
            }

            /* Animasi Chevron */
            .dropdown-trigger-caret {
                transition: transform 0.2s;
                color: var(--color-text-muted);
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
                background: var(--color-surface);
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
                color: var(--color-primary);
            }
            .flatpickr-calendar.year-only-picker .year-picker-nav {
                width: 26px;
                height: 26px;
                border: none;
                border-radius: var(--radius-sm, 6px);
                background: transparent;
                font-size: 14px;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                color: var(--color-text);
            }
            .flatpickr-calendar.year-only-picker .year-picker-nav:hover:not(:disabled) {
                background: var(--color-row-hover);
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
                border: 1px solid var(--color-border);
                border-radius: var(--radius-sm);
                padding: 7px 4px;
                font-size: 12px;
                font-weight: 600;
                text-align: center;
                background: var(--color-surface);
                color: var(--color-text);
                cursor: pointer;
                transition: all 0.15s;
            }
            .flatpickr-calendar.year-only-picker .year-picker-option:hover {
                background: var(--color-row-hover);
            }
            .flatpickr-calendar.year-only-picker .year-picker-option.active {
                background: var(--color-primary);
                border-color: var(--color-primary);
                color: var(--color-text-on-primary);
            }
        </style>
    </head>

    <body>
        @include('layouts.notification')
        @include('layouts.layout-vertical')

        <div class="pc-container">
            <div class="pc-content" style="max-width: 1560px; margin: 0 auto;">

                <!-- PAGE HEADER & PRIMARY ACTIONS -->
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
                    <div>
                        <h1 style="font-size: 22px; font-weight: 700; margin: 0; color: var(--color-text); line-height: 1.2; letter-spacing: -0.02em;">
                            Pusat Ekspor
                        </h1>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="export-container">
                    <!-- Left Column: Jenis Selection & TTD Setup -->
                    <div class="export-main">
                        <!-- Section 0: Pilih Tipe Export - selalu tampil -->
                        <div class="section-card" id="section-pilih-tipe">
                            <div class="section-header">
                                <div class="section-header-title" style="padding-top: 16px;">
                                    <h5>Katalog Dokumen & Laporan Resmi</h5>
                                    <span>Pilih format buku inventaris atau dokumen laporan yang akan diekspor</span>
                                </div>
                            </div>
                            <div class="section-body" style="padding-top: 14px;">
                                <input type="hidden" name="export_type" id="export_type_hidden" value="buku_inventaris_aset">
                                
                                <!-- Visual Report Cards Grid -->
                                <div class="export-type-grid" id="export-type-cards-container">
                                    <div class="export-type-card active" data-id="buku_inventaris_aset" onclick="selectExportTypeCard('buku_inventaris_aset')">
                                        <div class="export-type-card-icon" style="background-color: transparent !important;">
                                            <i data-feather="archive"></i>
                                        </div>
                                        <div class="export-type-card-title">Buku Inventaris Desa</div>
                                        <div class="export-type-card-desc">Rekapitulasi seluruh aset desa lengkap (KIB A s/d F)</div>
                                    </div>

                                    <div class="export-type-card" data-id="kib" onclick="selectExportTypeCard('kib')">
                                        <div class="export-type-card-icon" style="background-color: transparent !important;">
                                            <i data-feather="book-open"></i>
                                        </div>
                                        <div class="export-type-card-title">Buku Inventaris (KIB)</div>
                                        <div class="export-type-card-desc">Format standar KIB A (Tanah) s/d KIB F (KDP)</div>
                                    </div>

                                    <div class="export-type-card" data-id="ruangan" onclick="selectExportTypeCard('ruangan')">
                                        <div class="export-type-card-icon" style="background-color: transparent !important;">
                                            <i data-feather="grid"></i>
                                        </div>
                                        <div class="export-type-card-title">Kartu Inventaris Ruangan (KIR)</div>
                                        <div class="export-type-card-desc">Cetak KIR per ruangan kantor desa & unit kerja</div>
                                    </div>

                                    <div class="export-type-card" data-id="jenis" onclick="selectExportTypeCard('jenis')">
                                        <div class="export-type-card-icon" style="background-color: transparent !important;">
                                            <i data-feather="file-text"></i>
                                        </div>
                                        <div class="export-type-card-title">Format Jenis Barang</div>
                                        <div class="export-type-card-desc">Cetak format tabel khusus per kategori inventaris</div>
                                    </div>

                                    <div class="export-type-card" data-id="laporan" onclick="selectExportTypeCard('laporan')">
                                        <div class="export-type-card-icon" style="background-color: transparent !important;">
                                            <i data-feather="bar-chart-2"></i>
                                        </div>
                                        <div class="export-type-card-title">Laporan Periodik / Mutasi</div>
                                        <div class="export-type-card-desc">Laporan saldo awal, mutasi penambahan, &amp; saldo akhir</div>
                                    </div>
                                </div>

                                <div class="searchable-dropdown" id="export-type-dropdown-container" style="display:none;">
                                    <button type="button" class="dropdown-trigger" id="export-type-dropdown-trigger">
                                        <span id="export-type-selected-label" style="color:#9ca3af;font-size:13px;font-weight:500;">Buku Inventaris Aset Desa</span>
                                        <i data-feather="chevron-down" class="dropdown-trigger-caret" style="width:16px;height:16px;"></i>
                                    </button>
                                    
                                    <div class="dropdown-menu-panel" id="export-type-dropdown-menu">
                                        <ul class="dropdown-options-list" id="export-type-dropdown-options-list"></ul>
                                    </div>
                                </div>

                                <!-- Container Form Dinamis -->
                                <div id="section-pilih-jenis" style="display:none; margin-top:24px;">
                                    {{-- Divider antara Katalog Dokumen & Laporan Resmi dan Section Format Terpilih --}}
                                    <hr class="sima-divider" style="margin: 0 0 24px 0 !important;" />
                                    <!-- Konten Jenis Inventaris - hidden sampai dipilih -->
                                <div id="content-jenis" style="display:none;">
                                @if($jenisInventaris->isEmpty())
                                <div style="width:100%;padding:40px 16px;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;color:#9ca3af;gap:8px;">
                                    <i data-feather="inbox" style="width:44px;height:44px;color:var(--color-text-muted);stroke:var(--color-text-muted);"></i>
                                    <div style="font-size:14px;font-weight:600;color:#6b7280;margin:0;">Tidak Ada Data</div>
                                    <div style="font-size:12px;color:#9ca3af;">Belum ada jenis inventaris yang tersedia.<br>Tambahkan terlebih dahulu di menu <a href="{{ route('jenis-inventaris.index') }}" style="color:#1f3a2a;font-weight:600;">Jenis Inventaris</a>.</div>
                                </div>
                                @else
                                <div class="buku-inventaris-section" style="margin-top:0;">
                                    <div class="buku-inventaris-header" style="margin-bottom:14px;">
                                        <div class="buku-inventaris-header-content">
                                            <span class="buku-inventaris-title">Jenis Inventaris</span>
                                            <p class="buku-inventaris-desc">Pilih jenis inventaris untuk menampilkan form isian data yang sesuai.</p>
                                        </div>
                                    </div>
                                    <div class="jenis-flow-helper" id="jenis-flow-helper" style="margin-bottom:16px;">
                                        <i data-feather="info"></i>
                                        <span>Pilih jenis inventaris terlebih dahulu untuk mengaktifkan opsi export, tanda tangan, pejabat custom, dan tanggal dokumen.</span>
                                    </div>
                                    <input type="hidden" name="jenis_inventaris_id" id="jenis_inventaris_id_hidden" value="">
                                    
                                    <div class="searchable-dropdown" id="jenis-dropdown-container">
                                        <button type="button" class="dropdown-trigger" id="jenis-dropdown-trigger">
                                            <span id="jenis-selected-label" style="color:#9ca3af;font-size:13px;font-weight:500;">Pilih Jenis Inventaris</span>
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
                                    <i data-feather="inbox" style="width:44px;height:44px;color:var(--color-text-muted);stroke:var(--color-text-muted);"></i>
                                    <div style="font-size:14px;font-weight:600;color:#6b7280;margin:0;">Belum Ada Ruangan</div>
                                    <div style="font-size:12px;color:#9ca3af;">Tambahkan data ruangan di menu <a href="{{ route('ruangan.index') }}" style="color:#1f3a2a;font-weight:600;">Master Data &raquo; Ruangan</a>.</div>
                                </div>
                                @else
                                <div class="buku-inventaris-section" style="margin-top:0;">
                                    <div class="buku-inventaris-header" style="margin-bottom:14px;">
                                        <div class="buku-inventaris-header-content">
                                            <span class="buku-inventaris-title">Ruangan</span>
                                            <p class="buku-inventaris-desc">Pilih ruangan yang akan diekspor sebagai Kartu Inventaris Ruangan.</p>
                                        </div>
                                    </div>
                                    <div class="jenis-flow-helper" style="margin-bottom:16px;">
                                        <i data-feather="info"></i>
                                        <span>Pilih ruangan untuk mengaktifkan opsi export Kartu Inventaris Ruangan.</span>
                                    </div>
                                    <input type="hidden" name="ruangan_id_selected" id="ruangan_id_hidden" value="">

                                    <div class="searchable-dropdown" id="ruangan-dropdown-container">
                                        <button type="button" class="dropdown-trigger" id="ruangan-dropdown-trigger">
                                            <span id="ruangan-selected-label" style="color:#9ca3af;font-size:13px;font-weight:500;">Pilih Ruangan</span>
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
                                        <div style="font-size:12.5px;font-weight:600;color:var(--color-text);margin-bottom:8px;">
                                            Template Format KIR
                                        </div>
                                        <div style="display:flex;flex-direction:column;gap:8px;">
                                            <label style="display:flex;align-items:center;gap:10px;padding:10px 12px;border:1.5px solid var(--color-primary);background:var(--color-surface);border-radius:var(--radius-sm);cursor:pointer;transition:all 0.15s;" id="kir-format-standard-label">
                                                <input type="radio" name="kir-format" value="standard" checked
                                                       style="width:16px;height:16px;accent-color:var(--color-primary);flex-shrink:0;margin:0;"
                                                       onchange="onKirFormatChange(this)">
                                                <div>
                                                    <div style="font-size:13px;font-weight:600;color:var(--color-text);">Format Standar (Permendagri)</div>
                                                    <div style="font-size:11.5px;color:var(--color-text-muted);margin-top:1px;">2 TTD: Sekretaris Desa &amp; Petugas Barang</div>
                                                </div>
                                            </label>
                                            <label style="display:flex;align-items:center;gap:10px;padding:10px 12px;border:1.5px solid var(--color-border);background:var(--color-surface);border-radius:var(--radius-sm);cursor:pointer;transition:all 0.15s;" id="kir-format-custom-label">
                                                <input type="radio" name="kir-format" value="custom"
                                                       style="width:16px;height:16px;accent-color:var(--color-primary);flex-shrink:0;margin:0;"
                                                       onchange="onKirFormatChange(this)">
                                                <div>
                                                    <div style="font-size:13px;font-weight:600;color:var(--color-text);">Format Khusus Desa (3 TTD)</div>
                                                    <div style="font-size:11.5px;color:var(--color-text-muted);margin-top:1px;">3 TTD: Perbekel, Penanggung Jawab, &amp; Pengurus Barang</div>
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
                                    'all' => ['bg' => 'linear-gradient(135deg,#1f3a2a,#1c5240)', 'icon' => 'layers'],
                                    'A'   => ['bg' => 'linear-gradient(135deg,#10b981,#059669)', 'icon' => 'map-pin'],
                                    'B'   => ['bg' => 'linear-gradient(135deg,#3b82f6,#2563eb)', 'icon' => 'tool'],
                                    'C'   => ['bg' => 'linear-gradient(135deg,#8b5cf6,#7c3aed)', 'icon' => 'home'],
                                    'D'   => ['bg' => 'linear-gradient(135deg,#06b6d4,#0891b2)', 'icon' => 'navigation'],
                                    'E'   => ['bg' => 'linear-gradient(135deg,#f59e0b,#d97706)', 'icon' => 'package'],
                                    'F'   => ['bg' => 'linear-gradient(135deg,#ef4444,#dc2626)', 'icon' => 'settings'],
                                ];
                                @endphp
                                <div id="content-kib" style="display:none;">
                                  <div class="buku-inventaris-section" style="margin-top:0;">
                                    <div class="buku-inventaris-header" style="margin-bottom:14px;">
                                        <div class="buku-inventaris-header-content">
                                            <span class="buku-inventaris-title">Buku Inventaris (KIB)</span>
                                            <p class="buku-inventaris-desc">Pilih KIB yang akan diekspor sesuai standar Permendagri.</p>
                                        </div>
                                    </div>
                                    <div class="jenis-flow-helper" style="margin-bottom:16px;">
                                      <i data-feather="info"></i>
                                      <span>Pilih KIB terlebih dahulu untuk mengaktifkan opsi export. Filter tahun perolehan berlaku untuk semua KIB.</span>
                                    </div>
                                    <input type="hidden" name="kib_type_selected" id="kib_type_hidden" value="">
                                    <div class="searchable-dropdown" id="kib-dropdown-container">
                                        <button type="button" class="dropdown-trigger" id="kib-dropdown-trigger">
                                            <span id="kib-selected-label" style="color:#9ca3af;font-size:13px;font-weight:500;">Pilih KIB</span>
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

                                {{-- Konten Buku Inventaris Aset Desa --}}
                                <div id="content-buku-inventaris-aset" style="display:none;">
                                  <div class="buku-inventaris-section" style="margin-top:0;">
                                    <div class="buku-inventaris-header" style="margin-bottom:14px;">
                                        <div class="buku-inventaris-header-content">
                                            <span class="buku-inventaris-title">Buku Inventaris Aset Desa</span>
                                            <p class="buku-inventaris-desc">Export Buku Inventaris Aset Desa mencakup seluruh aset di sistem (KIB A-F).</p>
                                        </div>
                                    </div>
                                    <div class="jenis-flow-helper" style="margin-bottom:16px;">
                                        <i data-feather="info"></i>
                                        <span>Export Buku Inventaris Aset Desa mencakup seluruh aset di sistem (KIB A-F). Pilih periode laporan di bawah.</span>
                                    </div>

                                    {{-- Pilih Periode --}}
                                    <div style="font-size:12px;font-weight:600;color:#6b7280;margin-bottom:8px;">Periode Buku Inventaris</div>
                                    <input type="hidden" name="buku_inventaris_periode" id="buku_inventaris_periode_hidden" value="tahunan">
                                    <div class="searchable-dropdown" id="buku-inventaris-dropdown-container">
                                        <button type="button" class="dropdown-trigger" id="buku-inventaris-dropdown-trigger">
                                            <span id="buku-inventaris-selected-label" style="color:#9ca3af;font-size:13px;font-weight:500;">Pilih Periode</span>
                                            <i data-feather="chevron-down" class="dropdown-trigger-caret" style="width:16px;height:16px;"></i>
                                        </button>
                                        <div class="dropdown-menu-panel" id="buku-inventaris-dropdown-menu">
                                            <div class="dropdown-search-wrapper">
                                                <i data-feather="search"></i>
                                                <input type="text" id="buku-inventaris-dropdown-search" class="dropdown-search-input" placeholder="Cari periode..." autocomplete="off">
                                            </div>
                                            <ul class="dropdown-options-list" id="buku-inventaris-dropdown-options-list"></ul>
                                        </div>
                                    </div>
                                  </div>
                                </div>{{-- end content-buku-inventaris-aset --}}

                                {{-- Konten Laporan Aset --}}
                                <div id="content-laporan" style="display:none;">
                                  <div class="buku-inventaris-section" style="margin-top:0;">
                                    <div class="buku-inventaris-header" style="margin-bottom:14px;">
                                        <div class="buku-inventaris-header-content">
                                            <span class="buku-inventaris-title">Laporan Periodik / Mutasi</span>
                                            <p class="buku-inventaris-desc">Laporan saldo awal, mutasi penambahan, dan saldo akhir per semester atau tahunan.</p>
                                        </div>
                                    </div>
                                    <div class="jenis-flow-helper" style="margin-bottom:16px;">
                                        <i data-feather="info"></i>
                                        <span>Pilih periode laporan. Tahun perolehan dipilih melalui Filter Tahun Perolehan di bawah.</span>
                                    </div>

                                    {{-- Pilih Periode --}}
                                    <div style="font-size:12px;font-weight:600;color:#6b7280;margin-bottom:8px;">Periode Laporan</div>
                                    <input type="hidden" name="laporan_periode" id="laporan_periode_hidden" value="tahunan">
                                    <div class="searchable-dropdown" id="laporan-dropdown-container">
                                        <button type="button" class="dropdown-trigger" id="laporan-dropdown-trigger">
                                            <span id="laporan-selected-label" style="color:#9ca3af;font-size:13px;font-weight:500;">Pilih Periode Laporan</span>
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
                                  </div>
                                </div>{{-- end content-laporan --}}

                                <!-- Subsection: Informasi Dokumen (Lampiran & Nomor BA) - Hanya untuk Export Jenis Inventaris -->
                                <div id="section-dokumen-info" style="display:none; margin-top: 24px;">
                                    {{-- Divider antara Section Format dan Informasi Dokumen --}}
                                    <hr class="sima-divider" style="margin: 0 0 24px 0 !important;" />
                                    <div class="buku-inventaris-header" style="margin-bottom: 14px;">
                                        <div class="buku-inventaris-header-content">
                                            <span class="buku-inventaris-title">Informasi Dokumen</span>
                                            <p class="buku-inventaris-desc">Isi informasi lampiran dan berita acara (opsional)</p>
                                        </div>
                                    </div>
                                    <!-- Info Helper -->
                                    <div class="doc-flow-helper" style="margin-bottom: 16px;">
                                        <i data-feather="info"></i>
                                        <span>Informasi ini akan muncul di bagian header laporan export. Boleh dikosongkan jika tidak diperlukan.</span>
                                    </div>
                                    <div class="ttd-setup">
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
                                                    Preview di Export:
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

                                <!-- Subsection: Atur Pejabat & Tanggal Export -->
                                <div id="section-ttd-setup" style="display:none; margin-top: 24px;">
                                    {{-- Divider antara Section Pilihan Format dan Atur Pejabat & Tanggal Export --}}
                                    <hr class="sima-divider" style="margin: 0 0 24px 0 !important;" />
                                    <div class="buku-inventaris-header" style="margin-bottom: 14px;">
                                        <div class="buku-inventaris-header-content">
                                            <span class="buku-inventaris-title">Atur Pejabat &amp; Tanggal Export</span>
                                            <p class="buku-inventaris-desc">Konfigurasi pejabat dan filter tahun data untuk export</p>
                                        </div>
                                    </div>
                                    <div class="ttd-setup">
                                        <!-- Filter Tahun -->
                                        <div class="ttd-subsection">
                                            <div class="ttd-subsection-title" id="filter-tahun-section-title">                                      
                                                <span id="filter-tahun-title-text">Filter Tahun Perolehan</span>
                                            </div>
                                            <div class="filter-tahun-wrap">
                                                <input type="text" id="filter-tahun"
                                                    class="year-picker-export date-input-field"
                                                    placeholder="Pilih tahun atau semua tahun"
                                                    value="{{ date('Y') }}"
                                                    readonly>
                                                <div class="data-info-badge">
                                                    <span id="tahun-data-count-text"><span id="tahun-data-count">-</span> aset akan diexport</span>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Pejabat Default -->
                                        <div class="ttd-subsection" id="ttd-subsec-pejabat-default">
                                            <div class="ttd-subsection-title">
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
                                            <div class="ttd-subsection-title" style="display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 12px;">
                                                <span id="custom-pejabat-section-title">Ganti Penandatangan (Opsional)</span>
                                                <button type="button" id="btn-custom-pejabat" class="btn-sima-primary" onclick="openCustomPejabatModal()" style="margin: 0; padding: 7px 18px; font-size: 12px; white-space: nowrap; flex-shrink: 0; width: auto; border-radius: var(--radius-full) !important;">
                                                    Ganti Penandatangan
                                                </button>
                                            </div>
                                            <div class="pejabat-display">
                                                <span class="pejabat-display-label" id="custom-pejabat-section-label">Penandatangan pengganti untuk dokumen ini:</span>
                                                <div class="pejabat-items" id="custom-pejabat-list">
                                                    <div class="pejabat-empty">Belum diubah — menggunakan penandatangan default dari sistem.</div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Tanggal Dokumen -->
                                        <div class="ttd-subsection" id="ttd-subsection-dokumen" style="margin-top: 24px;">
                                            {{-- Divider antara Atur Pejabat & Tanggal Export dan Tanggal Dokumen --}}
                                            <hr class="sima-divider" style="margin: 0 0 24px 0 !important;" />
                                            <div class="buku-inventaris-header-content" style="margin-bottom: 10px;">
                                                <span class="ttd-subsection-title" style="margin-bottom: 2px;">Tanggal Dokumen</span>
                                                <p class="buku-inventaris-desc">Atur tanggal penerbitan yang akan dicantumkan pada dokumen export</p>
                                            </div>
                                            <div class="doc-flow-helper" id="doc-flow-helper-text" style="margin-bottom: 12px;">
                                                <i data-feather="info"></i>
                                                <span>Tanggal dokumen digunakan untuk mengisi informasi tanggal pada hasil export. Default adalah hari ini.</span>
                                            </div>
                                            <div class="date-input-group" style="padding-bottom: 16px;">
                                                <label class="date-input-label">Tanggal Dokumen</label>
                                                <input type="text" id="doc-tanggal" class="date-input-field" placeholder="Pilih tanggal dokumen">
                                            </div>
                                        </div>
                                    </div>
                                </div>{{-- close #section-ttd-setup --}}

                            </div>{{-- close #section-pilih-jenis --}}
                        </div>{{-- close .section-body --}}
                    </div>{{-- close #section-pilih-tipe --}}
                </div>{{-- close .export-main --}}

                    <!-- Right Sidebar: Export Options -->
                    <div class="export-sidebar">
                        <!-- Export Options -->
                        <div class="export-options-card" id="export-options-card">
                            <div class="export-options-body">
                                <!-- Format Selection -->
                                <div class="signature-label" style="padding-top:16px;">
                                    Opsi Format File
                                </div>
                                <div class="format-options">
                                    <div class="format-option">
                                        <input type="radio" name="export-format" id="format-excel" value="excel" checked class="sr-only" style="display:none !important;">
                                        <label for="format-excel">
                                            <div class="format-info">
                                                <div class="format-label">Excel</div>
                                                <div class="format-desc">Format .xlsx</div>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="format-option">
                                        <input type="radio" name="export-format" id="format-pdf" value="pdf" class="sr-only" style="display:none !important;">
                                        <label for="format-pdf">
                                            <div class="format-info">
                                                <div class="format-label">PDF</div>
                                                <div class="format-desc">Format PDF</div>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <!-- Signature Options -->
                                <div class="signature-label">
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
                                    <div class="signature-label">
                                        Ukuran Kertas
                                    </div>
                                    <div class="signature-options-group">
                                        <div class="signature-option">
                                            <input type="radio" name="paper-size" id="paper-legal" value="legal" checked>
                                            <label for="paper-legal">
                                                <span style="font-size:12.5px;font-weight:600;color:var(--color-text);display:block;">Legal</span>
                                                <span style="font-size:11px;color:var(--color-text-muted);">8.5 &times; 14 in &nbsp;|&nbsp; 21.59 &times; 35.56 cm</span>
                                            </label>
                                        </div>
                                        <div class="signature-option">
                                            <input type="radio" name="paper-size" id="paper-letter" value="letter">
                                            <label for="paper-letter">
                                                <span style="font-size:12.5px;font-weight:600;color:var(--color-text);display:block;">Letter</span>
                                                <span style="font-size:11px;color:var(--color-text-muted);">8.5 &times; 11 in &nbsp;|&nbsp; 21.59 &times; 27.94 cm</span>
                                            </label>
                                        </div>
                                        <div class="signature-option">
                                            <input type="radio" name="paper-size" id="paper-f4" value="f4">
                                            <label for="paper-f4">
                                                <span style="font-size:12.5px;font-weight:600;color:var(--color-text);display:block;">F4 / Folio</span>
                                                <span style="font-size:11px;color:var(--color-text-muted);">8.27 &times; 13 in &nbsp;|&nbsp; 21 &times; 33 cm</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Export Button -->
                                <button class="export-action-btn" id="btn-preview-export" style="margin-bottom:16px"disabled>
                                    <i data-feather="eye"></i>
                                    Preview Export
                                </button>
                            </div>
                        </div>

                        <!-- Summary Card -->
                        <div class="section-card export-summary-card" id="export-summary-card" style="display: none;">
                            <div class="section-header">
                                <div class="section-header-title" style="margin-top:16px;">
                                    <h5>Ringkasan Export</h5>
                                </div>
                            </div>
                            <div class="section-body">
                                <div class="simple-info-list">

                                    <div class="simple-item">
                                        <div class="simple-icon">
                                            <i data-feather="book"></i>
                                        </div>
                                        <div class="simple-text">
                                            <div class="simple-title">Jenis</div>
                                            <div class="simple-sub" id="info-jenis">Buku Inventaris Aset Desa</div>
                                        </div>
                                    </div>

                                    <div class="simple-item">
                                        <div class="simple-icon">
                                            <i data-feather="calendar"></i>
                                        </div>
                                        <div class="simple-text">
                                            <div class="simple-title">Tahun</div>
                                            <div class="simple-sub" id="info-tahun">{{ date('Y') }}</div>
                                        </div>
                                    </div>

                                    <div class="simple-item">
                                        <div class="simple-icon">
                                            <i data-feather="file-text"></i>
                                        </div>
                                        <div class="simple-text">
                                            <div class="simple-title">Format</div>
                                            <div class="simple-sub" id="info-format">Excel (.xlsx)</div>
                                        </div>
                                    </div>

                                    <div class="simple-item" style="margin-bottom:16px;">
                                        <div class="simple-icon">
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
                    <div id="kir-format2-hint" style="display:none; margin-bottom:14px; padding:14px 16px; background:var(--color-primary-tint); border:1px solid var(--color-border); border-radius:var(--radius-sm);">
                        <div style="font-size:12.5px;font-weight:700;color:var(--color-primary);margin-bottom:8px;">
                            Cara memilih penandatangan Format Desa {{ config('desa.nama_lokasi', 'Demo') }}
                        </div>
                        <div style="display:flex;flex-direction:column;gap:5px;font-size:12px;color:var(--color-text);line-height:1.6;">
                            <div>— Orang pertama yang anda centang menjadi <strong>Penanggung Jawab</strong></div>
                            <div>— Orang kedua yang anda centang menjadi <strong>Pengurus Barang</strong></div>
                            <div>— Pilih tepat <strong>2 orang</strong>. Perbekel sudah otomatis diisi sistem dan tidak perlu dipilih di sini.</div>
                        </div>
                        <div id="kir-format2-hint-count" style="margin-top:8px;font-size:11.5px;font-weight:600;color:var(--color-primary);"></div>
                    </div>
                    <div class="pejabat-list" id="modal-pejabat-list" style="border: 1px solid var(--color-border); border-radius: var(--radius-sm); max-height: 300px; overflow-y: auto; background: var(--color-surface);">
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
                            <div class="preview-info-value" id="preview-date">Demo, -</div>
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
                        <div id="preview-wrapper-pdf" style="display:none; border-radius:8px; overflow:hidden; border:1px solid #e5e7eb;">
                            <div class="excel-toolbar" style="background:#dc2626;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="white"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8" fill="none" stroke="white" stroke-width="2"/></svg>
                                <span id="pdf-filename">export.pdf</span>
                            </div>
                            <div id="pdf-preview-content" style="background:#ffffff; max-height:360px; overflow-y:auto; overflow-x:auto; padding:16px 20px;"></div>
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
            // ===== DATA DESA & KONFIGURASI DARI PHP =====
            const desaConfig = {!! json_encode([
                'nama_lokasi'    => strtoupper(config('desa.nama_lokasi') ?: (config('desa.desa_nama') ?: 'DEMO')),
                'provinsi_nama'  => strtoupper(config('desa.provinsi_nama') ?: 'BALI'),
                'kabupaten_nama' => strtoupper(config('desa.kabupaten_nama') ?: 'KABUPATEN DEMO'),
                'kecamatan_nama' => strtoupper(config('desa.kecamatan_nama') ?: 'KECAMATAN DEMO'),
                'desa_nama'      => strtoupper(config('desa.desa_nama') ?: (config('desa.nama_lokasi') ?: 'DEMO')),
                'provinsi_kode'  => config('desa.provinsi_kode') ?: '51',
                'kabupaten_kode' => config('desa.kabupaten_kode') ?: '03',
                'kecamatan_kode' => config('desa.kecamatan_kode') ?: '06',
                'kode_lokasi'    => config('desa.kode_lokasi') ?: '51.03.06.2002',
                'upb'            => config('desa.upb') ?: 'PEMERINTAH DESA DEMO',
            ]) !!};

            // ===== DATA DARI PHP =====
            const exportTypeOptions = [
                {
                    id: 'buku_inventaris_aset',
                    nama: 'Buku Inventaris Desa',
                    deskripsi: 'Rekapitulasi seluruh aset desa lengkap (KIB A s/d F)',
                    icon: 'archive',
                    gradient: 'linear-gradient(135deg,#1f3a2a 0%,#1c5240 100%)'
                },
                {
                    id: 'kib',
                    nama: 'Buku Inventaris (KIB)',
                    deskripsi: 'Format standar KIB A (Tanah) s/d KIB F (KDP)',
                    icon: 'book-open',
                    gradient: 'linear-gradient(135deg,#f59e0b 0%,#d97706 100%)'
                },
                {
                    id: 'ruangan',
                    nama: 'Kartu Inventaris Ruangan (KIR)',
                    deskripsi: 'Cetak KIR per ruangan kantor desa & unit kerja',
                    icon: 'grid',
                    gradient: 'linear-gradient(135deg,#10b981 0%,#059669 100%)'
                },
                {
                    id: 'jenis',
                    nama: 'Format Jenis Barang',
                    deskripsi: 'Cetak format tabel khusus per kategori inventaris',
                    icon: 'file-text',
                    gradient: 'linear-gradient(135deg,#3b82f6 0%,#2563eb 100%)'
                },
                {
                    id: 'laporan',
                    nama: 'Laporan Periodik / Mutasi',
                    deskripsi: 'Laporan saldo awal, mutasi penambahan, & saldo akhir',
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
                'color_gradient' => ['#1f3a2a', '#2d3a52']
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
                        ['gradient' => 'linear-gradient(135deg,#1f3a2a 0%,#2d3a52 100%)', 'icon' => 'layers'],
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
                    'gradient' => 'linear-gradient(135deg,#1f3a2a 0%,#2d3a52 100%)',
                ])->values()
            ) !!};

            const kibOptions = {!! json_encode(
                collect([
                    ['id' => 'all', 'nama' => 'Semua KIB', 'deskripsi' => 'KIB A - E', 'icon' => 'layers', 'gradient' => 'linear-gradient(135deg,#1f3a2a,#1c5240)', 'disabled' => false],
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
                if (stdLabel) stdLabel.style.borderColor = radio.value === 'standard' ? '#1f3a2a' : '#e5e7eb';
                if (cstLabel) cstLabel.style.borderColor = radio.value === 'custom'   ? '#1f3a2a' : '#e5e7eb';
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

            window.selectExportTypeCard = function(id) {
                document.querySelectorAll('#export-type-cards-container .export-type-card').forEach(card => {
                    if (card.dataset.id === id) {
                        card.classList.add('active');
                    } else {
                        card.classList.remove('active');
                    }
                });
                const hiddenInput = document.getElementById('export_type_hidden');
                if (hiddenInput) hiddenInput.value = id;
                selectedExportType = id;
                switchExportTab(id);
            };

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
                                <span style="font-size:13px;font-weight:600;color:var(--color-text);">${selectedData.nama}</span>
                            `;
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
                            document.getElementById('jenis-selected-label').innerHTML = `
                                <span style="font-size:13px;font-weight:600;color:var(--color-text);">${selectedData.nama}</span>
                            `;
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
                                <span style="font-size:13px;font-weight:600;color:var(--color-text);">${sel.nama}</span>`;
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
                                <span style="font-size:13px;font-weight:600;color:var(--color-text);">${sel.nama}</span>`;
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
                        <span style="font-size:13px;font-weight:600;color:var(--color-text);">${defaultOption.nama}</span>`;
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
                                <span style="font-size:13px;font-weight:600;color:var(--color-text);">${sel.nama}</span>`;
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
                            <span style="font-size:13px;font-weight:600;color:var(--color-text);">${sel.nama}</span>`;
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

            function renderBukuInventarisDropdown() {
                const optionsList = document.getElementById('buku-inventaris-dropdown-options-list');
                const trigger     = document.getElementById('buku-inventaris-dropdown-trigger');
                const menu        = document.getElementById('buku-inventaris-dropdown-menu');
                const searchInput = document.getElementById('buku-inventaris-dropdown-search');
                const container   = document.getElementById('buku-inventaris-dropdown-container');
                const hiddenInput = document.getElementById('buku_inventaris_periode_hidden');

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
                const defaultOption = laporanPeriodeOptions.find(o => o.id === (hiddenInput?.value || 'tahunan'));
                if (defaultOption) {
                    optionsList.querySelectorAll('.dropdown-option-item').forEach(o => {
                        if (o.dataset.id === defaultOption.id) o.classList.add('selected');
                    });
                    const labelEl = document.getElementById('buku-inventaris-selected-label');
                    if (labelEl) {
                        labelEl.innerHTML = `
                            <span style="font-size:13px;font-weight:600;color:var(--color-text);">${defaultOption.nama}</span>`;
                    }
                }

                optionsList.querySelectorAll('.dropdown-option-item').forEach(item => {
                    item.addEventListener('click', function(e) {
                        e.stopPropagation();
                        optionsList.querySelectorAll('.dropdown-option-item').forEach(o => o.classList.remove('selected'));
                        this.classList.add('selected');

                        const id = this.dataset.id;
                        if (hiddenInput) hiddenInput.value = id;

                        const sel = laporanPeriodeOptions.find(l => String(l.id) === String(id));
                        if (sel) {
                            const labelEl = document.getElementById('buku-inventaris-selected-label');
                            if (labelEl) {
                                labelEl.innerHTML = `
                                    <span style="font-size:13px;font-weight:600;color:var(--color-text);">${sel.nama}</span>`;
                            }
                        }

                        updateUI(); updateUIForActiveTab(); loadDefaultPejabat(); updateDataCount();
                        menu.classList.remove('show');
                        container.classList.remove('open');
                    });
                });

                if (trigger) {
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
                }

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

                const closeBukuInvDropdown = function(e) {
                    if (!container.contains(e.target)) {
                        menu.classList.remove('show');
                        container.classList.remove('open');
                    }
                };
                document.removeEventListener('click', closeBukuInvDropdown);
                document.addEventListener('click', closeBukuInvDropdown);
            }

            function switchExportTab(tab) {
                activeExportTab = tab;

                // Reset filter tahun ke default (tahun sekarang) setiap kali pindah tab
                const defaultTahun = '{{ date("Y") }}';
                selectedTahun = defaultTahun;
                const filterTahunInput = document.getElementById('filter-tahun');
                if (filterTahunInput) {
                    filterTahunInput.value = defaultTahun;
                    filterTahunInput.placeholder = (tab === 'laporan' || tab === 'buku_inventaris_aset') ? 'Pilih tahun laporan' : 'Pilih tahun atau semua tahun';
                    if (filterTahunInput._flatpickr) {
                        filterTahunInput._flatpickr.setDate(defaultTahun + '-01-01', false);
                    }
                }

                const filterTahunTitle = document.getElementById('filter-tahun-title-text');
                if (filterTahunTitle) {
                    filterTahunTitle.textContent = (tab === 'laporan' || tab === 'buku_inventaris_aset') ? 'Tahun Laporan' : 'Filter Tahun Perolehan';
                }

                // Tampilkan section pilih jenis/ruangan
                const sectionPilih = document.getElementById('section-pilih-jenis');
                if (sectionPilih) sectionPilih.style.display = '';

                // Untuk laporan aset & buku inventaris: sembunyikan subsection pejabat custom jika tidak diperlukan
                const subsecPejabatDefault = document.getElementById('ttd-subsec-pejabat-default');
                const subsecPejabatCustom  = document.getElementById('ttd-subsec-pejabat-custom');
                const subsecTtdOptions     = document.getElementById('ttd-subsec-ttd-options');
                const subsecDocDate        = document.getElementById('ttd-subsec-doc-date');
                if (tab === 'laporan') {
                    if (subsecPejabatDefault) subsecPejabatDefault.style.display = 'none';
                    if (subsecPejabatCustom)  subsecPejabatCustom.style.display  = 'none';
                    if (subsecTtdOptions)     subsecTtdOptions.style.display     = 'none';
                    if (subsecDocDate)        subsecDocDate.style.display        = 'none';
                } else if (tab === 'buku_inventaris_aset') {
                    if (subsecPejabatDefault) subsecPejabatDefault.style.display = 'none';
                    if (subsecPejabatCustom)  subsecPejabatCustom.style.display  = 'none';
                    if (subsecTtdOptions)     subsecTtdOptions.style.display     = '';
                    if (subsecDocDate)        subsecDocDate.style.display        = '';
                } else {
                    if (subsecPejabatDefault) subsecPejabatDefault.style.display = '';
                    if (subsecPejabatCustom)  subsecPejabatCustom.style.display  = '';
                    if (subsecTtdOptions)     subsecTtdOptions.style.display     = '';
                    if (subsecDocDate)        subsecDocDate.style.display        = '';
                }

                // Show/hide konten di dalam section yang sama
                const contentJenis          = document.getElementById('content-jenis');
                const contentRuangan        = document.getElementById('content-ruangan');
                const contentKib            = document.getElementById('content-kib');
                const contentLaporan        = document.getElementById('content-laporan');
                const contentBukuInventaris = document.getElementById('content-buku-inventaris-aset');
                if (contentJenis)          contentJenis.style.display          = tab === 'jenis'                ? '' : 'none';
                if (contentRuangan)        contentRuangan.style.display        = tab === 'ruangan'              ? '' : 'none';
                if (contentKib)            contentKib.style.display            = tab === 'kib'                  ? '' : 'none';
                if (contentLaporan)        contentLaporan.style.display        = tab === 'laporan'              ? '' : 'none';
                if (contentBukuInventaris) contentBukuInventaris.style.display = tab === 'buku_inventaris_aset' ? '' : 'none';

                // Update header section title
                const sectionTitle = document.querySelector('#section-pilih-jenis .section-header-title h5');
                const sectionSub   = document.querySelector('#section-pilih-jenis .section-header-title span');
                const titles = {
                    jenis: 'Pilih Jenis Inventaris',
                    ruangan: 'Pilih Ruangan',
                    kib: 'Pilih KIB',
                    buku_inventaris_aset: 'Buku Inventaris Aset Desa',
                    laporan: 'Pilih Periode Laporan'
                };
                const subs = {
                    jenis: 'Pilih jenis inventaris yang akan diekspor',
                    ruangan: 'Pilih ruangan yang akan diekspor',
                    kib: 'Pilih Kartu Inventaris Barang (KIB) yang akan diekspor',
                    buku_inventaris_aset: 'Export rekapitulasi seluruh aset desa (KIB A-F)',
                    laporan: 'Pilih tahun dan periode untuk laporan aset'
                };
                if (sectionTitle) sectionTitle.textContent = titles[tab] || titles.jenis;
                if (sectionSub)   sectionSub.textContent   = subs[tab]   || subs.jenis;

                // Update icon & warna section header - selalu netral (list + gelap)
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
                    document.getElementById('jenis-selected-label').innerHTML = '<span style="color:#9ca3af;font-size:13px;font-weight:500;">Pilih Jenis Inventaris</span>';
                } else if (tab === 'ruangan') {
                    selectedJenisId = null;
                    selectedKib = null;
                    selectedRuangan = null;
                    document.getElementById('jenis_inventaris_id_hidden').value = '';
                    document.getElementById('jenis-selected-label').innerHTML = '<span style="color:#9ca3af;font-size:13px;font-weight:500;">Pilih Jenis Inventaris</span>';
                    const rHidden = document.getElementById('ruangan_id_hidden');
                    const rLabel  = document.getElementById('ruangan-selected-label');
                    if (rHidden) rHidden.value = '';
                    if (rLabel)  rLabel.innerHTML = '<span style="color:#9ca3af;font-size:13px;font-weight:500;">Pilih Ruangan</span>';
                    document.querySelectorAll('#ruangan-dropdown-options-list .dropdown-option-item').forEach(o => o.classList.remove('selected'));
                    const kfs = document.getElementById('kir-format-section');
                    if (kfs) kfs.style.display = 'none';
                } else if (tab === 'kib') {
                    selectedJenisId = null;
                    selectedRuangan = null;
                    selectedKib = null;
                    document.getElementById('jenis_inventaris_id_hidden').value = '';
                    document.getElementById('jenis-selected-label').innerHTML = '<span style="color:#9ca3af;font-size:13px;font-weight:500;">Pilih Jenis Inventaris</span>';
                    const kHidden = document.getElementById('kib_type_hidden');
                    const kLabel  = document.getElementById('kib-selected-label');
                    if (kHidden) kHidden.value = '';
                    if (kLabel)  kLabel.innerHTML = '<span style="color:#9ca3af;font-size:13px;font-weight:500;">Pilih KIB</span>';
                    document.querySelectorAll('#kib-dropdown-options-list .dropdown-option-item').forEach(o => o.classList.remove('selected'));
                } else if (tab === 'laporan' || tab === 'buku_inventaris_aset') {
                    selectedJenisId = null;
                    selectedRuangan = null;
                    selectedKib = null;
                    document.getElementById('jenis_inventaris_id_hidden').value = '';
                    document.getElementById('jenis-selected-label').innerHTML = '<span style="color:#9ca3af;font-size:13px;font-weight:500;">Pilih Jenis Inventaris</span>';
                }

                updateUI();
                loadDefaultPejabat();
                updateCustomPejabatLabels();
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
                } else if (activeExportTab === 'buku_inventaris_aset') {
                    previewBtn.disabled = false;
                    previewBtn.innerHTML = '<i data-feather="eye" style="width:16px;height:16px;"></i> Preview Buku Inventaris';
                    const tahunVal = selectedTahun;
                    const periode = document.getElementById('buku_inventaris_periode_hidden')?.value || 'tahunan';
                    const selP = laporanPeriodeOptions.find(o => o.id === periode);
                    const periodeLabel = selP ? selP.nama : 'Per Tahun';
                    document.getElementById('info-jenis').textContent = 'Buku Inventaris Aset ' + periodeLabel + ' ' + tahunVal;
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
                        <div style="margin-bottom: 8px; border-bottom: 1px solid #e5e7eb; padding-bottom: 8px;">
                            <button type="button" class="year-picker-all-btn" id="btn-year-picker-all" style="width:100%;padding:8px 12px;border:1.5px solid #1f3a2a;border-radius: var(--radius-full);background:#ecfdf5;color:#1f3a2a;font-size:12.5px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;text-align:center;transition:all 0.15s;">
                                <span>Semua Tahun (Seluruh Data)</span>
                            </button>
                        </div>
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
                        allBtn: panel.querySelector('#btn-year-picker-all'),
                        pageStart: Math.max(minYear, maxYear - 11),
                        selectedYear: selectedTahun === 'all' ? 'all' : (parseInt(input.value) || new Date().getFullYear()),
                    };
                    cal._exportYearState = state;

                    state.allBtn.addEventListener('click', function(ev) {
                        ev.stopPropagation();
                        state.selectedYear = 'all';
                        input.value = 'Semua Tahun';
                        selectedTahun = 'all';
                        instance.close();
                        updateUI();
                        updateDataCount();
                        renderExportYearGrid(instance, state, minYear, maxYear);
                    });

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
                    if (state.allBtn) {
                        if (activeExportTab === 'laporan' || activeExportTab === 'buku_inventaris_aset') {
                            state.allBtn.style.display = 'none';
                        } else {
                            state.allBtn.style.display = '';
                            if (selectedTahun === 'all' || input.value === 'Semua Tahun') {
                                state.allBtn.style.background = '#1f3a2a';
                                state.allBtn.style.color = '#ffffff';
                                state.allBtn.style.borderColor = '#1f3a2a';
                            } else {
                                state.allBtn.style.background = '#ecfdf5';
                                state.allBtn.style.color = '#1f3a2a';
                                state.allBtn.style.borderColor = '#1f3a2a';
                            }
                        }
                    }
                    const start = state.pageStart;
                    const end   = Math.min(start + 11, maxYear);
                    state.range.textContent = start + ' - ' + end;
                    state.prev.disabled = start <= minYear;
                    state.next.disabled = end >= maxYear;
                    let html = '';
                    for (let y = start; y <= end; y++) {
                        const active = (selectedTahun !== 'all' && y === state.selectedYear) ? ' active' : '';
                        html += `<button type="button" class="year-picker-option${active}" data-year="${y}">${y}</button>`;
                    }
                    state.grid.innerHTML = html;
                }

                flatpickr(input, {
                    dateFormat: 'Y',
                    defaultDate: input.value && input.value !== 'Semua Tahun' ? input.value + '-01-01' : null,
                    disableMobile: true,
                    allowInput: false,
                    onReady: function(_, __, instance) { ensureYearPanel(instance); },
                    onOpen:  function(_, __, instance) { ensureYearPanel(instance); },
                });

                // Sync manual input
                input.addEventListener('change', function() {
                    if (this.value === 'Semua Tahun' || this.value === 'all') {
                        selectedTahun = 'all';
                        updateUI();
                        updateDataCount();
                        return;
                    }
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

                // Tab buku_inventaris_aset: tanggal dokumen selalu aktif
                if (activeExportTab === 'buku_inventaris_aset') return true;

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
                const hasSelection = activeExportTab === 'ruangan' ? !!selectedRuangan : (activeExportTab === 'kib' ? !!selectedKib : (activeExportTab === 'buku_inventaris_aset' || activeExportTab === 'laporan' ? true : !!selectedJenisId));
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
                        btnEl.textContent = 'Ganti Penanggung Jawab / Pengurus Barang';
                    } else if (isRuangan) {
                        btnEl.textContent = 'Ganti Pengurus Barang';
                    } else {
                        btnEl.textContent = 'Ganti Penandatangan';
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
                if (customPejabat.length > 0) {
                    customList.innerHTML = customPejabat.map((p, idx) => {
                        // Untuk Format 2 & KIB: tampilkan label posisi di card
                        let posLabel = '';
                        if (isKirFormat2) {
                            posLabel = idx === 0 ? '<span style="font-size:10px;font-weight:700;color:#1f3a2a;background:#e8ecf4;padding:1px 7px;border-radius:6px;margin-left:6px;">Penanggung Jawab</span>'
                                     : idx === 1 ? '<span style="font-size:10px;font-weight:700;color:#1f3a2a;background:#e8ecf4;padding:1px 7px;border-radius:6px;margin-left:6px;">Pengurus Barang</span>'
                                     : '';
                        } else if (activeExportTab === 'kib') {
                            posLabel = idx === 0 ? '<span style="font-size:10px;font-weight:700;color:#1f3a2a;background:#e8ecf4;padding:1px 7px;border-radius:6px;margin-left:6px;">Mengetahui (Kiri)</span>'
                                     : idx === 1 ? '<span style="font-size:10px;font-weight:700;color:#1f3a2a;background:#e8ecf4;padding:1px 7px;border-radius:6px;margin-left:6px;">Petugas Barang (Kanan)</span>'
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
                const badge = document.getElementById('tahun-data-count-text');
                if (activeExportTab === 'ruangan') {
                    if (!selectedRuangan) { 
                        if (el) el.textContent = '-'; 
                        if (badge) badge.innerHTML = `<span id="tahun-data-count">-</span> aset akan diexport`;
                        currentDataCount = 0; 
                        return; 
                    }
                    if (el) el.textContent = '...';
                    const param = selectedRuangan === 'all' ? 'all' : selectedRuangan;
                    fetch(`{{ route('export-data.ruangan-count') }}?ruangan_id=${param}&tahun=${selectedTahun}`)
                        .then(r => r.json())
                        .then(data => { 
                            currentDataCount = data.count || 0; 
                            if (el) el.textContent = currentDataCount; 
                            if (badge) badge.innerHTML = `<span id="tahun-data-count">${currentDataCount}</span> aset akan diexport`;
                        })
                        .catch(() => { 
                            if (el) el.textContent = '-'; 
                            if (badge) badge.innerHTML = `<span id="tahun-data-count">-</span> aset akan diexport`;
                            currentDataCount = 0; 
                        });
                    return;
                }
                if (activeExportTab === 'kib') {
                    if (!selectedKib) { 
                        if (el) el.textContent = '-'; 
                        if (badge) badge.innerHTML = `<span id="tahun-data-count">-</span> aset akan diexport`;
                        currentDataCount = 0; 
                        return; 
                    }
                    if (el) el.textContent = '...';
                    const kibParam = selectedKib === 'all' ? 'all' : selectedKib;
                    fetch(`{{ route('export-data.kib-count') }}?kib_type=${kibParam}&tahun=${selectedTahun}`)
                        .then(r => r.json())
                        .then(data => { 
                            currentDataCount = data.count || 0; 
                            if (el) el.textContent = currentDataCount; 
                            if (badge) badge.innerHTML = `<span id="tahun-data-count">${currentDataCount}</span> aset akan diexport`;
                        })
                        .catch(() => { 
                            if (el) el.textContent = '-'; 
                            if (badge) badge.innerHTML = `<span id="tahun-data-count">-</span> aset akan diexport`;
                            currentDataCount = 0; 
                        });
                    return;
                }
                if (activeExportTab === 'buku_inventaris_aset') {
                    if (el) el.textContent = '...';
                    const periode = document.getElementById('buku_inventaris_periode_hidden')?.value || 'tahunan';
                    fetch(`{{ route('export-data.buku-inventaris-count') }}?tahun=${selectedTahun}&periode=${periode}`, {
                        credentials: 'same-origin',
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                    })
                    .then(r => r.json())
                    .then(data => {
                        currentDataCount = data.count || 0;
                        if (el) el.textContent = currentDataCount;
                        if (badge) badge.innerHTML = `<span id="tahun-data-count">${currentDataCount}</span> aset akan diexport`;
                    })
                    .catch(() => { 
                        if (el) el.textContent = '0'; 
                        if (badge) badge.innerHTML = `<span id="tahun-data-count">0</span> aset akan diexport`;
                    });
                    return;
                }
                if (activeExportTab === 'laporan') {
                    if (el) el.textContent = '...';
                    // Fetch count data laporan secara async
                    const periode = document.getElementById('laporan_periode_hidden')?.value || 'tahunan';
                    fetch(`{{ route('export-data.laporan-count') }}?tahun=${selectedTahun}&periode=${periode}`, {
                        credentials: 'same-origin',
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                    })
                    .then(r => r.json())
                    .then(data => {
                        currentDataCount = data.count || 0;
                        if (el) el.textContent = currentDataCount;
                        if (badge) badge.innerHTML = `<span id="tahun-data-count">${currentDataCount}</span> baris data laporan akan diexport`;
                    })
                    .catch(() => { 
                        if (el) el.textContent = '0'; 
                        if (badge) badge.innerHTML = `<span id="tahun-data-count">0</span> baris data laporan akan diexport`;
                    });
                    return;
                }
                if (!selectedJenisId) { 
                    if (el) el.textContent = '-'; 
                    if (badge) badge.innerHTML = `<span id="tahun-data-count">-</span> aset akan diexport`;
                    currentDataCount = 0; 
                    return; 
                }
                if (el) el.textContent = '...';
                fetch(`{{ route('export-data.data-count') }}?jenis_inventaris_id=${selectedJenisId}&tahun=${selectedTahun}`)
                    .then(r => r.json())
                    .then(data => { 
                        currentDataCount = data.count || 0; 
                        if (el) el.textContent = currentDataCount; 
                        if (badge) badge.innerHTML = `<span id="tahun-data-count">${currentDataCount}</span> aset akan diexport`;
                    })
                    .catch(() => { 
                        if (el) el.textContent = '-'; 
                        if (badge) badge.innerHTML = `<span id="tahun-data-count">-</span> aset akan diexport`;
                        currentDataCount = 0; 
                    });
            }

            // Date preview
            function updateDatePreview() {
                const dateEl = document.getElementById('date-preview');
                if (!dateEl) return;
                const tanggal = document.getElementById('doc-tanggal')?.value;
                const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                
                if (tanggal) {
                    const date = new Date(tanggal);
                    dateEl.textContent = `${date.getDate()} ${months[date.getMonth()]} ${date.getFullYear()}`;
                } else {
                    dateEl.textContent = '-';
                }
            }
            const docTanggalEl = document.getElementById('doc-tanggal');
            if (docTanggalEl) docTanggalEl.addEventListener('change', updateDatePreview);

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
                    : (activeExportTab === 'laporan' || activeExportTab === 'buku_inventaris_aset' ? false
                    : !selectedJenisId));

                setFlowLockedState(isLocked);

                // Show/hide section dokumen info (hanya untuk tab jenis DAN sudah pilih jenis)
                const sectionDokumen = document.getElementById('section-dokumen-info');
                if (sectionDokumen) {
                    const shouldShowDokumen = activeExportTab === 'jenis' && selectedJenisId;
                    sectionDokumen.style.display = shouldShowDokumen ? '' : 'none';
                }

                // Show/hide section ttd setup (muncul ketika item/jenis sudah dipilih)
                const sectionTtd = document.getElementById('section-ttd-setup');
                if (sectionTtd) {
                    sectionTtd.style.display = isLocked ? 'none' : '';
                }

                const tahunLaporan = selectedTahun === 'all' ? 'Semua Tahun' : selectedTahun;
                const periode = activeExportTab === 'buku_inventaris_aset'
                    ? (document.getElementById('buku_inventaris_periode_hidden')?.value || 'tahunan')
                    : (document.getElementById('laporan_periode_hidden')?.value || 'tahunan');
                const selP = laporanPeriodeOptions.find(o => o.id === periode);
                const periodeLabel = selP ? selP.nama : 'Per Tahun';

                const jenisLabel = activeExportTab === 'ruangan'
                    ? (selectedRuangan === 'all' ? 'Semua Ruangan' : (selectedRuangan ? (ruanganOptions.find(r => String(r.id) === String(selectedRuangan))?.nama || 'Ruangan') : 'Pilih ruangan'))
                    : (activeExportTab === 'kib'
                        ? (selectedKib === 'all' ? 'Semua KIB' : (selectedKib ? 'KIB ' + selectedKib : 'Pilih KIB'))
                        : (activeExportTab === 'buku_inventaris_aset'
                            ? ('Buku Inventaris Aset ' + periodeLabel + ' ' + tahunLaporan)
                            : (activeExportTab === 'laporan'
                                ? ('Laporan Aset ' + periodeLabel + ' ' + tahunLaporan)
                                : (selectedJenisId === 'all'
                                    ? 'Semua Jenis (' + {{ $jenisInventaris->count() }} + ' jenis)'
                                    : (selectedJenisId && jenisData[selectedJenisId] ? jenisData[selectedJenisId].nama : 'Pilih jenis')))));

                document.getElementById('info-jenis').textContent = jenisLabel;
                document.getElementById('info-tahun').textContent = selectedTahun === 'all' ? 'Semua Tahun' : (activeExportTab === 'laporan' || activeExportTab === 'buku_inventaris_aset' ? tahunLaporan : selectedTahun);
                document.getElementById('info-format').textContent = formatLabel(format);
                document.getElementById('info-signature').textContent = sig === 'with' ? 'Dengan Tanda Tangan' : 'Tanpa Tanda Tangan';

                const previewBtn = document.getElementById('btn-preview-export');
                if (activeExportTab === 'ruangan') {
                    previewBtn.disabled = !selectedRuangan;
                    previewBtn.innerHTML = '<i data-feather="eye" style="width:16px;height:16px;"></i> Preview Export Ruangan';
                } else if (activeExportTab === 'kib') {
                    previewBtn.disabled = !selectedKib;
                    previewBtn.innerHTML = '<i data-feather="eye" style="width:16px;height:16px;"></i> Preview Export KIB';
                } else if (activeExportTab === 'buku_inventaris_aset') {
                    previewBtn.disabled = false;
                    previewBtn.innerHTML = '<i data-feather="eye" style="width:16px;height:16px;"></i> Preview Buku Inventaris';
                } else if (activeExportTab === 'laporan') {
                    previewBtn.disabled = false;
                    previewBtn.innerHTML = '<i data-feather="eye" style="width:16px;height:16px;"></i> Preview Laporan Aset';
                } else {
                    previewBtn.disabled = !selectedJenisId;
                    previewBtn.innerHTML = '<i data-feather="eye" style="width:16px;height:16px;"></i> Preview Export';
                }
                if (typeof feather !== 'undefined') feather.replace();

                // Show/hide Ringkasan Export dengan animasi smooth
                const summaryCard = document.getElementById('export-summary-card');
                const hasSelectedTarget = (function() {
                    if (!activeExportTab) return false;
                    if (activeExportTab === 'ruangan') return !!selectedRuangan;
                    if (activeExportTab === 'kib') return !!selectedKib;
                    if (activeExportTab === 'jenis') return !!selectedJenisId;
                    if (activeExportTab === 'buku_inventaris_aset' || activeExportTab === 'laporan') return true;
                    return false;
                })();

                if (summaryCard) {
                    if (hasSelectedTarget) {
                        if (summaryCard.style.display === 'none' || summaryCard.classList.contains('fade-out')) {
                            summaryCard.style.display = 'block';
                            summaryCard.classList.remove('fade-out');
                            summaryCard.classList.add('fade-in');
                        }
                    } else {
                        if (summaryCard.style.display !== 'none' && !summaryCard.classList.contains('fade-out')) {
                            summaryCard.classList.remove('fade-in');
                            summaryCard.classList.add('fade-out');
                            setTimeout(() => {
                                if (summaryCard.classList.contains('fade-out')) {
                                    summaryCard.style.display = 'none';
                                    summaryCard.classList.remove('fade-out');
                                }
                            }, 240);
                        }
                    }
                }

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
                if (activeExportTab === 'jenis' && !selectedJenisId) {
                    showToast('Pilih jenis inventaris terlebih dahulu', 'warning');
                    return;
                }
                if (activeExportTab === 'kib' && !selectedKib) {
                    showToast('Pilih jenis KIB terlebih dahulu', 'warning');
                    return;
                }
                if (activeExportTab === 'ruangan' && !selectedRuangan) {
                    showToast('Pilih ruangan terlebih dahulu', 'warning');
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
                        ? 'Pilih Penandatangan (Format Khusus 3 TTD)'
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
                    // Untuk Format 2 & KIB: tampilkan label urutan
                    let orderBadge = '';
                    if ((isKirFormat2 || isKib) && isChecked) {
                        const posIdx = customPejabatOrder.indexOf(Number(p.id));
                        const posLabel = isKirFormat2
                            ? (posIdx === 0 ? 'Penanggung Jawab' : (posIdx === 1 ? 'Pengurus Barang' : ''))
                            : (posIdx === 0 ? 'Mengetahui (Kiri)' : (posIdx === 1 ? 'Petugas Barang (Kanan)' : ''));
                        if (posLabel) {
                            orderBadge = `<span class="format2-badge" style="font-size:10px;font-weight:700;color:#fff;background:#1f3a2a;padding:2px 7px;border-radius:8px;margin-left:6px;">${posLabel}</span>`;
                        }
                    }
                    return `
                        <div class="pejabat-row-selectable" data-id="${p.id}" style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-bottom: 1px solid #f1f5f9; cursor: pointer;">
                            <input type="checkbox" class="pejabat-cb" data-id="${p.id}" ${isChecked ? 'checked' : ''} style="width: 16px; height: 16px; accent-color: #1f3a2a; flex-shrink:0;">
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

                bindCustomPejabatModalEvents(isKirFormat2 || isKib);
                document.getElementById('custom-pejabat-modal').classList.add('show');
                if(typeof feather !== 'undefined') feather.replace();
            }

            function updateFormat2HintCount() {
                const hintCount = document.getElementById('kir-format2-hint-count');
                if (!hintCount) return;
                const kirFormatElModal = document.querySelector('input[name="kir-format"]:checked');
                const isKirFormat2 = activeExportTab === 'ruangan' && kirFormatElModal?.value === 'custom';
                const isKib = activeExportTab === 'kib';
                if (!isKirFormat2 && !isKib) { hintCount.textContent = ''; return; }
                const checked = document.querySelectorAll('#modal-pejabat-list .pejabat-cb:checked').length;
                if (checked === 0) {
                    hintCount.textContent = 'Belum ada yang dipilih.';
                    hintCount.style.color = '#64748b';
                } else if (checked === 1) {
                    hintCount.textContent = isKib ? '1 dipilih → Mengetahui (Kiri). Pilih 1 lagi untuk Petugas Barang (Kanan).' : '1 dipilih → posisi Penanggung Jawab. Pilih 1 lagi untuk Pengurus Barang.';
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

            function bindCustomPejabatModalEvents(isOrderContext) {
                const rows = document.querySelectorAll('#modal-pejabat-list .pejabat-row-selectable');
                rows.forEach(row => {
                    row.addEventListener('click', function() {
                        togglePejabatByRow(this);
                        updateFormat2HintCount();
                        // Update label badge di baris yang sudah dicek
                        if (isOrderContext) refreshFormat2Badges();
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
                        if (isOrderContext) refreshFormat2Badges();
                    });
                });
            }

            function refreshFormat2Badges() {
                const kirFormatEl = document.querySelector('input[name="kir-format"]:checked');
                const isKirFormat2 = activeExportTab === 'ruangan' && kirFormatEl?.value === 'custom';
                const isKib = activeExportTab === 'kib';

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
                    if (cb.checked && (isKirFormat2 || isKib)) {
                        const posIdx = customPejabatOrder.indexOf(id);
                        const posLabel = isKirFormat2
                            ? (posIdx === 0 ? 'Penanggung Jawab' : (posIdx === 1 ? 'Pengurus Barang' : ''))
                            : (posIdx === 0 ? 'Mengetahui (Kiri)' : (posIdx === 1 ? 'Petugas Barang (Kanan)' : ''));
                        if (posLabel) {
                            const badge = document.createElement('span');
                            badge.className = 'format2-badge';
                            badge.style.cssText = 'font-size:10px;font-weight:700;color:#fff;background:#1f3a2a;padding:2px 7px;border-radius:8px;margin-left:6px;';
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
                const isKib = activeExportTab === 'kib';

                // Untuk Format 2 & KIB: batasi hanya 2 orang pertama
                if (isKirFormat2 || isKib) {
                    if (customPejabatOrder.length > 2) {
                        customPejabatOrder = customPejabatOrder.slice(0, 2);
                        showToast(isKib ? 'Hanya 2 penandatangan yang digunakan (Mengetahui & Petugas Barang)' : 'Hanya 2 penandatangan yang digunakan (Penanggung Jawab & Pengurus Barang)', 'warning');
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
                        const docDateStr = document.getElementById('date-preview')?.textContent || (desaConfig.nama_lokasi + ', ' + new Date().toLocaleDateString('id-ID', {day:'numeric',month:'long',year:'numeric'}));

                        const thStyle = 'border:1px solid #000000 !important;background:#ffffff !important;color:#000000 !important;padding:3.5px 2px;font-weight:bold;font-size:7.5px;text-align:center;vertical-align:middle;';
                        const tdStyle = (align = 'left') => `border:1px solid #000000 !important;background:#ffffff !important;color:#000000 !important;padding:3px 4px;font-size:7.5px;text-align:${align};vertical-align:middle;`;

                        let pdfHtml = `
                            <div style="font-family:Arial,sans-serif;color:#000;background:#ffffff;">
                                <div style="text-align:center;font-weight:bold;font-size:12px;margin-bottom:12px;letter-spacing:0.5px;">KARTU INVENTARIS RUANGAN</div>
                                <div style="font-family:'Courier New',Courier,monospace;font-size:8.5px;line-height:1.45;margin-bottom:12px;">
                                    <div style="display:flex;"><span style="width:130px;flex-shrink:0;">Provinsi</span><span>: ${desaConfig.provinsi_kode}. ${desaConfig.provinsi_nama}</span></div>
                                    <div style="display:flex;"><span style="width:130px;flex-shrink:0;">Kab./Kota</span><span>: ${desaConfig.kabupaten_kode}. ${desaConfig.kabupaten_nama}</span></div>
                                    <div style="display:flex;"><span style="width:130px;flex-shrink:0;">Kecamatan</span><span>: ${desaConfig.kecamatan_kode}. ${desaConfig.kecamatan_nama}</span></div>
                                    <div style="display:flex;"><span style="width:130px;flex-shrink:0;">Desa</span><span>: ${desaConfig.kode_lokasi}. ${desaConfig.desa_nama}</span></div>
                                    <div style="display:flex;"><span style="width:130px;flex-shrink:0;">UPB</span><span>: ${desaConfig.upb}</span></div>
                                    <div style="display:flex;"><span style="width:130px;flex-shrink:0;">No. Kode Lokasi</span><span>: ${desaConfig.kode_lokasi}.</span></div>
                                </div>
                                <div style="overflow-x:auto;margin-bottom:4px;">
                                    <table style="width:100%;border-collapse:collapse;font-size:7.5px;background:#ffffff;">
                                        <thead>
                                            <tr>
                                                <th rowspan="2" style="${thStyle};width:24px;">NO</th>
                                                <th rowspan="2" style="${thStyle};width:140px;">JENIS BARANG / NAMA BARANG</th>
                                                <th colspan="2" style="${thStyle}">N O M O R</th>
                                                <th rowspan="2" style="${thStyle};width:50px;">TAHUN PEROLEHAN</th>
                                                <th colspan="2" style="${thStyle}">I D E N T I T A S</th>
                                                <th rowspan="2" style="${thStyle};width:70px;">ASAL-USUL BARANG</th>
                                                <th rowspan="2" style="${thStyle};width:85px;">HARGA (Ribuan) Rp</th>
                                                <th rowspan="2" style="${thStyle};width:70px;">KETERANGAN</th>
                                            </tr>
                                            <tr>
                                                <th style="${thStyle};width:65px;">KODE BARANG</th>
                                                <th style="${thStyle};width:35px;">REG</th>
                                                <th style="${thStyle};width:110px;">KONSTRUKSI/LUAS/MERK/TYPE/WARNA/JUDUL/PENCIPTA</th>
                                                <th style="${thStyle};width:45px;">NO. POL</th>
                                            </tr>
                                            <tr>
                                                <th style="${thStyle}">1</th>
                                                <th style="${thStyle}">2</th>
                                                <th style="${thStyle}">3</th>
                                                <th style="${thStyle}">4</th>
                                                <th style="${thStyle}">5</th>
                                                <th colspan="2" style="${thStyle}">6</th>
                                                <th style="${thStyle}">7</th>
                                                <th style="${thStyle}">8</th>
                                                <th style="${thStyle}">9</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="pdf-gray-subheader">
                                                <td colspan="10" class="pdf-gray-bg" style="border:1px solid #000 !important;padding:3.5px 6px;background:#d9d9d9 !important;font-weight:bold;font-style:italic;font-size:8px;text-align:left;">Nama Ruangan : ${ruanganLabel}</td>
                                            </tr>`;
                        for (let r = 0; r < placeholders; r++) {
                            pdfHtml += `
                                            <tr>
                                                <td style="${tdStyle('center')}">${r+1}</td>
                                                <td style="${tdStyle()}">...</td>
                                                <td style="${tdStyle('center')}">...</td>
                                                <td style="${tdStyle('center')}">...</td>
                                                <td style="${tdStyle('center')}">...</td>
                                                <td style="${tdStyle('center')}">...</td>
                                                <td style="${tdStyle('center')}">...</td>
                                                <td style="${tdStyle('center')}">...</td>
                                                <td style="${tdStyle('right')}">...</td>
                                                <td style="${tdStyle()}">...</td>
                                            </tr>`;
                        }
                        if (currentDataCount > 3) {
                            pdfHtml += `
                                            <tr>
                                                <td colspan="10" style="${tdStyle('center')};color:#6b7280;font-style:italic;">... +${currentDataCount - 3} data lainnya</td>
                                            </tr>`;
                        }
                        pdfHtml += `
                                            <tr style="font-weight:bold;background:#ffffff;">
                                                <td colspan="8" style="border:1px solid #000 !important;padding:4px;text-align:center;font-weight:bold;">JUMLAH HARGA</td>
                                                <td style="border:1px solid #000 !important;padding:4px;text-align:right;font-weight:bold;">SUM</td>
                                                <td style="border:1px solid #000 !important;"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <table style="width:100%;border-collapse:collapse;border:none;margin-top:20px;font-size:8.5px;">
                                    <tr>
                                        <td style="width:50%;text-align:center;border:none;vertical-align:top;">
                                            <div style="margin-bottom:3px;">Mengetahui,</div>
                                            <div style="font-weight:bold;margin-bottom:38px;">Sekretaris Desa</div>
                                            <div style="font-weight:bold;text-decoration:underline;">( .................................................. )</div>
                                            <div style="font-size:8px;margin-top:2px;">NIP. .........................................</div>
                                        </td>
                                        <td style="width:50%;text-align:center;border:none;vertical-align:top;">
                                            <div style="margin-bottom:3px;">${desaConfig.nama_lokasi}, ${docDateStr}</div>
                                            <div style="font-weight:bold;margin-bottom:38px;">Pengurus Barang / Petugas Ruangan</div>
                                            <div style="font-weight:bold;text-decoration:underline;">( .................................................. )</div>
                                            <div style="font-size:8px;margin-top:2px;">NIP. .........................................</div>
                                        </td>
                                    </tr>
                                </table>
                            </div>`;
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

                // Handle tab Buku Inventaris Aset Desa
                if (activeExportTab === 'buku_inventaris_aset') {
                    const tahunVal = selectedTahun;
                    const periode  = document.getElementById('buku_inventaris_periode_hidden')?.value || 'tahunan';
                    let periodeLabel;
                    if (periode === 'semester_1') {
                        periodeLabel = 'Semester I (1 Januari – 30 Juni)';
                    } else if (periode === 'semester_2') {
                        periodeLabel = 'Semester II (1 Juli – 31 Desember)';
                    } else {
                        periodeLabel = 'Per Tahun (1 Januari – 31 Desember)';
                    }
                    const format = document.querySelector('input[name="export-format"]:checked').value;
                    const sig    = document.querySelector('input[name="signature-option"]:checked').value;

                    document.getElementById('preview-format').textContent = formatLabel(format);
                    document.getElementById('preview-count').textContent = 'Buku Inventaris ' + periodeLabel;
                    document.getElementById('preview-sig').textContent = sig === 'with' ? 'Dengan Tanda Tangan' : 'Tanpa Tanda Tangan';
                    document.getElementById('preview-date').textContent = document.getElementById('date-preview').textContent || tahunVal;

                    const wrapExcel = document.getElementById('preview-wrapper-excel');
                    const wrapPdf   = document.getElementById('preview-wrapper-pdf');
                    wrapExcel.style.display = format === 'excel' ? '' : 'none';
                    wrapPdf.style.display   = format === 'pdf'   ? '' : 'none';

                    const periodeSuffix = periode === 'semester_1' ? 'semester_1' : (periode === 'semester_2' ? 'semester_2' : 'tahunan');
                    const baseName = 'buku_inventaris_aset_' + periodeSuffix + '_' + tahunVal;

                    const cols = ['NO','KODE','NAMA BARANG','NUP','MERK/TYPE','BUKTI KEPEMILIKAN','TAHUN','UKURAN M2','NILAI (Rp.)','KET.'];

                    if (format === 'excel') {
                        const tbody  = document.getElementById('excel-preview-table');
                        const colBar = document.getElementById('excel-col-bar');
                        document.getElementById('excel-filename').textContent = baseName + '.xlsx';
                        const letters = 'ABCDEFGHIJ';
                        colBar.innerHTML = cols.map((_, i) => `<div class="excel-col-cell">${letters[i]||'?'}</div>`).join('');
                        const hS = `class="excel-cell excel-cell-header" style="text-align:center;font-size:9px;"`;
                        const rows = [];
                        rows.push(`<tr><td class="excel-row-num">1</td><td class="excel-cell excel-cell-title" colspan="10" style="padding:4px 5px;font-weight:bold;">BUKU INVENTARIS ASET DESA ${desaConfig.desa_nama || 'DEMO'}</td></tr>`);
                        rows.push(`<tr><td class="excel-row-num">2</td><td class="excel-cell excel-cell-subtitle" colspan="10" style="text-align:center;">PERIODE ${periodeLabel.toUpperCase()} TAHUN ${tahunVal}</td></tr>`);
                        rows.push(`<tr><td class="excel-row-num">3</td>${cols.map(h => `<td ${hS}>${h}</td>`).join('')}</tr>`);
                        for (let r = 0; r < 3; r++) {
                            const cls = r % 2 === 0 ? 'excel-cell-data' : 'excel-cell-data alt';
                            rows.push(`<tr><td class="excel-row-num">${r+4}</td>${Array(10).fill(`<td class="excel-cell ${cls}">...</td>`).join('')}</tr>`);
                        }
                        rows.push(`<tr><td class="excel-row-num"></td><td class="excel-cell excel-cell-header" colspan="8" style="text-align:center;">JUMLAH</td><td class="excel-cell excel-cell-header" style="text-align:right;">SUM</td><td class="excel-cell"></td></tr>`);
                        tbody.innerHTML = rows.join('');
                    } else {
                        document.getElementById('pdf-filename').textContent = baseName + '.pdf';
                        const pdfContent = document.getElementById('pdf-preview-content');
                        const docDateStr = document.getElementById('date-preview')?.textContent || (desaConfig.nama_lokasi + ', ' + new Date().toLocaleDateString('id-ID', {day:'numeric',month:'long',year:'numeric'}));
                        const periodeTitle = periode === 'semester_1' ? 'SEMESTER I' : (periode === 'semester_2' ? 'SEMESTER II' : 'TAHUNAN');

                        const thStyle = 'border:1px solid #000000 !important;background:#9bc2e6 !important;color:#000000 !important;padding:5px 3px;font-weight:bold;font-size:7.5px;text-align:center;vertical-align:middle;';
                        const tdStyle = (align = 'left') => `border:1px solid #000000 !important;background:#ffffff !important;color:#000000 !important;padding:3.5px 4px;font-size:7.5px;text-align:${align};vertical-align:middle;`;

                        let pdfHtml = `
                            <div style="font-family:Arial,sans-serif;color:#000;background:#ffffff;">
                                <div style="display:flex;justify-content:flex-end;margin-bottom:8px;">
                                    <table style="font-size:8.5px;font-weight:bold;border-collapse:collapse;border:none;">
                                        <tr><td style="padding:1px 6px 1px 0;border:none !important;">PROVINSI</td><td style="padding:1px 0;border:none !important;">: ${desaConfig.provinsi_nama}</td></tr>
                                        <tr><td style="padding:1px 6px 1px 0;border:none !important;">KABUPATEN</td><td style="padding:1px 0;border:none !important;">: ${desaConfig.kabupaten_nama}</td></tr>
                                        <tr><td style="padding:1px 6px 1px 0;border:none !important;">KECAMATAN</td><td style="padding:1px 0;border:none !important;">: ${desaConfig.kecamatan_nama}</td></tr>
                                        <tr><td style="padding:1px 6px 1px 0;border:none !important;">DESA</td><td style="padding:1px 0;border:none !important;">: ${desaConfig.desa_nama}</td></tr>
                                    </table>
                                </div>
                                <div style="text-align:center;font-weight:bold;font-size:12px;margin-bottom:2px;">BUKU INVENTARIS ASET DESA ${desaConfig.nama_lokasi}</div>
                                <div style="text-align:center;font-weight:bold;font-size:10.5px;margin-bottom:12px;">TAHUN ${tahunVal} (${periodeTitle})</div>
                                <div style="overflow-x:auto;margin-bottom:4px;">
                                    <table class="pdf-blue-table" style="width:100%;border-collapse:collapse;font-size:7.5px;">
                                        <thead>
                                            <tr>
                                                <th style="${thStyle};width:24px;">NO</th>
                                                <th style="${thStyle};width:70px;">KODE BARANG</th>
                                                <th style="${thStyle};width:140px;">NAMA BARANG</th>
                                                <th style="${thStyle};width:35px;">NUP</th>
                                                <th style="${thStyle};width:85px;">MERK / TYPE</th>
                                                <th style="${thStyle};width:85px;">BUKTI KEPEMILIKAN</th>
                                                <th style="${thStyle};width:65px;">TAHUN PEROLEHAN</th>
                                                <th style="${thStyle};width:60px;">UKURAN M²</th>
                                                <th style="${thStyle};width:85px;">NILAI (Rp.)</th>
                                                <th style="${thStyle};width:80px;">KET</th>
                                            </tr>
                                        </thead>
                                        <tbody>`;
                        for (let r = 0; r < 3; r++) {
                            pdfHtml += `
                                            <tr>
                                                <td style="${tdStyle('center')}">${r+1}</td>
                                                <td style="${tdStyle('center')}">...</td>
                                                <td style="${tdStyle()}">...</td>
                                                <td style="${tdStyle('center')}">...</td>
                                                <td style="${tdStyle()}">...</td>
                                                <td style="${tdStyle('center')}">...</td>
                                                <td style="${tdStyle('center')}">...</td>
                                                <td style="${tdStyle('center')}">...</td>
                                                <td style="${tdStyle('right')}">...</td>
                                                <td style="${tdStyle()}">...</td>
                                            </tr>`;
                        }
                        if (currentDataCount > 3) {
                            pdfHtml += `
                                            <tr>
                                                <td colspan="10" style="${tdStyle('center')};color:#6b7280;font-style:italic;">... +${currentDataCount - 3} data lainnya</td>
                                            </tr>`;
                        }
                        pdfHtml += `
                                            <tr class="pdf-blue-total" style="background:#9bc2e6 !important;font-weight:bold;">
                                                <td colspan="8" class="pdf-blue-bg" style="border:1px solid #000 !important;padding:4px;text-align:center;background:#9bc2e6 !important;font-weight:bold;">NILAI ASET</td>
                                                <td class="pdf-blue-bg" style="border:1px solid #000 !important;padding:4px;text-align:right;background:#9bc2e6 !important;font-weight:bold;">SUM</td>
                                                <td class="pdf-blue-bg" style="border:1px solid #000 !important;background:#9bc2e6 !important;"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div style="display:flex;justify-content:flex-end;margin-top:20px;font-size:8.5px;">
                                    <div style="width:240px;text-align:center;">
                                        <div style="margin-bottom:3px;">${desaConfig.nama_lokasi}, ${docDateStr}</div>
                                        <div style="font-weight:bold;margin-bottom:38px;">Perbekel ${desaConfig.nama_lokasi || 'Desa'}</div>
                                        <div style="font-weight:bold;text-decoration:underline;">( .................................................. )</div>
                                    </div>
                                </div>
                            </div>`;
                        pdfContent.innerHTML = pdfHtml;
                    }

                    const signerPanel = document.getElementById('preview-signer-panel');
                    if (signerPanel) signerPanel.style.display = 'none';

                    const btnConfirm = document.getElementById('btn-confirm-export');
                    const btnText    = document.getElementById('btn-confirm-export-text');
                    if (btnConfirm) { btnConfirm.disabled = false; btnConfirm.style.opacity = ''; btnConfirm.style.cursor = ''; }
                    if (btnText)    { btnText.textContent = 'Export Buku Inventaris'; }

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
                        const docDateStr = document.getElementById('date-preview')?.textContent || (desaConfig.nama_lokasi + ', ' + new Date().toLocaleDateString('id-ID', {day:'numeric',month:'long',year:'numeric'}));

                        let judulPeriodeLaporan = 'PERTAHUN';
                        let tglSaldoAwal = '1 JANUARI ';
                        let labelAkhir = '31 DESEMBER ' + tahunLaporan;
                        if (periode === 'semester_1') {
                            judulPeriodeLaporan = 'SEMESTER I';
                            tglSaldoAwal = '1 JANUARI ';
                            labelAkhir = '30 JUNI ' + tahunLaporan;
                        } else if (periode === 'semester_2') {
                            judulPeriodeLaporan = 'SEMESTER II';
                            tglSaldoAwal = '1 JULI ';
                            labelAkhir = '31 DESEMBER ' + tahunLaporan;
                        }

                        const thStyle = 'border:1px solid #000000 !important;background:#ffffff !important;color:#000000 !important;padding:3.5px 2px;font-weight:bold;font-size:7px;text-align:center;vertical-align:middle;';
                        const tdStyle = (align = 'left') => `border:1px solid #000000 !important;background:#ffffff !important;color:#000000 !important;padding:3px 4px;font-size:7px;text-align:${align};vertical-align:middle;`;

                        let pdfHtml = `
                            <div style="font-family:Arial,sans-serif;color:#000;background:#ffffff;">
                                <div style="text-align:center;font-weight:bold;font-size:12px;margin-bottom:2px;">LAPORAN ASET DESA ${desaConfig.nama_lokasi} ${judulPeriodeLaporan}</div>
                                <div style="text-align:center;font-weight:bold;font-size:11px;margin-bottom:2px;">RINCIAN PENGELOMPOKAN BARANG</div>
                                <div style="text-align:center;font-weight:bold;font-size:11px;margin-bottom:12px;">TAHUN ANGGARAN ${tahunLaporan}</div>
                                <div style="overflow-x:auto;margin-bottom:4px;">
                                    <table style="width:100%;border-collapse:collapse;font-size:7px;background:#ffffff;">
                                        <thead>
                                            <tr>
                                                <th rowspan="3" style="${thStyle};width:55px;">KODE</th>
                                                <th rowspan="3" style="${thStyle};width:130px;">NAMA BARANG</th>
                                                <th rowspan="3" style="${thStyle};width:38px;">SATUAN</th>
                                                <th colspan="2" rowspan="2" style="${thStyle}">SALDO PER ${tglSaldoAwal}${tahunLaporan}</th>
                                                <th colspan="4" style="${thStyle}">MUTASI</th>
                                                <th colspan="2" rowspan="2" style="${thStyle}">SALDO PER ${labelAkhir}</th>
                                            </tr>
                                            <tr>
                                                <th colspan="2" style="${thStyle}">BERTAMBAH</th>
                                                <th colspan="2" style="${thStyle}">BERKURANG</th>
                                            </tr>
                                            <tr>
                                                <th style="${thStyle};width:38px;">KWANTITAS</th>
                                                <th style="${thStyle};width:65px;">NILAI (RP)</th>
                                                <th style="${thStyle};width:38px;">KWANTITAS</th>
                                                <th style="${thStyle};width:65px;">NILAI (RP)</th>
                                                <th style="${thStyle};width:38px;">KWANTITAS</th>
                                                <th style="${thStyle};width:65px;">NILAI (RP)</th>
                                                <th style="${thStyle};width:38px;">KWANTITAS</th>
                                                <th style="${thStyle};width:65px;">NILAI (RP)</th>
                                            </tr>
                                            <tr>
                                                <th style="${thStyle}">1</th>
                                                <th style="${thStyle}">2</th>
                                                <th style="${thStyle}">3</th>
                                                <th style="${thStyle}">4</th>
                                                <th style="${thStyle}">5</th>
                                                <th style="${thStyle}">6</th>
                                                <th style="${thStyle}">7</th>
                                                <th style="${thStyle}">8</th>
                                                <th style="${thStyle}">9</th>
                                                <th style="${thStyle}">10</th>
                                                <th style="${thStyle}">11</th>
                                            </tr>
                                        </thead>
                                        <tbody>`;
                        for (let r = 0; r < 3; r++) {
                            pdfHtml += `
                                            <tr>
                                                <td style="${tdStyle('center')}">...</td>
                                                <td style="${tdStyle()}">...</td>
                                                <td style="${tdStyle('center')}">...</td>
                                                <td style="${tdStyle('center')}">...</td>
                                                <td style="${tdStyle('right')}">...</td>
                                                <td style="${tdStyle('center')}">...</td>
                                                <td style="${tdStyle('right')}">...</td>
                                                <td style="${tdStyle('center')}">...</td>
                                                <td style="${tdStyle('right')}">...</td>
                                                <td style="${tdStyle('center')}">...</td>
                                                <td style="${tdStyle('right')}">...</td>
                                            </tr>`;
                        }
                        pdfHtml += `
                                            <tr style="font-weight:bold;background:#ffffff;">
                                                <td colspan="3" style="border:1px solid #000 !important;padding:4px;text-align:center;font-weight:bold;">JUMLAH</td>
                                                <td style="border:1px solid #000 !important;"></td>
                                                <td style="border:1px solid #000 !important;padding:4px;text-align:right;font-weight:bold;">SUM</td>
                                                <td style="border:1px solid #000 !important;"></td>
                                                <td style="border:1px solid #000 !important;padding:4px;text-align:right;font-weight:bold;">SUM</td>
                                                <td style="border:1px solid #000 !important;"></td>
                                                <td style="border:1px solid #000 !important;padding:4px;text-align:right;font-weight:bold;">SUM</td>
                                                <td style="border:1px solid #000 !important;"></td>
                                                <td style="border:1px solid #000 !important;padding:4px;text-align:right;font-weight:bold;">SUM</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <table style="width:100%;border-collapse:collapse;border:none;margin-top:20px;font-size:8.5px;">
                                    <tr>
                                        <td style="width:50%;text-align:center;border:none;vertical-align:top;">
                                            <div style="margin-bottom:3px;">Mengetahui,</div>
                                            <div style="font-weight:bold;margin-bottom:38px;">Kepala Urusan Tata Usaha &amp; Umum</div>
                                            <div style="font-weight:bold;text-decoration:underline;">( .................................................. )</div>
                                        </td>
                                        <td style="width:50%;text-align:center;border:none;vertical-align:top;">
                                            <div style="margin-bottom:3px;">${desaConfig.nama_lokasi}, ${docDateStr}</div>
                                            <div style="font-weight:bold;margin-bottom:38px;">Perbekel ${desaConfig.nama_lokasi || 'Desa'}</div>
                                            <div style="font-weight:bold;text-decoration:underline;">( .................................................. )</div>
                                        </td>
                                    </tr>
                                </table>
                            </div>`;
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
                        rows.push(`<tr><td class="excel-row-num">1</td><td class="excel-cell excel-cell-title" colspan="8" style="padding:4px 5px;font-weight:bold;">KARTU INVENTARIS BARANG - ${kibLabel}</td></tr>`);
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
                        const docDateStr = document.getElementById('date-preview')?.textContent || (desaConfig.nama_lokasi + ', ' + new Date().toLocaleDateString('id-ID', {day:'numeric',month:'long',year:'numeric'}));
                        const tahunKibStr = selectedTahun === 'all' ? '(Semua Tahun)' : selectedTahun;

                        const thStyle = 'border:1px solid #000000 !important;background:#ffffff !important;color:#000000 !important;padding:3.5px 2px;font-weight:bold;font-size:7px;text-align:center;vertical-align:middle;';
                        const tdStyle = (align = 'left') => `border:1px solid #000000 !important;background:#ffffff !important;color:#000000 !important;padding:3px 4px;font-size:7px;text-align:${align};vertical-align:middle;`;

                        const renderKibPage = (kType, pageIdx, totalPages) => {
                            let kTitle = 'KARTU INVENTARIS BARANG (KIB) ' + kType;
                            let kSub = 'TANAH';
                            let theadRows = '';
                            let totalCols = 13;
                            let beforeHarga = 11;
                            let afterHarga = 1;

                            if (kType === 'A') {
                                kSub = 'TANAH';
                                totalCols = 13; beforeHarga = 11; afterHarga = 1;
                                theadRows = `
                                    <tr>
                                        <th rowspan="2" style="${thStyle};width:24px;">NO</th>
                                        <th rowspan="2" style="${thStyle};width:140px;">JENIS BARANG / NAMA BARANG</th>
                                        <th colspan="2" style="${thStyle}">NOMOR</th>
                                        <th colspan="4" style="${thStyle}">IDENTITAS</th>
                                        <th colspan="3" style="${thStyle}">STATUS TANAH</th>
                                        <th rowspan="2" style="${thStyle};width:70px;">ASAL-USUL BARANG</th>
                                        <th rowspan="2" style="${thStyle};width:80px;">HARGA<br>Rp</th>
                                        <th rowspan="2" style="${thStyle};width:75px;">KETERANGAN</th>
                                    </tr>
                                    <tr>
                                        <th style="${thStyle};width:65px;">KODE BARANG</th>
                                        <th style="${thStyle};width:35px;">REG.</th>
                                        <th style="${thStyle};width:60px;">LUAS (M2)</th>
                                        <th style="${thStyle};width:55px;">TAHUN PEROLEHAN</th>
                                        <th style="${thStyle};width:85px;">ALAMAT</th>
                                        <th style="${thStyle};width:70px;">PENGGUNAAN</th>
                                        <th style="${thStyle};width:55px;">HAK</th>
                                        <th style="${thStyle};width:65px;">TGL SERTIFIKAT</th>
                                        <th style="${thStyle};width:65px;">NOMOR SERTIFIKAT</th>
                                    </tr>
                                    <tr>
                                        <th style="${thStyle}">1</th>
                                        <th style="${thStyle}">2</th>
                                        <th colspan="2" style="${thStyle}">3</th>
                                        <th colspan="4" style="${thStyle}">4</th>
                                        <th colspan="3" style="${thStyle}">5</th>
                                        <th style="${thStyle}">6</th>
                                        <th style="${thStyle}">7</th>
                                        <th style="${thStyle}">8</th>
                                    </tr>`;
                            } else if (kType === 'B') {
                                kSub = 'PERALATAN DAN MESIN';
                                totalCols = 16; beforeHarga = 14; afterHarga = 1;
                                theadRows = `
                                    <tr>
                                        <th rowspan="2" style="${thStyle};width:24px;">NO</th>
                                        <th rowspan="2" style="${thStyle};width:140px;">JENIS BARANG / NAMA BARANG</th>
                                        <th colspan="2" style="${thStyle}">NOMOR</th>
                                        <th colspan="7" style="${thStyle}">IDENTITAS</th>
                                        <th colspan="2" style="${thStyle}">NOMOR DOKUMEN</th>
                                        <th rowspan="2" style="${thStyle};width:70px;">ASAL-USUL BARANG</th>
                                        <th rowspan="2" style="${thStyle};width:80px;">HARGA<br>Rp</th>
                                        <th rowspan="2" style="${thStyle};width:75px;">KETERANGAN</th>
                                    </tr>
                                    <tr>
                                        <th style="${thStyle};width:65px;">KODE BARANG</th>
                                        <th style="${thStyle};width:35px;">REG.</th>
                                        <th style="${thStyle};width:70px;">MERK / TYPE</th>
                                        <th style="${thStyle};width:55px;">UKURAN / CC</th>
                                        <th style="${thStyle};width:55px;">BAHAN</th>
                                        <th style="${thStyle};width:55px;">TAHUN PEROLEHAN</th>
                                        <th style="${thStyle};width:60px;">NO. PABRIK</th>
                                        <th style="${thStyle};width:60px;">NO. RANGKA</th>
                                        <th style="${thStyle};width:60px;">NO. MESIN</th>
                                        <th style="${thStyle};width:55px;">NO. POLISI</th>
                                        <th style="${thStyle};width:55px;">NO. BPKB</th>
                                    </tr>
                                    <tr>
                                        <th style="${thStyle}">1</th>
                                        <th style="${thStyle}">2</th>
                                        <th colspan="2" style="${thStyle}">3</th>
                                        <th colspan="7" style="${thStyle}">4</th>
                                        <th colspan="2" style="${thStyle}">5</th>
                                        <th style="${thStyle}">6</th>
                                        <th style="${thStyle}">7</th>
                                        <th style="${thStyle}">8</th>
                                    </tr>`;
                            } else if (kType === 'C') {
                                kSub = 'GEDUNG DAN BANGUNAN';
                                totalCols = 15; beforeHarga = 13; afterHarga = 1;
                                theadRows = `
                                    <tr>
                                        <th rowspan="2" style="${thStyle};width:24px;">NO</th>
                                        <th rowspan="2" style="${thStyle};width:140px;">JENIS BARANG / NAMA BARANG</th>
                                        <th colspan="2" style="${thStyle}">NOMOR</th>
                                        <th colspan="6" style="${thStyle}">IDENTITAS</th>
                                        <th rowspan="2" style="${thStyle};width:60px;">STATUS<br>TANAH</th>
                                        <th rowspan="2" style="${thStyle};width:70px;">NOMOR KODE<br>TANAH</th>
                                        <th rowspan="2" style="${thStyle};width:70px;">ASAL-USUL BARANG</th>
                                        <th rowspan="2" style="${thStyle};width:80px;">HARGA<br>Rp</th>
                                    </tr>
                                    <tr>
                                        <th style="${thStyle};width:65px;">KODE BARANG</th>
                                        <th style="${thStyle};width:35px;">REG.</th>
                                        <th style="${thStyle};width:65px;">BERTINGKAT</th>
                                        <th style="${thStyle};width:65px;">BETON</th>
                                        <th style="${thStyle};width:55px;">LUAS (M2)</th>
                                        <th style="${thStyle};width:55px;">TAHUN PEROLEHAN</th>
                                        <th style="${thStyle};width:45px;">BUKU</th>
                                        <th style="${thStyle};width:55px;">TANGGAL</th>
                                        <th style="${thStyle};width:55px;">NOMOR</th>
                                        <th style="${thStyle};width:85px;">ALAMAT</th>
                                    </tr>
                                    <tr>
                                        <th style="${thStyle}">1</th>
                                        <th style="${thStyle}">2</th>
                                        <th colspan="2" style="${thStyle}">3</th>
                                        <th style="${thStyle}">4</th>
                                        <th colspan="2" style="${thStyle}">5</th>
                                        <th colspan="6" style="${thStyle}">6</th>
                                        <th style="${thStyle}">7</th>
                                        <th style="${thStyle}">8</th>
                                        <th style="${thStyle}">9</th>
                                        <th style="${thStyle}">10</th>
                                    </tr>`;
                            } else if (kType === 'D') {
                                kSub = 'JALAN, IRIGASI DAN JARINGAN';
                                totalCols = 18; beforeHarga = 15; afterHarga = 2;
                                theadRows = `
                                    <tr>
                                        <th rowspan="2" style="${thStyle};width:24px;">NO</th>
                                        <th rowspan="2" style="${thStyle};width:140px;">JENIS BARANG / NAMA BARANG</th>
                                        <th colspan="2" style="${thStyle}">NOMOR</th>
                                        <th rowspan="2" style="${thStyle};width:60px;">KONS-<br>TRUKSI</th>
                                        <th colspan="8" style="${thStyle}">IDENTITAS</th>
                                        <th colspan="2" style="${thStyle}">TANAH</th>
                                        <th rowspan="2" style="${thStyle};width:70px;">ASAL-USUL BARANG</th>
                                        <th rowspan="2" style="${thStyle};width:80px;">HARGA<br>Rp</th>
                                        <th rowspan="2" style="${thStyle};width:55px;">KONDISI</th>
                                        <th rowspan="2" style="${thStyle};width:75px;">KETERANGAN</th>
                                    </tr>
                                    <tr>
                                        <th style="${thStyle};width:65px;">KODE BARANG</th>
                                        <th style="${thStyle};width:35px;">REG.</th>
                                        <th style="${thStyle};width:55px;">PANJANG (KM)</th>
                                        <th style="${thStyle};width:55px;">LEBAR (M)</th>
                                        <th style="${thStyle};width:55px;">LUAS (M2)</th>
                                        <th style="${thStyle};width:55px;">TAHUN PEROLEHAN</th>
                                        <th style="${thStyle};width:45px;">BUKU</th>
                                        <th style="${thStyle};width:55px;">TGL</th>
                                        <th style="${thStyle};width:55px;">NOMOR</th>
                                        <th style="${thStyle};width:85px;">ALAMAT</th>
                                        <th style="${thStyle};width:55px;">STATUS</th>
                                        <th style="${thStyle};width:55px;">KODE</th>
                                    </tr>
                                    <tr>
                                        <th style="${thStyle}">1</th>
                                        <th style="${thStyle}">2</th>
                                        <th colspan="2" style="${thStyle}">3</th>
                                        <th style="${thStyle}">4</th>
                                        <th colspan="8" style="${thStyle}">5</th>
                                        <th colspan="2" style="${thStyle}">6</th>
                                        <th style="${thStyle}">7</th>
                                        <th style="${thStyle}">8</th>
                                        <th style="${thStyle}">9</th>
                                        <th style="${thStyle}">10</th>
                                    </tr>`;
                            } else if (kType === 'E') {
                                kSub = 'ASET TETAP LAINNYA';
                                totalCols = 16; beforeHarga = 14; afterHarga = 1;
                                theadRows = `
                                    <tr>
                                        <th rowspan="2" style="${thStyle};width:24px;">NO</th>
                                        <th rowspan="2" style="${thStyle};width:140px;">JENIS BARANG / NAMA BARANG</th>
                                        <th colspan="2" style="${thStyle}">NOMOR</th>
                                        <th colspan="2" style="${thStyle}">BUKU / PERPUSTAKAAN</th>
                                        <th colspan="3" style="${thStyle}">BARANG BERCORAK KESENIAN/KEBUDAYAAN</th>
                                        <th colspan="2" style="${thStyle}">HEWAN/TERNAK DAN TUMBUHAN</th>
                                        <th rowspan="2" style="${thStyle};width:55px;">KONDISI</th>
                                        <th colspan="2" style="${thStyle}">TAHUN PEROLEHAN/BUKU</th>
                                        <th rowspan="2" style="${thStyle};width:70px;">ASAL-USUL BARANG</th>
                                        <th rowspan="2" style="${thStyle};width:80px;">HARGA<br>Rp</th>
                                        <th rowspan="2" style="${thStyle};width:75px;">KETERANGAN</th>
                                    </tr>
                                    <tr>
                                        <th style="${thStyle};width:65px;">KODE BARANG</th>
                                        <th style="${thStyle};width:35px;">REG.</th>
                                        <th style="${thStyle};width:75px;">JUDUL/PENCIPTA</th>
                                        <th style="${thStyle};width:65px;">SPESIFIKASI</th>
                                        <th style="${thStyle};width:65px;">ASAL DAERAH</th>
                                        <th style="${thStyle};width:65px;">PENCIPTA</th>
                                        <th style="${thStyle};width:55px;">BAHAN</th>
                                        <th style="${thStyle};width:55px;">JENIS</th>
                                        <th style="${thStyle};width:55px;">UKURAN</th>
                                        <th style="${thStyle};width:55px;">PEROLEHAN</th>
                                        <th style="${thStyle};width:45px;">BUKU</th>
                                    </tr>
                                    <tr>
                                        <th style="${thStyle}">1</th>
                                        <th style="${thStyle}">2</th>
                                        <th colspan="2" style="${thStyle}">3</th>
                                        <th colspan="2" style="${thStyle}">4</th>
                                        <th colspan="3" style="${thStyle}">5</th>
                                        <th colspan="2" style="${thStyle}">6</th>
                                        <th style="${thStyle}">7</th>
                                        <th colspan="2" style="${thStyle}">8</th>
                                        <th style="${thStyle}">9</th>
                                        <th style="${thStyle}">10</th>
                                        <th style="${thStyle}">11</th>
                                    </tr>`;
                            } else {
                                kSub = 'KONSTRUKSI DALAM PENGERJAAN';
                                totalCols = 12; beforeHarga = 10; afterHarga = 1;
                                theadRows = `
                                    <tr>
                                        <th rowspan="2" style="${thStyle};width:24px;">NO</th>
                                        <th rowspan="2" style="${thStyle};width:140px;">JENIS BARANG / NAMA BARANG</th>
                                        <th colspan="2" style="${thStyle}">NOMOR</th>
                                        <th rowspan="2" style="${thStyle};width:60px;">KONDISI<br>BANGUNAN<br>(B,RR,RB)</th>
                                        <th colspan="3" style="${thStyle}">IDENTITAS</th>
                                        <th rowspan="2" style="${thStyle};width:70px;">BIDANG</th>
                                        <th rowspan="2" style="${thStyle};width:70px;">SUMBER<br>DANA</th>
                                        <th rowspan="2" style="${thStyle};width:70px;">ASAL-USUL BARANG</th>
                                        <th rowspan="2" style="${thStyle};width:80px;">HARGA<br>Rp</th>
                                        <th rowspan="2" style="${thStyle};width:75px;">KETERANGAN</th>
                                    </tr>
                                    <tr>
                                        <th style="${thStyle};width:65px;">KODE BARANG</th>
                                        <th style="${thStyle};width:35px;">REG.</th>
                                        <th style="${thStyle};width:55px;">TAHUN BUKU</th>
                                        <th style="${thStyle};width:65px;">TGL DOKUMEN</th>
                                        <th style="${thStyle};width:75px;">NO DOKUMEN</th>
                                    </tr>
                                    <tr>
                                        <th style="${thStyle}">1</th>
                                        <th style="${thStyle}">2</th>
                                        <th colspan="2" style="${thStyle}">3</th>
                                        <th style="${thStyle}">4</th>
                                        <th colspan="3" style="${thStyle}">5</th>
                                        <th style="${thStyle}">6</th>
                                        <th style="${thStyle}">7</th>
                                        <th style="${thStyle}">8</th>
                                        <th style="${thStyle}">9</th>
                                        <th style="${thStyle}">10</th>
                                    </tr>`;
                            }

                            let pageRows = '';
                            for (let r = 0; r < 3; r++) {
                                pageRows += `
                                    <tr>
                                        <td style="${tdStyle('center')}">${r+1}</td>
                                        <td style="${tdStyle()}">...</td>
                                        <td style="${tdStyle('center')}">...</td>
                                        <td style="${tdStyle('center')}">...</td>
                                        ${Array(totalCols - 5).fill(`<td style="${tdStyle('center')}">...</td>`).join('')}
                                        <td style="${tdStyle('right')}">...</td>
                                        ${afterHarga > 0 ? Array(afterHarga).fill(`<td style="${tdStyle()}">...</td>`).join('') : ''}
                                    </tr>`;
                            }

                            return `
                                <div style="font-family:Arial,sans-serif;color:#000;background:#ffffff;padding-bottom:${pageIdx < totalPages - 1 ? '24px' : '0'};margin-bottom:${pageIdx < totalPages - 1 ? '24px' : '0'};border-bottom:${pageIdx < totalPages - 1 ? '2px dashed #d1d5db' : 'none'};">
                                    <div style="text-align:center;font-weight:bold;font-size:12px;margin-bottom:2px;">${kTitle}</div>
                                    <div style="text-align:center;font-weight:bold;font-size:11px;margin-bottom:2px;">${kSub}</div>
                                    <div style="text-align:center;font-size:9.5px;margin-bottom:12px;">Periode Tanggal, 31 Desember ${tahunKibStr}</div>
                                    <div style="font-family:'Courier New',Courier,monospace;font-size:8.5px;line-height:1.45;margin-bottom:12px;">
                                        <div style="display:flex;"><span style="width:130px;flex-shrink:0;">Provinsi</span><span>: ${desaConfig.provinsi_kode}. ${desaConfig.provinsi_nama}</span></div>
                                        <div style="display:flex;"><span style="width:130px;flex-shrink:0;">Kab./Kota</span><span>: ${desaConfig.kabupaten_kode}. ${desaConfig.kabupaten_nama}</span></div>
                                        <div style="display:flex;"><span style="width:130px;flex-shrink:0;">Kecamatan</span><span>: ${desaConfig.kecamatan_kode}. ${desaConfig.kecamatan_nama}</span></div>
                                        <div style="display:flex;"><span style="width:130px;flex-shrink:0;">Desa</span><span>: ${desaConfig.kode_lokasi}. ${desaConfig.desa_nama}</span></div>
                                        <div style="display:flex;"><span style="width:130px;flex-shrink:0;">UPB</span><span>: ${desaConfig.upb}</span></div>
                                        <div style="display:flex;"><span style="width:130px;flex-shrink:0;">No. Kode Lokasi</span><span>: ${desaConfig.kode_lokasi}.</span></div>
                                    </div>
                                    <div style="overflow-x:auto;margin-bottom:4px;">
                                        <table style="width:100%;border-collapse:collapse;font-size:7px;background:#ffffff;">
                                            <thead>${theadRows}</thead>
                                            <tbody>
                                                ${pageRows}
                                                <tr style="font-weight:bold;background:#ffffff;">
                                                    <td colspan="${beforeHarga}" style="border:1px solid #000 !important;padding:4px;text-align:center;font-weight:bold;">JUMLAH HARGA</td>
                                                    <td style="border:1px solid #000 !important;padding:4px;text-align:right;font-weight:bold;">SUM</td>
                                                    ${afterHarga > 0 ? Array(afterHarga).fill('<td style="border:1px solid #000 !important;"></td>').join('') : ''}
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <table style="width:100%;border-collapse:collapse;border:none;margin-top:20px;font-size:8.5px;">
                                        <tr>
                                            <td style="width:50%;text-align:center;border:none;vertical-align:top;">
                                                <div style="margin-bottom:3px;">MENGETAHUI</div>
                                                <div style="font-weight:bold;margin-bottom:38px;">${customPejabat.length >= 1 ? (customPejabat[0].jabatan || 'SEKRETARIS DESA').toUpperCase() : 'SEKRETARIS DESA'}</div>
                                                <div style="font-weight:bold;text-decoration:underline;">( ${customPejabat.length >= 1 ? customPejabat[0].nama : '..................................................'} )</div>
                                            </td>
                                            <td style="width:50%;text-align:center;border:none;vertical-align:top;">
                                                <div style="margin-bottom:3px;">${desaConfig.nama_lokasi}, ${docDateStr}</div>
                                                <div style="font-weight:bold;margin-bottom:38px;">${customPejabat.length >= 2 ? (customPejabat[1].jabatan || 'PETUGAS / PENGURUS BARANG').toUpperCase() : 'PETUGAS / PENGURUS BARANG MILIK DESA'}</div>
                                                <div style="font-weight:bold;text-decoration:underline;">( ${customPejabat.length >= 2 ? customPejabat[1].nama : '..................................................'} )</div>
                                            </td>
                                        </tr>
                                    </table>
                                    ${totalPages > 1 ? `
                                    <div style="margin-top:12px;padding-top:6px;border-top:1px solid #111;font-size:8.5px;display:flex;justify-content:space-between;">
                                        <span>Kartu Inventaris Barang (KIB) - Semua Jenis</span>
                                        <span>Halaman : ${pageIdx + 1} dari ${totalPages}</span>
                                    </div>` : ''}
                                </div>`;
                        };

                        if (selectedKib === 'all') {
                            const kibTypes = ['A', 'B', 'C', 'D', 'E', 'F'];
                            let allHtml = '';
                            kibTypes.forEach((kt, i) => {
                                allHtml += renderKibPage(kt, i, kibTypes.length);
                            });
                            pdfContent.innerHTML = allHtml;
                        } else {
                            pdfContent.innerHTML = renderKibPage(selectedKib.toUpperCase(), 0, 1);
                        }
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

                const buildJenisPdfPage = (targetJenis, pageIdx = 0, totalPages = 1) => {
                    const docDateStr = document.getElementById('date-preview')?.textContent || (desaConfig.nama_lokasi + ', ' + new Date().toLocaleDateString('id-ID', {day:'numeric',month:'long',year:'numeric'}));
                    const tahunPeriodeStr = selectedTahun === 'all' ? 'Periode Tanggal, 31 Desember (Semua Tahun)' : ('Periode Tanggal, 31 Desember ' + selectedTahun);

                    const thStyle = 'border:1px solid #000000 !important;background:#ffffff !important;color:#000000 !important;padding:3.5px 2px;font-weight:bold;font-size:7px;text-align:center;vertical-align:middle;';
                    const tdStyle = (align = 'left') => `border:1px solid #000000 !important;background:#ffffff !important;color:#000000 !important;padding:3px 4px;font-size:7px;text-align:${align};vertical-align:middle;`;

                    let theadHtml = '';
                    let totalCols = 1;
                    let dataRowCells = '';

                    if (targetJenis.is_virtual) {
                        totalCols = 11;
                        theadHtml = `
                            <tr>
                                <th rowspan="2" style="${thStyle};width:24px;">NO</th>
                                <th rowspan="2" style="${thStyle};width:90px;">JENIS BARANG</th>
                                <th rowspan="2" style="${thStyle};width:65px;">KODE BARANG</th>
                                <th rowspan="2" style="${thStyle};width:130px;">NAMA / IDENTITAS BARANG</th>
                                <th colspan="3" style="${thStyle}">ASAL USUL BARANG</th>
                                <th rowspan="2" style="${thStyle};width:55px;">TAHUN PEROLEHAN</th>
                                <th rowspan="2" style="${thStyle};width:80px;">NILAI ASET (Rp)</th>
                                <th rowspan="2" style="${thStyle};width:70px;">PENGGUNA</th>
                                <th rowspan="2" style="${thStyle};width:65px;">KET.</th>
                            </tr>
                            <tr>
                                <th style="${thStyle};width:60px;">APBDES</th>
                                <th style="${thStyle};width:75px;">PEROLEHAN LAIN</th>
                                <th style="${thStyle};width:75px;">KEKAYAAN ASLI</th>
                            </tr>
                            <tr>
                                <th style="${thStyle}">1</th>
                                <th style="${thStyle}">2</th>
                                <th style="${thStyle}">3</th>
                                <th style="${thStyle}">4</th>
                                <th colspan="3" style="${thStyle}">5</th>
                                <th style="${thStyle}">6</th>
                                <th style="${thStyle}">7</th>
                                <th style="${thStyle}">8</th>
                                <th style="${thStyle}">9</th>
                            </tr>`;
                        dataRowCells = `
                            <td style="${tdStyle('center')}">{NUM}</td>
                            <td style="${tdStyle()}">...</td>
                            <td style="${tdStyle('center')}">...</td>
                            <td style="${tdStyle()}">...</td>
                            <td style="${tdStyle('center')}">...</td>
                            <td style="${tdStyle('center')}">...</td>
                            <td style="${tdStyle('center')}">...</td>
                            <td style="${tdStyle('center')}">...</td>
                            <td style="${tdStyle('right')}">...</td>
                            <td style="${tdStyle()}">...</td>
                            <td style="${tdStyle()}">...</td>`;
                    } else {
                        const fields = (targetJenis.fields || []).filter(f => f.fixed !== 'jenis_barang');
                        const hasKondisi = fields.some(f => f.type === 'fixed' && f.fixed === 'kondisi');

                        let row1 = `<tr><th rowspan="${hasKondisi ? '2' : '1'}" style="${thStyle};width:24px;">NO</th>`;
                        let row2 = '<tr>';
                        let row3 = `<tr><th style="${thStyle}">1</th>`;
                        let cNum = 2;
                        let colCount = 1;

                        fields.forEach((f) => {
                            let label = f.label;
                            if (f.fixed === 'nama_barang') label = 'JENIS BARANG / NAMA BARANG';

                            if (f.type === 'fixed' && f.fixed === 'kondisi') {
                                row1 += `<th colspan="3" style="${thStyle}">KONDISI BARANG</th>`;
                                row2 += `<th style="${thStyle};width:40px;">BAIK</th><th style="${thStyle};width:45px;">RUSAK RINGAN</th><th style="${thStyle};width:45px;">RUSAK BERAT</th>`;
                                row3 += `<th colspan="3" style="${thStyle}">${cNum++}</th>`;
                                colCount += 3;
                            } else {
                                row1 += `<th rowspan="${hasKondisi ? '2' : '1'}" style="${thStyle}">${label.toUpperCase()}</th>`;
                                row3 += `<th style="${thStyle}">${cNum++}</th>`;
                                colCount += 1;
                            }
                        });

                        row1 += '</tr>';
                        row2 += '</tr>';
                        row3 += '</tr>';

                        theadHtml = hasKondisi ? (row1 + row2 + row3) : (row1 + row3);
                        totalCols = colCount;

                        dataRowCells = `<td style="${tdStyle('center')}">{NUM}</td>`;
                        fields.forEach(f => {
                            if (f.type === 'fixed' && f.fixed === 'kondisi') {
                                dataRowCells += `<td style="${tdStyle('center')}">X</td><td style="${tdStyle('center')}"></td><td style="${tdStyle('center')}"></td>`;
                            } else if ((f.label || '').toLowerCase().includes('nilai') || (f.label || '').toLowerCase().includes('harga')) {
                                dataRowCells += `<td style="${tdStyle('right')}">...</td>`;
                            } else if (['kode_barang', 'nup', 'tahun_perolehan'].includes(f.fixed) || (f.label || '').toLowerCase().includes('tahun') || (f.label || '').toLowerCase().includes('kode')) {
                                dataRowCells += `<td style="${tdStyle('center')}">...</td>`;
                            } else {
                                dataRowCells += `<td style="${tdStyle()}">...</td>`;
                            }
                        });
                    }

                    let dataRowsHtml = '';
                    const numPlaceholders = (totalPages === 1 && currentDataCount > 0) ? Math.min(currentDataCount, 3) : 3;
                    for (let r = 0; r < numPlaceholders; r++) {
                        dataRowsHtml += `<tr>${dataRowCells.replace('{NUM}', r + 1)}</tr>`;
                    }
                    if (totalPages === 1 && currentDataCount > 3) {
                        dataRowsHtml += `<tr><td colspan="${totalCols}" style="${tdStyle('center')};color:#6b7280;font-style:italic;">... +${currentDataCount - 3} data lainnya</td></tr>`;
                    } else if (totalPages === 1 && currentDataCount === 0) {
                        dataRowsHtml += `<tr><td colspan="${totalCols}" style="${tdStyle('center')};color:#9ca3af;font-style:italic;">Tidak ada data ${selectedTahun === 'all' ? 'dalam sistem' : 'untuk tahun ' + selectedTahun}</td></tr>`;
                    }

                    const signers = getActiveSignerList();
                    const signerHtml = signers.length > 0 ? `
                        <table style="width:100%;border-collapse:collapse;border:none;margin-top:14px;font-size:8.5px;">
                            <tr>
                                <td style="width:55%;border:none;vertical-align:top;"></td>
                                <td style="width:45%;border:none;vertical-align:top;padding-left:12px;">
                                    <div style="font-size:10.5px;font-weight:bold;text-align:center;margin-bottom:10px;">TIM INVENTARISASI</div>
                                    ${signers.map((p, i) => `
                                        <div style="display:flex;align-items:flex-end;gap:4px;margin-bottom:14px;">
                                            <span style="font-size:9.5px;white-space:nowrap;flex-shrink:0;">${i+1}. ${p.nama || p.nama_pejabat || ''}</span>
                                            <span style="font-size:10.5px;flex-shrink:0;margin-left:4px;">&nbsp;(</span>
                                            <span style="flex:1;border-bottom:1px solid #111;min-height:16px;display:inline-block;"></span>
                                            <span style="font-size:10.5px;flex-shrink:0;">)</span>
                                        </div>
                                    `).join('')}
                                </td>
                            </tr>
                        </table>` : '';

                    return `
                        <div style="font-family:Arial,sans-serif;color:#000;background:#ffffff;padding-bottom:${pageIdx < totalPages - 1 ? '24px' : '0'};margin-bottom:${pageIdx < totalPages - 1 ? '24px' : '0'};border-bottom:${pageIdx < totalPages - 1 ? '2px dashed #d1d5db' : 'none'};">
                            <div style="text-align:center;font-weight:bold;font-size:12px;margin-bottom:2px;">LAPORAN HASIL INVENTARIS ASET DESA</div>
                            <div style="text-align:center;font-weight:bold;font-size:11px;margin-bottom:2px;">BERUPA ${targetJenis.nama.toUpperCase()}</div>
                            <div style="text-align:center;font-size:9.5px;margin-bottom:12px;">${tahunPeriodeStr}</div>
                            
                            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:12px;font-size:8.5px;">
                                <div style="font-family:'Courier New',Courier,monospace;line-height:1.45;">
                                    <div style="display:flex;"><span style="width:130px;flex-shrink:0;">Provinsi</span><span>: ${desaConfig.provinsi_kode}. ${desaConfig.provinsi_nama}</span></div>
                                    <div style="display:flex;"><span style="width:130px;flex-shrink:0;">Kab./Kota</span><span>: ${desaConfig.kabupaten_kode}. ${desaConfig.kabupaten_nama}</span></div>
                                    <div style="display:flex;"><span style="width:130px;flex-shrink:0;">Kecamatan</span><span>: ${desaConfig.kecamatan_kode}. ${desaConfig.kecamatan_nama}</span></div>
                                    <div style="display:flex;"><span style="width:130px;flex-shrink:0;">Desa</span><span>: ${desaConfig.kode_lokasi}. ${desaConfig.desa_nama}</span></div>
                                    <div style="display:flex;"><span style="width:130px;flex-shrink:0;">UPB</span><span>: ${desaConfig.upb}</span></div>
                                    <div style="display:flex;"><span style="width:130px;flex-shrink:0;">No. Kode Lokasi</span><span>: ${desaConfig.kode_lokasi}.</span></div>
                                </div>
                                <div style="font-weight:bold;line-height:1.45;">
                                    <table style="border:none;border-collapse:collapse;font-size:8.5px;">
                                        <tr><td style="border:none;padding:1px 4px 1px 0;">Lampiran</td><td style="border:none;padding:1px 0;">: -</td></tr>
                                        <tr><td colspan="2" style="border:none;padding:2px 0 1px;">Berita Acara Inventarisasi Aset Desa</td></tr>
                                        <tr><td style="border:none;padding:1px 4px 1px 0;">Nomor</td><td style="border:none;padding:1px 0;">: -</td></tr>
                                        <tr><td style="border:none;padding:1px 4px 1px 0;">Tanggal</td><td style="border:none;padding:1px 0;">: ${docDateStr}</td></tr>
                                    </table>
                                </div>
                            </div>

                            <div style="overflow-x:auto;margin-bottom:4px;">
                                <table style="width:100%;border-collapse:collapse;font-size:7px;background:#ffffff;">
                                    <thead>${theadHtml}</thead>
                                    <tbody>
                                        ${dataRowsHtml}
                                        <tr style="font-weight:bold;background:#ffffff;">
                                            <td colspan="${totalCols - 1}" style="border:1px solid #000 !important;padding:4px;text-align:center;font-weight:bold;">JUMLAH HARGA</td>
                                            <td style="border:1px solid #000 !important;padding:4px;text-align:right;font-weight:bold;">SUM</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            ${signerHtml}

                            ${totalPages > 1 ? `
                            <div style="margin-top:12px;padding-top:6px;border-top:1px solid #111;font-size:8.5px;display:flex;justify-content:space-between;">
                                <span>Laporan Hasil Inventaris Aset Desa - Semua Jenis</span>
                                <span>Halaman : ${pageIdx + 1} dari ${totalPages}</span>
                            </div>` : ''}
                        </div>`;
                };

                if (selectedJenisId === 'all') {
                    const baseName = 'inventaris_semua_jenis_' + (selectedTahun === 'all' ? 'semua_tahun' : selectedTahun);
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

                        // 3 placeholder data rows
                        for (let r = 0; r < 3; r++) {
                            const cls = r % 2 === 0 ? 'excel-cell-data' : 'excel-cell-data alt';
                            rows.push(`<tr><td class="excel-row-num">${r+5}</td><td class="excel-cell ${cls}" style="text-align:center;">${r+1}</td>${cols.slice(1).map(() => `<td class="excel-cell ${cls}">...</td>`).join('')}</tr>`);
                        }
                        rows.push(`<tr><td class="excel-row-num" style="color:#9ca3af;">...</td><td class="excel-cell" colspan="${span}" style="text-align:center;color:#9ca3af;font-style:italic;padding:4px;">(Setiap jenis inventaris akan berada di Sheet terpisah)</td></tr>`);

                        // Jumlah row
                        const nilaiIdxFirst = cols.findIndex((c, i) => i > 0 && c.toLowerCase().includes('nilai'));
                        let jumlahCells = '';
                        if (nilaiIdxFirst > 0) {
                            const beforeCount = nilaiIdxFirst;
                            const afterCount  = cols.length - nilaiIdxFirst - 1;
                            jumlahCells = `<td class="excel-cell" colspan="${beforeCount}" style="font-weight:700;font-size:10px;">Jumlah ${'-'.repeat(12)}</td>
                                <td class="excel-cell" style="font-weight:700;text-align:right;font-size:10px;color:#217346;">SUM</td>
                                ${afterCount > 0 ? `<td class="excel-cell" colspan="${afterCount}"></td>` : ''}`;
                        } else {
                            jumlahCells = `<td class="excel-cell" colspan="${span}" style="font-weight:700;font-size:10px;">Jumlah ${'-'.repeat(20)}</td>`;
                        }
                        rows.push(`<tr><td class="excel-row-num">9</td>${jumlahCells}</tr>`);
                        rows.push(`<tr><td style="width:32px;min-width:32px;background:#f2f2f2;border-right:1px solid #d0d5dd;"></td><td colspan="${span}" style="border:none;height:6px;"></td></tr>`);

                        // Signers preview
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
                    } else {
                        document.getElementById('pdf-filename').textContent = baseName + '.pdf';
                        const pdfContent = document.getElementById('pdf-preview-content');

                        // PDF preview "Semua Jenis": render semua page secara vertikal tanpa navigation tabs
                        let allPagesHtml = '';
                        jenisList.forEach((jItem, pageIdx) => {
                            allPagesHtml += buildJenisPdfPage(jItem, pageIdx, jenisList.length);
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
                const baseName = jenis.nama.toLowerCase().replace(/\s+/g, '_') + '_' + (selectedTahun === 'all' ? 'semua_tahun' : selectedTahun);
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
                        rows[rows.length - 1] = `<tr><td class="excel-row-num">5</td><td class="excel-cell" colspan="${span}" style="text-align:center;color:#9ca3af;font-style:italic;padding:6px;">Tidak ada data ${selectedTahun === 'all' ? 'dalam sistem' : 'untuk tahun ' + selectedTahun}</td></tr>`;
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
                    pdfContent.innerHTML = buildJenisPdfPage(jenis, 0, 1);
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

                const tahunLabelPrompt = tahun === 'all' ? 'Semua Tahun' : 'tahun ' + tahun;
                if (!skipPrompt && !confirm(`Export semua jenis inventaris (${tahunLabelPrompt}) ke 1 file ${format.toUpperCase()}?

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
                    let json = null;
                    try { json = await response.json(); } catch(e) {}
                    if (!response.ok || !json || json.success === false) {
                        throw new Error((json && json.message) || ('Export semua jenis gagal (Status ' + response.status + ')'));
                    }
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
                // Handle tab Buku Inventaris Aset Desa
                if (activeExportTab === 'buku_inventaris_aset') {
                    const tahunVal = selectedTahun;
                    const periode  = document.getElementById('buku_inventaris_periode_hidden')?.value || 'tahunan';
                    const format   = document.querySelector('input[name="export-format"]:checked').value;
                    const sig      = document.querySelector('input[name="signature-option"]:checked').value;

                    if (!tahunVal) {
                        showToast('Pilih tahun laporan terlebih dahulu', 'warning');
                        return;
                    }

                    const btnConfirm = document.getElementById('btn-confirm-export');
                    const btnText    = document.getElementById('btn-confirm-export-text');
                    if (btnConfirm) { btnConfirm.disabled = true; }
                    if (btnText)    { btnText.textContent = 'Mengexport...'; }

                    const formData = new FormData();
                    formData.append('tahun', tahunVal);
                    formData.append('periode', periode);
                    formData.append('format', format);
                    formData.append('paper_size', getSelectedPaperSize());
                    formData.append('signature_option', sig);
                    formData.append('tanggal_dokumen', document.getElementById('doc-tanggal').value || '');

                    if (typeof AppLoader !== 'undefined') AppLoader.show();
                    fetch('{{ route("export-data.store-buku-inventaris-aset") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                        }
                    })
                    .then(async response => {
                        let json = null;
                        try { json = await response.json(); } catch(e) {}
                        if (!response.ok || !json || json.success === false) {
                            throw new Error((json && json.message) || ('Export Buku Inventaris Aset gagal (Status ' + response.status + ')'));
                        }
                        return json;
                    })
                    .then(data => {
                        closePreviewModal();
                        showToast('Export Buku Inventaris Aset berhasil! File sedang diunduh...', 'success');
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
                        console.error('Export Buku Inventaris Aset error:', error);
                        showToast(error.message || 'Terjadi kesalahan saat export buku inventaris aset', 'error');
                    })
                    .finally(() => {
                        if (typeof AppLoader !== 'undefined') AppLoader.hide();
                        if (btnConfirm) { btnConfirm.disabled = false; }
                        if (btnText)    { btnText.textContent = 'Export Sekarang'; }
                    });

                    return;
                }

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
                        let json = null;
                        try { json = await response.json(); } catch(e) {}
                        if (!response.ok || !json || json.success === false) {
                            throw new Error((json && json.message) || ('Export Laporan Aset gagal (Status ' + response.status + ')'));
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

                    // Pejabat override untuk KIB: bisa 2 penandatangan (kiri = Mengetahui, kanan = Petugas Barang)
                    if (customPejabat.length >= 2) {
                        if (customPejabat[0]) formData.append('pejabat_kiri', customPejabat[0].id);
                        if (customPejabat[1]) formData.append('pejabat_kanan', customPejabat[1].id);
                    } else if (customPejabat.length === 1 && customPejabat[0]) {
                        formData.append('pejabat_kiri', customPejabat[0].id);
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
                        let json = null;
                        try { json = await response.json(); } catch(e) {}
                        if (!response.ok || !json || json.success === false) {
                            throw new Error((json && json.message) || ('Export KIB gagal (Status ' + response.status + ')'));
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
                    // Format KIR: standard (Permendagri) atau custom (Format Khusus 3 TTD) — hanya berlaku untuk PDF
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
                        let json = null;
                        try { json = await response.json(); } catch(e) {}
                        if (json && json.no_sekretaris) {
                            // Modal peringatan khusus: belum ada Sekretaris Desa
                            closePreviewModal();
                            showSekretarisWarningModal(json.message);
                            return;
                        }
                        if (!response.ok || !json || json.success === false) {
                            throw new Error((json && json.message) || ('Export gagal (Status ' + response.status + ')'));
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
                    showToast('Tidak ada data untuk diexport ' + (tahun === 'all' ? 'dalam sistem' : 'pada tahun ' + tahun), 'warning');
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
                    let json = null;
                    try { json = await response.json(); } catch(e) {}
                    if (!response.ok || !json || json.success === false) {
                        throw new Error((json && json.message) || ('Export gagal (Status ' + response.status + ')'));
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
                                <button onclick="document.getElementById('sekretaris-warning-modal').remove()" style="padding:9px 18px;border:1px solid #d1d5db;background:#fff;color:#374151;border-radius: var(--radius-full);font-size:13px;font-weight:600;cursor:pointer;">Tutup</button>
                                <a href="{{ route('users.index') }}" style="padding:9px 18px;background:#1f3a2a;color:#fff;border-radius: var(--radius-full);font-size:13px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
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
                renderBukuInventarisDropdown();
                selectExportTypeCard('buku_inventaris_aset');
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
            " onmouseenter="this.parentNode.querySelector('.widget-tooltip').style.opacity = '1'" onmouseleave="this.parentNode.querySelector('.widget-tooltip').style.opacity = '0'" onmouseover="this.style.transform = 'scale(1.05)';" onmouseout="this.style.transform = 'scale(1)';">
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
        @include('layouts.modals')
    </body>
</html>

