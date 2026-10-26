<!DOCTYPE html>
<html lang="en">
<head>
    @include('layouts.head-page-meta', ['title' => 'Penghapusan Aset - Sistem Manajemen Aset Desa'])
    @include('layouts.head-css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        /* Stabilkan viewport scrollbar agar lebar konten kedua tab 100% konsisten & sejajar */
        html {
            overflow-y: scroll;
            scrollbar-gutter: stable;
        }

        /* Year Only Picker Styling (Matching Manajemen Aset Index & Create) */
        .flatpickr-calendar.year-only-picker {
            width: 250px !important;
            background: var(--color-surface, #f8faf7);
            border-radius: var(--radius-md, 12px);
            box-shadow: none;
            border: 1.5px solid var(--color-border, #cfdcc8);
            overflow: hidden;
            z-index: 1060 !important;
        }
        .flatpickr-calendar.year-only-picker .flatpickr-innerContainer,
        .flatpickr-calendar.year-only-picker .flatpickr-weekdays,
        .flatpickr-calendar.year-only-picker .flatpickr-days,
        .flatpickr-calendar.year-only-picker .flatpickr-monthDropdown-months,
        .flatpickr-calendar.year-only-picker .flatpickr-prev-month,
        .flatpickr-calendar.year-only-picker .flatpickr-next-month,
        .flatpickr-calendar.year-only-picker .flatpickr-months {
            display: none !important;
        }
        .flatpickr-calendar.year-only-picker .year-picker-panel {
            padding: 12px;
        }
        .flatpickr-calendar.year-only-picker .year-picker-all-btn {
            width: 100%;
            padding: 7px 10px;
            border: 1px solid var(--color-primary);
            border-radius: var(--radius-full);
            background: var(--color-primary-tint);
            color: var(--color-primary);
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: all 0.15s;
            margin-bottom: 10px;
        }
        .flatpickr-calendar.year-only-picker .year-picker-all-btn:hover {
            background: var(--color-row-hover);
        }
        .flatpickr-calendar.year-only-picker .year-picker-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .flatpickr-calendar.year-only-picker .year-picker-range {
            font-size: 13px;
            font-weight: 700;
            color: var(--color-primary);
        }
        .flatpickr-calendar.year-only-picker .year-picker-nav {
            width: 26px;
            height: 26px;
            border-radius: var(--radius-sm, 6px);
            border: none;
            background: transparent;
            color: var(--color-text);
            font-size: 16px;
            line-height: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.15s;
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
            gap: 6px;
        }
        .flatpickr-calendar.year-only-picker .year-picker-option {
            border: 1px solid var(--color-border);
            border-radius: var(--radius-sm);
            background: var(--color-surface);
            color: var(--color-text);
            font-size: 12px;
            font-weight: 600;
            padding: 8px 0;
            cursor: pointer;
            transition: all 0.15s;
            text-align: center;
        }
        .flatpickr-calendar.year-only-picker .year-picker-option:hover {
            background: var(--color-row-hover);
            border-color: var(--color-primary);
            color: var(--color-primary);
        }
        .flatpickr-calendar.year-only-picker .year-picker-option.active {
            background: var(--color-primary);
            border-color: var(--color-primary);
            color: var(--color-text-on-primary);
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .page-header-card {
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-lg);
            padding: 24px 28px;
            margin-bottom: 24px;
            position: relative;
        }
        .page-header-card h1 { color: var(--color-text); font-size: 22px; font-weight: 700; margin: 0 0 6px; }
        .page-header-card p  { color: var(--color-text-muted); font-size: 13.5px; margin: 0; }
        
        /* Stats Grid (4 Standard KPI Cards) */
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; }
        @media(max-width:1200px){ .stats-grid { grid-template-columns: repeat(2, 1fr); } }
        @media(max-width:640px){ .stats-grid { grid-template-columns: 1fr; } }
        
        .stat-card{
            background: var(--color-surface);
            border-radius: var(--radius-lg);
            padding: 16px 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            border: 1px solid var(--color-border);
            transition: border-color 0.2s ease;
            position: relative;
        }
        .stat-card:hover { border-color: #b8c9b3; }
        .stat-card-label { font-size: 11px; font-weight: 700; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px; }
        .stat-card-value { font-size: 22px; font-weight: 700; color: var(--color-text); line-height: 1.2; font-variant-numeric: tabular-nums; }
        .kpi-trend-wrapper { display: flex; align-items: center; gap: 6px; margin-top: 6px; }
        .kpi-trend-pill { display: inline-flex; align-items: center; gap: 3px; padding: 0 !important; background: transparent !important; border-radius: 0 !important; font-size: 11.5px; font-weight: 600; line-height: 1.3; }
        .kpi-trend-pill.up { background: transparent !important; color: var(--color-primary); }
        .kpi-trend-pill.down { background: transparent !important; color: var(--color-danger); }
        .kpi-trend-pill.neutral { background: transparent !important; color: var(--color-text-muted); }
        .kpi-trend-label { font-size: 11px; color: var(--color-text-muted); font-weight: 500; }

        /* Status Tabs (Underline Pattern - Standard SIMA, No Card Wrapper) */
        .tabs-sima {
            display: flex;
            align-items: center;
            gap: 24px;
            border-bottom: none !important;
            margin-bottom: 24px;
            overflow-x: auto;
        }
        .tab-item-sima {
            padding: 10px 0;
            color: var(--color-text-muted);
            font-size: 14px;
            font-weight: 500;
            border-bottom: 2px solid transparent;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: color 150ms, border-bottom-color 150ms;
            user-select: none;
        }
        .tab-item-sima:hover {
            color: var(--color-text);
        }
        .tab-item-sima.active {
            color: var(--color-primary);
            border-bottom-color: var(--color-primary);
            font-weight: 600;
        }
        .tab-badge-sima {
            background: var(--color-primary-tint);
            color: var(--color-primary);
            font-size: 11px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: var(--radius-full);
            transition: all 0.15s;
        }
        .tab-item-sima.active .tab-badge-sima {
            background: var(--color-primary-tint) !important;
            color: var(--color-primary) !important;
        }

        .tab-pane-content {
            transition: opacity 0.15s ease;
        }

        @keyframes tabFadeIn {
            0% {
                opacity: 0;
            }
            100% {
                opacity: 1;
            }
        }

        .tab-anim-fade {
            animation: tabFadeIn 0.16s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .data-card { 
            background: var(--color-surface); 
            border-radius: var(--radius-lg); 
            box-shadow: none; 
            border: 1px solid var(--color-border); 
            margin-bottom: 40px; 
        }
        .data-card-header { 
            padding: 16px 20px; 
            border-bottom: 1px solid var(--color-border); 
            display: flex; 
            align-items: center; 
            justify-content: space-between; 
            flex-wrap: wrap; 
            gap: 16px; 
        }
        .data-card-title h5 { 
            font-size: 16px; 
            font-weight: 600; 
            color: var(--color-text); 
            margin: 0 0 2px 0; 
        }
        .data-card-title span { 
            font-size: 12.5px; 
            color: var(--color-text-muted); 
        }

        .controls-bar { display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
        .controls-bar .dropdown {
            position: relative;
            z-index: 1050;
        }

        .filter-dropdown-menu {
            min-width: 290px;
            max-width: 340px;
            max-height: min(490px, calc(100vh - 140px));
            overflow-y: auto;
            overscroll-behavior: contain;
            z-index: 1050;
            border-radius: var(--radius-md);
            border: 1px solid var(--color-border);
            box-shadow: none;
            background: var(--color-surface);
            padding: 16px 18px;
        }
        .filter-dropdown-menu::-webkit-scrollbar {
            width: 5px;
        }
        .filter-dropdown-menu::-webkit-scrollbar-track {
            background: transparent;
        }
        .filter-dropdown-menu::-webkit-scrollbar-thumb {
            background: var(--color-border);
            border-radius: 9999px;
        }
        .filter-dropdown-menu::-webkit-scrollbar-thumb:hover {
            background: var(--color-text-muted);
        }
        .filter-dropdown-menu[data-popper-placement^="top"],
        .dropdown-menu[data-popper-placement^="top"] {
            margin-bottom: 8px !important;
        }
        .filter-dropdown-menu[data-popper-placement^="bottom"],
        .dropdown-menu[data-popper-placement^="bottom"] {
            margin-top: 8px !important;
        }

        /* Searchable Dropdown Component (Consistent with Export/Form standard) */
        .searchable-dropdown {
            position: relative;
            width: 100%;
        }
        .dropdown-trigger:hover {
            border-color: var(--color-primary) !important;
            background-color: var(--color-row-hover) !important;
        }
        .dropdown-trigger:focus,
        .dropdown-trigger.active {
            border-color: var(--color-primary) !important;
            background-color: var(--color-surface) !important;
            box-shadow: none !important;
            outline: none !important;
        }
        .dropdown-trigger.active #existing-doc-caret,
        .dropdown-trigger.active .dropdown-trigger-caret {
            transform: rotate(180deg);
        }
        .dropdown-menu-panel {
            box-sizing: border-box;
        }
        .dropdown-search-input {
            transition: border-color 0.2s ease;
        }
        .dropdown-search-input:hover,
        .dropdown-search-input:focus {
            border-color: #9ca3af !important;
            box-shadow: none !important;
            outline: none !important;
        }
        .dropdown-options-list::-webkit-scrollbar {
            width: 5px;
        }
        .dropdown-options-list::-webkit-scrollbar-track {
            background: transparent;
        }
        .dropdown-options-list::-webkit-scrollbar-thumb {
            background: var(--color-border);
            border-radius: 9999px;
        }
        .dropdown-options-list::-webkit-scrollbar-thumb:hover {
            background: var(--color-text-muted);
        }
        .dropdown-option-item:hover {
            background: #f3f4f6 !important;
        }

        .search-box {
            display: flex; align-items: center; background: var(--color-surface); border-radius: var(--radius-sm); padding: 0 14px; border: 1px solid var(--color-border); transition: all 0.2s;
        }
        .search-box:focus-within { border-color: var(--color-primary); box-shadow: none; }
        .table-responsive.is-loading { opacity: 0.5; pointer-events: none; transition: opacity 0.2s ease; }
        .search-box input { border: none; background: transparent; padding: 10px 0; font-size: 13.5px; width: 210px; outline: none; color: var(--color-text); }
        .search-box button { border: none; background: transparent; padding: 0; cursor: pointer; color: var(--color-text-muted); }
        .search-box button i { width: 16px; height: 16px; }

        .reset-filter-btn{
            height: 42px; padding: 0 16px; border-radius: var(--radius-full) !important; border: 1px solid var(--color-border); background: var(--color-surface); display: inline-flex; align-items: center; justify-content: center; gap: 8px; cursor: pointer; transition: all 0.2s ease; color: var(--color-text-muted); font-size: 13px; font-weight: 600; white-space: nowrap; flex-shrink: 0; text-decoration: none; box-sizing: border-box;
        }
        .reset-filter-btn:hover { background: var(--color-danger-soft); border-color: #fca5a5; color: var(--color-danger); }
        .reset-filter-btn i, .reset-filter-btn svg { width: 15px; height: 15px; transition: transform 0.3s ease; }
        .reset-filter-btn:hover i, .reset-filter-btn:hover svg { transform: rotate(-90deg); }

        .filter-btn{
            width: 42px; height: 42px; border-radius: var(--radius-sm); border: 1px solid var(--color-border); background: var(--color-surface); display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; color: var(--color-text-muted); box-sizing: border-box;
        }
        .filter-btn:hover { background: var(--color-primary-tint); border-color: var(--color-primary); color: var(--color-primary); }
        .filter-btn i { width: 16px; height: 16px; }

        /* Tombol Utama Sistem Sesuai Standar SIMA */
        .btn-restore-top{
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            height: 42px;
            padding: 0 18px;
            background: var(--color-primary);
            color: #eaf1e7;
            border-radius: var(--radius-full) !important;
            font-size: 13.5px;
            font-weight: 600;
            border: 1px solid var(--color-primary);
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: none;
            white-space: nowrap;
            text-decoration: none;
            flex-shrink: 0;
            box-sizing: border-box;
        }
        .btn-restore-top:hover {
            color: #eaf1e7;
            background: var(--color-primary-hover);
            border-color: var(--color-primary-hover);
        }
        .btn-restore-top i, .btn-restore-top svg {
            width: 16px;
            height: 16px;
        }

        /* Bulk Action Bar */
        .bulk-bar-wrap {
            display: none; align-items: center; justify-content: flex-start; background: var(--color-danger-soft); border-bottom: 1px solid var(--color-border); padding: 12px 20px; flex-wrap: wrap; gap: 14px; animation: modalFadeIn 0.2s ease;
        }
        .bulk-bar-left { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .bulk-btn-delete {
            background: var(--color-danger); color: #ffffff; border: none; padding: 8px 18px; border-radius: var(--radius-full); font-size: 13px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s ease;
        }
        .bulk-btn-delete:hover { background: #8b2f23; }
        .bulk-btn-restore {
            background: var(--color-primary);
            color: #eaf1e7;
            border: none;
            padding: 8px 18px;
            border-radius: var(--radius-full);
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background-color 0.2s ease;
        }
        .bulk-btn-restore:hover {
            background: var(--color-primary-hover);
            color: #eaf1e7;
        }
        .bulk-btn-cancel {
            background: var(--color-surface); color: var(--color-text); border: 1px solid var(--color-border); padding: 8px 18px; border-radius: var(--radius-full); font-size: 13px; font-weight: 500; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s;
        }
        .bulk-btn-cancel:hover { background: var(--color-row-hover); }

        /* Modern Table (Prodex Style) */
        .data-card-body { padding: 0; }
        .table-responsive { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; touch-action: pan-x pan-y; position: relative; }
        .modern-table { width: 100%; min-width: 860px; border-collapse: collapse; text-align: left; }
        .modern-table thead th {
            background: transparent; padding: 12px 16px; text-align: left; font-size: 12px; font-weight: 500; color: var(--color-text-muted); text-transform: none; border-bottom: 1px solid var(--color-border); white-space: nowrap;
        }
        .modern-table tbody {
            transition: opacity 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .table-responsive.is-loading .modern-table tbody {
            opacity: 0.45;
            pointer-events: none;
        }
        .modern-table tbody tr { transition: all 0.15s ease; border-bottom: 1px solid var(--color-border); }
        .modern-table tbody tr:last-child,
        .modern-table tbody tr:last-child td { border-bottom: none !important; }
        .modern-table tbody tr:hover { background: var(--color-row-hover); }
        .modern-table tbody td { padding: 14px 16px; font-size: 14px; color: var(--color-text); vertical-align: middle; }

        /* Aset Cell & Jenis Cell */
        .aset-cell { display: flex; align-items: center; gap: 12px; }
        .aset-info h6 { font-size: 13.5px; font-weight: 600; color: var(--color-text); margin: 0 0 2px 0; line-height: 1.3; }
        .aset-info span { font-size: 12px; color: var(--color-text-muted); }
        
        .jenis-cell { display: flex; align-items: center; gap: 10px; }
        .jenis-icon-inline {
            width: 36px; height: 36px; border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; flex-shrink: 0;
            background: var(--color-primary-tint);
            color: var(--color-primary);
        }
        .jenis-icon-inline i, .jenis-icon-inline svg { width: 17px; height: 17px; color: var(--color-primary); stroke: var(--color-primary); }

        /* Action Buttons */
        .action-btns { display: flex; align-items: center; gap: 6px; justify-content: center; }
        .action-btn{
            width: 32px; height: 32px; border-radius: var(--radius-sm); border: none; background: transparent; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.15s ease; padding: 0;
        }
        .action-btn i, .action-btn svg { width: 18px; height: 18px; stroke: currentColor; }
        .action-btn.view { color: var(--color-info); }
        .action-btn.view:hover { background: var(--color-info-soft); color: #2B4E5B; }
        .action-btn.delete { color: var(--color-danger); }
        .action-btn.delete:hover { background: var(--color-danger-soft); color: #7F271B; }
        .action-btn.restore { color: var(--color-primary); }
        .action-btn.restore:hover { background: var(--color-primary-tint); color: var(--color-primary); }

        /* Pagination Bar */
        .pagination-bar {
            display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-top: 1px solid var(--color-border); gap: 16px; flex-wrap: wrap; background: var(--color-surface);
        }
        .pagination-left { display: flex; align-items: center; gap: 12px; }
        .page-size-wrap { display: flex; align-items: center; gap: 8px; font-size: 13.5px; color: var(--color-text-muted); }
        .per-page-select {
            min-width: 68px; padding: 6px 30px 6px 12px; font-size: 13px; font-weight: 500; color: var(--color-text); background-color: var(--color-surface); border: 1px solid var(--color-border); border-radius: var(--radius-sm); cursor: pointer; outline: none; transition: all 0.2s ease; appearance: none; -webkit-appearance: none; -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%235E6C5A'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 10px center; background-size: 12px; box-sizing: border-box; text-align: left;
        }
        .per-page-select:hover { border-color: var(--color-primary); }
        .per-page-select:focus { border-color: var(--color-primary); }
        
        .pagination-center { display: flex; align-items: center; gap: 10px; justify-content: center; flex-wrap: wrap; }
        .pagination-nav { display: flex; align-items: center; justify-content: center; gap: 4px; }
        .page-btn {
            min-width: 32px; height: 32px; padding: 0 8px; border: 1px solid var(--color-border); border-radius: var(--radius-sm); background: var(--color-surface); color: var(--color-text-muted); font-size: 13px; font-weight: 500; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; text-decoration: none;
        }
        .page-btn:hover { background: var(--color-row-hover); color: var(--color-text); border-color: var(--color-primary); }
        .page-btn.active { background: var(--color-primary); color: #eaf1e7; font-weight: 700; border-color: var(--color-primary); }
        .page-btn.disabled { opacity: 0.4; cursor: not-allowed; }
        .page-btn i { width: 14px; height: 14px; }

        .page-jump-wrap { display: flex; align-items: center; gap: 6px; margin-left: 6px; }
        .page-jump-wrap label { font-size: 13px; color: var(--color-text-muted); white-space: nowrap; }
        .page-jump-input { width: 52px; height: 32px; border: 1px solid var(--color-border); border-radius: var(--radius-sm); text-align: center; font-size: 13px; color: var(--color-text); background: var(--color-surface); outline: none; transition: border-color .15s; }
        .page-jump-input:focus { border-color: var(--color-primary); box-shadow: none; }

        .pagination-right { display: flex; align-items: center; gap: 5px; font-size: 13.5px; color: var(--color-text-muted); white-space: nowrap; }
        .pagination-right strong { color: var(--color-text); font-weight: 600; margin: 0 3px; }

        /* Modal Styles */
        .modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.45); display: none; align-items: center; justify-content: center; z-index: 9999; padding: 20px; backdrop-filter: blur(4px); }
        .modal-box { background: var(--color-surface); border: 1px solid var(--color-border); border-radius: var(--radius-xl); max-width: 820px; width: 95%; max-height: 90vh; overflow-y: auto; box-shadow: none; animation: modalFadeIn 0.25s ease; }
        @keyframes modalFadeIn { from { opacity: 0; transform: translateY(-16px); } to { opacity: 1; transform: translateY(0); } }
        .modal-header { padding: 18px 24px; border-bottom: none; display: flex; align-items: center; justify-content: space-between; }
        .modal-header h4 { margin: 0; font-size: 17px; font-weight: 700; color: var(--color-text); }
        .modal-close { background: none; border: none; font-size: 22px; color: var(--color-text-muted); cursor: pointer; line-height: 1; }
        .modal-body { padding: 24px 28px; }
        .modal-footer { padding: 16px 24px; border-top: none; display: flex; justify-content: flex-end; gap: 12px; }

        /* Standard Form Styles */
        .form-section-card {
            margin-bottom: 24px;
        }
        .form-section-card:last-child {
            margin-bottom: 0;
        }
        .form-section-title {
            font-size: 14.5px;
            font-weight: 700;
            color: var(--color-primary);
            margin-bottom: 14px;
        }
        .form-group { margin-bottom: 18px; }
        .form-label { font-weight: 600; font-size: 13.5px; color: var(--color-text); margin-bottom: 6px; display: block; }
        .form-label .required { color: #dc2626; margin-left: 2px; }
        .form-control {
            width: 100%; height: 46px; padding: 0 16px; border: 1.5px solid var(--color-border); border-radius: var(--radius-md); font-size: 14px; color: var(--color-text); background: var(--color-surface); outline: none; transition: all 0.2s ease; box-sizing: border-box;
        }
        textarea.form-control {
            height: auto;
            min-height: 80px;
            padding: 12px 16px;
        }
        .form-control:focus { border-color: var(--color-primary) !important; box-shadow: none !important; }
        .form-control::placeholder { color: var(--color-text-muted); }

        /* Standard File Upload Area */
        .file-upload-area {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 24px 20px;
            background: var(--color-surface);
            border: 2px dashed var(--color-border);
            border-radius: var(--radius-lg);
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: center;
        }
        .file-upload-area:hover {
            border-color: var(--color-primary);
            background: var(--color-primary-tint);
        }
        .file-upload-area.has-file {
            border-color: var(--color-primary);
            background: var(--color-primary-tint);
        }
        .file-upload-icon {
            background: transparent !important;
            width: auto !important;
            height: auto !important;
            display: block !important;
            margin: 0 auto 8px auto !important;
        }
        .file-upload-icon i, .file-upload-icon svg {
            width: 28px !important;
            height: 28px !important;
            stroke-width: 1.5 !important;
            color: var(--color-text-muted) !important;
            stroke: var(--color-text-muted) !important;
        }
        .file-upload-text {
            font-size: 14px;
            color: var(--color-text);
            font-weight: 600;
        }
        .file-upload-hint {
            font-size: 12px;
            color: var(--color-text-muted);
        }

        /* File Preview Card */
        .file-preview {
            display: none;
            margin-top: 12px;
            padding: 12px 16px;
            background: var(--color-surface);
            border: 1.5px solid var(--color-primary);
            border-radius: var(--radius-lg);
            align-items: center;
            gap: 12px;
        }
        .file-preview.show {
            display: flex;
        }
        .file-preview-img {
            width: 36px;
            height: 36px;
            border-radius: var(--radius-sm);
            object-fit: cover;
            border: 1px solid var(--color-border);
            background: var(--color-surface);
            flex-shrink: 0;
        }
        .file-preview-info {
            flex: 1;
            overflow: hidden;
            min-width: 0;
        }
        .file-preview-name {
            font-weight: 600;
            font-size: 13.5px;
            color: var(--color-text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .file-preview-size {
            font-size: 12px;
            color: var(--color-text-muted);
            margin-top: 2px;
        }
        .file-preview-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }
        .file-preview-link-text {
            color: var(--color-primary);
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            white-space: nowrap;
            transition: color 0.2s;
        }
        .file-preview-link-text:hover {
            text-decoration: underline;
            text-underline-offset: 3px;
            color: var(--color-primary-hover);
        }
        .file-preview-change {
            background: none;
            border: none;
            color: var(--color-text-muted);
            cursor: pointer;
            padding: 4px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            transition: color 0.2s;
        }
        .file-preview-change:hover {
            color: var(--color-primary);
        }
        .file-preview-remove {
            background: none;
            border: none;
            color: var(--color-text-muted);
            cursor: pointer;
            padding: 4px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            transition: color 0.2s;
        }
        .file-preview-remove:hover {
            color: #b91c1c;
        }

        .asset-chip-row {
            display: flex; align-items: center; justify-content: space-between; background: var(--color-surface); border: 1px solid var(--color-border); border-radius: var(--radius-md); padding: 8px 12px; font-size: 12.5px; color: var(--color-text);
        }
        @media (max-width: 768px) {
            .tabs-sima {
                gap: 16px;
                overflow-x: auto;
                padding-bottom: 10px;
                margin-bottom: 20px;
            }
            .data-card-header {
                padding: 18px 16px;
                gap: 14px;
            }
            .controls-bar {
                width: 100%;
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
            }
            .search-box {
                flex: 1 1 100%;
                min-width: 0;
                width: 100%;
            }
            .search-box input {
                width: 100%;
                min-width: 0;
                font-size: 16px;
            }
            .reset-filter-btn{
                flex: 1 1 auto;
                justify-content: center;
            }
            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                touch-action: pan-x pan-y;
            }
            .modern-table {
                min-width: 860px;
            }
            .pagination-bar {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                align-items: center;
                text-align: center;
                gap: 12px 16px;
                padding: 16px 12px 20px 12px;
                width: 100%;
            }
            .pagination-left {
                display: contents;
            }
            .pagination-center {
                display: contents;
            }
            .pagination-nav {
                order: 1;
                width: 100%;
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                align-items: center;
                gap: 6px;
            }
            .page-size-wrap {
                order: 2;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 6px;
                font-size: 13px;
                margin: 0;
            }
            .page-size-wrap select {
                padding: 4px 28px 4px 10px;
                height: 34px;
                font-size: 13px;
            }
            .page-jump-wrap {
                order: 3;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 6px;
                font-size: 13px;
                margin: 0;
            }
            .page-jump-input {
                width: 52px;
                height: 34px;
                font-size: 13px;
            }
            .pagination-right {
                order: 4;
                width: 100%;
                display: flex;
                justify-content: center;
                text-align: center;
                font-size: 13px;
                white-space: normal;
                margin-top: 2px;
            }
        }
    </style>
</head>
<body data-pc-preset="preset-1" data-pc-sidebar-caption="true" data-pc-direction="ltr" data-pc-theme="light">
    @include('layouts.notification')
    @include('layouts.modals')
    @include('layouts.layout-vertical')

    <div class="pc-container">
        <div class="pc-content" style="max-width: 1560px; margin: 0 auto;">
            <!-- PAGE HEADER & PRIMARY ACTIONS -->
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
                <div>
                    <h1 style="font-size: 22px; font-weight: 700; margin: 0; color: var(--color-text); line-height: 1.2; letter-spacing: -0.02em;">
                        Penghapusan Aset (Mutasi Berkurang)
                    </h1>
                </div>
                <div class="page-header-actions" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                    <div class="sima-dropdown-wrapper" style="position: relative; display: inline-block;">
                        <button type="button" id="btn-export-penghapusan" class="btn-sima-secondary btn-sima-pill-white" onclick="toggleExportMenu(this, event)">
                            <i data-feather="download" style="width: 14px; height: 14px;"></i>
                            <span>Ekspor Data</span>
                            <i data-feather="chevron-down" style="width: 12px; height: 12px; opacity: 0.7;"></i>
                        </button>
                        <div class="export-dropdown-menu" style="display: none;">
                            <a class="export-menu-item" id="export-penghapusan-excel" href="{{ route('penghapusan-aset.export', ['format' => 'excel']) }}" onclick="if(window.showToast) window.showToast('Ekspor data penghapusan aset (Excel) berhasil! Berkas sedang diunduh...', 'success'); this.closest('.export-dropdown-menu').style.display='none';">
                                <i data-feather="file-text" style="width: 14px; height: 14px; color: #10b981;"></i>
                                <span>Ekspor Excel (.xlsx)</span>
                            </a>
                            <hr class="dropdown-divider sima-divider" />
                            <a class="export-menu-item" id="export-penghapusan-pdf" href="{{ route('penghapusan-aset.export', ['format' => 'pdf']) }}" onclick="if(window.showToast) window.showToast('Ekspor data penghapusan aset (PDF) berhasil! Berkas sedang diunduh...', 'success'); this.closest('.export-dropdown-menu').style.display='none';">
                                <i data-feather="file" style="width: 14px; height: 14px; color: #ef4444;"></i>
                                <span>Ekspor PDF (.pdf)</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 1. STAT CARDS (Prodex 4-Card Summary Bar) -->
            <div class="stat-card-row" id="section-stat-penghapusan" data-search-title="Ringkasan Statistik Penghapusan">
                <div class="stat-card-sima">
                    <div class="stat-header">
                        <span style="font-weight: 600;">TOTAL ASET DIHAPUS</span>
                    </div>
                    <div class="stat-number stat-card-value">{{ number_format($totalDihapus, 0, ',', '.') }}</div>
                    <div class="kpi-trend-wrapper stat-delta">
                        @php 
                            $tDihapus = $trends['totalDihapus']['percent'] ?? 0;
                            $dDihapus = $trends['totalDihapus']['diff'] ?? 0;
                        @endphp
                        @if($tDihapus > 0 || $dDihapus > 0)
                            <span class="stat-delta up">
                                <i data-feather="trending-up" style="width: 12px; height: 12px;"></i>
                                +{{ $tDihapus }}% (+{{ number_format($dDihapus, 0, ',', '.') }})
                            </span>
                        @elseif($tDihapus < 0 || $dDihapus < 0)
                            <span class="stat-delta down">
                                <i data-feather="trending-down" style="width: 12px; height: 12px;"></i>
                                {{ $tDihapus }}% ({{ number_format($dDihapus, 0, ',', '.') }})
                            </span>
                        @else
                            <span class="stat-delta" style="color: var(--color-text-muted);">0% (0)</span>
                        @endif
                        <span class="kpi-trend-label" style="color: var(--color-text-muted); font-size: 11.5px; margin-left: 4px;">vs. bulan lalu</span>
                    </div>
                </div>

                <div class="stat-card-sima">
                    <div class="stat-header">
                        <span style="font-weight: 600;">TOTAL NILAI MUTASI</span>
                    </div>
                    <div class="stat-number stat-card-value">Rp {{ number_format($totalNilai, 0, ',', '.') }}</div>
                    <div class="kpi-trend-wrapper stat-delta">
                        <span class="stat-delta" style="color: var(--color-text-muted);">Mutasi Berkurang</span>
                        <span class="kpi-trend-label" style="color: var(--color-text-muted); font-size: 11.5px; margin-left: 4px;">Buku Aset Desa</span>
                    </div>
                </div>

                <div class="stat-card-sima">
                    <div class="stat-header">
                        <span style="font-weight: 600;">DIHAPUS TAHUN INI</span>
                    </div>
                    <div class="stat-number stat-card-value">{{ number_format($dihapusTahunIni, 0, ',', '.') }}</div>
                    <div class="kpi-trend-wrapper stat-delta">
                        <span class="stat-delta up">Tahun {{ date('Y') }}</span>
                        <span class="kpi-trend-label" style="color: var(--color-text-muted); font-size: 11.5px; margin-left: 4px;">periode berjalan</span>
                    </div>
                </div>

                <div class="stat-card-sima">
                    <div class="stat-header">
                        <span style="font-weight: 600;">JENIS SERING DIHAPUS</span>
                    </div>
                    <div class="stat-number stat-card-value" style="font-size: 18px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $topKategoriNama }}">{{ $topKategoriNama }}</div>
                    <div class="kpi-trend-wrapper stat-delta">
                        <span class="stat-delta up">{{ $topKategoriTotal }} Unit</span>
                        <span class="kpi-trend-label" style="color: var(--color-text-muted); font-size: 11.5px; margin-left: 4px;">akumulasi aset</span>
                    </div>
                </div>
            </div>

            <!-- Status Tabs (Underline Pattern - Standard SIMA, No Card Wrapper) -->
            <div class="tabs-sima" id="section-tabs-penghapusan" data-search-title="Kategori Tab Penghapusan">
                <div class="tab-item-sima {{ $activeTab === 'aktif' ? 'active' : '' }}" id="tab-btn-aktif" onclick="switchTab('aktif')">
                    <span>Daftar Aset Aktif (Siap Dihapus)</span>
                    <span class="tab-badge-sima" id="badge-count-aktif">{{ number_format($totalActiveCount) }}</span>
                </div>
                <div class="tab-item-sima {{ $activeTab === 'riwayat' ? 'active' : '' }}" id="tab-btn-riwayat" onclick="switchTab('riwayat')">
                    <span>Riwayat Aset Dihapus (Mutasi Berkurang)</span>
                    <span class="tab-badge-sima" id="badge-count-riwayat">{{ number_format($totalDeletedCount) }}</span>
                </div>
            </div>

            {{-- ========================================================================= --}}
            {{-- TAB 1: DAFTAR ASET AKTIF (SIAP DIHAPUS) --}}
            {{-- ========================================================================= --}}
            <div id="tab-pane-aktif" class="tab-pane-content" style="{{ $activeTab === 'aktif' ? 'display:block; opacity:1;' : 'display:none; opacity:0;' }}">
                <div class="sima-card" id="section-tabel-aktif" data-search-title="Tabel Aset Siap Dihapus" style="padding: 0; overflow: visible; margin-bottom: 32px;">
                    <!-- Action Controls Bar (Prodex Table Standard) -->
                    <div style="padding: 20px 20px; border-bottom: 1px solid var(--color-border); border-top-left-radius: var(--radius-lg); border-top-right-radius: var(--radius-lg); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                        <div style="display: flex; align-items: center; gap: 10px; flex: 1; max-width: 480px;">
                            <!-- Search Box with Shortcut Badge -->
                            <div class="topbar-search-box" style="background: var(--color-surface);">
                                <i data-feather="search" style="width: 15px; height: 15px; color: var(--color-text-muted); flex-shrink: 0;"></i>
                                <input type="text" id="searchInputAktif" class="topbar-search-input" placeholder="Cari nama barang, kode, atau NUP..." value="{{ ($activeTab === 'aktif') ? request('search') : '' }}" oninput="onSearchInput('aktif', this)" onkeypress="if(event.key==='Enter'){applyFilterParams('aktif', {page:1});}" />
                                <span class="topbar-search-shortcut" id="clear-search-btn-aktif" onclick="clearSearch('aktif')" style="cursor: pointer; {{ ($activeTab === 'aktif' && request('search')) ? 'display:inline-block;' : 'display:none;' }} color: var(--color-danger);">Batal</span>
                            </div>
                        </div>

                        <!-- Right Controls -->
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <!-- Reset Filter Button -->
                            <button type="button" class="btn-sima-secondary btn-reset-filter" id="btn-reset-filter-aktif" onclick="resetFilters('aktif')" title="Reset Filter" style="padding: 7px 12px; font-size: 13px;">
                                <i data-feather="rotate-ccw" style="width: 13px; height: 13px;"></i>
                                <span>Reset Filter</span>
                            </button>

                            <!-- Filter Popover Dropdown -->
                            <div class="dropdown" id="filter-dropdown-container-aktif" style="position: relative;">
                                <button type="button" id="btn-filter-toggle-aktif" class="btn-sima-secondary" onclick="toggleFilterPopoverTab('aktif', event)" title="Filter" style="padding: 7px 12px; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
                                    <i data-feather="sliders" style="width: 13px; height: 13px;"></i>
                                    <span>Filter</span>
                                </button>
                                <div id="filter-dropdown-menu-aktif" class="sima-filter-popover" onclick="event.stopPropagation();" style="display: none; position: absolute; top: calc(100% + 4px); right: 0; min-width: 330px; padding: 20px 24px; max-height: 85vh; overflow-y: auto; z-index: 1050; border-radius: var(--radius-lg); border: 1px solid var(--color-border); box-shadow: var(--shadow-float); background: var(--color-surface); margin: 0;">
                                    <div style="font-size: 13px; font-weight: 700; color: var(--color-primary); margin-bottom: 8px;">Klasifikasi (KIB)</div>
                                    <div style="display: flex; flex-direction: column; gap: 6px; margin-bottom: 12px;" id="jenisFilterListAktif">
                                        @php
                                            $reqJenisAktif = (array) request('jenis', []);
                                        @endphp
                                        @foreach($jenisList as $jenis)
                                        @if($jenis->nama === 'Buku Inventaris Aset Desa') @continue @endif
                                        <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text);">
                                            <input type="checkbox" name="jenisAktif[]" value="{{ $jenis->id }}" class="jenis-filter-aktif sima-checkbox" {{ in_array($jenis->id, $reqJenisAktif) && $activeTab === 'aktif' ? 'checked' : '' }}>
                                            <span>{{ $jenis->nama }}</span>
                                        </label>
                                        @endforeach
                                    </div>
                                    
                                    <hr style="border: none; border-top: 1px solid var(--color-border); margin: 12px 0;" />
                                    
                                    <div style="font-size: 13px; font-weight: 700; color: var(--color-primary); margin-bottom: 8px;">Tahun Perolehan</div>
                                    <div style="margin-bottom: 12px; position: relative;" id="tahunFilterContainerAktif">
                                        <input type="text" id="tahunFilterAktif" class="form-control-sima" placeholder="Semua Tahun" readonly
                                            style="font-size: 13px; padding-right: 32px; cursor: pointer;"
                                            value="{{ request('tahun') && request('tahun') !== 'all' && $activeTab === 'aktif' ? request('tahun') : '' }}" />
                                        <button type="button" id="clearTahunFilterBtnAktif" title="Reset ke Semua Tahun"
                                            style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--color-text-muted); cursor: pointer; padding: 2px; display: {{ request('tahun') && request('tahun') !== 'all' && $activeTab === 'aktif' ? 'inline-flex' : 'none' }}; align-items: center; justify-content: center;">
                                            <i data-feather="x" style="width: 14px; height: 14px;"></i>
                                        </button>
                                    </div>

                                    <div style="font-size: 13px; font-weight: 700; color: var(--color-primary); margin-bottom: 8px;">Ruangan / Lokasi</div>
                                    <div style="margin-bottom: 12px;">
                                        <select id="ruanganFilterAktif" class="form-select-sima" style="font-size: 13px; cursor: pointer;">
                                            <option value="all">Semua Ruangan</option>
                                            <option value="none" {{ (request('ruangan') == 'none' && $activeTab === 'aktif') ? 'selected' : '' }}>Tanpa Ruangan</option>
                                            @foreach($ruanganList as $rg)
                                                <option value="{{ $rg->id }}" {{ (request('ruangan') == $rg->id && $activeTab === 'aktif') ? 'selected' : '' }}>{{ $rg->nama_with_kode ?? $rg->nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <hr style="border: none; border-top: 1px solid var(--color-border); margin: 12px 0;" />

                                    <div style="font-size: 13px; font-weight: 700; color: var(--color-primary); margin-bottom: 8px;">Kondisi Aset</div>
                                    <div style="display: flex; flex-direction: column; gap: 6px; margin-bottom: 12px;" id="kondisiFilterListAktif">
                                        @php
                                            $reqKondisiAktif = (array) request('kondisi', []);
                                        @endphp
                                        <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text);">
                                            <input type="checkbox" name="kondisiAktif[]" value="baik" class="kondisi-filter-aktif sima-checkbox" {{ in_array('baik', $reqKondisiAktif) && $activeTab === 'aktif' ? 'checked' : '' }}>
                                            <span>Baik</span>
                                        </label>
                                        <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text);">
                                            <input type="checkbox" name="kondisiAktif[]" value="rusak_ringan" class="kondisi-filter-aktif sima-checkbox" {{ in_array('rusak_ringan', $reqKondisiAktif) && $activeTab === 'aktif' ? 'checked' : '' }}>
                                            <span>Rusak Ringan</span>
                                        </label>
                                        <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text);">
                                            <input type="checkbox" name="kondisiAktif[]" value="rusak_berat" class="kondisi-filter-aktif sima-checkbox" {{ in_array('rusak_berat', $reqKondisiAktif) && $activeTab === 'aktif' ? 'checked' : '' }}>
                                            <span>Rusak Berat</span>
                                        </label>
                                    </div>

                                    <hr style="border: none; border-top: 1px solid var(--color-border); margin: 12px 0;" />

                                    <div style="font-size: 13px; font-weight: 700; color: var(--color-primary); margin-bottom: 8px;">Urutan Tampilan</div>
                                    <div style="display: flex; flex-direction: column; gap: 6px; margin-bottom: 14px;">
                                        <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text);">
                                            <input type="radio" name="sortFilterAktif" value="nama_asc" class="sort-filter" {{ request('sort') === 'nama_asc' ? 'checked' : '' }} style="accent-color: var(--color-primary);"> Nama A-Z
                                        </label>
                                        <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text);">
                                            <input type="radio" name="sortFilterAktif" value="nama_desc" class="sort-filter" {{ request('sort') === 'nama_desc' ? 'checked' : '' }} style="accent-color: var(--color-primary);"> Nama Z-A
                                        </label>
                                        <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text);">
                                            <input type="radio" name="sortFilterAktif" value="terbaru" class="sort-filter" {{ request('sort', 'terbaru') === 'terbaru' ? 'checked' : '' }} style="accent-color: var(--color-primary);"> Terbaru Terdaftar
                                        </label>
                                        <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text);">
                                            <input type="radio" name="sortFilterAktif" value="terlama" class="sort-filter" {{ request('sort') === 'terlama' ? 'checked' : '' }} style="accent-color: var(--color-primary);"> Terlama Terdaftar
                                        </label>
                                    </div>

                                    <button type="button" onclick="applyFilterParams('aktif', { page: 1 }); event.stopPropagation();" class="btn-sima-primary" style="width: 100%; font-size: 13px; padding: 8px 14px;">
                                        Terapkan Filter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(auth()->user()->canDeleteAset())
                    <!-- Floating Bulk Action Bar (Aset Aktif) -->
                    <div id="bulk-bar" class="bulk-action-bar-sima">
                        <span class="bulk-count"><span id="bulk-count">0</span> aset dipilih</span>
                        <div class="bulk-divider"></div>
                        <button type="button" onclick="openBulkHapusModal()" class="btn-sima-danger" style="padding: 5px 14px; font-size: 12.5px;">
                            <i data-feather="trash-2" style="width: 13px; height: 13px;"></i>
                            <span>Hapus Aset Resmi</span>
                        </button>
                        <button type="button" onclick="clearSelection()" class="btn-sima-icon" style="width: 28px; height: 28px;" title="Batal Pilih">
                            <i data-feather="x" style="width: 13px; height: 13px;"></i>
                        </button>
                    </div>
                    @endif

                    <div class="data-card-body">
                        <div class="table-responsive" id="table-wrap-aktif">
                            <table class="modern-table">
                                <thead>
                                    <tr>
                                        @if(auth()->user()->canDeleteAset())
                                        <th style="width: 40px; text-align: center;">
                                            <input type="checkbox" id="check-all" class="sima-checkbox" onchange="toggleAll(this)" title="Pilih Semua di Halaman Ini">
                                        </th>
                                        @endif
                                        <th style="width: 50px; text-align: center;">No</th>
                                        <th>Nama Barang</th>
                                        <th>Jenis Inventaris</th>
                                        <th style="min-width: 140px; text-align: center;">Merk / Type</th>
                                        <th style="min-width: 150px; text-align: center;">Tahun & Nilai Perolehan</th>
                                        @if(auth()->user()->canDeleteAset())
                                        <th style="width: 80px; text-align: center;">Aksi</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody id="tbody-aktif">
                                    @forelse($activeAssets as $index => $item)
                                        @php
                                            $jenis = $item->jenis;
                                        @endphp
                                        <tr>
                                            @if(auth()->user()->canDeleteAset())
                                            <td style="text-align: center;">
                                                <input
                                                    type="checkbox"
                                                    class="row-check"
                                                    value="{{ $item->id }}"
                                                    data-nama="{{ $item->nama_barang }}"
                                                    data-kode="{{ $item->kode_barang }}"
                                                    data-nup="{{ $item->nup ?: '1' }}"
                                                    onchange="updateBulkBar()"
                                                    style="cursor: pointer; accent-color: #1f3a2a; width: 16px; height: 16px;"
                                                >
                                            </td>
                                            @endif
                                            <td style="text-align: center; font-weight: 500; color: #6b7280;">
                                                {{ $activeAssets->firstItem() + $index }}
                                            </td>
                                            <td>
                                                <div class="aset-cell">
                                                    <div class="aset-info">
                                                        <h6 style="margin: 0 0 4px 0; font-size: 14px; font-weight: 600; color: #111827; line-height: 1.3;">{{ $item->nama_barang }}</h6>
                                                        <div style="display: flex; align-items: center; gap: 6px; font-size: 13px; color: #4b5563; line-height: 1.2;">
                                                            <span style="font-weight: 500; color: #374151;">{{ $item->kode_barang ?? '-' }}</span>
                                                            <span style="color: #cbd5e1;">•</span>
                                                            <span style="color: #64748b;">NUP: <strong style="font-weight: 600; color: #1e293b;">{{ $item->nup ?: '1' }}</strong></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span style="font-size: 13px; color: var(--color-text); font-weight: 500;">{{ $jenis->nama ?? 'Aset Tetap Lainnya' }}</span>
                                            </td>
                                            <td style="text-align: center;">
                                                <span style="font-size: 13px; color: var(--color-text); font-weight: 500;">{{ $item->merk_type ?? '-' }}</span>
                                            </td>
                                            <td style="text-align: center;">
                                                <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 2px; text-align: center;">
                                                    <span style="font-size: 13.5px; font-weight: 600; color: var(--color-text); line-height: 1.2;">{{ $item->tahun_perolehan ? 'Tahun ' . $item->tahun_perolehan : '-' }}</span>
                                                    @if(!is_null($item->nilai_perolehan) && $item->nilai_perolehan > 0)
                                                        <span style="font-size: 12.5px; font-weight: 500; color: var(--color-text); line-height: 1.2; white-space: nowrap;">
                                                            Rp {{ number_format((float)$item->nilai_perolehan, fmod((float)$item->nilai_perolehan, 1) != 0 ? 2 : 0, ',', '.') }}
                                                        </span>
                                                    @else
                                                        <span style="font-size: 12px; color: var(--color-text-muted); font-style: italic; line-height: 1.2;">-</span>
                                                    @endif
                                                </div>
                                            </td>
                                            @if(auth()->user()->canDeleteAset())
                                            <td style="text-align: center;">
                                                <div class="action-btns">
                                                    <button type="button" class="btn-sima-icon" style="color: var(--color-danger); width: 32px; height: 32px;" onclick="openHapusAsetModal({{ json_encode([
                                                        'id' => $item->id,
                                                        'nama' => $item->nama_barang,
                                                        'kode' => $item->kode_barang,
                                                        'nup' => $item->nup ?: '1',
                                                        'merk' => $item->merk_type,
                                                        'tahun' => $item->tahun_perolehan,
                                                        'nilai' => $item->nilai_perolehan,
                                                        'jenis' => $jenis->nama ?? 'Aset Desa',
                                                        'ruangan' => $item->ruangan ? $item->ruangan->nama : '-',
                                                        'kondisi' => $item->kondisi ?? 'baik',
                                                    ]) }})" title="Proses Penghapusan Resmi">
                                                        <i data-feather="trash-2" style="width: 14px; height: 14px;"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            @endif
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ auth()->user()->canDeleteAset() ? 7 : 5 }}" style="text-align:center;padding:48px 20px;color:var(--color-text-muted);">
                                                <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;">
                                                    <i data-feather="inbox" style="width:42px;height:42px;stroke-width:1.5;color:var(--color-border-dark);"></i>
                                                    <p style="margin:0;font-size:13.5px;font-weight:500;">Tidak ada data aset aktif yang sesuai</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination Bar Aktif -->
                        <div class="pagination-bar" id="pagination-bar-aktif" style="padding: 32px 20px; border-top: 1px solid var(--color-border); border-bottom-left-radius: var(--radius-lg); border-bottom-right-radius: var(--radius-lg); background: var(--color-surface); flex-wrap: wrap; gap: 14px;">
                            <div class="pagination-left">
                                <div class="page-size-wrap">
                                    <span>Tampilkan</span>
                                    <select id="perPageSelectAktif" class="per-page-select" onchange="changePerPage('aktif', this.value)">
                                        <option value="10" {{ ($activeAssets->perPage() == 10) ? 'selected' : '' }}>10</option>
                                        <option value="20" {{ ($activeAssets->perPage() == 20) ? 'selected' : '' }}>20</option>
                                        <option value="50" {{ ($activeAssets->perPage() == 50) ? 'selected' : '' }}>50</option>
                                        <option value="100" {{ ($activeAssets->perPage() == 100) ? 'selected' : '' }}>100</option>
                                    </select>
                                    <span>data</span>
                                </div>
                            </div>

                            <div class="pagination-center">
                                <nav class="pagination-nav" id="paginationNavAktif">
                                    @if ($activeAssets->onFirstPage())
                                        <span class="page-btn disabled" title="Previous"><i data-feather="chevron-left"></i></span>
                                    @else
                                        <button type="button" class="page-btn" onclick="goToPage('aktif', {{ $activeAssets->currentPage() - 1 }})" title="Previous"><i data-feather="chevron-left"></i></button>
                                    @endif

                                    @php
                                        $currentPage  = $activeAssets->currentPage();
                                        $lastPage     = $activeAssets->lastPage();
                                        $windowStart  = max(1, $currentPage - 2);
                                        $windowEnd    = min($lastPage, $currentPage + 2);
                                        if (($windowEnd - $windowStart + 1) < 5) {
                                            if ($windowStart === 1) {
                                                $windowEnd = min($lastPage, 5);
                                            } else {
                                                $windowStart = max(1, $windowEnd - 4);
                                            }
                                        }
                                    @endphp
                                    @for ($p = $windowStart; $p <= $windowEnd; $p++)
                                        @if ($p == $currentPage)
                                            <span class="page-btn active">{{ $p }}</span>
                                        @else
                                            <button type="button" class="page-btn" onclick="goToPage('aktif', {{ $p }})">{{ $p }}</button>
                                        @endif
                                    @endfor

                                    @if ($activeAssets->hasMorePages())
                                        <button type="button" class="page-btn" onclick="goToPage('aktif', {{ $activeAssets->currentPage() + 1 }})" title="Next"><i data-feather="chevron-right"></i></button>
                                    @else
                                        <span class="page-btn disabled" title="Next"><i data-feather="chevron-right"></i></span>
                                    @endif
                                </nav>
                                <div class="page-jump-wrap">
                                    <label>Ke hal.</label>
                                    <input
                                        type="number"
                                        class="page-jump-input"
                                        id="pageJumpInputAktif"
                                        min="1"
                                        max="{{ $activeAssets->lastPage() }}"
                                        value="{{ $activeAssets->currentPage() }}"
                                        placeholder="1–{{ $activeAssets->lastPage() }}"
                                        onkeypress="if(event.key==='Enter'){goToPage('aktif', this.value);}"
                                    >
                                </div>
                            </div>

                            <div class="pagination-right" id="paginationRangeInfoAktif">
                                Menampilkan <strong>{{ $activeAssets->firstItem() ?? 0 }}-{{ $activeAssets->lastItem() ?? 0 }}</strong> dari <strong>{{ $activeAssets->total() }}</strong> data
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ========================================================================= --}}
            {{-- TAB 2: RIWAYAT ASET DIHAPUS (MUTASI BERKURANG) --}}
            {{-- ========================================================================= --}}
            <div id="tab-pane-riwayat" class="tab-pane-content" style="{{ $activeTab === 'riwayat' ? 'display:block; opacity:1;' : 'display:none; opacity:0;' }}">
                <div class="sima-card" id="section-tabel-riwayat" data-search-title="Tabel Riwayat Aset Dihapus" style="padding: 0; overflow: visible; margin-bottom: 32px;">
                    <!-- Action Controls Bar (Prodex Table Standard) -->
                    <div style="padding: 16px 20px; border-bottom: 1px solid var(--color-border); border-top-left-radius: var(--radius-lg); border-top-right-radius: var(--radius-lg); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                        <div style="display: flex; align-items: center; gap: 10px; flex: 1; max-width: 480px;">
                            <!-- Search Box with Shortcut Badge -->
                            <div class="topbar-search-box" style="background: var(--color-surface);">
                                <i data-feather="search" style="width: 15px; height: 15px; color: var(--color-text-muted); flex-shrink: 0;"></i>
                                <input type="text" id="searchInputRiwayat" class="topbar-search-input" placeholder="Cari nama barang, kode, atau nomor SK..." value="{{ ($activeTab === 'riwayat') ? request('search') : '' }}" oninput="onSearchInput('riwayat', this)" onkeypress="if(event.key==='Enter'){applyFilterParams('riwayat', {page:1});}" />
                                <span class="topbar-search-shortcut" id="clear-search-btn-riwayat" onclick="clearSearch('riwayat')" style="cursor: pointer; {{ ($activeTab === 'riwayat' && request('search')) ? 'display:inline-block;' : 'display:none;' }} color: var(--color-danger);">Batal</span>
                            </div>
                        </div>

                        <!-- Right Controls -->
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <!-- Reset Filter Button -->
                            <button type="button" class="btn-sima-secondary btn-reset-filter" id="btn-reset-filter-riwayat" onclick="resetFilters('riwayat')" title="Reset Filter" style="padding: 7px 12px; font-size: 13px;">
                                <i data-feather="rotate-ccw" style="width: 13px; height: 13px;"></i>
                                <span>Reset Filter</span>
                            </button>

                            <!-- Filter Popover Dropdown -->
                            <div class="dropdown" id="filter-dropdown-container-riwayat" style="position: relative;">
                                <button type="button" id="btn-filter-toggle-riwayat" class="btn-sima-secondary" onclick="toggleFilterPopoverTab('riwayat', event)" title="Filter" style="padding: 7px 12px; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
                                    <i data-feather="sliders" style="width: 13px; height: 13px;"></i>
                                    <span>Filter</span>
                                </button>
                                <div id="filter-dropdown-menu-riwayat" class="sima-filter-popover" onclick="event.stopPropagation();" style="display: none; position: absolute; top: calc(100% + 4px); right: 0; min-width: 330px; padding: 20px 24px; max-height: 85vh; overflow-y: auto; z-index: 1050; border-radius: var(--radius-lg); border: 1px solid var(--color-border); box-shadow: var(--shadow-float); background: var(--color-surface); margin: 0;">
                                    <div style="font-size: 13px; font-weight: 700; color: var(--color-primary); margin-bottom: 8px;">Klasifikasi (KIB)</div>
                                    <div style="display: flex; flex-direction: column; gap: 6px; margin-bottom: 12px;" id="jenisFilterListRiwayat">
                                        @php
                                            $reqJenisRiwayat = (array) (request('jenis_nama') ?: request('jenis', []));
                                        @endphp
                                        @foreach($jenisList as $jenis)
                                        @if($jenis->nama === 'Buku Inventaris Aset Desa') @continue @endif
                                        <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text);">
                                            <input type="checkbox" name="jenisRiwayat[]" value="{{ $jenis->nama }}" class="jenis-filter-riwayat sima-checkbox" {{ in_array($jenis->nama, $reqJenisRiwayat) && $activeTab === 'riwayat' ? 'checked' : '' }}>
                                            <span>{{ $jenis->nama }}</span>
                                        </label>
                                        @endforeach
                                    </div>

                                    <hr style="border: none; border-top: 1px solid var(--color-border); margin: 12px 0;" />

                                    <div style="font-size: 13px; font-weight: 700; color: var(--color-primary); margin-bottom: 8px;">Tahun Penghapusan</div>
                                    <div style="margin-bottom: 12px; position: relative;" id="tahunHapusFilterContainerRiwayat">
                                        <input type="text" id="tahunHapusFilterRiwayat" class="form-control-sima" placeholder="Semua Tahun" readonly
                                            style="font-size: 13px; padding-right: 32px; cursor: pointer;"
                                            value="{{ request('tahun_hapus') && request('tahun_hapus') !== 'all' && $activeTab === 'riwayat' ? request('tahun_hapus') : (request('tahun') && request('tahun') !== 'all' && $activeTab === 'riwayat' ? request('tahun') : '') }}" />
                                        <button type="button" id="clearTahunHapusFilterBtnRiwayat" title="Reset ke Semua Tahun"
                                            style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--color-text-muted); cursor: pointer; padding: 2px; display: {{ (request('tahun_hapus') && request('tahun_hapus') !== 'all' && $activeTab === 'riwayat') || (request('tahun') && request('tahun') !== 'all' && $activeTab === 'riwayat') ? 'inline-flex' : 'none' }}; align-items: center; justify-content: center;">
                                            <i data-feather="x" style="width: 14px; height: 14px;"></i>
                                        </button>
                                    </div>

                                    <div style="font-size: 13px; font-weight: 700; color: var(--color-primary); margin-bottom: 8px;">Tahun Perolehan</div>
                                    <div style="margin-bottom: 12px; position: relative;" id="tahunFilterContainerRiwayat">
                                        <input type="text" id="tahunFilterRiwayat" class="form-control-sima" placeholder="Semua Tahun" readonly
                                            style="font-size: 13px; padding-right: 32px; cursor: pointer;"
                                            value="{{ request('tahun_perolehan') && request('tahun_perolehan') !== 'all' && $activeTab === 'riwayat' ? request('tahun_perolehan') : '' }}" />
                                        <button type="button" id="clearTahunFilterBtnRiwayat" title="Reset ke Semua Tahun"
                                            style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--color-text-muted); cursor: pointer; padding: 2px; display: {{ request('tahun_perolehan') && request('tahun_perolehan') !== 'all' && $activeTab === 'riwayat' ? 'inline-flex' : 'none' }}; align-items: center; justify-content: center;">
                                            <i data-feather="x" style="width: 14px; height: 14px;"></i>
                                        </button>
                                    </div>

                                    <div style="font-size: 13px; font-weight: 700; color: var(--color-primary); margin-bottom: 8px;">Ruangan / Lokasi</div>
                                    <div style="margin-bottom: 12px;">
                                        <select id="ruanganFilterRiwayat" class="form-select-sima" style="font-size: 13px; cursor: pointer;">
                                            <option value="all">Semua Ruangan</option>
                                            <option value="none" {{ (request('ruangan_nama') == 'none' && $activeTab === 'riwayat') ? 'selected' : '' }}>Tanpa Ruangan</option>
                                            @foreach($ruanganList as $rng)
                                                <option value="{{ $rng->nama }}" {{ (request('ruangan_nama') == $rng->nama && $activeTab === 'riwayat') ? 'selected' : '' }}>{{ $rng->nama_with_kode ?? $rng->nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <hr style="border: none; border-top: 1px solid var(--color-border); margin: 12px 0;" />

                                    <div style="font-size: 13px; font-weight: 700; color: var(--color-primary); margin-bottom: 8px;">Urutan Tampilan</div>
                                    <div style="display: flex; flex-direction: column; gap: 6px; margin-bottom: 14px;">
                                        <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text);">
                                            <input type="radio" name="sortFilterRiwayat" value="nama_asc" class="sort-filter" {{ request('sort') === 'nama_asc' ? 'checked' : '' }} style="accent-color: var(--color-primary);"> Nama A-Z
                                        </label>
                                        <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text);">
                                            <input type="radio" name="sortFilterRiwayat" value="nama_desc" class="sort-filter" {{ request('sort') === 'nama_desc' ? 'checked' : '' }} style="accent-color: var(--color-primary);"> Nama Z-A
                                        </label>
                                        <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text);">
                                            <input type="radio" name="sortFilterRiwayat" value="terbaru" class="sort-filter" {{ request('sort', 'terbaru') === 'terbaru' ? 'checked' : '' }} style="accent-color: var(--color-primary);"> Terbaru Terdaftar
                                        </label>
                                        <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text);">
                                            <input type="radio" name="sortFilterRiwayat" value="terlama" class="sort-filter" {{ request('sort') === 'terlama' ? 'checked' : '' }} style="accent-color: var(--color-primary);"> Terlama Terdaftar
                                        </label>
                                    </div>

                                    <button type="button" onclick="applyFilterParams('riwayat', { page: 1 }); event.stopPropagation();" class="btn-sima-primary" style="width: 100%; font-size: 13px; padding: 8px 14px;">
                                        Terapkan Filter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(auth()->user()->canDeleteAset())
                    <!-- Floating Bulk Action Bar (Riwayat Aset Dihapus) -->
                    <div id="bulk-bar-riwayat" class="bulk-action-bar-sima">
                        <span class="bulk-count"><span id="bulk-count-riwayat">0</span> aset dipilih</span>
                        <div class="bulk-divider"></div>
                        <button type="button" onclick="openBulkRestoreModal()" class="btn-sima-primary" style="padding: 5px 14px; font-size: 12.5px;">
                            <i data-feather="rotate-ccw" style="width: 13px; height: 13px;"></i>
                            <span>Pulihkan Massal</span>
                        </button>
                        <button type="button" onclick="clearSelectionRiwayat()" class="btn-sima-icon" style="width: 28px; height: 28px;" title="Batal Pilih">
                            <i data-feather="x" style="width: 13px; height: 13px;"></i>
                        </button>
                    </div>
                    @endif

                    <div class="data-card-body">
                        <div class="table-responsive" id="table-wrap-riwayat">
                            <table class="modern-table">
                                <thead>
                                    <tr>
                                        @if(auth()->user()->canDeleteAset())
                                        <th style="width: 40px; text-align: center;">
                                            <input type="checkbox" id="check-all-riwayat" class="sima-checkbox" onchange="toggleAllRiwayat(this)" title="Pilih Semua Aset Riwayat di Halaman Ini" />
                                        </th>
                                        @endif
                                        <th style="width: 50px; text-align: center;">No</th>
                                        <th>Nama Barang</th>
                                        <th>Jenis Inventaris</th>
                                        <th style="min-width: 140px; text-align: center;">Merk / Type</th>
                                        <th style="min-width: 150px; text-align: center;">Tahun & Nilai Perolehan</th>
                                        <th style="min-width: 120px; white-space: nowrap;">Tgl Hapus</th>
                                        <th style="width: 100px; text-align: center;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody-riwayat">
                                    @forelse($deletedLogs as $index => $log)
                                        @php
                                            $icon = $log->jenis_icon;
                                            $colors = $log->jenis_colors;
                                        @endphp
                                        <tr>
                                            @if(auth()->user()->canDeleteAset())
                                            <td style="text-align: center;">
                                                <input type="checkbox" class="row-check-riwayat sima-checkbox" value="{{ $log->id }}" data-nama="{{ $log->nama_barang }}" data-kode="{{ $log->kode_barang }}" data-nup="{{ $log->nup ?: '1' }}" data-nilai="{{ $log->nilai_perolehan }}" onchange="updateBulkBarRiwayat()" />
                                            </td>
                                            @endif
                                            <td style="text-align: center; font-weight: 500; color: #6b7280;">
                                                {{ $deletedLogs->firstItem() + $index }}
                                            </td>
                                            <td>
                                                <div class="aset-cell">
                                                    <div class="aset-info">
                                                        <h6 style="margin: 0 0 4px 0; font-size: 14px; font-weight: 600; color: #111827; line-height: 1.3;">{{ $log->nama_barang }}</h6>
                                                        <div style="display: flex; align-items: center; gap: 6px; font-size: 13px; color: #4b5563; line-height: 1.2;">
                                                            <span style="font-weight: 500; color: #374151;">{{ $log->kode_barang ?? '-' }}</span>
                                                            <span style="color: #cbd5e1;">•</span>
                                                            <span style="color: #64748b;">NUP: <strong style="font-weight: 600; color: #1e293b;">{{ $log->nup ?: '1' }}</strong></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span style="font-size: 13px; color: var(--color-text); font-weight: 500;">{{ $log->jenis_nama ?? 'Aset Tetap Lainnya' }}</span>
                                            </td>
                                            <td style="text-align: center;">
                                                <span style="font-size: 13px; color: var(--color-text); font-weight: 500;">{{ $log->merk_type ?? '-' }}</span>
                                            </td>
                                            <td style="text-align: center;">
                                                <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 2px; text-align: center;">
                                                    <span style="font-size: 13.5px; font-weight: 600; color: var(--color-text); line-height: 1.2;">{{ $log->tahun_perolehan ? 'Tahun ' . $log->tahun_perolehan : '-' }}</span>
                                                    @if(!is_null($log->nilai_perolehan) && $log->nilai_perolehan > 0)
                                                        <span style="font-size: 12.5px; font-weight: 500; color: var(--color-text); line-height: 1.2; white-space: nowrap;">
                                                            Rp {{ number_format((float)$log->nilai_perolehan, fmod((float)$log->nilai_perolehan, 1) != 0 ? 2 : 0, ',', '.') }}
                                                        </span>
                                                    @else
                                                        <span style="font-size: 12px; color: var(--color-text-muted); font-style: italic; line-height: 1.2;">-</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td style="white-space: nowrap;">
                                                <span style="font-size: 13px; color: var(--color-text-muted); font-weight: 500;">
                                                    {{ $log->tanggal_hapus ? \Carbon\Carbon::parse($log->tanggal_hapus)->translatedFormat('d M Y') : ($log->deleted_at ? $log->deleted_at->translatedFormat('d M Y') : '-') }}
                                                </span>
                                            </td>
                                            <td style="text-align: center;">
                                                <div class="action-btns">
                                                    <button type="button" class="btn-sima-icon" style="width: 32px; height: 32px;" onclick="openDetailModal({{ $log->id }})" title="Lihat Detail & Dokumen SK">
                                                        <i data-feather="eye" style="width: 14px; height: 14px;"></i>
                                                    </button>
                                                    @if(auth()->user()->canDeleteAset())
                                                    <button type="button" class="btn-sima-icon" style="color: var(--color-primary); width: 32px; height: 32px;" onclick="openSingleRestoreModal({{ $log->id }}, '{{ addslashes($log->nama_barang) }}', '{{ $log->kode_barang ?? '-' }}', '{{ $log->nup ?: '1' }}', '{{ $log->nilai_perolehan }}')" title="Pulihkan Aset Ini ke Daftar Aset Aktif">
                                                        <i data-feather="rotate-ccw" style="width: 14px; height: 14px;"></i>
                                                    </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ auth()->user()->canDeleteAset() ? '8' : '7' }}" style="text-align:center;padding:48px 20px;color:var(--color-text-muted);">
                                                <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;">
                                                    <i data-feather="inbox" style="width:42px;height:42px;stroke-width:1.5;color:var(--color-border-dark);"></i>
                                                    <p style="margin:0;font-size:13.5px;font-weight:500;">Belum ada riwayat penghapusan aset yang sesuai</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination Bar Riwayat -->
                        <div class="pagination-bar" id="pagination-bar-riwayat" style="padding: 32px 20px; border-top: 1px solid var(--color-border); border-bottom-left-radius: var(--radius-lg); border-bottom-right-radius: var(--radius-lg); background: var(--color-surface); flex-wrap: wrap; gap: 14px;">
                            <div class="pagination-left">
                                <div class="page-size-wrap">
                                    <span>Tampilkan</span>
                                    <select id="perPageSelectRiwayat" class="per-page-select" onchange="changePerPage('riwayat', this.value)">
                                        <option value="10" {{ ($deletedLogs->perPage() == 10) ? 'selected' : '' }}>10</option>
                                        <option value="20" {{ ($deletedLogs->perPage() == 20) ? 'selected' : '' }}>20</option>
                                        <option value="50" {{ ($deletedLogs->perPage() == 50) ? 'selected' : '' }}>50</option>
                                        <option value="100" {{ ($deletedLogs->perPage() == 100) ? 'selected' : '' }}>100</option>
                                    </select>
                                    <span>data</span>
                                </div>
                            </div>

                            <div class="pagination-center">
                                <nav class="pagination-nav" id="paginationNavRiwayat">
                                    @if ($deletedLogs->onFirstPage())
                                        <span class="page-btn disabled" title="Previous"><i data-feather="chevron-left"></i></span>
                                    @else
                                        <button type="button" class="page-btn" onclick="goToPage('riwayat', {{ $deletedLogs->currentPage() - 1 }})" title="Previous"><i data-feather="chevron-left"></i></button>
                                    @endif

                                    @php
                                        $currentPage  = $deletedLogs->currentPage();
                                        $lastPage     = $deletedLogs->lastPage();
                                        $windowStart  = max(1, $currentPage - 2);
                                        $windowEnd    = min($lastPage, $currentPage + 2);
                                        if (($windowEnd - $windowStart + 1) < 5) {
                                            if ($windowStart === 1) {
                                                $windowEnd = min($lastPage, 5);
                                            } else {
                                                $windowStart = max(1, $windowEnd - 4);
                                            }
                                        }
                                    @endphp
                                    @for ($p = $windowStart; $p <= $windowEnd; $p++)
                                        @if ($p == $currentPage)
                                            <span class="page-btn active">{{ $p }}</span>
                                        @else
                                            <button type="button" class="page-btn" onclick="goToPage('riwayat', {{ $p }})">{{ $p }}</button>
                                        @endif
                                    @endfor

                                    @if ($deletedLogs->hasMorePages())
                                        <button type="button" class="page-btn" onclick="goToPage('riwayat', {{ $deletedLogs->currentPage() + 1 }})" title="Next"><i data-feather="chevron-right"></i></button>
                                    @else
                                        <span class="page-btn disabled" title="Next"><i data-feather="chevron-right"></i></span>
                                    @endif
                                </nav>
                                <div class="page-jump-wrap">
                                    <label>Ke hal.</label>
                                    <input
                                        type="number"
                                        class="page-jump-input"
                                        id="pageJumpInputRiwayat"
                                        min="1"
                                        max="{{ $deletedLogs->lastPage() }}"
                                        value="{{ $deletedLogs->currentPage() }}"
                                        placeholder="1–{{ $deletedLogs->lastPage() }}"
                                        onkeypress="if(event.key==='Enter'){goToPage('riwayat', this.value);}"
                                    >
                                </div>
                            </div>

                            <div class="pagination-right" id="paginationRangeInfoRiwayat">
                                Menampilkan <strong>{{ $deletedLogs->firstItem() ?? 0 }}-{{ $deletedLogs->lastItem() ?? 0 }}</strong> dari <strong>{{ $deletedLogs->total() }}</strong> data
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    @include('layouts.footer-block')

        @if(auth()->user()->canDeleteAset())
    <!-- Modal Form: Proses Hapus Aset Resmi -->
    <div id="modal-create-penghapusan" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header" style="display:flex; flex-direction: row; align-items: center; justify-content:center;">
                <h4 id="modal-form-title" style="text-align: center;">Formulir Penghapusan Aset Resmi</h4>
            </div>
            <form action="{{ route('penghapusan-aset.store') }}" method="POST" enctype="multipart/form-data" onsubmit="return validatePenghapusanForm(event)" novalidate>
                @csrf
                <input type="hidden" name="inventaris_id" id="form_inventaris_id" value="" />
                <input type="hidden" name="inventaris_ids" id="form_inventaris_ids" value="" />
                
                <div class="modal-body">
                    <div class="info-box" style="margin-bottom:16px;">
                        <i data-feather="info"></i>
                        <div class="info-box-text">Aset ini akan <strong>dikeluarkan dari daftar aset aktif</strong> dan otomatis dicatat ke dalam <strong>Mutasi Berkurang (Buku Penghapusan Aset)</strong>.</div>
                    </div>

                    <!-- Section 1: Informasi Aset Terpilih -->
                    <div class="form-section-card">
                        <div class="form-section-title">
                            Informasi Aset Yang Dihapus
                        </div>

                        <!-- Single Asset Preview (Vertical Stack with Aligned Colons) -->
                        <div id="selected-single-preview" style="font-size:14px;">
                            <div style="display:flex;flex-direction:column;gap:10px;">
                                <div style="display:flex;align-items:flex-start;">
                                    <span style="color:var(--color-text-muted);width:130px;flex-shrink:0;">Nama Aset</span>
                                    <span style="color:var(--color-text-muted);margin-right:12px;">:</span>
                                    <span id="preview-nama" style="color:var(--color-text);font-weight:600;">-</span>
                                </div>
                                <div style="display:flex;align-items:center;">
                                    <span style="color:var(--color-text-muted);width:130px;flex-shrink:0;">Kode Barang</span>
                                    <span style="color:var(--color-text-muted);margin-right:12px;">:</span>
                                    <span id="preview-kode" style="color:var(--color-text);font-weight:600;">-</span>
                                </div>
                                <div style="display:flex;align-items:center;">
                                    <span style="color:var(--color-text-muted);width:130px;flex-shrink:0;">NUP</span>
                                    <span style="color:var(--color-text-muted);margin-right:12px;">:</span>
                                    <span id="preview-nup" style="color:var(--color-text);font-weight:600;">-</span>
                                </div>
                                <div style="display:flex;align-items:center;">
                                    <span style="color:var(--color-text-muted);width:130px;flex-shrink:0;">Tahun Perolehan</span>
                                    <span style="color:var(--color-text-muted);margin-right:12px;">:</span>
                                    <span id="preview-tahun" style="color:var(--color-text);font-weight:600;">-</span>
                                </div>
                                <div style="display:flex;align-items:center;">
                                    <span style="color:var(--color-text-muted);width:130px;flex-shrink:0;">Jenis Inventaris</span>
                                    <span style="color:var(--color-text-muted);margin-right:12px;">:</span>
                                    <span id="preview-jenis" style="color:var(--color-text);font-weight:600;">-</span>
                                </div>
                                <div style="display:flex;align-items:center;">
                                    <span style="color:var(--color-text-muted);width:130px;flex-shrink:0;">Ruangan</span>
                                    <span style="color:var(--color-text-muted);margin-right:12px;">:</span>
                                    <span id="preview-ruangan" style="color:var(--color-text);font-weight:600;">-</span>
                                </div>
                                <div style="display:flex;align-items:center;">
                                    <span style="color:var(--color-text-muted);width:130px;flex-shrink:0;">Nilai Perolehan</span>
                                    <span style="color:var(--color-text-muted);margin-right:12px;">:</span>
                                    <span id="preview-nilai" style="color:var(--color-text);font-weight:600;">-</span>
                                </div>
                                <div style="display:flex;align-items:center;">
                                    <span style="color:var(--color-text-muted);width:130px;flex-shrink:0;">Kondisi Aset</span>
                                    <span style="color:var(--color-text-muted);margin-right:12px;">:</span>
                                    <span id="preview-kondisi" style="color:var(--color-text);font-weight:600;">-</span>
                                </div>
                            </div>
                        </div>

                        <!-- Bulk Asset Preview (Vertical Stack) -->
                        <div id="selected-bulk-preview" style="display:none;font-size:13.5px;">
                            <div style="font-weight:700;font-size:14px;color:var(--color-danger, #b91c1c);margin-bottom:6px;">
                                Penghapusan Masal: <span id="preview-bulk-total">0</span> Aset Terpilih
                            </div>
                            <p style="font-size:12.5px;color:var(--color-text-muted);margin:0 0 10px 0;">Seluruh aset berikut akan dihapus secara bersamaan menggunakan 1 dokumen SK yang sama:</p>
                            <div id="preview-bulk-chips" style="max-height:130px;overflow-y:auto;background:var(--color-surface);border:1px solid var(--color-border);border-radius:var(--radius-md);padding:8px;display:flex;flex-direction:column;gap:6px;">
                                <!-- Populated via JS -->
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Dokumen & Tanggal Penghapusan -->
                    <div class="form-section-card">
                        <div class="form-section-title">
                            Dokumen & Tanggal Penghapusan
                        </div>

                        <!-- Tanggal Hapus -->
                        <div class="form-group" style="margin-bottom:16px;">
                            <label for="tanggal_hapus" class="form-label">Tanggal Penghapusan <span class="required">*</span></label>
                            <input type="date" name="tanggal_hapus" id="tanggal_hapus" class="form-control" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}" required />
                        </div>

                        <!-- Pilihan Metode Dokumen (Hanya tampil jika ada riwayat dokumen yang sudah ada) -->
                        @if(!empty($existingDocuments) && count($existingDocuments) > 0)
                        <div class="form-group" style="margin-bottom:14px;">
                            <label class="form-label">Pilihan Dokumen Berita Acara / SK <span class="required">*</span></label>
                            <input type="hidden" name="metode_dokumen" id="metode_dokumen" value="existing">
                            
                            <div style="display:flex; gap:10px; margin-bottom:12px; flex-wrap:wrap;">
                                <button type="button" id="btn-metode-existing" class="btn btn-sm" onclick="setMetodeDokumen('existing')" style="border-radius: var(--radius-full); font-size:12.5px; font-weight:600; padding:7px 16px; display:inline-flex; align-items:center; gap:6px; border:1.5px solid var(--color-primary); background:var(--color-primary-tint); color:var(--color-primary); cursor:pointer;">
                                    <i data-feather="folder" style="width:14px;height:14px;"></i>
                                    <span>Pilih Dokumen yang Sudah Ada ({{ count($existingDocuments) }})</span>
                                </button>
                                <button type="button" id="btn-metode-upload" class="btn btn-sm" onclick="setMetodeDokumen('upload')" style="border-radius: var(--radius-full); font-size:12.5px; font-weight:600; padding:7px 16px; display:inline-flex; align-items:center; gap:6px; border:1.5px solid var(--color-border); background:var(--color-surface); color:var(--color-text); cursor:pointer;">
                                    <i data-feather="upload-cloud" style="width:14px;height:14px;"></i>
                                    <span>Upload Dokumen Baru</span>
                                </button>
                            </div>
                        </div>
                        @else
                        <input type="hidden" name="metode_dokumen" id="metode_dokumen" value="upload">
                        <div style="margin-bottom:8px;">
                            <label class="form-label">Dokumen Berita Acara / SK Penghapusan <span class="required">*</span></label>
                        </div>
                        @endif

                        <!-- Mode 1: Pilih Dokumen yang Sudah Ada (Searchable Dropdown) -->
                        @if(!empty($existingDocuments) && count($existingDocuments) > 0)
                        <div id="container-dokumen-existing" style="display:block; margin-bottom:0;">
                            <input type="hidden" name="existing_dokumen_path" id="existing_dokumen_path" value="">

                            <div class="searchable-dropdown" id="existing-doc-dropdown-container" style="position:relative; width:100%;">
                                <button type="button" class="dropdown-trigger" id="existing-doc-dropdown-trigger" onclick="toggleExistingDocDropdown(event)" style="width:100%; display:flex; align-items:center; justify-content:space-between; padding:12px 18px; min-height:48px; background:var(--color-surface); border:1px solid var(--color-border); border-radius: var(--radius-sm); cursor:pointer; text-align:left; transition:all 0.2s;">
                                    <span id="existing-doc-selected-label" style="color:var(--color-text-muted); font-size:13px; font-weight:500; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; margin-right:8px;">Pilih Dokumen yang Sudah Pernah Diupload</span>
                                    <i data-feather="chevron-down" id="existing-doc-caret" class="dropdown-trigger-caret" style="width:16px; height:16px; color:var(--color-text-muted); flex-shrink:0; transition:transform 0.2s;"></i>
                                </button>

                                <div class="dropdown-menu-panel" id="existing-doc-dropdown-menu" style="position:absolute; top:100%; left:0; right:0; z-index:1050; background:var(--color-surface); border:1px solid var(--color-border); border-radius:var(--radius-md); box-shadow:none; margin-top:6px; display:none; padding:8px;">
                                    <div class="dropdown-search-wrapper" style="position:relative; margin-bottom:8px;">
                                        <i data-feather="search" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--color-text-muted); width:14px; height:14px; pointer-events:none;"></i>
                                        <input type="text" id="existing-doc-search-input" class="dropdown-search-input" placeholder="Cari nama dokumen..." autocomplete="off" oninput="filterExistingDocs(this.value)" style="width:100%; padding:8px 12px 8px 34px; font-size:13px; border:1px solid var(--color-border); border-radius:var(--radius-sm); outline:none; box-sizing:border-box; background:var(--color-surface); color:var(--color-text);">
                                    </div>

                                    <ul class="dropdown-options-list" id="existing-doc-options-list" style="max-height:220px; overflow-y:auto; padding:0; margin:0; list-style:none;">
                                         @foreach($existingDocuments as $doc)
                                        <li class="dropdown-option-item" 
                                            data-path="{{ $doc['path'] }}" 
                                            data-name="{{ $doc['filename'] }}" 
                                            data-url="{{ $doc['url'] }}" 
                                            data-is-image="{{ $doc['is_image'] ? '1' : '0' }}" 
                                            data-ext="{{ $doc['ext'] }}" 
                                            data-size="{{ $doc['size'] ?? '-' }}" 
                                            onclick="selectExistingDoc('{{ addslashes($doc['path']) }}', '{{ addslashes($doc['filename']) }}', '{{ addslashes($doc['url']) }}', {{ $doc['is_image'] ? 'true' : 'false' }}, '{{ $doc['ext'] }}', '{{ $doc['size'] ?? '-' }}')"
                                            style="display:flex; align-items:center; gap:10px; padding:8px 10px; border-radius:var(--radius-sm); cursor:pointer; transition:background 0.15s; border-bottom:1px solid var(--color-border);">
                                            <div style="width:32px; height:32px; border-radius:var(--radius-sm); background:{{ $doc['is_image'] ? 'var(--color-bg)' : 'var(--color-primary-tint)' }}; display:flex; align-items:center; justify-content:center; flex-shrink:0; overflow:hidden;">
                                                @if($doc['is_image'])
                                                <img src="{{ $doc['url'] }}" alt="thumb" style="width:100%; height:100%; object-fit:cover;" />
                                                @else
                                                <i data-feather="file-text" style="width:16px; height:16px; color:var(--color-primary);"></i>
                                                @endif
                                            </div>
                                            <div style="flex:1; min-width:0;">
                                                <div class="doc-opt-name" style="font-size:12.5px; font-weight:600; color:var(--color-text); overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $doc['filename'] }}</div>
                                                <div style="font-size:11px; color:var(--color-text-muted);">{{ strtoupper($doc['ext']) }} &bull; {{ $doc['size'] ?? 'Dokumen Bukti' }}</div>
                                            </div>
                                        </li>
                                        @endforeach
                                        <li id="existing-doc-empty-msg" style="display:none; padding:16px; text-align:center; font-size:12.5px; color:var(--color-text-muted);">
                                            Tidak ada dokumen yang cocok.
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Preview Kartu Dokumen Eksisting yang Dipilih -->
                            <div id="existing-doc-preview" style="display:none; align-items:center; gap:12px; padding:12px 16px; background:var(--color-surface); border:1.5px solid var(--color-primary); border-radius:var(--radius-lg); margin-top:10px;">
                                <div id="existing-preview-img-container" style="display:none; width:36px; height:36px; border-radius:var(--radius-sm); overflow:hidden; flex-shrink:0; cursor:pointer;" onclick="previewExistingDocFile()" title="Klik untuk memperbesar gambar">
                                    <img id="existing-preview-img" src="" alt="Thumbnail" style="width:100%; height:100%; object-fit:cover; cursor:zoom-in;" />
                                </div>
                                <div id="existing-preview-icon" style="display:flex; align-items:center; justify-content:center; flex-shrink:0; cursor:pointer;" onclick="previewExistingDocFile()" title="Klik untuk membuka dokumen">
                                    <i data-feather="file-text" style="width:26px; height:26px; color:var(--color-primary); stroke:var(--color-primary);"></i>
                                </div>
                                <div style="flex:1; min-width:0; overflow:hidden;">
                                    <div id="existing-preview-name" style="font-size:13.5px; font-weight:600; color:var(--color-text); word-break:break-all; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">nama_file.pdf</div>
                                    <div id="existing-preview-size" style="font-size:12px; color:var(--color-text-muted); margin-top:2px;">-</div>
                                </div>
                                <a id="existing-preview-link" href="#" style="display:none;"></a>
                                <button type="button" onclick="previewExistingDocFile()" style="background:none; border:none; padding:0; cursor:pointer; color:var(--color-primary); text-decoration:underline; text-underline-offset:3px; font-size:13px; font-weight:500; white-space:nowrap; margin-right:4px;" title="Lihat Dokumen">Lihat Dokumen</button>
                                <button type="button" onclick="clearSelectedExistingDoc(event)" class="file-preview-remove" title="Batal pilih">
                                    <i data-feather="x" style="width:18px;height:18px;"></i>
                                </button>
                            </div>
                        </div>
                        @endif

                        <!-- Mode 2: Upload Dokumen Baru -->
                        <div id="container-dokumen-upload" style="display:{{ (!empty($existingDocuments) && count($existingDocuments) > 0) ? 'none' : 'block' }}; margin-bottom:0;">
                            <input type="file" name="dokumen_pdf" id="dokumen_pdf" accept=".pdf,.png,.jpg,.jpeg,.webp,application/pdf,image/png,image/jpeg,image/webp" style="display:none;" onchange="handlePdfSelected(this)" />
                            
                            <div class="file-upload-area" id="pdf-drop-area" onclick="document.getElementById('dokumen_pdf').click()">
                                <div style="display: flex; justify-content: center; align-items: center; width: 100%; margin-bottom: 8px;">
                                    <i data-feather="upload-cloud" style="width: 28px; height: 28px; stroke-width: 1.5; color: var(--color-text-muted); display: block; margin: 0 auto;"></i>
                                </div>
                                <div class="file-upload-text">Klik atau drag &amp; drop untuk upload dokumen baru</div>
                                <div class="file-upload-hint">Format: PDF, PNG, dan JPG (Maks. 10MB)</div>
                            </div>

                            <div class="file-preview" id="pdf-file-preview">
                                <img id="pdf-preview-img" src="" alt="Thumbnail File" class="file-preview-img" style="display:none; cursor:pointer;" onclick="previewUploadedDocFile()" title="Klik untuk memperbesar gambar" />
                                <div id="pdf-preview-icon-box" style="display:flex; align-items:center; justify-content:center; flex-shrink:0; cursor:pointer;" onclick="previewUploadedDocFile()" title="Klik untuk membuka dokumen">
                                    <i data-feather="file-text" style="width:26px; height:26px; color:var(--color-primary); stroke:var(--color-primary);"></i>
                                </div>
                                <div class="file-preview-info">
                                    <div class="file-preview-name" id="pdf-preview-name">nama_file.pdf</div>
                                    <div style="display:flex; align-items:center; gap:16px; margin-top:2px;">
                                        <span class="file-preview-size" id="pdf-preview-size" style="margin-top:0;">1.2 MB</span>
                                        <a id="uploaded-preview-link" href="#" style="display:none;"></a>
                                        <button type="button" id="uploaded-preview-btn" onclick="previewUploadedDocFile()" style="display:none; background:none; border:none; padding:0; cursor:pointer; color:var(--color-primary); text-decoration:underline; text-underline-offset:3px; font-size:12px; font-weight:500; white-space:nowrap;" title="Lihat Dokumen">Lihat Dokumen</button>
                                    </div>
                                </div>
                                <div class="file-preview-actions">
                                    <label for="dokumen_pdf" class="file-preview-change" title="Ganti file">
                                        <i data-feather="refresh-cw" style="width:16px;height:16px;"></i>
                                    </label>
                                    <button type="button" class="file-preview-remove" onclick="removeSelectedPdf(event)" title="Hapus file">
                                        <i data-feather="x" style="width:18px;height:18px;"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div id="dokumen-pdf-error" style="display:none;margin-top:10px;font-size:12.5px;color:var(--color-danger, #b91c1c);font-weight:500;">
                            <i data-feather="alert-circle" style="width:14px;height:14px;display:inline-block;vertical-align:middle;margin-right:4px;"></i>
                            <span id="dokumen-pdf-error-text">Dokumen Berita Acara / SK atau foto bukti fisik wajib dipilih atau diunggah.</span>
                        </div>
                    </div>

                </div>
                <div class="modal-footer" style="padding-top: 18px; margin-top: 20px; border-top: none !important; justify-content: center !important;">
                    <button type="button" class="btn btn-secondary" style="border-radius: var(--radius-full);font-size:13.5px;font-weight:600;padding:9px 20px;border:1px solid var(--color-border);background:var(--color-surface);color:var(--color-text);display:inline-flex;align-items:center;gap:6px;cursor:pointer;" onclick="closeCreateModal()">
                        <i data-feather="x" style="width:14px;height:14px;"></i>
                        <span>Batal</span>
                    </button>
                    <button type="submit" class="btn btn-danger" id="btn-submit-hapus" style="border-radius: var(--radius-full);font-size:13.5px;font-weight:600;padding:9px 22px;background:var(--color-danger, #b91c1c);border:1px solid var(--color-danger-border, #991b1b);color:#ffffff;white-space:nowrap;cursor:pointer;">
                        Konfirmasi Hapus Resmi
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Modal Detail Penghapusan -->
    <div id="modal-detail-penghapusan" class="modal-overlay">
        <div class="modal-box" style="max-width:760px; border-radius: var(--radius-xl); background:var(--color-surface); border:1px solid var(--color-border);">
            <div class="modal-header" style="border-bottom: none; padding: 22px 28px 16px 28px;">
                <h4 style="font-size: 17px; font-weight: 700; color: var(--color-text);">Detail Penghapusan Aset</h4>
                <button type="button" class="modal-close" onclick="closeDetailModal()">&times;</button>
            </div>
            <div class="modal-body" id="detail-modal-body" style="padding: 20px 28px;">
                <div style="text-align:center;padding:40px 0;color:var(--color-text-muted);">
                    <div style="font-size:14px;">Memuat data detail...</div>
                </div>
            </div>
            <div class="modal-footer" style="border-top: none; padding: 16px 28px;">
                <button type="button" class="btn btn-secondary" style="border-radius: var(--radius-full);font-weight:600;border:1px solid var(--color-border);padding:9px 20px;font-size:13.5px;background:var(--color-surface);color:var(--color-text);cursor:pointer;" onclick="closeDetailModal()">Tutup</button>
                <a id="detail-btn-download-pdf" href="#" class="btn btn-danger" style="border-radius: var(--radius-full);font-weight:600;background:var(--color-danger, #b91c1c);border:1px solid var(--color-danger-border, #991b1b);color:#ffffff;display:none;padding:9px 20px;font-size:13.5px;">
                    Unduh Dokumen PDF
                </a>
            </div>
        </div>
    </div>

    @if(auth()->user()->canDeleteAset())
    <!-- Modal Konfirmasi Restore Aset -->
    <div id="modal-restore-aset" class="modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); backdrop-filter:blur(4px); z-index:1060; align-items:center; justify-content:center; padding:16px;">
        <div class="modal-box" style="background:var(--color-surface); border-radius:var(--radius-xl); max-width:560px; width:100%; box-shadow:none; border:1px solid var(--color-border); overflow:hidden;">
            <div class="modal-header" style="display:flex; align-items:center; justify-content:center; padding:20px 24px 16px; border-bottom:none;">
                <div>
                    <h4 id="modal-restore-title" style="margin:0; font-size:17px; font-weight:700; color:var(--color-text); text-align:center;">Pulihkan Data Aset</h4>
                </div>
            </div>

            <form id="form-restore-aset" action="{{ route('penghapusan-aset.restore') }}" method="POST" onsubmit="submitRestoreForm(event)" novalidate>
                @csrf
                <input type="hidden" name="log_id" id="restore_single_id" value="" />
                <input type="hidden" name="log_ids" id="restore_bulk_ids" value="" />

                <div class="modal-body" style="padding:20px 26px;">
                    <div class="info-box" style="margin-bottom:16px;">
                        <i data-feather="info"></i>
                        <div class="info-box-text">Data aset yang dipulihkan akan <strong>kembali aktif di Daftar Aset Inventaris</strong> dan riwayat pencatatan mutasi berkurang ini akan dihapus.</div>
                    </div>

                    <!-- Single Preview -->
                    <div id="restore-single-preview" style="display:block;">
                        <div style="padding: 6px 4px 8px 4px;">
                            <div style="display:flex; flex-direction:column; gap:10px; font-size:13.5px;">
                                <div style="display:flex; align-items:flex-start;">
                                    <span style="color:var(--color-text-muted); width:120px; flex-shrink:0;">Nama Aset</span>
                                    <span style="color:var(--color-text-muted); margin-right:10px;">:</span>
                                    <span id="restore-preview-nama" style="color:var(--color-text); font-weight:600;">-</span>
                                </div>
                                <div style="display:flex; align-items:center;">
                                    <span style="color:var(--color-text-muted); width:120px; flex-shrink:0;">Kode Barang</span>
                                    <span style="color:var(--color-text-muted); margin-right:10px;">:</span>
                                    <span id="restore-preview-kode" style="color:var(--color-text); font-weight:500;">-</span>
                                </div>
                                <div style="display:flex; align-items:center;">
                                    <span style="color:var(--color-text-muted); width:120px; flex-shrink:0;">NUP</span>
                                    <span style="color:var(--color-text-muted); margin-right:10px;">:</span>
                                    <span id="restore-preview-nup" style="color:var(--color-text); font-weight:600;">-</span>
                                </div>
                                <div style="display:flex; align-items:center;">
                                    <span style="color:var(--color-text-muted); width:120px; flex-shrink:0;">Nilai Perolehan</span>
                                    <span style="color:var(--color-text-muted); margin-right:10px;">:</span>
                                    <span id="restore-preview-nilai" style="color:var(--color-text); font-weight:600;">-</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bulk Preview -->
                    <div id="restore-bulk-preview" style="display:none;">
                        <div style="font-weight:700; font-size:14px; color:var(--color-text); margin-bottom:8px;">
                            Pemulihan Masal: <span id="restore-bulk-total">0</span> Aset Terpilih
                        </div>
                        <div id="restore-bulk-chips" style="max-height:160px; overflow-y:auto; background:var(--color-bg); border:1px solid var(--color-border); border-radius:var(--radius-md); padding:10px; display:flex; flex-direction:column; gap:8px;">
                            <!-- JS populated -->
                        </div>
                    </div>
                </div>

                <div class="modal-footer" style="display:flex; align-items:center; justify-content:center; gap:12px; padding:16px 26px 22px; border-top:none; background:var(--color-surface);">
                    <button type="button" class="btn btn-secondary" onclick="closeRestoreModal()" style="border-radius: var(--radius-full); font-weight:600; border:1px solid var(--color-border); padding:9px 22px; font-size:13.5px; color:var(--color-text); background:var(--color-surface); cursor:pointer;">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary" id="btn-submit-restore" style="border-radius: var(--radius-full); font-size:13.5px; font-weight:600; padding:9px 24px; background:var(--color-primary); border:1px solid var(--color-primary-hover); color:#fff; box-shadow: none; cursor:pointer; display:inline-flex; align-items:center; justify-content:center; transition:all 0.2s ease;">
                        <span id="btn-submit-restore-text">Konfirmasi Pulihkan Aset</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    @include('layouts.modals')
    @include('layouts.footer-js')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        let currentTab = "{{ $activeTab }}";
        const canDeleteAset = {{ auth()->user()->canDeleteAset() ? 'true' : 'false' }};

        document.addEventListener('DOMContentLoaded', function() {
            if (typeof feather !== 'undefined') feather.replace();

            // Flatpickr
            flatpickr('#tanggal_hapus', {
                dateFormat: 'Y-m-d',
                defaultDate: new Date(),
                maxDate: 'today',
                allowInput: true
            });

            // Drag and drop for upload area
            const dropArea = document.getElementById('pdf-drop-area');
            if (dropArea) {
                ['dragenter', 'dragover'].forEach(eventName => {
                    dropArea.addEventListener(eventName, (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        dropArea.classList.add('has-file');
                    }, false);
                });
                ['dragleave', 'drop'].forEach(eventName => {
                    dropArea.addEventListener(eventName, (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        dropArea.classList.remove('has-file');
                    }, false);
                });
                dropArea.addEventListener('drop', (e) => {
                    const dt = e.dataTransfer;
                    const files = dt.files;
                    const fileInput = document.getElementById('dokumen_pdf');
                    if (files.length > 0) {
                        fileInput.files = files;
                        handlePdfSelected(fileInput);
                    }
                }, false);
            }

            // Check if preselected assets exist from URL ?inventaris_ids=XX,YY or ?inventaris_id=XX
            @php
                $preselectedBulkData = (isset($preselectedAsets) && $preselectedAsets->isNotEmpty())
                    ? $preselectedAsets->map(function($a) {
                        return [
                            'id' => $a->id,
                            'nama' => $a->nama_barang,
                            'kode' => $a->kode_barang,
                            'nup' => $a->nup ?: '1',
                            'tahun' => $a->tahun_perolehan,
                            'nilai' => $a->nilai_perolehan,
                            'jenis' => $a->jenis->nama ?? 'Aset Desa',
                            'ruangan' => $a->ruangan->nama ?? '-',
                        ];
                    })->values()->all()
                    : [];
            @endphp
            @if(!empty($preselectedBulkData) && auth()->user()->canDeleteAset())
                openPreselectedBulkModal({!! json_encode($preselectedBulkData) !!});
            @elseif(isset($preselectedAset) && auth()->user()->canDeleteAset())
                openHapusAsetModal({
                    id: {{ $preselectedAset->id }},
                    nama: "{{ addslashes($preselectedAset->nama_barang) }}",
                    kode: "{{ addslashes($preselectedAset->kode_barang) }}",
                    nup: "{{ addslashes($preselectedAset->nup ?: '1') }}",
                    tahun: "{{ $preselectedAset->tahun_perolehan }}",
                    nilai: "{{ $preselectedAset->nilai_perolehan }}",
                    jenis: "{{ addslashes($preselectedAset->jenis->nama ?? 'Aset Desa') }}",
                    ruangan: "{{ addslashes($preselectedAset->ruangan->nama ?? '-') }}"
                });
            @endif
        });

        let isTabSwitching = false;

        // Instant Tab Switching without layout shift or width differences
        function switchTab(tabName) {
            if (currentTab === tabName || isTabSwitching) return;
            isTabSwitching = true;

            currentTab = tabName;

            const btnAktif = document.getElementById('tab-btn-aktif');
            const btnRiwayat = document.getElementById('tab-btn-riwayat');
            const paneAktif = document.getElementById('tab-pane-aktif');
            const paneRiwayat = document.getElementById('tab-pane-riwayat');

            const outgoingPane = (tabName === 'riwayat') ? paneAktif : paneRiwayat;
            const incomingPane = (tabName === 'riwayat') ? paneRiwayat : paneAktif;

            // 1. Update button active states immediately
            if (tabName === 'aktif') {
                btnAktif.classList.add('active');
                btnRiwayat.classList.remove('active');
            } else {
                btnRiwayat.classList.add('active');
                btnAktif.classList.remove('active');
            }

            // 2. Hide outgoing pane and show incoming pane without horizontal translation
            outgoingPane.style.display = 'none';
            outgoingPane.style.opacity = '0';

            incomingPane.style.display = 'block';
            incomingPane.style.opacity = '1';
            incomingPane.classList.add('tab-anim-fade');

            setTimeout(() => {
                incomingPane.classList.remove('tab-anim-fade');
                isTabSwitching = false;
                if (typeof feather !== 'undefined') feather.replace();
            }, 160);

            // 3. Sync bulk bars between active/riwayat tabs
            const barAktif = document.getElementById('bulk-bar');
            const barRiwayat = document.getElementById('bulk-bar-riwayat');
            if (tabName === 'aktif') {
                if (barRiwayat) barRiwayat.classList.remove('show');
                const checkedAktif = document.querySelectorAll('.row-check:checked');
                if (barAktif && checkedAktif.length > 0) barAktif.classList.add('show');
            } else {
                if (barAktif) barAktif.classList.remove('show');
                const checkedRiwayat = document.querySelectorAll('.row-check-riwayat:checked');
                if (barRiwayat && checkedRiwayat.length > 0) barRiwayat.classList.add('show');
            }

            // Update URL cleanly for target tab without carrying over previous tab's parameters
            const url = new URL(window.location.origin + window.location.pathname);
            url.searchParams.set('tab', tabName);
            window.history.pushState({}, '', url);
        }

        // Bulk Selection Logic
        function toggleAll(headerCheckbox) {
            const rowChecks = document.querySelectorAll('.row-check');
            rowChecks.forEach(cb => cb.checked = headerCheckbox.checked);
            updateBulkBar();
        }

        function updateBulkBar() {
            const checked = document.querySelectorAll('.row-check:checked');
            const all = document.querySelectorAll('.row-check');
            const headerCheck = document.getElementById('check-all');
            const bulkBar = document.getElementById('bulk-bar');
            const bulkCount = document.getElementById('bulk-count');
            const bulkBtnCount = document.getElementById('bulk-btn-count');

            if (headerCheck) {
                headerCheck.checked = (all.length > 0 && checked.length === all.length);
            }

            const count = checked.length;
            if (bulkCount) bulkCount.textContent = count;
            if (bulkBtnCount) bulkBtnCount.textContent = count;

            if (bulkBar) {
                if (count > 0) {
                    bulkBar.classList.add('show');
                } else {
                    bulkBar.classList.remove('show');
                }
            }

            if (typeof feather !== 'undefined') feather.replace();
        }

        function clearSelection() {
            const rowChecks = document.querySelectorAll('.row-check');
            rowChecks.forEach(cb => cb.checked = false);
            const headerCheck = document.getElementById('check-all');
            if (headerCheck) headerCheck.checked = false;
            const bulkBar = document.getElementById('bulk-bar');
            if (bulkBar) bulkBar.classList.remove('show');
            updateBulkBar();
        }

        function showSystemAlert(title, message, type = 'warning') {
            const modalType = (type === 'danger' || type === 'error') ? 'danger' : (type === 'success' ? 'success' : 'warning');

            if (window.AppModal && typeof window.AppModal.show === 'function') {
                window.AppModal.show({
                    type: modalType,
                    title: title,
                    message: message,
                    confirmText: 'Tutup',
                    showCancel: false
                });
                return;
            }
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: modalType === 'danger' ? 'error' : modalType,
                    title: title,
                    text: message,
                    showConfirmButton: false,
                    showCloseButton: true
                });
                return;
            }
            // Fallback UI modal jika komponen belum termuat sempurna (tanpa browser native alert)
            let fb = document.getElementById('system-fallback-alert-modal');
            if (!fb) {
                fb = document.createElement('div');
                fb.id = 'system-fallback-alert-modal';
                fb.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,0.6);backdrop-filter:blur(4px);z-index:999999;display:flex;align-items:center;justify-content:center;padding:16px;';
                fb.innerHTML = `
                    <div style="position:relative;background:#fff;border-radius:14px;box-shadow: none;max-width:440px;width:100%;overflow:hidden;animation:slideDown 0.3s ease;text-align:center;padding:28px 24px;">
                        <button type="button" onclick="document.getElementById('system-fallback-alert-modal').style.display='none';" style="position:absolute;top:14px;right:16px;background:none;border:none;font-size:24px;line-height:1;color:#9ca3af;cursor:pointer;padding:2px 6px;transition:color 0.2s;" onmouseover="this.style.color='#4b5563'" onmouseout="this.style.color='#9ca3af'" title="Tutup">&times;</button>
                        <div id="fb-alert-icon" style="width:56px;height:56px;border-radius:50%;margin:0 auto 14px;display:flex;align-items:center;justify-content:center;background:#fef3c7;color:#d97706;font-size:24px;font-weight:bold;">!</div>
                        <h3 id="fb-alert-title" style="font-size:18px;font-weight:700;color:#111827;margin:0 0 8px 0;"></h3>
                        <p id="fb-alert-msg" style="font-size:14px;color:#6b7280;line-height:1.5;margin:0;"></p>
                    </div>
                `;
                document.body.appendChild(fb);
            }
            const iconSymbol = (modalType === 'danger') ? '✕' : ((modalType === 'success') ? '✓' : '!');
            const fbIcon = document.getElementById('fb-alert-icon');
            if (fbIcon) {
                fbIcon.style.background = iconBg;
                fbIcon.style.color = iconColor;
                fbIcon.textContent = iconSymbol;
            }
            document.getElementById('fb-alert-title').textContent = title;
            document.getElementById('fb-alert-msg').textContent = message;
            fb.style.display = 'flex';
        }

        function openPreselectedBulkModal(items) {
            if (!items || items.length === 0) return;

            const ids = [];
            const chipsContainer = document.getElementById('preview-bulk-chips');
            if (chipsContainer) {
                chipsContainer.innerHTML = '';
                items.forEach((item, idx) => {
                    ids.push(item.id);
                    const chip = document.createElement('div');
                    chip.className = 'asset-chip-row';
                    const isLast = idx === items.length - 1;
                    chip.style.cssText = 'display:flex; justify-content:space-between; align-items:center; padding:9px 4px; ' + (isLast ? '' : 'border-bottom:1px dashed var(--color-border); ') + 'font-size:13px; background:transparent;';
                    chip.innerHTML = `
                        <div style="flex:1; min-width:0;">
                            <div style="font-weight:600; color:var(--color-text); line-height:1.3; font-size:13.5px;">${item.nama || 'Aset'}</div>
                            <div style="font-size:12px; color:var(--color-text-muted); margin-top:2px;"><code>${item.kode || '-'}</code> &bull; NUP ${item.nup || '1'}</div>
                        </div>
                    `;
                    chipsContainer.appendChild(chip);
                });
            }

            document.getElementById('form_inventaris_ids').value = ids.join(',');
            document.getElementById('form_inventaris_id').value = '';

            // Switch to bulk preview mode
            document.getElementById('selected-single-preview').style.display = 'none';
            document.getElementById('selected-bulk-preview').style.display = 'block';
            document.getElementById('preview-bulk-total').textContent = items.length;
            document.getElementById('btn-submit-hapus').textContent = `Konfirmasi Hapus ${items.length} Aset Resmi`;

            resetPdfUpload();

            document.getElementById('modal-create-penghapusan').style.display = 'flex';
            if (typeof feather !== 'undefined') feather.replace();
        }

        function openBulkHapusModal() {
            const checked = document.querySelectorAll('.row-check:checked');
            if (checked.length === 0) {
                showSystemAlert('Pilih Aset', 'Pilih setidaknya 1 aset untuk dihapus masal.');
                return;
            }

            const ids = [];
            const chipsContainer = document.getElementById('preview-bulk-chips');
            chipsContainer.innerHTML = '';

            const checkedArr = Array.from(checked);
            checkedArr.forEach((cb, idx) => {
                ids.push(cb.value);
                const nama = cb.getAttribute('data-nama') || 'Aset';
                const nup = cb.getAttribute('data-nup') || '1';
                const kode = cb.getAttribute('data-kode') || '-';
                
                const chip = document.createElement('div');
                chip.className = 'asset-chip-row';
                const isLast = idx === checkedArr.length - 1;
                chip.style.cssText = 'display:flex; justify-content:space-between; align-items:center; padding:9px 4px; ' + (isLast ? '' : 'border-bottom:1px dashed var(--color-border); ') + 'font-size:13px; background:transparent;';
                chip.innerHTML = `
                    <div style="flex:1; min-width:0;">
                        <div style="font-weight:600; color:var(--color-text); line-height:1.3; font-size:13.5px;">${nama}</div>
                        <div style="font-size:12px; color:var(--color-text-muted); margin-top:2px;"><code>${kode}</code> &bull; NUP ${nup}</div>
                    </div>
                `;
                chipsContainer.appendChild(chip);
            });

            document.getElementById('form_inventaris_ids').value = ids.join(',');
            document.getElementById('form_inventaris_id').value = '';

            // Switch to bulk preview mode
            document.getElementById('selected-single-preview').style.display = 'none';
            document.getElementById('selected-bulk-preview').style.display = 'block';
            document.getElementById('preview-bulk-total').textContent = ids.length;
            document.getElementById('btn-submit-hapus').textContent = `Konfirmasi Hapus ${ids.length} Aset Resmi`;

            resetPdfUpload();

            document.getElementById('modal-create-penghapusan').style.display = 'flex';
            if (typeof feather !== 'undefined') feather.replace();
        }

        function openHapusAsetModal(item) {
            document.getElementById('form_inventaris_id').value = item.id;
            document.getElementById('form_inventaris_ids').value = '';

            // Switch to single preview mode (Vertical Stack)
            document.getElementById('selected-single-preview').style.display = 'block';
            document.getElementById('selected-bulk-preview').style.display = 'none';
            document.getElementById('btn-submit-hapus').textContent = 'Konfirmasi Hapus Resmi';

            document.getElementById('preview-nama').textContent = item.nama || '-';
            document.getElementById('preview-kode').textContent = item.kode || '-';
            document.getElementById('preview-nup').textContent = item.nup || '1';
            document.getElementById('preview-tahun').textContent = item.tahun || '-';
            document.getElementById('preview-jenis').textContent = item.jenis || '-';
            document.getElementById('preview-ruangan').textContent = item.ruangan || '-';
            document.getElementById('preview-nilai').textContent = item.nilai ? 'Rp ' + Number(item.nilai).toLocaleString('id-ID') : '-';
            const kondisiLabels = {
                'baik': 'Baik',
                'rusak_ringan': 'Rusak Ringan',
                'rusak_berat': 'Rusak Berat'
            };
            const rawKondisi = (item.kondisi || '').toLowerCase();
            document.getElementById('preview-kondisi').textContent = kondisiLabels[rawKondisi] || (item.kondisi || '-');

            resetPdfUpload();

            document.getElementById('modal-create-penghapusan').style.display = 'flex';
            if (typeof feather !== 'undefined') feather.replace();
        }

        // =========================================================================
        // Tab Riwayat: Bulk Selection & Restore Logic
        // =========================================================================
        function toggleAllRiwayat(headerCheckbox) {
            const rowChecks = document.querySelectorAll('.row-check-riwayat');
            rowChecks.forEach(cb => cb.checked = headerCheckbox.checked);
            updateBulkBarRiwayat();
        }

        function updateBulkBarRiwayat() {
            const checked = document.querySelectorAll('.row-check-riwayat:checked');
            const all = document.querySelectorAll('.row-check-riwayat');
            const headerCheck = document.getElementById('check-all-riwayat');
            const bulkBar = document.getElementById('bulk-bar-riwayat');
            const bulkCount = document.getElementById('bulk-count-riwayat');
            const bulkBtnCount = document.getElementById('bulk-btn-count-riwayat');
            const topBtnText = document.getElementById('btn-restore-top-riwayat-text');

            if (headerCheck) {
                headerCheck.checked = (all.length > 0 && checked.length === all.length);
            }

            const count = checked.length;
            if (bulkCount) bulkCount.textContent = count;
            if (bulkBtnCount) bulkBtnCount.textContent = count;
            if (topBtnText) {
                topBtnText.textContent = count > 0 ? `Pulihkan (${count})` : 'Pulihkan Aset';
            }

            if (bulkBar) {
                if (count > 0) {
                    bulkBar.classList.add('show');
                } else {
                    bulkBar.classList.remove('show');
                }
            }

            if (typeof feather !== 'undefined') feather.replace();
        }

        function clearSelectionRiwayat() {
            const rowChecks = document.querySelectorAll('.row-check-riwayat');
            rowChecks.forEach(cb => cb.checked = false);
            const headerCheck = document.getElementById('check-all-riwayat');
            if (headerCheck) headerCheck.checked = false;
            const bulkBar = document.getElementById('bulk-bar-riwayat');
            if (bulkBar) bulkBar.classList.remove('show');
            updateBulkBarRiwayat();
        }

        function triggerRestoreRiwayat() {
            const checked = document.querySelectorAll('.row-check-riwayat:checked');
            const validChecked = Array.from(checked).filter(cb => cb.value && cb.value !== '');
            if (validChecked.length > 0) {
                openBulkRestoreModal();
            } else {
                const all = document.querySelectorAll('.row-check-riwayat');
                if (all.length === 0) {
                    showSystemAlert('Riwayat Kosong', 'Belum ada riwayat penghapusan aset yang dapat dipulihkan.', 'warning');
                    return;
                }
                showSystemAlert('Pilih Aset', 'Silakan centang satu atau beberapa aset pada tabel riwayat yang ingin Anda pulihkan.', 'warning');
            }
        }

        function openSingleRestoreModal(id, nama, kode, nup, nilai) {
            document.getElementById('restore_single_id').value = id;
            document.getElementById('restore_bulk_ids').value = '';

            document.getElementById('restore-single-preview').style.display = 'block';
            document.getElementById('restore-bulk-preview').style.display = 'none';

            document.getElementById('modal-restore-title').textContent = 'Pulihkan Data Aset';
            document.getElementById('restore-preview-nama').textContent = nama || '-';
            document.getElementById('restore-preview-kode').textContent = kode || '-';
            document.getElementById('restore-preview-nup').textContent = nup || '1';
            document.getElementById('restore-preview-nilai').textContent = (nilai && Number(nilai) > 0) ? ('Rp ' + Number(nilai).toLocaleString('id-ID')) : '-';

            document.getElementById('btn-submit-restore-text').textContent = 'Konfirmasi Pulihkan Aset';

            const modal = document.getElementById('modal-restore-aset');
            if (modal) modal.style.display = 'flex';
            if (typeof feather !== 'undefined') feather.replace();
        }

        function openBulkRestoreModal() {
            const checked = document.querySelectorAll('.row-check-riwayat:checked');
            const validChecked = Array.from(checked).filter(cb => cb.value && cb.value !== '');
            if (validChecked.length === 0) {
                showSystemAlert('Pilih Aset', 'Pilih setidaknya 1 aset dari riwayat untuk dipulihkan.', 'warning');
                return;
            }

            const ids = [];
            const chipsContainer = document.getElementById('restore-bulk-chips');
            chipsContainer.innerHTML = '';

            validChecked.forEach(cb => {
                ids.push(cb.value);
                const nama = cb.getAttribute('data-nama') || 'Aset';
                const nup = cb.getAttribute('data-nup') || '1';
                const kode = cb.getAttribute('data-kode') || '-';
                const nilai = cb.getAttribute('data-nilai');
                const formattedNilai = (nilai && Number(nilai) > 0) ? ('Rp ' + Number(nilai).toLocaleString('id-ID')) : '-';

                const chip = document.createElement('div');
                chip.style.cssText = 'background:#ffffff;border:1px solid #e2e8f0;border-radius:10px;padding:8px 12px;font-size:13px;display:flex;align-items:center;justify-content:space-between;box-shadow: none;';
                chip.innerHTML = `
                    <div>
                        <div style="font-weight:600;color:#111827;">${escapeHtml(nama)}</div>
                        <div style="font-size:12px;color:#64748b;">${escapeHtml(kode)} &bull; NUP ${escapeHtml(nup)}</div>
                    </div>
                    <div style="font-size:12.5px;font-weight:600;color:#111827;">${formattedNilai}</div>
                `;
                chipsContainer.appendChild(chip);
            });

            document.getElementById('restore_bulk_ids').value = ids.join(',');
            document.getElementById('restore_single_id').value = '';

            document.getElementById('restore-single-preview').style.display = 'none';
            document.getElementById('restore-bulk-preview').style.display = 'block';
            document.getElementById('restore-bulk-total').textContent = ids.length;

            document.getElementById('modal-restore-title').textContent = `Pulihkan Masal (${ids.length} Aset)`;
            document.getElementById('btn-submit-restore-text').textContent = `Konfirmasi Pulihkan ${ids.length} Aset`;

            const modal = document.getElementById('modal-restore-aset');
            if (modal) modal.style.display = 'flex';
            if (typeof feather !== 'undefined') feather.replace();
        }

        function closeRestoreModal() {
            const modal = document.getElementById('modal-restore-aset');
            if (modal) modal.style.display = 'none';
        }

        function submitRestoreForm(e) {
            if (e) e.preventDefault();
            const form = document.getElementById('form-restore-aset');
            if (!form) return;

            const btnSubmit = document.getElementById('btn-submit-restore');
            const btnText = document.getElementById('btn-submit-restore-text');
            const originalText = btnText ? btnText.textContent : 'Konfirmasi Pulihkan';

            if (btnSubmit) {
                btnSubmit.disabled = true;
                if (btnText) btnText.textContent = 'Memulihkan...';
            }

            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || formData.get('_token')
                },
                body: formData
            })
            .then(r => r.json())
            .then(res => {
                if (btnSubmit) {
                    btnSubmit.disabled = false;
                    if (btnText) btnText.textContent = originalText;
                }

                if (res.success) {
                    closeRestoreModal();
                    clearSelectionRiwayat();
                    applyFilterParams('riwayat', { page: 1 });
                    showSystemAlert('Berhasil Dipulihkan!', res.message, 'success');
                } else {
                    showSystemAlert('Gagal Memulihkan', res.message || 'Gagal memulihkan aset.', 'danger');
                }
            })
            .catch(err => {
                if (btnSubmit) {
                    btnSubmit.disabled = false;
                    if (btnText) btnText.textContent = originalText;
                }
                console.error(err);
                showSystemAlert('Terjadi Kesalahan', 'Terjadi kesalahan saat memulihkan data aset.', 'danger');
            });
        }

        function escapeHtml(str) {
            if (!str) return '';
            return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }
        function escapeJsQuote(str) {
            if (!str) return '';
            return String(str).replace(/'/g, "\\'").replace(/"/g, '&quot;');
        }

        function setMetodeDokumen(mode) {
            const hiddenInput = document.getElementById('metode_dokumen');
            if (hiddenInput) hiddenInput.value = mode;

            const btnExisting = document.getElementById('btn-metode-existing');
            const btnUpload = document.getElementById('btn-metode-upload');
            const containerExisting = document.getElementById('container-dokumen-existing');
            const containerUpload = document.getElementById('container-dokumen-upload');
            const errEl = document.getElementById('dokumen-pdf-error');

            if (errEl) errEl.style.display = 'none';

            if (mode === 'existing') {
                if (btnExisting) {
                    btnExisting.style.border = '1.5px solid var(--color-primary)';
                    btnExisting.style.background = 'var(--color-primary-tint)';
                    btnExisting.style.color = 'var(--color-primary)';
                }
                if (btnUpload) {
                    btnUpload.style.border = '1.5px solid var(--color-border)';
                    btnUpload.style.background = 'var(--color-surface)';
                    btnUpload.style.color = 'var(--color-text)';
                }
                if (containerExisting) containerExisting.style.display = 'block';
                if (containerUpload) containerUpload.style.display = 'none';
            } else {
                if (btnExisting) {
                    btnExisting.style.border = '1.5px solid var(--color-border)';
                    btnExisting.style.background = 'var(--color-surface)';
                    btnExisting.style.color = 'var(--color-text)';
                }
                if (btnUpload) {
                    btnUpload.style.border = '1.5px solid var(--color-primary)';
                    btnUpload.style.background = 'var(--color-primary-tint)';
                    btnUpload.style.color = 'var(--color-primary)';
                }
                if (containerExisting) containerExisting.style.display = 'none';
                if (containerUpload) containerUpload.style.display = 'block';
            }
            if (typeof feather !== 'undefined') feather.replace();
        }
        window.setMetodeDokumen = setMetodeDokumen;

        function toggleExistingDocDropdown(e) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            const menu = document.getElementById('existing-doc-dropdown-menu');
            const trigger = document.getElementById('existing-doc-dropdown-trigger');
            const searchInput = document.getElementById('existing-doc-search-input');
            if (!menu) return;

            const isVisible = menu.style.display === 'block';
            if (isVisible) {
                menu.style.display = 'none';
                if (trigger) trigger.classList.remove('active');
            } else {
                menu.style.display = 'block';
                if (trigger) trigger.classList.add('active');
                if (searchInput) {
                    searchInput.value = '';
                    filterExistingDocs('');
                    setTimeout(() => searchInput.focus(), 50);
                }
            }
            if (typeof feather !== 'undefined') feather.replace();
        }
        window.toggleExistingDocDropdown = toggleExistingDocDropdown;

        function filterExistingDocs(query) {
            const list = document.getElementById('existing-doc-options-list');
            if (!list) return;
            const items = list.querySelectorAll('.dropdown-option-item');
            const emptyMsg = document.getElementById('existing-doc-empty-msg');
            let visibleCount = 0;
            const q = (query || '').toLowerCase().trim();

            items.forEach(item => {
                const name = (item.getAttribute('data-name') || '').toLowerCase();
                if (!q || name.includes(q)) {
                    item.style.display = 'flex';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });

            if (emptyMsg) {
                emptyMsg.style.display = visibleCount === 0 ? 'block' : 'none';
            }
        }
        window.filterExistingDocs = filterExistingDocs;

        function selectExistingDoc(path, filename, url, isImage, ext, size) {
            const input = document.getElementById('existing_dokumen_path');
            const label = document.getElementById('existing-doc-selected-label');
            const menu = document.getElementById('existing-doc-dropdown-menu');
            const trigger = document.getElementById('existing-doc-dropdown-trigger');
            const previewEl = document.getElementById('existing-doc-preview');
            const nameEl = document.getElementById('existing-preview-name');
            const sizeEl = document.getElementById('existing-preview-size');
            const imgEl = document.getElementById('existing-preview-img');
            const imgContainer = document.getElementById('existing-preview-img-container');
            const iconEl = document.getElementById('existing-preview-icon');
            const errEl = document.getElementById('dokumen-pdf-error');

            if (input) input.value = path;
            if (label) {
                label.textContent = filename;
                label.style.color = 'var(--color-text)';
                label.style.fontWeight = '600';
            }
            if (errEl) errEl.style.display = 'none';

            // Highlight selected item in options list
            const list = document.getElementById('existing-doc-options-list');
            if (list) {
                list.querySelectorAll('.dropdown-option-item').forEach(item => {
                    if (item.getAttribute('data-path') === path) {
                        item.style.background = 'var(--color-primary-tint)';
                    } else {
                        item.style.background = 'transparent';
                    }
                });
            }

            // Update preview card
            if (nameEl) nameEl.textContent = filename;
            if (sizeEl) sizeEl.textContent = size || '-';
            if (isImage && url) {
                if (imgEl) imgEl.src = url;
                if (imgContainer) imgContainer.style.display = 'block';
                if (iconEl) iconEl.style.display = 'none';
            } else {
                if (imgEl) imgEl.src = '';
                if (imgContainer) imgContainer.style.display = 'none';
                if (iconEl) iconEl.style.display = 'flex';
            }
            const linkEl = document.getElementById('existing-preview-link');
            if (linkEl) {
                linkEl.href = url || '#';
                linkEl.style.display = url ? 'inline-block' : 'none';
            }
            if (previewEl) previewEl.style.display = 'flex';

            if (menu) menu.style.display = 'none';
            if (trigger) trigger.classList.remove('active');
            if (typeof feather !== 'undefined') feather.replace();
        }
        window.selectExistingDoc = selectExistingDoc;

        function clearSelectedExistingDoc(e) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            const input = document.getElementById('existing_dokumen_path');
            const label = document.getElementById('existing-doc-selected-label');
            const previewEl = document.getElementById('existing-doc-preview');
            const sizeEl = document.getElementById('existing-preview-size');
            const imgEl = document.getElementById('existing-preview-img');
            const imgContainer = document.getElementById('existing-preview-img-container');
            const linkEl = document.getElementById('existing-preview-link');

            if (input) input.value = '';
            if (label) {
                label.textContent = 'Pilih Dokumen yang Sudah Pernah Diupload';
                label.style.color = 'var(--color-text-muted)';
                label.style.fontWeight = '500';
            }
            if (previewEl) previewEl.style.display = 'none';
            if (sizeEl) sizeEl.textContent = '-';
            if (imgEl) imgEl.src = '';
            if (imgContainer) imgContainer.style.display = 'none';
            if (linkEl) {
                linkEl.href = '#';
                linkEl.style.display = 'none';
            }

            const list = document.getElementById('existing-doc-options-list');
            if (list) {
                list.querySelectorAll('.dropdown-option-item').forEach(item => {
                    item.style.background = 'transparent';
                });
            }
        }
        window.clearSelectedExistingDoc = clearSelectedExistingDoc;

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const container = document.getElementById('existing-doc-dropdown-container');
            const menu = document.getElementById('existing-doc-dropdown-menu');
            const trigger = document.getElementById('existing-doc-dropdown-trigger');
            if (container && menu && !container.contains(e.target)) {
                menu.style.display = 'none';
                if (trigger) trigger.classList.remove('active');
            }
        });

        function resetPdfUpload() {
            const input = document.getElementById('dokumen_pdf');
            if (input) input.value = '';
            clearSelectedExistingDoc();
            const preview = document.getElementById('pdf-file-preview');
            const dropArea = document.getElementById('pdf-drop-area');
            const imgEl = document.getElementById('pdf-preview-img');
            const iconBox = document.getElementById('pdf-preview-icon-box');
            const errEl = document.getElementById('dokumen-pdf-error');
            const uploadLink = document.getElementById('uploaded-preview-link');
            const uploadBtn = document.getElementById('uploaded-preview-btn');

            if (errEl) errEl.style.display = 'none';
            if (imgEl) {
                imgEl.src = '';
                imgEl.style.display = 'none';
            }
            if (iconBox) iconBox.style.display = 'flex';
            if (uploadLink) {
                uploadLink.href = '#';
                uploadLink.style.display = 'none';
            }
            if (uploadBtn) {
                uploadBtn.style.display = 'none';
            }
            if (preview) preview.style.display = 'none';
            if (dropArea) {
                dropArea.style.display = 'flex';
                dropArea.classList.remove('has-file');
            }

            const hasExisting = {{ (!empty($existingDocuments) && count($existingDocuments) > 0) ? 'true' : 'false' }};
            setMetodeDokumen(hasExisting ? 'existing' : 'upload');

            if (typeof feather !== 'undefined') feather.replace();
        }

        function previewExistingDocFile() {
            const linkEl = document.getElementById('existing-preview-link');
            const url = linkEl ? linkEl.href : '';
            const imgEl = document.getElementById('existing-preview-img');
            const nameEl = document.getElementById('existing-preview-name');
            const filename = nameEl ? nameEl.textContent : 'Dokumen Bukti';
            const imgContainer = document.getElementById('existing-preview-img-container');
            const isImage = (imgContainer && imgContainer.style.display !== 'none' && imgEl && imgEl.src && !imgEl.src.endsWith('#'));

            if (isImage && typeof window.openSimaImageViewer === 'function') {
                window.openSimaImageViewer(imgEl.src, 0, filename);
            } else if (url && url !== '#' && !url.startsWith('javascript:')) {
                window.open(url, '_blank');
            }
        }
        window.previewExistingDocFile = previewExistingDocFile;

        function previewUploadedDocFile() {
            const imgEl = document.getElementById('pdf-preview-img');
            const nameEl = document.getElementById('pdf-preview-name');
            const filename = nameEl ? nameEl.textContent : 'Dokumen Bukti';
            const linkEl = document.getElementById('uploaded-preview-link');
            const url = linkEl ? linkEl.href : '';

            if (imgEl && imgEl.src && imgEl.style.display !== 'none' && typeof window.openSimaImageViewer === 'function') {
                window.openSimaImageViewer(imgEl.src, 0, filename);
            } else if (url && url !== '#' && !url.startsWith('javascript:')) {
                window.open(url, '_blank');
            }
        }
        window.previewUploadedDocFile = previewUploadedDocFile;

        function removeSelectedPdf(e) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            resetPdfUpload();
        }
        window.removeSelectedPdf = removeSelectedPdf;

        function removePdfFile() {
            resetPdfUpload();
        }
        window.removePdfFile = removePdfFile;

        function handlePdfSelected(input) {
            const file = input.files[0];
            const errEl = document.getElementById('dokumen-pdf-error');
            const errText = document.getElementById('dokumen-pdf-error-text');
            const preview = document.getElementById('pdf-file-preview');
            const dropArea = document.getElementById('pdf-drop-area');
            const nameEl = document.getElementById('pdf-preview-name');
            const sizeEl = document.getElementById('pdf-preview-size');
            const imgEl = document.getElementById('pdf-preview-img');
            const iconBox = document.getElementById('pdf-preview-icon-box');
            const uploadLink = document.getElementById('uploaded-preview-link');
            const uploadBtn = document.getElementById('uploaded-preview-btn');

            if (!file) return;

            // Validasi file: pdf, png, jpg, jpeg, webp
            const allowedExts = ['pdf', 'png', 'jpg', 'jpeg', 'webp'];
            const fileExt = file.name.split('.').pop().toLowerCase();
            if (!allowedExts.includes(fileExt)) {
                if (errText) errText.textContent = 'Format berkas tidak didukung. Harap unggah berkas PDF atau gambar (PNG/JPG).';
                if (errEl) errEl.style.display = 'block';
                input.value = '';
                return;
            }

            // Maksimal 10MB
            if (file.size > 10 * 1024 * 1024) {
                if (errText) errText.textContent = 'Ukuran berkas terlalu besar. Maksimal 10 MB.';
                if (errEl) errEl.style.display = 'block';
                input.value = '';
                return;
            }

            if (errEl) errEl.style.display = 'none';
            nameEl.textContent = file.name;
            sizeEl.textContent = (file.size / (1024 * 1024)).toFixed(2) + ' MB';

            if (uploadLink) {
                try {
                    const objectUrl = URL.createObjectURL(file);
                    uploadLink.href = objectUrl;
                    if (uploadBtn) uploadBtn.style.display = 'inline-block';
                } catch(e) {
                    uploadLink.style.display = 'none';
                    if (uploadBtn) uploadBtn.style.display = 'none';
                }
            }

            // Jika file gambar, tampilkan thumbnail pratinjau asli
            if (['png', 'jpg', 'jpeg', 'webp'].includes(fileExt)) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imgEl.src = e.target.result;
                    imgEl.style.display = 'block';
                    iconBox.style.display = 'none';
                };
                reader.readAsDataURL(file);
            } else {
                imgEl.style.display = 'none';
                imgEl.src = '';
                iconBox.style.display = 'flex';
            }

            dropArea.style.display = 'none';
            preview.style.display = 'flex';
            if (typeof feather !== 'undefined') feather.replace();
        }

        function validatePenghapusanForm(e) {
            const errEl = document.getElementById('dokumen-pdf-error');
            const errText = document.getElementById('dokumen-pdf-error-text');

            const tglInput = document.getElementById('tanggal_hapus');
            if (tglInput && tglInput.value) {
                const today = new Date().toISOString().split('T')[0];
                if (tglInput.value > today) {
                    if (e) e.preventDefault();
                    if (errText) errText.textContent = 'Tanggal penghapusan tidak boleh di masa depan (maksimal hari ini).';
                    if (errEl) errEl.style.display = 'block';
                    tglInput.focus();
                    return false;
                }
            }

            const metodeInput = document.getElementById('metode_dokumen');
            const metode = metodeInput ? metodeInput.value : 'upload';

            if (metode === 'existing') {
                const select = document.getElementById('existing_dokumen_path');
                if (!select || !select.value) {
                    if (e) e.preventDefault();
                    if (errText) errText.textContent = 'Pilih salah satu dokumen yang sudah ada dari daftar.';
                    if (errEl) errEl.style.display = 'block';
                    return false;
                }
            } else {
                const input = document.getElementById('dokumen_pdf');
                if (!input || !input.files || input.files.length === 0) {
                    if (e) e.preventDefault();
                    if (errText) errText.textContent = 'Dokumen Berita Acara / SK atau foto bukti fisik wajib diunggah.';
                    if (errEl) errEl.style.display = 'block';
                    return false;
                }
            }

            if (errEl) errEl.style.display = 'none';
            return true;
        }

        function closeCreateModal() {
            document.getElementById('modal-create-penghapusan').style.display = 'none';
        }

        let _searchTimerAktif = null;
        let _searchTimerRiwayat = null;

        function toggleSearchClear(tab, input) {
            const clearBtn = document.getElementById('clear-search-btn-' + tab);
            const searchBtn = document.getElementById('search-icon-btn-' + tab);
            if (!input || !clearBtn || !searchBtn) return;
            if (input.value) {
                clearBtn.style.display = 'block';
                searchBtn.style.display = 'none';
            } else {
                clearBtn.style.display = 'none';
                searchBtn.style.display = 'block';
            }
        }

        function onSearchInput(tab, input) {
            toggleSearchClear(tab, input);
            if (tab === 'aktif') {
                clearTimeout(_searchTimerAktif);
                _searchTimerAktif = setTimeout(() => { applyFilterParams('aktif', { page: 1 }); }, 400);
            } else {
                clearTimeout(_searchTimerRiwayat);
                _searchTimerRiwayat = setTimeout(() => { applyFilterParams('riwayat', { page: 1 }); }, 400);
            }
        }

        function clearSearch(tab) {
            const input = document.getElementById(tab === 'aktif' ? 'searchInputAktif' : 'searchInputRiwayat');
            if (input) input.value = '';
            toggleSearchClear(tab, input);
            applyFilterParams(tab, { page: 1 });
        }

        function resetFilters(tab) {
            const targetTab = tab || currentTab;
            if (targetTab === 'aktif') {
                const searchInput = document.getElementById('searchInputAktif');
                if (searchInput) searchInput.value = '';
                toggleSearchClear('aktif', searchInput);

                document.querySelectorAll('.jenis-filter-aktif').forEach(cb => { cb.checked = false; });
                document.querySelectorAll('.kondisi-filter-aktif').forEach(cb => { cb.checked = false; });

                const tahun = document.getElementById('tahunFilterAktif');
                if (tahun) {
                    tahun.value = '';
                    if (tahun._flatpickr && tahun._flatpickr.calendarContainer && tahun._flatpickr.calendarContainer._yearPickerState) {
                        tahun._flatpickr.calendarContainer._yearPickerState.selectedYear = null;
                    }
                }
                const clearTahunBtn = document.getElementById('clearTahunFilterBtnAktif');
                if (clearTahunBtn) clearTahunBtn.style.display = 'none';

                const ruangan = document.getElementById('ruanganFilterAktif');
                if (ruangan) ruangan.value = 'all';

                const defaultSort = document.querySelector('input[name="sortFilterAktif"][value="terbaru"]');
                if (defaultSort) defaultSort.checked = true;

                applyFilterParams('aktif', { page: 1 });
            } else {
                const searchInput = document.getElementById('searchInputRiwayat');
                if (searchInput) searchInput.value = '';
                toggleSearchClear('riwayat', searchInput);

                document.querySelectorAll('.jenis-filter-riwayat').forEach(cb => { cb.checked = false; });

                const tahunHapus = document.getElementById('tahunHapusFilterRiwayat');
                if (tahunHapus) {
                    tahunHapus.value = '';
                    if (tahunHapus._flatpickr && tahunHapus._flatpickr.calendarContainer && tahunHapus._flatpickr.calendarContainer._yearPickerState) {
                        tahunHapus._flatpickr.calendarContainer._yearPickerState.selectedYear = null;
                    }
                }
                const clearTahunHapusBtn = document.getElementById('clearTahunHapusFilterBtnRiwayat');
                if (clearTahunHapusBtn) clearTahunHapusBtn.style.display = 'none';

                const tahunPerolehan = document.getElementById('tahunFilterRiwayat');
                if (tahunPerolehan) {
                    tahunPerolehan.value = '';
                    if (tahunPerolehan._flatpickr && tahunPerolehan._flatpickr.calendarContainer && tahunPerolehan._flatpickr.calendarContainer._yearPickerState) {
                        tahunPerolehan._flatpickr.calendarContainer._yearPickerState.selectedYear = null;
                    }
                }
                const clearTahunPerolehanBtn = document.getElementById('clearTahunFilterBtnRiwayat');
                if (clearTahunPerolehanBtn) clearTahunPerolehanBtn.style.display = 'none';

                const ruangan = document.getElementById('ruanganFilterRiwayat');
                if (ruangan) ruangan.value = 'all';

                const defaultSort = document.querySelector('input[name="sortFilterRiwayat"][value="terbaru"]');
                if (defaultSort) defaultSort.checked = true;

                applyFilterParams('riwayat', { page: 1 });
            }

            document.querySelectorAll('.dropdown-menu.show').forEach(el => el.classList.remove('show'));
            document.querySelectorAll('.dropdown.show').forEach(el => el.classList.remove('show'));
            document.querySelectorAll('.sima-filter-popover').forEach(el => el.style.display = 'none');
        }

        window.toggleFilterPopoverTab = function(tab, e) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            const menu = document.getElementById('filter-dropdown-menu-' + tab);
            if (!menu) return;
            const isVisible = menu.style.display === 'block';
            document.querySelectorAll('.sima-filter-popover').forEach(el => el.style.display = 'none');
            menu.style.display = isVisible ? 'none' : 'block';
            if (!isVisible && typeof feather !== 'undefined') {
                feather.replace();
            }
        };

        window.closeFilterPopoverTab = function(tab, e) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            const menu = document.getElementById('filter-dropdown-menu-' + tab);
            if (menu) menu.style.display = 'none';
        };

        document.addEventListener('click', function(e) {
            ['aktif', 'riwayat'].forEach(tab => {
                const container = document.getElementById('filter-dropdown-container-' + tab);
                const menu = document.getElementById('filter-dropdown-menu-' + tab);
                if (container && menu && !container.contains(e.target)) {
                    menu.style.display = 'none';
                }
            });
        });

        function applyFilterParams(tab, extraParams = {}, isAutoRefresh = false) {
            if (!isAutoRefresh) {
                const popover = document.getElementById('filter-dropdown-menu-' + (tab || currentTab));
                if (popover) popover.style.display = 'none';
            }

            const targetTab = tab || currentTab;
            const params = new URLSearchParams();
            params.set('tab', targetTab);

            if (targetTab === 'aktif') {
                const search = document.getElementById('searchInputAktif')?.value.trim() || '';
                const selectedJenis = Array.from(document.querySelectorAll('.jenis-filter-aktif:checked')).map(cb => cb.value);
                const selectedKondisi = Array.from(document.querySelectorAll('.kondisi-filter-aktif:checked')).map(cb => cb.value);
                const tahun = document.getElementById('tahunFilterAktif')?.value.trim() || '';
                const ruangan = document.getElementById('ruanganFilterAktif')?.value || 'all';
                const sort = document.querySelector('input[name="sortFilterAktif"]:checked')?.value || 'terbaru';
                const perPage = document.getElementById('perPageSelectAktif')?.value || '10';

                if (search) params.set('search', search);
                selectedJenis.forEach(v => params.append('jenis[]', v));
                selectedKondisi.forEach(v => params.append('kondisi[]', v));
                if (tahun) params.set('tahun', tahun);
                if (ruangan !== 'all') params.set('ruangan', ruangan);
                if (sort !== 'terbaru') params.set('sort', sort);
                if (perPage !== '10') params.set('per_page', perPage);
            } else {
                const search = document.getElementById('searchInputRiwayat')?.value.trim() || '';
                const selectedJenis = Array.from(document.querySelectorAll('.jenis-filter-riwayat:checked')).map(cb => cb.value);
                const tahunHapus = document.getElementById('tahunHapusFilterRiwayat')?.value.trim() || '';
                const tahunPerolehan = document.getElementById('tahunFilterRiwayat')?.value.trim() || '';
                const ruangan = document.getElementById('ruanganFilterRiwayat')?.value || 'all';
                const sort = document.querySelector('input[name="sortFilterRiwayat"]:checked')?.value || 'terbaru';
                const perPage = document.getElementById('perPageSelectRiwayat')?.value || '10';

                if (search) params.set('search', search);
                selectedJenis.forEach(v => params.append('jenis_nama[]', v));
                if (tahunHapus) params.set('tahun_hapus', tahunHapus);
                if (tahunPerolehan) params.set('tahun_perolehan', tahunPerolehan);
                if (ruangan !== 'all') params.set('ruangan_nama', ruangan);
                if (sort !== 'terbaru') params.set('sort', sort);
                if (perPage !== '10') params.set('per_page', perPage);
            }

            const urlParams = new URLSearchParams(window.location.search);
            let activePage = 1;
            const currentUrlTab = urlParams.get('tab') || 'aktif';
            if (currentUrlTab === targetTab) {
                activePage = parseInt(urlParams.get('page')) || 1;
            }
            let currentPage = (extraParams.page !== undefined && extraParams.page !== null)
                ? parseInt(extraParams.page)
                : activePage;

            if (currentPage > 1) {
                params.set('page', currentPage);
            }

            for (const key in extraParams) {
                if (key === 'page') continue;
                if (extraParams[key] !== null && extraParams[key] !== undefined) {
                    params.set(key, extraParams[key]);
                } else {
                    params.delete(key);
                }
            }

            const fetchUrl = "{{ route('penghapusan-aset.index') }}?" + params.toString();
            if (!isAutoRefresh) {
                history.replaceState(null, '', fetchUrl);
                try {
                    sessionStorage.setItem('penghapusan_aset_filter_state', params.toString());
                } catch(e) {}
            }

            const wrap = document.getElementById(targetTab === 'aktif' ? 'table-wrap-aktif' : 'table-wrap-riwayat');
            if (!isAutoRefresh && wrap) wrap.classList.add('is-loading');

            fetch(fetchUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(r => r.json())
            .then(data => {
                if (isAutoRefresh) {
                    // Cek jika user sedang mencentang checkbox saat response tiba, jangan timpa
                    const hasCheckedAktif = document.querySelectorAll('.row-check:checked, #check-all:checked').length > 0;
                    const hasCheckedRiwayat = document.querySelectorAll('.row-check-riwayat:checked, #check-all-riwayat:checked').length > 0;
                    if ((targetTab === 'aktif' && hasCheckedAktif) || (targetTab === 'riwayat' && hasCheckedRiwayat)) {
                        return;
                    }
                }

                if (targetTab === 'aktif') {
                    updateTableAktif(data.items, data.pagination);
                    updatePaginationAktif(data.pagination);
                    if (data.totalActiveCount !== undefined) {
                        const badge = document.getElementById('badge-count-aktif');
                        if (badge) badge.textContent = Number(data.totalActiveCount).toLocaleString('id-ID');
                    }
                } else {
                    updateTableRiwayat(data.items, data.pagination);
                    updatePaginationRiwayat(data.pagination);
                    if (data.stats) updateStatsRiwayat(data.stats);
                    if (data.totalDeletedCount !== undefined) {
                        const badge = document.getElementById('badge-count-riwayat');
                        if (badge) badge.textContent = Number(data.totalDeletedCount).toLocaleString('id-ID');
                    }
                }
                if (typeof feather !== 'undefined') feather.replace();
            })
            .catch(err => console.error('Error fetching penghapusan aset:', err))
            .finally(() => {
                if (wrap) wrap.classList.remove('is-loading');
                if (!isAutoRefresh) {
                    document.querySelectorAll('.dropdown-menu.show').forEach(el => el.classList.remove('show'));
                    document.querySelectorAll('.dropdown.show').forEach(el => el.classList.remove('show'));
                }
            });
        }

        function updateTableAktif(items, pagination) {
            clearSelection();
            const tbody = document.getElementById('tbody-aktif');
            if (!tbody) return;

            if (!items || items.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="${canDeleteAset ? 7 : 5}" style="text-align:center;padding:48px 20px;color:var(--color-text-muted);">
                            <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;">
                                <i data-feather="inbox" style="width:42px;height:42px;stroke-width:1.5;color:var(--color-border-dark);"></i>
                                <p style="margin:0;font-size:13.5px;font-weight:500;">Tidak ada data aset aktif yang sesuai</p>
                            </div>
                        </td>
                    </tr>
                `;
                if (typeof feather !== 'undefined') feather.replace();
                return;
            }

            const firstItem = pagination ? (pagination.firstItem || 1) : 1;
            let html = '';
            items.forEach((item, index) => {
                const itemJson = JSON.stringify({
                    id: item.id,
                    nama: item.nama_barang,
                    kode: item.kode_barang,
                    nup: item.nup || '1',
                    merk: item.merk_type || '-',
                    tahun: item.tahun_perolehan,
                    nilai: item.nilai_perolehan,
                    jenis: item.jenis_nama || 'Aset Desa',
                    ruangan: item.ruangan_nama || '-',
                    kondisi: item.kondisi || '-'
                }).replace(/"/g, '&quot;');

                const formattedNilai = (item.nilai_perolehan !== null && item.nilai_perolehan !== undefined && Number(item.nilai_perolehan) > 0)
                    ? `<span style="font-size: 12.5px; font-weight: 500; color: #111827; line-height: 1.2; white-space: nowrap;">Rp ${Number(item.nilai_perolehan).toLocaleString('id-ID')}</span>`
                    : `<span style="font-size: 12px; color: #9ca3af; font-style: italic; line-height: 1.2;">-</span>`;

                const formattedTahun = (item.tahun_perolehan && item.tahun_perolehan !== '-') ? ('Tahun ' + item.tahun_perolehan) : '-';

                html += `
                    <tr>
                        ${canDeleteAset ? `
                        <td style="text-align: center;">
                            <input
                                type="checkbox"
                                class="row-check"
                                value="${item.id}"
                                data-nama="${(item.nama_barang || '').replace(/"/g, '&quot;')}"
                                data-kode="${(item.kode_barang || '').replace(/"/g, '&quot;')}"
                                data-nup="${item.nup || '1'}"
                                onchange="updateBulkBar()"
                                style="cursor: pointer; accent-color: #1f3a2a; width: 16px; height: 16px;"
                            >
                        </td>` : ''}
                        <td style="text-align: center; font-weight: 500; color: #6b7280;">
                            ${firstItem + index}
                        </td>
                        <td>
                            <div class="aset-cell">
                                <div class="aset-info">
                                    <h6 style="margin: 0 0 4px 0; font-size: 14px; font-weight: 600; color: #111827; line-height: 1.3;">${item.nama_barang}</h6>
                                    <div style="display: flex; align-items: center; gap: 6px; font-size: 13px; color: #4b5563; line-height: 1.2;">
                                        <span style="font-weight: 500; color: #374151;">${item.kode_barang || '-'}</span>
                                        <span style="color: #cbd5e1;">•</span>
                                        <span style="color: #64748b;">NUP: <strong style="font-weight: 600; color: #1e293b;">${item.nup || '1'}</strong></span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span style="font-size: 13px; color: var(--color-text); font-weight: 500;">${item.jenis_nama || 'Aset Tetap Lainnya'}</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="font-size: 13px; color: #1e293b; font-weight: 500;">${item.merk_type || '-'}</span>
                        </td>
                        <td style="text-align: center;">
                            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 2px; text-align: center;">
                                <span style="font-size: 13.5px; font-weight: 600; color: #1f2937; line-height: 1.2;">${formattedTahun}</span>
                                ${formattedNilai}
                            </div>
                        </td>
                        ${canDeleteAset ? `
                        <td style="text-align: center;">
                            <div class="action-btns">
                                <button type="button" class="action-btn delete" onclick="openHapusAsetModal(${itemJson})" title="Proses Penghapusan Resmi">
                                    <i data-feather="trash-2"></i>
                                </button>
                            </div>
                        </td>` : ''}
                    </tr>
                `;
            });
            tbody.innerHTML = html;
        }

        function updatePaginationAktif(pagination) {
            if (!pagination) return;
            const firstItem = pagination.firstItem || 0;
            const lastItem = pagination.lastItem || 0;
            const total = pagination.total || 0;
            const currentPage = pagination.currentPage || 1;
            const lastPage = pagination.lastPage || 1;

            const rangeEl = document.getElementById('paginationRangeInfoAktif');
            if (rangeEl) {
                rangeEl.innerHTML = `Menampilkan <strong>${firstItem}-${lastItem}</strong> dari <strong>${total}</strong> data`;
            }

            const jumpInput = document.getElementById('pageJumpInputAktif');
            if (jumpInput) {
                jumpInput.max = lastPage;
                jumpInput.value = currentPage;
                jumpInput.placeholder = `1–${lastPage}`;
            }

            const nav = document.getElementById('paginationNavAktif');
            if (!nav) return;

            let html = '';
            if (currentPage <= 1) {
                html += `<span class="page-btn disabled" title="Previous"><i data-feather="chevron-left"></i></span>`;
            } else {
                html += `<button type="button" class="page-btn" onclick="goToPage('aktif', ${currentPage - 1})" title="Previous"><i data-feather="chevron-left"></i></button>`;
            }

            let windowStart = Math.max(1, currentPage - 2);
            let windowEnd = Math.min(lastPage, currentPage + 2);
            if ((windowEnd - windowStart + 1) < 5) {
                if (windowStart === 1) {
                    windowEnd = Math.min(lastPage, 5);
                } else {
                    windowStart = Math.max(1, windowEnd - 4);
                }
            }

            for (let p = windowStart; p <= windowEnd; p++) {
                if (p === currentPage) {
                    html += `<span class="page-btn active">${p}</span>`;
                } else {
                    html += `<button type="button" class="page-btn" onclick="goToPage('aktif', ${p})">${p}</button>`;
                }
            }

            if (currentPage >= lastPage) {
                html += `<span class="page-btn disabled" title="Next"><i data-feather="chevron-right"></i></span>`;
            } else {
                html += `<button type="button" class="page-btn" onclick="goToPage('aktif', ${currentPage + 1})" title="Next"><i data-feather="chevron-right"></i></button>`;
            }

            nav.innerHTML = html;
        }

        function updateTableRiwayat(items, pagination) {
            const tbody = document.getElementById('tbody-riwayat');
            if (!tbody) return;

            if (!items || items.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="${canDeleteAset ? '8' : '7'}" style="text-align:center;padding:48px 20px;color:var(--color-text-muted);">
                            <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;">
                                <i data-feather="inbox" style="width:42px;height:42px;stroke-width:1.5;color:var(--color-border-dark);"></i>
                                <p style="margin:0;font-size:13.5px;font-weight:500;">Belum ada riwayat penghapusan aset yang sesuai</p>
                            </div>
                        </td>
                    </tr>
                `;
                if (typeof feather !== 'undefined') feather.replace();
                updateBulkBarRiwayat();
                return;
            }

            const firstItem = pagination ? (pagination.firstItem || 1) : 1;
            let html = '';
            items.forEach((log, index) => {
                const formattedNilai = (log.nilai_perolehan !== null && log.nilai_perolehan !== undefined && Number(log.nilai_perolehan) > 0)
                    ? `<span style="font-size: 12.5px; font-weight: 500; color: #111827; line-height: 1.2; white-space: nowrap;">Rp ${Number(log.nilai_perolehan).toLocaleString('id-ID')}</span>`
                    : `<span style="font-size: 12px; color: #9ca3af; font-style: italic; line-height: 1.2;">-</span>`;

                const formattedTahun = (log.tahun_perolehan && log.tahun_perolehan !== '-') ? ('Tahun ' + log.tahun_perolehan) : '-';

                const checkboxCell = canDeleteAset ? `
                    <td style="text-align: center;">
                        <input type="checkbox" class="row-check-riwayat sima-checkbox" value="${log.id}" data-nama="${escapeHtml(log.nama_barang)}" data-kode="${escapeHtml(log.kode_barang || '-')}" data-nup="${escapeHtml(log.nup || '1')}" data-nilai="${log.nilai_perolehan || 0}" onchange="updateBulkBarRiwayat()" />
                    </td>
                ` : '';

                const restoreBtn = canDeleteAset ? `
                    <button type="button" class="action-btn restore" onclick="openSingleRestoreModal(${log.id}, '${escapeJsQuote(log.nama_barang)}', '${escapeJsQuote(log.kode_barang || '-')}', '${escapeJsQuote(log.nup || '1')}', '${log.nilai_perolehan || 0}')" title="Pulihkan Aset Ini ke Daftar Aset Aktif">
                        <i data-feather="rotate-ccw"></i>
                    </button>
                ` : '';

                html += `
                    <tr>
                        ${checkboxCell}
                        <td style="text-align: center; font-weight: 500; color: #6b7280;">
                            ${firstItem + index}
                        </td>
                        <td>
                            <div class="aset-cell">
                                <div class="aset-info">
                                    <h6 style="margin: 0 0 4px 0; font-size: 14px; font-weight: 600; color: #111827; line-height: 1.3;">${escapeHtml(log.nama_barang)}</h6>
                                    <div style="display: flex; align-items: center; gap: 6px; font-size: 13px; color: #4b5563; line-height: 1.2;">
                                        <span style="font-weight: 500; color: #374151;">${escapeHtml(log.kode_barang || '-')}</span>
                                        <span style="color: #cbd5e1;">•</span>
                                        <span style="color: #64748b;">NUP: <strong style="font-weight: 600; color: #1e293b;">${escapeHtml(log.nup || '1')}</strong></span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span style="font-size: 13px; color: var(--color-text); font-weight: 500;">${escapeHtml(log.jenis_nama || 'Aset Tetap Lainnya')}</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="font-size: 13px; color: #1e293b; font-weight: 500;">${escapeHtml(log.merk_type || '-')}</span>
                        </td>
                        <td style="text-align: center;">
                            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 2px; text-align: center;">
                                <span style="font-size: 13.5px; font-weight: 600; color: #1f2937; line-height: 1.2;">${formattedTahun}</span>
                                ${formattedNilai}
                            </div>
                        </td>
                        <td style="white-space: nowrap;">
                            <span style="font-size: 13px; color: #374151; font-weight: 500;">${escapeHtml(log.tanggal_hapus || '-')}</span>
                        </td>
                        <td style="text-align: center;">
                            <div class="action-btns">
                                <button type="button" class="action-btn view" onclick="openDetailModal(${log.id})" title="Lihat Detail & Dokumen SK">
                                    <i data-feather="eye"></i>
                                </button>
                                ${restoreBtn}
                            </div>
                        </td>
                    </tr>
                `;
            });
            tbody.innerHTML = html;
            updateBulkBarRiwayat();
        }

        function updatePaginationRiwayat(pagination) {
            if (!pagination) return;
            const firstItem = pagination.firstItem || 0;
            const lastItem = pagination.lastItem || 0;
            const total = pagination.total || 0;
            const currentPage = pagination.currentPage || 1;
            const lastPage = pagination.lastPage || 1;

            const rangeEl = document.getElementById('paginationRangeInfoRiwayat');
            if (rangeEl) {
                rangeEl.innerHTML = `Menampilkan <strong>${firstItem}-${lastItem}</strong> dari <strong>${total}</strong> data`;
            }

            const jumpInput = document.getElementById('pageJumpInputRiwayat');
            if (jumpInput) {
                jumpInput.max = lastPage;
                jumpInput.value = currentPage;
                jumpInput.placeholder = `1–${lastPage}`;
            }

            const nav = document.getElementById('paginationNavRiwayat');
            if (!nav) return;

            let html = '';
            if (currentPage <= 1) {
                html += `<span class="page-btn disabled" title="Previous"><i data-feather="chevron-left"></i></span>`;
            } else {
                html += `<button type="button" class="page-btn" onclick="goToPage('riwayat', ${currentPage - 1})" title="Previous"><i data-feather="chevron-left"></i></button>`;
            }

            let windowStart = Math.max(1, currentPage - 2);
            let windowEnd = Math.min(lastPage, currentPage + 2);
            if ((windowEnd - windowStart + 1) < 5) {
                if (windowStart === 1) {
                    windowEnd = Math.min(lastPage, 5);
                } else {
                    windowStart = Math.max(1, windowEnd - 4);
                }
            }

            for (let p = windowStart; p <= windowEnd; p++) {
                if (p === currentPage) {
                    html += `<span class="page-btn active">${p}</span>`;
                } else {
                    html += `<button type="button" class="page-btn" onclick="goToPage('riwayat', ${p})">${p}</button>`;
                }
            }

            if (currentPage >= lastPage) {
                html += `<span class="page-btn disabled" title="Next"><i data-feather="chevron-right"></i></span>`;
            } else {
                html += `<button type="button" class="page-btn" onclick="goToPage('riwayat', ${currentPage + 1})" title="Next"><i data-feather="chevron-right"></i></button>`;
            }

            nav.innerHTML = html;
        }

        function renderKpiTrend(trendData) {
            if (!trendData) return '<span class="kpi-trend-pill neutral">0% (0)</span>';
            const percent = trendData.percent ?? 0;
            const diff = trendData.diff ?? 0;
            const formattedDiff = Number(Math.abs(diff)).toLocaleString('id-ID');

            if (percent > 0 || diff > 0) {
                return `<span class="kpi-trend-pill up">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M12 4l-8 8h16z"/></svg>
                    +${percent}% (+${formattedDiff})
                </span>`;
            } else if (percent < 0 || diff < 0) {
                return `<span class="kpi-trend-pill down">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M12 20l8-8H4z"/></svg>
                    ${percent}% (${diff < 0 ? '-' : ''}${formattedDiff})
                </span>`;
            } else {
                return '<span class="kpi-trend-pill neutral">0% (0)</span>';
            }
        }

        function updateCardTrend(cardEl, trendData) {
            if (!cardEl) return;
            const wrapper = cardEl.querySelector('.kpi-trend-wrapper');
            if (!wrapper) return;
            const label = wrapper.querySelector('.kpi-trend-label');
            const labelHtml = label ? label.outerHTML : '<span class="kpi-trend-label">vs. bulan lalu</span>';
            wrapper.innerHTML = renderKpiTrend(trendData) + labelHtml;
        }

        function updateStatsRiwayat(stats) {
            if (!stats) return;
            const cards = document.querySelectorAll('.stat-card-row .stat-card-sima, .stats-grid .stat-card');
            if (cards[0]) {
                const val = cards[0].querySelector('.stat-card-value');
                if (val && stats.totalDihapus !== undefined) {
                    val.textContent = Number(stats.totalDihapus).toLocaleString('id-ID');
                }
                if (stats.trends && stats.trends.totalDihapus) {
                    updateCardTrend(cards[0], stats.trends.totalDihapus);
                }
            }
            if (cards[1]) {
                const val = cards[1].querySelector('.stat-card-value');
                if (val && stats.totalNilai !== undefined) {
                    val.textContent = 'Rp ' + Number(stats.totalNilai).toLocaleString('id-ID');
                }
            }
            if (cards[2]) {
                const val = cards[2].querySelector('.stat-card-value');
                if (val && stats.dihapusTahunIni !== undefined) {
                    val.textContent = Number(stats.dihapusTahunIni).toLocaleString('id-ID');
                }
            }
            if (cards[3]) {
                const val = cards[3].querySelector('.stat-card-value');
                if (val && stats.topKategoriNama !== undefined) {
                    val.textContent = stats.topKategoriNama;
                    val.title = stats.topKategoriNama;
                }
                const pill = cards[3].querySelector('.kpi-trend-pill');
                if (pill && stats.topKategoriTotal !== undefined) {
                    pill.textContent = stats.topKategoriTotal + ' Unit';
                }
            }
        }

        function changePerPage(tab, limit) {
            applyFilterParams(tab, { per_page: limit, page: 1 });
        }

        function goToPage(tab, pageNum) {
            if (!pageNum || pageNum < 1) return;
            applyFilterParams(tab, { page: pageNum });
        }

        function openDetailModal(id) {
            const modal = document.getElementById('modal-detail-penghapusan');
            const body = document.getElementById('detail-modal-body');
            const downloadBtn = document.getElementById('detail-btn-download-pdf');
            modal.style.display = 'flex';
            body.innerHTML = `
                <div style="text-align:center;padding:40px 0;color:#9ca3af;">
                    <div style="font-size:14px;">Memuat data detail...</div>
                </div>
            `;
            downloadBtn.style.display = 'none';

            fetch(`{{ url('penghapusan-aset') }}/${id}`)
                .then(r => r.json())
                .then(res => {
                    if (!res.success) {
                        body.innerHTML = `<div class="alert alert-danger">Gagal memuat data.</div>`;
                        return;
                    }
                    const d = res.data;
                    let pdfHtml = '';
                    if (d.dokumen_pdf_url) {
                        downloadBtn.href = d.download_url;
                        downloadBtn.style.display = 'inline-flex';
                        pdfHtml = `
                            <div style="margin-top:20px;">
                                <div style="font-size:14px;font-weight:700;color:#1e293b;margin-bottom:10px;">Dokumen SK / Berita Acara:</div>
                                <div style="display:flex;align-items:center;justify-content:space-between;background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:14px 18px;">
                                    <div style="display:flex;align-items:center;gap:14px;">
                                        <i data-feather="file-text" style="width:24px;height:24px;color:#1f3a2a;"></i>
                                        <div>
                                            <div style="font-size:14px;font-weight:600;color:#111827;">${d.dokumen_pdf_name || 'Dokumen_Bukti.pdf'}</div>
                                            <div style="font-size:12.5px;color:#64748b;">Dokumen Berkas Tersimpan</div>
                                        </div>
                                    </div>
                                    <a href="${d.download_url}" class="btn-pdf-link" style="padding:8px 18px;background:var(--color-primary, #1f3a2a);color:#fff;border-radius: var(--radius-full);text-decoration:none;font-weight:600;font-size:13.5px;display:inline-flex;align-items:center;gap:6px;">
                                        <i data-feather="download" style="width:14px;height:14px;"></i> Unduh Berkas
                                    </a>
                                </div>
                            </div>
                        `;
                    } else {
                        pdfHtml = `<div style="margin-top:20px;color:#9ca3af;font-size:13.5px;">Tidak ada lampiran dokumen PDF / Foto.</div>`;
                    }

                    body.innerHTML = `
                        <div style="display:flex;flex-direction:column;gap:14px;font-size:15px;">
                            <div style="display:flex;align-items:flex-start;">
                                <span style="color:#4b5563;width:160px;flex-shrink:0;font-weight:500;">Nama Aset</span>
                                <span style="color:#4b5563;margin-right:16px;">:</span>
                                <span style="color:#111827;font-weight:500;">${d.nama_barang}</span>
                            </div>
                            <div style="display:flex;align-items:center;">
                                <span style="color:#4b5563;width:160px;flex-shrink:0;font-weight:500;">Kode Barang</span>
                                <span style="color:#4b5563;margin-right:16px;">:</span>
                                <span style="color:#111827;font-weight:500;">${d.kode_barang}</span>
                            </div>
                            <div style="display:flex;align-items:center;">
                                <span style="color:#4b5563;width:160px;flex-shrink:0;font-weight:500;">NUP / Tahun</span>
                                <span style="color:#4b5563;margin-right:16px;">:</span>
                                <span style="color:#111827;font-weight:500;">NUP ${d.nup} / Tahun ${d.tahun_perolehan}</span>
                            </div>
                            <div style="display:flex;align-items:center;">
                                <span style="color:#4b5563;width:160px;flex-shrink:0;font-weight:500;">Jenis Inventaris</span>
                                <span style="color:#4b5563;margin-right:16px;">:</span>
                                <span style="color:#111827;font-weight:500;">${d.jenis_inventaris_nama}</span>
                            </div>
                            <div style="display:flex;align-items:center;">
                                <span style="color:#4b5563;width:160px;flex-shrink:0;font-weight:500;">Merk / Type</span>
                                <span style="color:#4b5563;margin-right:16px;">:</span>
                                <span style="color:#111827;font-weight:500;">${d.merk_type || '-'}</span>
                            </div>
                            <div style="display:flex;align-items:center;">
                                <span style="color:#4b5563;width:160px;flex-shrink:0;font-weight:500;">Ruangan</span>
                                <span style="color:#4b5563;margin-right:16px;">:</span>
                                <span style="color:#111827;font-weight:500;">${d.ruangan_nama || '-'}</span>
                            </div>
                            <div style="display:flex;align-items:center;">
                                <span style="color:#4b5563;width:160px;flex-shrink:0;font-weight:500;">Nilai Perolehan</span>
                                <span style="color:#4b5563;margin-right:16px;">:</span>
                                <span style="color:#111827;font-weight:500;">${d.nilai_perolehan}</span>
                            </div>
                            <div style="display:flex;align-items:center;">
                                <span style="color:#4b5563;width:160px;flex-shrink:0;font-weight:500;">Tanggal Dihapus</span>
                                <span style="color:#4b5563;margin-right:16px;">:</span>
                                <span style="color:#111827;font-weight:500;">${d.tanggal_hapus}</span>
                            </div>
                        </div>

                        ${pdfHtml}
                    `;
                    if (typeof feather !== 'undefined') feather.replace();
                })
                .catch(() => {
                    body.innerHTML = `<div class="alert alert-danger">Gagal menghubungi server.</div>`;
                });
        }

        function closeDetailModal() {
            document.getElementById('modal-detail-penghapusan').style.display = 'none';
        }

        function setupYearOnlyPicker(inputId, clearBtnId) {
            const input = document.getElementById(inputId);
            const clearBtn = document.getElementById(clearBtnId);
            if (!input) return;

            const minYear = 1900;
            const maxYear = new Date().getFullYear() + 1;

            function ensureYearPanel(instance) {
                const cal = instance.calendarContainer;
                if (!cal) return;
                cal.classList.add('year-only-picker');
                cal.classList.add('dropdown-menu'); // prevents component.js drpclose
                cal.addEventListener('click', function(e) {
                    e.stopPropagation();
                });

                if (cal._yearPickerState) {
                    renderYearGrid(instance, cal._yearPickerState, minYear, maxYear);
                    return;
                }

                const panel = document.createElement('div');
                panel.className = 'year-picker-panel';
                panel.innerHTML = `
                    <button type="button" class="year-picker-all-btn">
                        <span>Semua Tahun (Seluruh Data)</span>
                    </button>
                    <div class="year-picker-header">
                        <button type="button" class="year-picker-nav" data-nav="prev" aria-label="Sebelumnya">&lsaquo;</button>
                        <span class="year-picker-range"></span>
                        <button type="button" class="year-picker-nav" data-nav="next" aria-label="Berikutnya">&rsaquo;</button>
                    </div>
                    <div class="year-picker-grid"></div>
                `;
                cal.appendChild(panel);

                const state = {
                    panel,
                    range: panel.querySelector('.year-picker-range'),
                    grid: panel.querySelector('.year-picker-grid'),
                    prev: panel.querySelector('[data-nav="prev"]'),
                    next: panel.querySelector('[data-nav="next"]'),
                    allBtn: panel.querySelector('.year-picker-all-btn'),
                    pageStart: Math.max(minYear, maxYear - 11),
                    selectedYear: parseInt(input.value) || null,
                };
                cal._yearPickerState = state;

                state.allBtn.addEventListener('click', function(ev) {
                    ev.preventDefault();
                    ev.stopPropagation();
                    input.value = '';
                    state.selectedYear = null;
                    if (clearBtn) clearBtn.style.display = 'none';
                    instance.close();
                });

                state.prev.addEventListener('click', function(ev) {
                    ev.preventDefault();
                    ev.stopPropagation();
                    state.pageStart = Math.max(minYear, state.pageStart - 12);
                    renderYearGrid(instance, state, minYear, maxYear);
                });

                state.next.addEventListener('click', function(ev) {
                    ev.preventDefault();
                    ev.stopPropagation();
                    const maxStart = Math.max(minYear, maxYear - 11);
                    state.pageStart = Math.min(maxStart, state.pageStart + 12);
                    renderYearGrid(instance, state, minYear, maxYear);
                });

                state.grid.addEventListener('click', function(ev) {
                    const btn = ev.target.closest('.year-picker-option');
                    if (!btn) return;
                    ev.preventDefault();
                    ev.stopPropagation();
                    const yr = parseInt(btn.dataset.year, 10);
                    if (!Number.isFinite(yr)) return;
                    input.value = String(yr);
                    state.selectedYear = yr;
                    if (clearBtn) clearBtn.style.display = 'inline-flex';
                    instance.close();
                });

                renderYearGrid(instance, state, minYear, maxYear);
            }

            function renderYearGrid(instance, state, minYear, maxYear) {
                const start = state.pageStart;
                const end = Math.min(start + 11, maxYear);

                state.range.textContent = `${start} - ${end}`;
                state.prev.disabled = start <= minYear;
                state.next.disabled = end >= maxYear;

                const curVal = parseInt(input.value) || null;
                state.selectedYear = curVal;

                let html = '';
                for (let yr = start; yr <= end; yr++) {
                    const isActive = yr === state.selectedYear ? ' active' : '';
                    html += `<button type="button" class="year-picker-option${isActive}" data-year="${yr}">${yr}</button>`;
                }
                state.grid.innerHTML = html;
            }

            if (typeof flatpickr !== 'undefined') {
                const fp = flatpickr(input, {
                    dateFormat: 'Y',
                    disableMobile: true,
                    allowInput: false,
                    minDate: `${minYear}-01-01`,
                    maxDate: `${maxYear}-12-31`,
                    position: 'auto left',
                    onReady: function(_, __, instance) {
                        ensureYearPanel(instance);
                    },
                    onOpen: function(_, __, instance) {
                        const val = parseInt(input.value);
                        if (val && Number.isFinite(val)) {
                            const maxStart = Math.max(minYear, maxYear - 11);
                            if (instance.calendarContainer && instance.calendarContainer._yearPickerState) {
                                instance.calendarContainer._yearPickerState.pageStart = Math.min(maxStart, Math.max(minYear, val - 5));
                            }
                        }
                        ensureYearPanel(instance);
                    }
                });

                if (clearBtn) {
                    clearBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        input.value = '';
                        clearBtn.style.display = 'none';
                        if (fp.calendarContainer && fp.calendarContainer._yearPickerState) {
                            fp.calendarContainer._yearPickerState.selectedYear = null;
                        }
                    });
                }
            }
        }

        function getNavigationType() {
            try {
                const entries = performance.getEntriesByType('navigation');
                if (entries && entries.length > 0) {
                    return entries[0].type;
                }
                if (window.performance && window.performance.navigation) {
                    const type = window.performance.navigation.type;
                    if (type === 1) return 'reload';
                    if (type === 2) return 'back_forward';
                    return 'navigate';
                }
            } catch(e) {}
            return 'navigate';
        }

        function restoreFilterState() {
            const navType = getNavigationType();

            @if(session('success'))
            try { sessionStorage.removeItem('penghapusan_aset_filter_state'); } catch(e) {}
            @endif

            let isReturning = false;
            try {
                if (sessionStorage.getItem('penghapusan_aset_returning') === '1') {
                    isReturning = true;
                    sessionStorage.removeItem('penghapusan_aset_returning');
                }
            } catch(e) {}

            if (navType === 'back_forward') {
                isReturning = true;
            }

            // KASUS 1: Manual Refresh (F5 / Reload) saat di index
            if (navType === 'reload' && !isReturning) {
                try { sessionStorage.removeItem('penghapusan_aset_filter_state'); } catch(e) {}
                if (window.location.search) {
                    history.replaceState(null, '', window.location.pathname);
                }
                resetFilters('aktif');
                resetFilters('riwayat');
                return;
            }

            // KASUS 2: Navigasi biasa tanpa query
            if (!isReturning && navType === 'navigate' && !window.location.search) {
                try { sessionStorage.removeItem('penghapusan_aset_filter_state'); } catch(e) {}
                return;
            }

            // KASUS 3: Restorasi query
            let currentSearch = window.location.search;
            let hasUrlQuery = (currentSearch && currentSearch.length > 1);

            if (!hasUrlQuery) {
                try {
                    const saved = sessionStorage.getItem('penghapusan_aset_filter_state');
                    if (saved && saved.length > 0) {
                        currentSearch = '?' + saved;
                        history.replaceState(null, '', window.location.pathname + currentSearch);
                        hasUrlQuery = true;
                    }
                } catch(e) {}
            }

            if (!hasUrlQuery) return;

            const urlParams = new URLSearchParams(currentSearch);
            const targetTab = urlParams.get('tab') || 'aktif';
            if (typeof switchTab === 'function') {
                switchTab(targetTab);
            }

            if (targetTab === 'aktif') {
                const q = urlParams.get('search') || '';
                const searchInput = document.getElementById('searchInputAktif');
                if (searchInput) {
                    searchInput.value = q;
                    toggleSearchClear('aktif', searchInput);
                }
                const jenisParams = urlParams.getAll('jenis[]');
                document.querySelectorAll('.jenis-filter-aktif').forEach(cb => {
                    cb.checked = jenisParams.includes(cb.value);
                });
                const kondisiParams = urlParams.getAll('kondisi[]');
                document.querySelectorAll('.kondisi-filter-aktif').forEach(cb => {
                    cb.checked = kondisiParams.includes(cb.value);
                });
                const thn = urlParams.get('tahun') || '';
                const tf = document.getElementById('tahunFilterAktif');
                const clearTBtn = document.getElementById('clearTahunFilterBtnAktif');
                if (tf) {
                    tf.value = thn;
                    if (clearTBtn) clearTBtn.style.display = thn ? 'inline-flex' : 'none';
                }
                const ruangan = urlParams.get('ruangan') || 'all';
                const rf = document.getElementById('ruanganFilterAktif');
                if (rf) rf.value = ruangan;
                const sort = urlParams.get('sort') || 'terbaru';
                const sr = document.querySelector('input[name="sortFilterAktif"][value="'+sort+'"]');
                if (sr) sr.checked = true;
                const perPage = urlParams.get('per_page');
                const pp = document.getElementById('perPageSelectAktif');
                if (pp && perPage) pp.value = perPage;
            } else {
                const q = urlParams.get('search') || '';
                const searchInput = document.getElementById('searchInputRiwayat');
                if (searchInput) {
                    searchInput.value = q;
                    toggleSearchClear('riwayat', searchInput);
                }
                const jenisParams = urlParams.getAll('jenis_nama[]');
                document.querySelectorAll('.jenis-filter-riwayat').forEach(cb => {
                    cb.checked = jenisParams.includes(cb.value);
                });
                const thnHapus = urlParams.get('tahun_hapus') || '';
                const tfHapus = document.getElementById('tahunHapusFilterRiwayat');
                const clearTHapusBtn = document.getElementById('clearTahunHapusFilterBtnRiwayat');
                if (tfHapus) {
                    tfHapus.value = thnHapus;
                    if (clearTHapusBtn) clearTHapusBtn.style.display = thnHapus ? 'inline-flex' : 'none';
                }
                const thnPerolehan = urlParams.get('tahun_perolehan') || '';
                const tfPerolehan = document.getElementById('tahunFilterRiwayat');
                const clearTPerolehanBtn = document.getElementById('clearTahunFilterBtnRiwayat');
                if (tfPerolehan) {
                    tfPerolehan.value = thnPerolehan;
                    if (clearTPerolehanBtn) clearTPerolehanBtn.style.display = thnPerolehan ? 'inline-flex' : 'none';
                }
                const ruangan = urlParams.get('ruangan_nama') || 'all';
                const rf = document.getElementById('ruanganFilterRiwayat');
                if (rf) rf.value = ruangan;
                const sort = urlParams.get('sort') || 'terbaru';
                const sr = document.querySelector('input[name="sortFilterRiwayat"][value="'+sort+'"]');
                if (sr) sr.checked = true;
                const perPage = urlParams.get('per_page');
                const pp = document.getElementById('perPageSelectRiwayat');
                if (pp && perPage) pp.value = perPage;
            }

            const page = parseInt(urlParams.get('page')) || 1;
            document.querySelectorAll('.table-loading-wrap').forEach(el => el.classList.remove('is-loading'));
            applyFilterParams(targetTab, { page: page }, true);
        }

        document.addEventListener('DOMContentLoaded', function() {
            setupYearOnlyPicker('tahunFilterAktif', 'clearTahunFilterBtnAktif');
            setupYearOnlyPicker('tahunHapusFilterRiwayat', 'clearTahunHapusFilterBtnRiwayat');
            setupYearOnlyPicker('tahunFilterRiwayat', 'clearTahunFilterBtnRiwayat');
            document.querySelectorAll('.table-loading-wrap').forEach(el => el.classList.remove('is-loading'));
            restoreFilterState();

            // Auto refresh silent (15 detik)
            const autoRefreshMs = 15000;
            setInterval(function() {
                if (document.hidden) return;
                // Jangan auto-refresh jika user sedang memilih checkbox di tab manapun
                if (document.querySelectorAll('.row-check:checked, .row-check-riwayat:checked, #check-all:checked, #check-all-riwayat:checked').length > 0) return;
                // Jangan auto-refresh jika modal sedang terbuka
                if (document.querySelector('.modal.show, [id*="modal"][style*="display: flex"], [id*="modal"][style*="display: block"]')) return;
                applyFilterParams(currentTab, {}, true);
            }, autoRefreshMs);
        });

        window.addEventListener('popstate', function() {
            document.querySelectorAll('.table-loading-wrap').forEach(el => el.classList.remove('is-loading'));
            restoreFilterState();
        });

        window.addEventListener('pageshow', function(event) {
            document.querySelectorAll('.table-loading-wrap').forEach(el => el.classList.remove('is-loading'));
            if (event.persisted) {
                restoreFilterState();
            }
        });
    </script>
</body>
</html>
