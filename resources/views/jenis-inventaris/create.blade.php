<!doctype html>
<html lang="en" data-pc-preset="{{ $pc_preset_theme }}" data-pc-theme="{{ $pc_dark_layout }}">
  <head>
    @include('layouts.head-page-meta', ['title' => 'Tambah Jenis Inventaris'])
    @include('layouts.head-css')
    <style>
      /* Form Styling */
      .form-section {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 16px;
      }
      .form-section-title {
        font-size: 14px;
        font-weight: 600;
        color: #064635;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
      }
      .form-section-title i {
        width: 18px;
        height: 18px;
      }
      .form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
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
      .form-full {
        grid-column: 1 / -1;
      }

      /* Card Header */
      .card-header-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        min-width: 44px;
        border-radius: 10px;
        background: #519259;
        margin-right: 12px;
        position: relative !important;
        flex-shrink: 0;
      }
      .card-header-icon svg,
      .card-header-icon i {
        color: #fff !important;
        stroke: #fff !important;
        width: 20px;
        height: 20px;
        position: relative !important;
        top: auto !important;
        left: auto !important;
      }

      /* Pejabat Multi-select */
      .pejabat-select-area {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        background: #fff;
        overflow: hidden;
      }
      .pejabat-select-header {
        background: #f4faf5;
        padding: 12px 16px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
      }
      .pejabat-select-header-title {
        font-size: 13px;
        font-weight: 600;
        color: #374151;
      }
      .pejabat-count {
        font-size: 12px;
        background: #064635;
        color: #fff;
        padding: 2px 10px;
        border-radius: 12px;
      }
      .pejabat-list {
        max-height: 240px;
        overflow-y: auto;
      }
      .pejabat-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        border-bottom: 1px solid #f1f5f9;
        cursor: pointer;
        transition: all 0.15s;
      }
      .pejabat-item:last-child {
        border-bottom: none;
      }
      .pejabat-item:hover {
        background: #f4faf5;
      }
      .pejabat-item.selected {
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        border-left: 3px solid #3b82f6;
      }
      .pejabat-item input[type="checkbox"] {
        width: 18px;
        height: 18px;
        accent-color: #064635;
        cursor: pointer;
      }
      .pejabat-info {
        flex: 1;
      }
      .pejabat-nama {
        font-size: 14px;
        font-weight: 500;
        color: #374151;
      }
      .pejabat-jabatan {
        font-size: 12px;
        color: #6b7280;
      }
      .pejabat-order {
        display: none;
        align-items: center;
        gap: 4px;
      }
      .pejabat-item.selected .pejabat-order {
        display: flex;
      }
      .order-badge {
        width: 24px;
        height: 24px;
        border-radius: 6px;
        background: #064635;
        color: #fff;
        font-size: 12px;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
      }
      .order-controls {
        display: flex;
        flex-direction: column;
        gap: 2px;
      }
      .order-btn {
        width: 20px;
        height: 16px;
        border: none;
        background: #e5e7eb;
        border-radius: 3px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s;
      }
      .order-btn:hover {
        background: #d1d5db;
      }
      .order-btn i {
        width: 12px;
        height: 12px;
        color: #374151;
      }

      /* Dynamic Fields Section */
      .fields-container {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        background: #fff;
        overflow: hidden;
      }
      .fields-header {
        background: linear-gradient(135deg, #064635 0%, #519259 100%);
        padding: 12px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
      }
      .fields-header-title {
        font-size: 13px;
        font-weight: 600;
        color: #fff;
        display: flex;
        align-items: center;
        gap: 8px;
      }
      .fields-header-title i {
        width: 16px;
        height: 16px;
      }
      .btn-add-field {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        background: rgba(255,255,255,0.2);
        border: 1px solid rgba(255,255,255,0.3);
        border-radius: 6px;
        color: #fff;
        font-size: 12px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
      }
      .btn-add-field:hover {
        background: rgba(255,255,255,0.3);
      }
      .btn-add-field i {
        width: 14px;
        height: 14px;
      }
      .fields-body {
        padding: 0;
      }
      .fields-empty {
        padding: 40px 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: #9ca3af;
      }
      .fields-empty-icon {
        width: 48px;
        height: 48px;
        margin-bottom: 12px;
        background: #f1f5f9;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
      }
      .fields-empty-icon i {
        width: 24px;
        height: 24px;
        color: #9ca3af;
      }
      .fields-empty-text {
        font-size: 14px;
        margin-bottom: 4px;
      }
      .fields-empty-hint {
        font-size: 12px;
      }
      .field-item {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        padding: 16px 10px;
        border-bottom: 1px solid #e5e7eb;
        background: #fff;
        transition: all 0.15s;
      }
      .field-item:hover {
        background: #f4faf5;
      }
      .field-item:last-child {
        border-bottom: none;
      }
      .field-drag {
        display: none;
      }
      .field-drag:active {
        cursor: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"><g fill="%2304A9F5"><circle cx="5" cy="7" r="1.2"/><circle cx="14" cy="5" r="1.2"/><circle cx="17" cy="11" r="1.2"/><circle cx="13" cy="17" r="1.2"/><circle cx="6" cy="17" r="1.2"/><circle cx="3" cy="11" r="1.2"/><path d="M 8 10 L 8 14 M 12 10 L 12 14 M 10 9 L 10 15" stroke="%2304A9F5" stroke-width="1.5" fill="%2304A9F5" stroke-linecap="round"/><path d="M 6 9 Q 4 9 4 11 L 4 17 Q 4 18 5 18 L 15 18 Q 16 18 16 17 L 16 10 Q 16 8 14 8" stroke="%2304A9F5" stroke-width="1.2" fill="none" stroke-linecap="round"/></g></svg>') 10 10, grabbing;
      }
      .field-drag i {
        width: 16px;
        height: 16px;
      }
      /* Nomor sejajar dengan baris atas (label+tipe) */
      .field-order-num {
        width: 24px;
        height: 24px;
        border-radius: 6px;
        background: #e5e7eb;
        color: #374151;
        font-size: 11px;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        margin-top: 8px;
      }
      .field-inputs {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 6px;
        min-width: 0;
      }
      /* Baris atas: label + tipe */
      .field-inputs-row {
        display: grid;
        grid-template-columns: 1fr 160px;
        gap: 8px;
        align-items: center;
      }
      /* Baris bawah: checkbox Wajib diisi + Masuk export */
      .field-toggles {
        display: flex;
        flex-direction: row;
        gap: 8px;
        align-items: center;
      }
      .field-input {
        padding: 8px 12px;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        font-size: 13px;
        transition: all 0.15s;
      }
      .field-input:focus {
        outline: none;
        border-color: #064635;
        box-shadow: 0 0 0 2px rgba(6, 70, 53, 0.1);
      }
      .field-input.field-select {
        background: #fff;
        cursor: pointer;
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
      }
      /* Pakem: mengacu checkbox di section Dropdown */
      .field-required-toggle,
      .field-export-toggle {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        font-weight: 500;
        cursor: pointer;
        user-select: none;
        white-space: nowrap;
      }
      .field-required-toggle input,
      .field-export-toggle input {
        width: 14px;
        height: 14px;
        margin: 0;
        accent-color: #064635;
        cursor: pointer;
      }
      .field-required-toggle span,
      .field-export-toggle span {
        line-height: 1;
      }
      /* Tombol hapus sejajar atas dengan baris label */
      .field-actions {
        display: flex;
        gap: 4px;
        flex-shrink: 0;
        margin-top: 4px;
      }
      .field-action-btn {
        width: 32px;
        height: 32px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s;
        background: transparent;
      }
      .field-action-btn i {
        width: 18px;
        height: 18px;
      }
      .field-action-btn.delete {
        background: transparent;
        color: #dc2626;
      }
      .field-action-btn.delete:hover {
        color: #b91c1c;
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
        color: #d97706 !important;
        stroke: #d97706 !important;
        flex-shrink: 0;
        width: 18px;
        height: 18px;
      }

      /* Order Section */
      .order-section {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 16px;
      }
      .order-section-title {
        font-size: 14px;
        font-weight: 600;
        color: #064635;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
      }
      .order-section-title i {
        width: 18px;
        height: 18px;
      }
      .order-hint {
        font-size: 12px;
        color: #92400e;
        margin-bottom: 16px;
        padding: 10px 12px;
        background: #fef3c7;
        border: 1px solid #fbbf24;
        border-radius: 6px;
        display: flex;
        align-items: flex-start;
        gap: 8px;
      }
      .order-hint i {
        color: #d97706;
        stroke: #d97706;
        flex-shrink: 0;
        margin-top: 2px;
        width: 16px;
        height: 16px;
      }
      .order-list {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 12px;
        min-height: 300px;
      }
      /* Wrapper untuk semua row — single column */
      .order-rows-wrapper {
        display: flex;
        flex-direction: column;
        gap: 8px;
      }
      /* Tiap row: nomor badge + field item berdampingan */
      .order-row {
        display: flex;
        align-items: stretch;
        gap: 12px;
      }
      /* Nomor badge otomatis mengikuti tinggi field item */
      .order-numbers-column {
        border-right: 2px solid #e5e7eb;
        padding-right: 12px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        align-self: stretch;
      }
      .order-number-badge {
        width: 56px;
        flex-shrink: 0;
        border-radius: 8px;
        background: linear-gradient(135deg, #064635 0%, #519259 100%);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 16px;
        transition: all 0.2s;
      }
      .order-number-badge.disabled {
        background: linear-gradient(135deg, #9ca3af 0%, #6b7280 100%);
        opacity: 0.5;
      }
      .order-fields-column {
        display: flex;
        flex-direction: column;
        gap: 8px;
      }
      .order-item {
        flex: 1;
        min-width: 0;
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        padding-right: 52px;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-left: 3px solid #e5e7eb;
        border-radius: 8px;
        cursor: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24'%3E%3Cpath d='M18 11V8.5a1.5 1.5 0 0 0-3 0V11m0 0V7.5a1.5 1.5 0 0 0-3 0V11m0 0V8a1.5 1.5 0 0 0-3 0v7l-1.5-2.5a1.5 1.5 0 0 0-2.6 1.5L8 20a6 6 0 0 0 6 4h1a5 5 0 0 0 5-5v-8' stroke='%233f4d67' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round' fill='none'/%3E%3C/svg%3E") 12 4, grab;
        transition: all 0.2s;
        min-height: 56px;
        position: relative;
      }
      .order-item:active {
        cursor: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24'%3E%3Cpath d='M9 11V6a1.5 1.5 0 0 1 3 0v5m0 0V5.5a1.5 1.5 0 0 1 3 0V11m0 0V8a1.5 1.5 0 0 1 3 0v8a6 6 0 0 1-6 6h-1a5 5 0 0 1-4.9-4l-1.1-4a1.5 1.5 0 0 1 2.6-1.5L9 16V11' stroke='%233f4d67' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round' fill='none'/%3E%3C/svg%3E") 12 4, grabbing;
      }
      .order-item:hover {
        border-left-color: #064635;
      }
      .order-item.highlighted {
        border-left-color: #3b82f6;
        border-left-width: 4px;
        background: linear-gradient(135deg, #dbeafe 0%, #eff6ff 100%);
        box-shadow: 0 2px 8px rgba(59, 130, 246, 0.2);
      }
      @keyframes pulse-highlight {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.9; }
      }
      .order-item.highlighted .order-item-actions {
        display: flex;
        opacity: 1;
      }
      .order-item.sortable-ghost {
        opacity: 0.4;
        background: #e5e7eb;
      }
      .order-item.sortable-drag {
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
      }
      .order-drag-handle {
        color: #9ca3af;
        cursor: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"><g fill="%2304A9F5"><circle cx="5" cy="8" r="1.5"/><circle cx="15" cy="6" r="1.5"/><circle cx="18" cy="12" r="1.5"/><circle cx="14" cy="18" r="1.5"/><circle cx="6" cy="18" r="1.5"/><circle cx="2" cy="12" r="1.5"/><path d="M 10 10 Q 5 8 5 8 Q 3 8 3 10 L 3 18 Q 3 19 4 19 L 16 19 Q 17 19 17 18 L 17 12 Q 17 10 15 10 Q 15 10 10 10" stroke="%2304A9F5" stroke-width="1.2" fill="none"/></g></svg>') 10 10, grab;
        display: flex;
        align-items: center;
      }
      .order-drag-handle:active {
        cursor: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"><g fill="%2304A9F5"><circle cx="5" cy="7" r="1.2"/><circle cx="14" cy="5" r="1.2"/><circle cx="17" cy="11" r="1.2"/><circle cx="13" cy="17" r="1.2"/><circle cx="6" cy="17" r="1.2"/><circle cx="3" cy="11" r="1.2"/><path d="M 8 10 L 8 14 M 12 10 L 12 14 M 10 9 L 10 15" stroke="%2304A9F5" stroke-width="1.5" fill="%2304A9F5" stroke-linecap="round"/><path d="M 6 9 Q 4 9 4 11 L 4 17 Q 4 18 5 18 L 15 18 Q 16 18 16 17 L 17 10 Q 16 8 14 8" stroke="%2304A9F5" stroke-width="1.2" fill="none" stroke-linecap="round"/></g></svg>') 10 10, grabbing;
      }
      .order-drag-handle i {
        width: 18px;
        height: 18px;
      }
      .order-number {
        display: none;
      }
      .order-item-content {
        flex: 1;
        display: flex;
        align-items: center;
        gap: 10px;
      }
      .order-item-label {
        flex: 1;
        font-size: 14px;
        font-weight: 500;
        color: #374151;
      }
      .order-item-badge {
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        flex-shrink: 0;
      }
      .order-item-badge.fixed {
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
        display: flex;
        align-items: center;
        gap: 4px;
      }
      .order-item-badge.fixed i {
        width: 12px;
        height: 12px;
      }
      .order-item-badge.custom {
        background: #dbeafe;
        color: #1e40af;
        border: 1px solid #93c5fd;
      }
      .order-item.disabled {
        opacity: 0.5;
        background: #f3f4f6;
      }
      .order-item.disabled .order-item-label {
        text-decoration: line-through;
        color: #9ca3af;
      }
      .order-item-checkbox {
        width: 20px;
        height: 20px;
        accent-color: #064635;
        cursor: pointer;
      }
      .order-item-required {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 12px;
        font-weight: 500;
        color: #9ca3af;
        white-space: nowrap;
        cursor: pointer;
        user-select: none;
      }
      .order-item-required input[type="checkbox"] {
        width: 14px;
        height: 14px;
        accent-color: #064635;
        cursor: pointer;
        margin: 0;
      }
      /* Container untuk Wajib diisi + Masuk export di order list */
      .order-item-flags {
        display: flex;
        flex-direction: column;
        gap: 5px;
        margin-right: 52px;
        flex-shrink: 0;
      }
      .order-item-export {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 12px;
        font-weight: 500;
        color: #9ca3af;
        white-space: nowrap;
        cursor: pointer;
        user-select: none;
      }
      .order-item-export input[type="checkbox"] {
        width: 14px;
        height: 14px;
        accent-color: #064635;
        cursor: pointer;
        margin: 0;
      }
      .order-item-actions {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        display: none;
        align-items: center;
        gap: 8px;
        opacity: 0;
        transition: opacity 0.2s;
        z-index: 10;
      }
      .order-item:hover .order-item-actions {
        display: flex;
        opacity: 1;
      }
      .order-move-btns {
        display: flex;
        flex-direction: column;
        gap: 4px;
      }
      .order-move-btn {
        width: 28px;
        height: 28px;
        border: 1px solid #e5e7eb;
        background: #fff;
        border-radius: 6px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s;
        flex-shrink: 0;
      }
      .order-move-btn:hover {
        background: #f3f4f6;
        border-color: #064635;
      }
      .order-move-btn:disabled {
        cursor: not-allowed;
        opacity: 0.3;
      }
      .order-move-btn i {
        width: 14px;
        height: 14px;
        color: #374151;
      }
      .order-empty {
        text-align: center;
        padding: 60px 20px;
        color: #9ca3af;
      }
      .order-empty i {
        width: 48px;
        height: 48px;
        margin-bottom: 12px;
        opacity: 0.5;
      }
      .order-empty-text {
        font-size: 14px;
        margin-bottom: 4px;
      }
      .order-empty-hint {
        font-size: 12px;
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
      .btn-test {
        padding: 10px 24px;
        border: 2px solid #f59e0b;
        background: #fff;
        color: #f59e0b;
        border-radius: 8px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 8px;
      }
      .btn-test:hover {
        background: #fef3c7;
        border-color: #d97706;
        color: #d97706;
      }
      .btn-test i {
        width: 18px;
        height: 18px;
      }

      @media (max-width: 768px) {
        .form-grid {
          grid-template-columns: 1fr;
        }
        .field-inputs {
          grid-template-columns: 1fr;
        }
        .field-required-toggle {
          justify-content: flex-start;
        }
      }

      /* ===== MOBILE (≤480px) ===== */
      @media (max-width: 480px) {

        /* Issue 1: Tombol Tambah Field stack ke bawah */
        .fields-header {
          flex-direction: column;
          align-items: flex-start;
          gap: 10px;
        }
        .btn-add-field {
          width: 100%;
          justify-content: center;
        }

        /* Issue 2: Field item — layout baru: label full, tipe full, wajib+delete 1 baris */
        .field-item {
          display: flex;
          flex-wrap: wrap;
          align-items: center;
          gap: 8px;
          padding: 12px;
          position: static;
          padding-right: 12px;
        }
        .field-drag,
        .field-order-num {
          display: none;
        }
        /* field-inputs jadi display:contents agar anak-anaknya langsung masuk flex .field-item */
        .field-inputs {
          display: contents;
        }
        /* Label: satu baris penuh */
        .field-inputs input[type="text"] {
          flex: 1 1 100%;
          width: 100%;
        }
        /* Hidden order input tidak ikut layout */
        .field-inputs input[type="hidden"] {
          display: none;
        }
        /* Tipe data: satu baris penuh */
        .field-inputs select.field-select {
          flex: 1 1 100%;
          width: 100%;
        }
        /* Wajib: ambil sisa ruang (kiri), delete (kanan) sejajar */
        .field-required-toggle {
          flex: 1;
          min-height: 36px;
        }
        .field-actions {
          position: static;
          flex: 0 0 auto;
          flex-direction: row;
          display: flex;
        }

        /* Issue 3: Order list — sembunyikan kolom angka terpisah, nomor inline */
        .order-list {
          grid-template-columns: 1fr;
          gap: 8px;
          padding: 8px;
        }
        .order-numbers-column {
          display: none;
        }
        .order-number {
          display: flex !important;
          width: 28px;
          height: 28px;
          min-width: 28px;
          border-radius: 6px;
          background: linear-gradient(135deg, #064635 0%, #519259 100%);
          color: #fff;
          font-size: 12px;
          font-weight: 700;
          align-items: center;
          justify-content: center;
          flex-shrink: 0;
        }
        .order-drag-handle {
          display: none;
        }
        /* Tombol naik/turun selalu tampil di mobile tanpa hover */
        .order-item {
          padding-right: 12px;
        }
        .order-item-actions {
          position: static;
          transform: none;
          display: flex !important;
          opacity: 1 !important;
          flex-direction: column;
          gap: 4px;
        }
        /* Khusus item Data Tambahan: pakai grid 2 baris */
        .order-item:has(.order-item-badge.custom) {
          display: grid;
          grid-template-columns: auto auto 1fr auto;
          grid-template-rows: auto auto;
          gap: 6px 8px;
          align-items: center;
        }
        .order-item:has(.order-item-badge.custom) .order-item-checkbox {
          grid-row: 1; grid-column: 1;
        }
        .order-item:has(.order-item-badge.custom) .order-number {
          grid-row: 1; grid-column: 2;
        }
        .order-item:has(.order-item-badge.custom) .order-item-content {
          display: contents;
        }
        .order-item:has(.order-item-badge.custom) .order-item-label {
          grid-row: 1; grid-column: 3;
        }
        .order-item:has(.order-item-badge.custom) .order-item-actions {
          grid-row: 1; grid-column: 4;
        }
        .order-item:has(.order-item-badge.custom) .order-item-badge.custom {
          grid-row: 2; grid-column: 1 / -1;
        }
      }
    </style>
  </head>

  <body>
    @include('layouts.modals')
    @include('layouts.layout-vertical')

    <div class="pc-container">
      <div class="pc-content">
        @include('layouts.breadcrumb', ['breadcrumbItemHome' => 'Master Data', 'breadcrumbItem' => 'Jenis Inventaris', 'breadcrumbItemUrl' => route('jenis-inventaris.index'), 'breadcrumbItemActive' => 'Tambah Jenis'])

        <div class="grid grid-cols-12 gap-x-6">
          <div class="col-span-12 lg:col-span-10 lg:col-start-2">
            <div class="card">
              <div class="card-header border-b border-gray-100">
                <div class="flex items-center">
                  <div class="card-header-icon">
                    <i data-feather="folder-plus"></i>
                  </div>
                  <div>
                    <h5 class="mb-1">Tambah Jenis Inventaris Baru</h5>
                    <p class="text-muted text-sm mb-0">Buat kategori inventaris baru dengan field dinamis dan pejabat default</p>
                  </div>
                </div>
              </div>

              <div class="card-body pt-4">
                <form id="jenis-form" action="{{ route('jenis-inventaris.store') }}" method="POST" class="no-loader">
                  @csrf
                  
                  <!-- Hidden inputs for field order -->
                  <div id="field-order-container"></div>
                  
                  <!-- Informasi Dasar -->
                  <div class="form-section">
                    <div class="form-section-title">
                      <i data-feather="info"></i>
                      Informasi Dasar
                    </div>
                    <div class="form-grid">
                      <div class="form-group form-full">
                        <label class="form-label" for="nama">
                          Nama Jenis Inventaris <span class="required">*</span>
                        </label>
                        <input type="text" id="nama" name="nama" class="form-control @error('nama') is-invalid @enderror" placeholder="Contoh: Inventaris Kendaraan" value="{{ old('nama') }}" required oninput="checkTemplateName(this.value)" />
                        @error('nama')
                        <div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>
                        @enderror
                        <div id="template-name-warning" style="display:none;font-size:12px;margin-top:6px;padding:10px 12px;background:#fef3c7;border:1px solid #fbbf24;border-radius:6px;color:#92400e;">
                          <i data-feather="alert-triangle" style="width:14px;height:14px;display:inline;vertical-align:middle;margin-right:4px;"></i>
                          <strong>Perhatian:</strong> Jenis inventaris dengan nama ini sudah ada sebagai template sistem dan tidak dapat dibuat ulang.
                        </div>
                        <p class="form-hint">Nama kategori yang akan muncul di menu dan form</p>
                      </div>
                      <div class="form-group form-full">
                        <label class="form-label" for="deskripsi">
                          Deskripsi <span class="required">*</span>
                        </label>
                        <textarea required id="deskripsi" name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" rows="3" placeholder="Jelaskan jenis inventaris ini...">{{ old('deskripsi') }}</textarea>
                        @error('deskripsi')
                        <div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>
                        @enderror
                        <p class="form-hint">Deskripsi singkat tentang jenis inventaris ini</p>
                      </div>
                      <!-- Requires Ruangan -->
                      <div class="form-group form-full">
                        <label style="display:flex;align-items:flex-start;gap:12px;cursor:pointer;padding:14px 16px;background:{{ old('requires_ruangan') ? '#519259' : '#ffffff' }};border:1.5px solid {{ old('requires_ruangan') ? '#519259' : '#e5e7eb' }};border-radius:10px;transition:all 0.2s;{{ old('requires_ruangan') ? 'color:#fff;' : '' }}" id="requires-ruangan-label">
                          <input type="checkbox" name="requires_ruangan" id="requires_ruangan" value="1"
                                 {{ old('requires_ruangan') ? 'checked' : '' }}
                                 style="width:18px;height:18px;accent-color:#064635;flex-shrink:0;margin-top:2px;"
                                 onchange="onRequiresRuanganChange(this.checked)">
                          <div>
                            <div style="font-size:13px;font-weight:600;color:{{ old('requires_ruangan') ? '#fff' : '#374151' }};">Membutuhkan Data Ruangan</div>
                            <div style="font-size:12px;color:{{ old('requires_ruangan') ? 'rgba(255,255,255,0.8)' : '#6b7280' }};margin-top:2px;">Jika dicentang, field Ruangan akan muncul saat input aset.</div>
                          </div>
                        </label>
                      </div>
                    </div>
                  </div>

                  <!-- Default Pejabat Penandatangan -->
                  <!-- Tampilan Ikon -->
                  <div class="form-section" id="icon-section">
                    <div class="form-section-title">
                      <i data-feather="image"></i>
                      Tampilan Ikon
                    </div>
                    <p class="text-muted text-sm mb-4">Pilih mode tampilan ikon dan warna background untuk jenis inventaris ini.</p>

                    <!-- Mode Toggle -->
                    <div style="display:flex;gap:12px;margin-bottom:20px;">
                      <label style="flex:1;cursor:pointer;">
                        <input type="radio" name="icon_mode" value="auto" id="icon_mode_auto" checked
                               style="display:none;" onchange="onIconModeChange(this.value)">
                        <div class="icon-mode-card" id="card-auto" style="border:2px solid #519259;background:#519259;border-radius:10px;padding:14px 16px;display:flex;align-items:center;gap:12px;">
                          <div style="width:36px;height:36px;border-radius:8px;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i data-feather="zap" style="width:18px;height:18px;color:#fff;stroke:#fff;"></i>
                          </div>
                          <div>
                            <div style="font-size:13px;font-weight:600;color:#fff;">Otomatis</div>
                            <div style="font-size:11px;color:rgba(255,255,255,0.8);">Icon & warna di-generate otomatis dari nama</div>
                          </div>
                        </div>
                      </label>
                      <label style="flex:1;cursor:pointer;">
                        <input type="radio" name="icon_mode" value="manual" id="icon_mode_manual"
                               style="display:none;" onchange="onIconModeChange(this.value)">
                        <div class="icon-mode-card" id="card-manual" style="border:2px solid #e5e7eb;background:#fff;border-radius:10px;padding:14px 16px;display:flex;align-items:center;gap:12px;">
                          <div style="width:36px;height:36px;border-radius:8px;background:#e5e7eb;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i data-feather="sliders" style="width:18px;height:18px;color:#6b7280;stroke:#6b7280;"></i>
                          </div>
                          <div>
                            <div style="font-size:13px;font-weight:600;color:#374151;">Manual</div>
                            <div style="font-size:11px;color:#6b7280;">Pilih icon & warna sendiri</div>
                          </div>
                        </div>
                      </label>
                    </div>

                    <!-- Manual Options (hidden by default) -->
                    <div id="manual-icon-options" style="display:none;">
                      <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;align-items:start;">

                        <!-- Icon Picker -->
                        <div>
                          <label class="form-label">Pilih Ikon</label>
                          <input type="hidden" name="custom_icon" id="custom_icon_input" value="">
                          <div id="icon-picker-grid" style="display:grid;grid-template-columns:repeat(6,1fr);gap:8px;padding:12px;background:#f4faf5;border:1px solid #e5e7eb;border-radius:8px;max-height:220px;overflow-y:auto;">
                            <!-- Rendered by JS -->
                          </div>
                          <p style="font-size:11px;color:#9ca3af;margin-top:6px;">Klik icon untuk memilih</p>
                        </div>

                        <!-- Color Picker -->
                        <div>
                          <label class="form-label">Pilih Warna Background</label>
                          <input type="hidden" name="custom_color" id="custom_color_input" value="">
                          <div id="color-preset-grid" style="display:grid;grid-template-columns:repeat(5,1fr);gap:8px;padding:12px;background:#f4faf5;border:1px solid #e5e7eb;border-radius:8px;">
                            <!-- Rendered by JS -->
                          </div>
                          <div style="margin-top:10px;display:flex;align-items:center;gap:8px;">
                            <label style="font-size:12px;color:#374151;font-weight:500;white-space:nowrap;">Warna custom:</label>
                            <input type="color" id="custom_color_picker" value="#064635"
                                   style="width:40px;height:32px;border:1px solid #d1d5db;border-radius:6px;cursor:pointer;padding:2px;"
                                   oninput="onCustomColorInput(this.value)">
                            <span id="custom_color_hex" style="font-size:12px;color:#6b7280;font-family:monospace;">#064635</span>
                          </div>
                        </div>
                      </div>

                      <!-- Live Preview -->
                      <div style="margin-top:16px;padding:14px 16px;background:#f4faf5;border:1px solid #e5e7eb;border-radius:8px;display:flex;align-items:center;gap:14px;">
                        <div id="icon-preview-box" style="width:48px;height:48px;border-radius:12px;background:#064635;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:all 0.2s;">
                          <i id="icon-preview-icon" data-feather="layers" style="width:22px;height:22px;color:#fff;stroke:#fff;"></i>
                        </div>
                        <div>
                          <div style="font-size:13px;font-weight:600;color:#1f2937;">Preview Tampilan</div>
                          <div style="font-size:11px;color:#9ca3af;">Begini tampilan ikon di sistem</div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Default Pejabat Penandatangan -->
                  <div class="form-section">
                    <div class="form-section-title">
                      <i data-feather="users"></i>
                      Pejabat yang bertanda tangan
                    </div>
                    <p class="text-muted text-sm mb-4">Pilih pejabat yang akan menjadi penandatangan default saat export inventaris jenis ini. Urutan menentukan posisi tanda tangan.</p>

                    <!-- Search box pejabat -->
                    <div style="display:flex;align-items:center;background:#ffffff;border-radius:12px;padding:0 16px;border:1px solid #d1d5db;transition:all 0.2s;margin-bottom:14px;"
                         onfocusin="this.style.borderColor='#064635';this.style.boxShadow='0 0 0 3px rgba(6, 70, 53,0.1)'"
                         onfocusout="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                      <input type="text" id="pejabat-search-input" placeholder="Cari pejabat..."
                             style="border:none!important;box-shadow:none!important;background:transparent;padding:10px 0;font-size:13px;flex:1;outline:none;appearance:none;-webkit-appearance:none;"
                             oninput="pejabatToggleSearchClear(this);filterPejabatList(this.value)" />
                      <button type="button" id="pejabat-search-icon-btn" style="border:none;background:transparent;padding:0;cursor:pointer;color:#9ca3af;display:flex;align-items:center;" onclick="document.getElementById('pejabat-search-input').focus()">
                        <i data-feather="search" style="width:16px;height:16px;"></i>
                      </button>
                      <button type="button" id="pejabat-search-clear" onclick="clearPejabatSearch()" style="border:none;background:transparent;padding:0;cursor:pointer;color:#ef4444;display:none;align-items:center;">
                        <i data-feather="x" style="width:16px;height:16px;"></i>
                      </button>
                    </div>
                    
                    <div class="pejabat-select-area">
                      <div class="pejabat-select-header">
                        <span class="pejabat-select-header-title">Daftar Pejabat Tersedia</span>
                        <span class="pejabat-count" id="selected-count">0 dipilih</span>
                      </div>
                      <div class="pejabat-list" id="pejabat-list">
                        <!-- Pejabat items akan di-render oleh JavaScript -->
                      </div>
                    </div>

                    <div class="info-box">
                      <i data-feather="info" style="color:#d97706 !important;stroke:#d97706 !important;"></i>
                      <div class="info-box-text">
                        <strong>Catatan:</strong> Pejabat yang sudah dipilih disini bisa diubah lagi saat diperlukan. Urutan pejabat dipilih akan menentukan posisi tanda tangan pada dokumen export.
                      </div>
                    </div>
                  </div>

                  <!-- Data Dinamis -->
                  <div class="form-section">
                    <div class="form-section-title">
                      <i data-feather="sliders"></i>
                      Pilih Data Tambahan
                    </div>

                    <p class="text-muted text-sm mb-4">Tambahkan data khusus sesuai kebutuhan jenis inventaris ini. Contoh: untuk Kendaraan bisa tambah "Nomor Polisi", "Merk", "Tipe", dll.</p>

                    <div class="fields-container">
                      <div class="fields-header">
                        <span class="fields-header-title">
                          <i data-feather="list"></i>
                          Data Tambahan
                        </span>
                        <button type="button" class="btn-add-field" onclick="addField()">
                          <i data-feather="plus"></i>
                          Tambah Field
                        </button>
                      </div>
                      <div class="fields-body" id="fields-body">
                        <div class="fields-empty" id="fields-empty">
                          <div class="fields-empty-icon">
                            <i data-feather="inbox"></i>
                          </div>
                          <div class="fields-empty-text">Belum ada data tambahan</div>
                          <div class="fields-empty-hint">Klik "Tambah Data" untuk menambahkan Data</div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Section Dropdown — antara Data Tambahan dan Urutan Export -->
                  <div class="form-section">
                    <div class="form-section-title">
                      <i data-feather="list"></i>
                      Pilih Dropdown
                    </div>

                    <p class="text-muted text-sm mb-4">Pilih dropdown dari master data yang akan digunakan sebagai field pilihan pada jenis inventaris ini.</p>

                    <div class="fields-container" id="dropdown-fields-section">
                      <div class="fields-header">
                        <span class="fields-header-title">
                          <i data-feather="list"></i>
                          Field Dropdown
                        </span>
                      </div>
                      <div class="fields-body" style="padding:16px;background:#fff;">
                        <label style="display:flex;align-items:flex-start;gap:12px;cursor:pointer;padding:14px 16px;background:{{ old('needs_dropdown') ? '#519259' : '#ffffff' }};border:1.5px solid {{ old('needs_dropdown') ? '#519259' : '#e5e7eb' }};border-radius:10px;transition:all 0.2s;{{ old('needs_dropdown') ? 'color:#fff;' : '' }}" id="needs-dropdown-label">
                          <input type="checkbox" name="needs_dropdown" id="needs_dropdown" value="1"
                                 style="width:18px;height:18px;accent-color:#064635;flex-shrink:0;margin-top:2px;"
                                 onchange="onNeedsDropdownChange(this.checked)">
                          <div>
                            <div style="font-size:13px;font-weight:600;color:{{ old('needs_dropdown') ? '#fff' : '#374151' }};">Jenis inventaris ini membutuhkan field dropdown</div>
                            <div style="font-size:12px;color:{{ old('needs_dropdown') ? 'rgba(255,255,255,0.8)' : '#6b7280' }};margin-top:2px;">Jika dicentang, pilih dropdown dari master data yang akan digunakan sebagai field pilihan pada jenis inventaris ini.</div>
                          </div>
                        </label>

                        <div id="dropdown-list-wrapper" style="display:none;margin-top:14px;">
                          @if($dropdownList->isEmpty())
                            <div style="padding:16px;background:#fef3c7;border:1px solid #fbbf24;border-radius:8px;font-size:13px;color:#92400e;">
                              <i data-feather="alert-triangle" style="width:14px;height:14px;display:inline;vertical-align:middle;margin-right:6px;"></i>
                              Belum ada dropdown di master data.
                              <a href="{{ route('dropdown.create') }}" target="_blank" style="color:#064635;font-weight:600;margin-left:4px;">Buat sekarang →</a>
                            </div>
                          @else
                            <!-- Search box dropdown -->
                            <div style="display:flex;align-items:center;background:#ffffff;border-radius:12px;padding:0 16px;border:1px solid #d1d5db;transition:all 0.2s;margin-bottom:10px;"
                                 onfocusin="this.style.borderColor='#064635';this.style.boxShadow='0 0 0 3px rgba(6, 70, 53,0.1)'"
                                 onfocusout="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                              <input type="text" id="dd-search-input" placeholder="Cari dropdown..."
                                     style="border:none!important;box-shadow:none!important;background:transparent;padding:10px 0;font-size:13px;flex:1;outline:none;appearance:none;-webkit-appearance:none;"
                                     oninput="ddToggleSearchClear(this);filterDropdownList(this.value)" />
                              <button type="button" id="dd-search-icon-btn" style="border:none;background:transparent;padding:0;cursor:pointer;color:#9ca3af;display:flex;align-items:center;" onclick="document.getElementById('dd-search-input').focus()">
                                <i data-feather="search" style="width:16px;height:16px;"></i>
                              </button>
                              <button type="button" id="dd-search-clear" onclick="clearDropdownSearch()" style="border:none;background:transparent;padding:0;cursor:pointer;color:#ef4444;display:none;align-items:center;">
                                <i data-feather="x" style="width:16px;height:16px;"></i>
                              </button>
                            </div>
                            <p style="font-size:12px;color:#6b7280;margin-bottom:10px;">Centang dropdown yang ingin digunakan, lalu atur opsinya:</p>
                            <div style="display:flex;flex-direction:column;gap:8px;" id="dropdown-checklist">
                              @foreach($dropdownList as $ddIdx => $dd)
                              @php $isHiddenCreate = $ddIdx >= 10; @endphp
                              <div style="display:{{ $isHiddenCreate ? 'none' : 'flex' }};align-items:center;gap:12px;padding:12px 16px;background:#ffffff;border:1.5px solid #e5e7eb;border-radius:10px;transition:all 0.2s;"
                                   class="dd-item-label {{ $isHiddenCreate ? 'dd-item-hidden' : '' }}" data-id="{{ $dd->id }}" data-name="{{ strtolower($dd->nama) }}">
                                <input type="checkbox" name="selected_dropdowns[{{ $dd->id }}][enabled]" value="1"
                                       class="dd-checkbox"
                                       style="width:18px;height:18px;accent-color:#064635;flex-shrink:0;cursor:pointer;"
                                       onchange="onDropdownItemCheck(this, {{ $dd->id }})">
                                <div style="flex:1;">
                                  <div style="font-size:13px;font-weight:600;color:#374151;">{{ $dd->nama }}</div>
                                  <div style="font-size:11px;color:#9ca3af;margin-top:1px;">{{ $dd->items->pluck('label')->implode(', ') }}</div>
                                </div>
                                <div id="dd-pills-{{ $dd->id }}" style="display:none;flex-direction:column;gap:4px;flex-shrink:0;min-width:90px;">
                                  <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:12px;color:#374151;white-space:nowrap;">
                                    <input type="checkbox" name="selected_dropdowns[{{ $dd->id }}][required]" value="1"
                                           class="dd-req-cb"
                                           style="width:14px;height:14px;accent-color:#064635;cursor:pointer;margin:0;"
                                           onchange="onPillChange(this, 'dd-pill-req-{{ $dd->id }}', '#064635')">
                                    <span id="dd-pill-req-{{ $dd->id }}" style="font-weight:500;color:#9ca3af;">Wajib diisi</span>
                                  </label>
                                  <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:12px;color:#374151;white-space:nowrap;">
                                    <input type="checkbox" name="selected_dropdowns[{{ $dd->id }}][include_in_export]" value="1"
                                           class="dd-exp-cb"
                                           style="width:14px;height:14px;accent-color:#064635;cursor:pointer;margin:0;"
                                           onchange="onPillChange(this, 'dd-pill-exp-{{ $dd->id }}', '#064635')">
                                    <span id="dd-pill-exp-{{ $dd->id }}" style="font-weight:500;color:#9ca3af;">Masuk export</span>
                                  </label>
                                </div>
                              </div>
                              @endforeach
                            </div>
                            @if($dropdownList->count() > 10)
                            <div id="dd-show-more-wrap" style="margin-top:8px;text-align:center;">
                              <button type="button" id="dd-show-more-btn" onclick="showMoreDropdowns()"
                                      style="font-size:12px;color:#064635;font-weight:600;background:#f0f4f8;border:none;cursor:pointer;padding:6px 12px;border-radius:8px;">
                                Tampilkan {{ $dropdownList->count() - 10 }} lainnya ↓
                              </button>
                            </div>
                            @endif
                          @endif
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Urutan Field untuk Export -->
                  <div class="order-section">
                    <div class="order-section-title">
                      <i data-feather="list"></i>
                      Urutan Data untuk Export
                    </div>
                    <div class="order-hint">
                      <i data-feather="info" style="color:#d97706;stroke:#d97706;width:16px;height:16px;flex-shrink:0;margin-top:2px;"></i>
                      <span>Atur urutan kolom yang akan muncul saat export Excel/PDF. Drag & drop untuk mengubah urutan.</span>
                    </div>
                    <div class="order-list" id="order-list">
                      <div class="order-empty" id="order-empty">
                        <i data-feather="list"></i>
                        <div class="order-empty-text">Urutan field akan muncul di sini</div>
                        <div class="order-empty-hint">Tambahkan data tambahan untuk melihat urutan export</div>
                      </div>
                    </div>
                  </div>

                  <!-- Form Actions -->
                  <div class="form-actions">
                    <a href="{{ route('jenis-inventaris.index') }}" class="btn-cancel" onclick="return confirm('Apakah Anda yakin ingin membatalkan? Data yang sudah diisi tidak akan tersimpan.');">
                      <i data-feather="x"></i>
                      Batal
                    </a>
                    {{-- <button type="button" class="btn-test" onclick="testFormData()">
                      <i data-feather="terminal"></i>
                      Test Form Data
                    </button> --}}
                    <button type="button" class="btn-submit" onclick="confirmSubmit()">
                      <i data-feather="save"></i>
                      Simpan Jenis
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
    @include('layouts.footer-js')
    <script>
        // Check nama template untuk mencegah duplikat
        function checkTemplateName(value) {
            const warning = document.getElementById('template-name-warning');
            const submitBtn = document.querySelector('.btn-submit');
            const templateName = 'Inventaris yang tidak ditemukan';
            
            if (value.trim().toLowerCase() === templateName.toLowerCase()) {
                warning.style.display = 'block';
                if (submitBtn) submitBtn.disabled = true;
                feather.replace();
            } else {
                warning.style.display = 'none';
                if (submitBtn) submitBtn.disabled = false;
            }
        }

        function onNeedsDropdownChange(checked) {
            var wrapper = document.getElementById('dropdown-list-wrapper');
            var label   = document.getElementById('needs-dropdown-label');
            if (wrapper) wrapper.style.display = checked ? 'block' : 'none';
            if (label) {
                label.style.borderColor = checked ? '#519259' : '#e5e7eb';
                label.style.background = checked ? '#519259' : '#ffffff';
                var allDivs = label.querySelectorAll('div');
                allDivs.forEach(function(d) {
                    if (d.style.fontSize === '13px') d.style.color = checked ? '#fff' : '#374151';
                    if (d.style.fontSize === '12px') d.style.color = checked ? 'rgba(255,255,255,0.8)' : '#6b7280';
                });
            }
            if (!checked) {
                document.querySelectorAll('.dd-checkbox').forEach(function(cb) {
                    cb.checked = false;
                    var ddId = cb.closest('[data-id]') ? cb.closest('[data-id]').dataset.id : null;
                    if (ddId) onDropdownItemCheck(cb, ddId);
                });
            }
        }
        function onDropdownItemCheck(cb, ddId) {
            var pills = document.getElementById('dd-pills-' + ddId);
            var row   = cb.closest('[data-id]');
            if (pills) pills.style.display = cb.checked ? 'flex' : 'none';
            if (row) {
                row.style.borderColor = cb.checked ? '#519259' : '#e5e7eb';
                row.style.background = cb.checked ? '#519259' : '#ffffff';
                
                var infoDiv = row.querySelector('div[style*="flex:1"]');
                if (infoDiv) {
                    var titleDiv = infoDiv.children[0];
                    var descDiv = infoDiv.children[1];
                    if (titleDiv) titleDiv.style.color = cb.checked ? '#ffffff' : '#374151';
                    if (descDiv) descDiv.style.color = cb.checked ? 'rgba(255,255,255,0.8)' : '#9ca3af';
                }
                
                if (pills) {
                    var labels = pills.querySelectorAll('label');
                    labels.forEach(function(lbl) {
                        lbl.style.color = cb.checked ? '#ffffff' : '#374151';
                    });
                    var reqSpan = document.getElementById('dd-pill-req-' + ddId);
                    var expSpan = document.getElementById('dd-pill-exp-' + ddId);
                    var reqCb = pills.querySelector('.dd-req-cb');
                    var expCb = pills.querySelector('.dd-exp-cb');
                    if (reqSpan && reqCb) reqSpan.style.color = reqCb.checked ? '#ffffff' : '#9ca3af';
                    if (expSpan && expCb) expSpan.style.color = expCb.checked ? '#ffffff' : '#9ca3af';
                }
            }
            if (!cb.checked && pills) {
                var reqCb = pills.querySelector('.dd-req-cb');
                var expCb = pills.querySelector('.dd-exp-cb');
                if (reqCb) { reqCb.checked = false; onPillChange(reqCb, 'dd-pill-req-' + ddId, '#064635'); }
                if (expCb) { expCb.checked = false; onPillChange(expCb, 'dd-pill-exp-' + ddId, '#064635'); }
                syncDropdownToOrderList(ddId, false, '');
            }
            if (cb.checked && pills) {
                var ddName = row ? (row.querySelector('div[style*="font-weight:600"]') ? row.querySelector('div[style*="font-weight:600"]').textContent.trim() : 'Dropdown') : 'Dropdown';
                syncDropdownToOrderList(ddId, true, ddName);
            }
        }
        function onPillChange(cb, pillId, activeColor) {
            var span = document.getElementById(pillId);
            if (!span) return;
            var ddId = pillId.replace('dd-pill-req-', '').replace('dd-pill-exp-', '');
            var itemCb = document.querySelector('input[name="selected_dropdowns[' + ddId + '][enabled]"]');
            var isRowChecked = itemCb && itemCb.checked;
            span.style.color = cb.checked ? (isRowChecked ? '#ffffff' : activeColor) : '#9ca3af';
            if (pillId && pillId.indexOf('dd-pill-exp-') === 0) {
                var ddId = pillId.replace('dd-pill-exp-', '');
                var row  = document.querySelector('[data-id="' + ddId + '"]');
                var ddName = row ? (row.querySelector('div[style*="font-weight:600"]') ? row.querySelector('div[style*="font-weight:600"]').textContent.trim() : 'Dropdown') : 'Dropdown';
                syncDropdownToOrderList(ddId, cb.checked, ddName);
            }
            // Sync wajib antara section dropdown dan urutan export
            if (pillId && pillId.indexOf('dd-pill-req-') === 0) {
                var ddId = pillId.replace('dd-pill-req-', '');
                syncRequiredToOrderList(ddId, cb.checked);
            }
        }
        function syncDropdownToOrderList(ddId, include, label) {
            if (!window.orderList) return;
            var orderId = 'dropdown_' + ddId;
            var existing = window.orderList.find(function(f) { return f.id === orderId; });
            var prevRequired = existing ? existing.is_required : false;
            var prevExport = existing !== undefined ? existing.include_in_export : false; // default tidak masuk export
            window.orderList = window.orderList.filter(function(f) { return f.id !== orderId; });
            if (include) {
                window.orderList.push({ id: orderId, label: label, type: 'custom', field_id: null, dropdown_id: ddId, enabled: true, is_required: prevRequired, include_in_export: prevExport });
            }
            if (typeof normalizeOrderListDisabledToBottom === 'function') normalizeOrderListDisabledToBottom();
            if (typeof renderOrderList === 'function') renderOrderList();
        }
        function syncRequiredToOrderList(ddId, isRequired) {
            if (!window.orderList) return;
            var orderId = 'dropdown_' + ddId;
            var item = window.orderList.find(function(f) { return f.id === orderId; });
            if (item) {
                item.is_required = isRequired;
                if (typeof renderOrderList === 'function') renderOrderList();
            }
        }
        function syncExportToOrderList(ddId, includeInExport) {
            if (!window.orderList) return;
            var orderId = 'dropdown_' + ddId;
            var item = window.orderList.find(function(f) { return f.id === orderId; });
            if (item) {
                item.include_in_export = includeInExport;
                if (typeof renderOrderList === 'function') renderOrderList();
            }
        }

        // Sync warna teks label toggle sesuai status checkbox (pakem dropdown)
        function onFieldToggleChange(cb, labelId) {
            var label = document.getElementById(labelId);
            if (label) {
                var span = label.querySelector('span');
                if (span) span.style.color = cb.checked ? '#064635' : '#9ca3af';
            }
        }
        window.onFieldToggleChange = onFieldToggleChange;
    </script>

    <script>
      (function(){
        // Real pejabat data from database
        const rawPejabatList = @json($pejabatsData ?? []);
        const pejabatList = (Array.isArray(rawPejabatList) ? rawPejabatList : Object.values(rawPejabatList || {})).map(p => ({
          ...p,
          id: Number(p.id)
        }));

        const selectedPejabats = [];
        let fieldCounter = 0;
        let pejabatSearchQuery = '';

        // Render pejabat list
        function renderPejabatList() {
          const container = document.getElementById('pejabat-list');
          const query = pejabatSearchQuery.toLowerCase().trim();
          
          const filteredList = pejabatList.filter(p => {
            return (p.nama && p.nama.toLowerCase().includes(query)) || 
                   (p.jabatan && p.jabatan.toLowerCase().includes(query));
          });

          if (filteredList.length === 0) {
            container.innerHTML = `
              <div style="padding: 20px; text-align: center; color: #9ca3af; font-size: 13px;">
                Pejabat tidak ditemukan
              </div>
            `;
            document.getElementById('selected-count').textContent = selectedPejabats.length + ' dipilih';
            return;
          }

          container.innerHTML = filteredList.map(p => {
            const isSelected = selectedPejabats.find(sp => Number(sp.id) === Number(p.id));
            const order = isSelected ? selectedPejabats.indexOf(isSelected) + 1 : 0;
            return `
              <div class="pejabat-item ${isSelected ? 'selected' : ''}" data-id="${p.id}" onclick="togglePejabat(${p.id})">
                <input type="checkbox" ${isSelected ? 'checked' : ''} onchange="togglePejabat(${p.id})" onclick="event.stopPropagation()" />
                <div class="pejabat-info">
                  <div class="pejabat-nama">${p.nama}</div>
                  <div class="pejabat-jabatan">${p.jabatan}</div>
                </div>
                <div class="pejabat-order">
                  <span class="order-badge">${order}</span>
                  <div class="order-controls">
                    <button type="button" class="order-btn" onclick="movePejabat(${p.id}, -1)" title="Naik">
                      <i data-feather="chevron-up"></i>
                    </button>
                    <button type="button" class="order-btn" onclick="movePejabat(${p.id}, 1)" title="Turun">
                      <i data-feather="chevron-down"></i>
                    </button>
                  </div>
                </div>
              </div>
            `;
          }).join('');
          
          document.getElementById('selected-count').textContent = selectedPejabats.length + ' dipilih';
          feather.replace();
        }

        window.filterPejabatList = function(value) {
          pejabatSearchQuery = value;
          renderPejabatList();
        };

        window.pejabatToggleSearchClear = function(input) {
          const clearBtn = document.getElementById('pejabat-search-clear');
          const searchIcon = document.getElementById('pejabat-search-icon-btn');
          if (clearBtn && searchIcon) {
            if (input.value.length > 0) {
              clearBtn.style.display = 'flex';
              searchIcon.style.display = 'none';
            } else {
              clearBtn.style.display = 'none';
              searchIcon.style.display = 'flex';
            }
          }
        };

        window.clearPejabatSearch = function() {
          const input = document.getElementById('pejabat-search-input');
          if (input) {
            input.value = '';
            window.pejabatToggleSearchClear(input);
            window.filterPejabatList('');
          }
        };

        // Toggle pejabat selection
        window.togglePejabat = function(id) {
          const targetId = Number(id);
          const idx = selectedPejabats.findIndex(p => Number(p.id) === targetId);
          if (idx > -1) {
            selectedPejabats.splice(idx, 1);
          } else {
            const pejabat = pejabatList.find(p => Number(p.id) === targetId);
            if (pejabat) {
              selectedPejabats.push({ ...pejabat, id: targetId, order: selectedPejabats.length + 1 });
            }
          }
          renderPejabatList();
          updateHiddenPejabatInputs();
        };

        // Move pejabat order
        window.movePejabat = function(id, direction) {
          const targetId = Number(id);
          const idx = selectedPejabats.findIndex(p => Number(p.id) === targetId);
          if (idx === -1) return;
          
          const newIdx = idx + direction;
          if (newIdx < 0 || newIdx >= selectedPejabats.length) return;
          
          // Swap
          [selectedPejabats[idx], selectedPejabats[newIdx]] = [selectedPejabats[newIdx], selectedPejabats[idx]];
          renderPejabatList();
          updateHiddenPejabatInputs();
        };

        // Update hidden inputs for form submission
        function updateHiddenPejabatInputs() {
          const form = document.getElementById('jenis-form');
          // Remove old hidden inputs
          const oldInputs = form.querySelectorAll('input[name="default_pejabats[]"]');
          oldInputs.forEach(input => input.remove());
          
          // Add new hidden inputs
          selectedPejabats.forEach(pejabat => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'default_pejabats[]';
            input.value = pejabat.id;
            form.appendChild(input);
          });
        }

        // Expose for global submit handler
        window.updateHiddenPejabatInputs = updateHiddenPejabatInputs;

        // Daftar dropdown dari master data
        const dropdownOptions = `{!! \App\Models\Dropdown::orderBy('nama')->get(['id','nama'])->map(fn($d) => '<option value="'.$d->id.'">'.$d->nama.'</option>')->implode('') !!}`;

        function onNeedsDropdownChange(checked) {
          const wrapper = document.getElementById('dropdown-list-wrapper');
          const label   = document.getElementById('needs-dropdown-label');
          if (wrapper) wrapper.style.display = checked ? 'block' : 'none';
          if (label) {
            label.style.borderColor = checked ? '#519259' : '#e5e7eb';
            label.style.background = checked ? '#519259' : '#ffffff';
            var allDivs = label.querySelectorAll('div');
            allDivs.forEach(function(d) {
              if (d.style.fontSize === '13px') d.style.color = checked ? '#fff' : '#374151';
              if (d.style.fontSize === '12px') d.style.color = checked ? 'rgba(255,255,255,0.8)' : '#6b7280';
            });
          }
          if (!checked) {
            document.querySelectorAll('.dd-checkbox').forEach(cb => {
              cb.checked = false;
              const ddId = cb.closest('[data-id]')?.dataset?.id;
              if (ddId) onDropdownItemCheck(cb, ddId);
            });
          }
        }

        function onDropdownItemCheck(cb, ddId) {
          const pills = document.getElementById('dd-pills-' + ddId);
          const row   = cb.closest('[data-id]');
          if (pills) pills.style.display = cb.checked ? 'flex' : 'none';
          if (row) {
            row.style.borderColor = cb.checked ? '#519259' : '#e5e7eb';
            row.style.background = cb.checked ? '#519259' : '#ffffff';
            
            var infoDiv = row.querySelector('div[style*="flex:1"]');
            if (infoDiv) {
              var titleDiv = infoDiv.children[0];
              var descDiv = infoDiv.children[1];
              if (titleDiv) titleDiv.style.color = cb.checked ? '#ffffff' : '#374151';
              if (descDiv) descDiv.style.color = cb.checked ? 'rgba(255,255,255,0.8)' : '#9ca3af';
            }
            
            if (pills) {
              var labels = pills.querySelectorAll('label');
              labels.forEach(function(lbl) {
                lbl.style.color = cb.checked ? '#ffffff' : '#374151';
              });
              var reqSpan = document.getElementById('dd-pill-req-' + ddId);
              var expSpan = document.getElementById('dd-pill-exp-' + ddId);
              var reqCb = pills.querySelector('.dd-req-cb');
              var expCb = pills.querySelector('.dd-exp-cb');
              if (reqSpan && reqCb) reqSpan.style.color = reqCb.checked ? '#ffffff' : '#9ca3af';
              if (expSpan && expCb) expSpan.style.color = expCb.checked ? '#ffffff' : '#9ca3af';
            }
          }
          if (!cb.checked && pills) {
            const reqCb = pills.querySelector('.dd-req-cb');
            const expCb = pills.querySelector('.dd-exp-cb');
            if (reqCb) { reqCb.checked = false; onPillChange(reqCb, 'dd-pill-req-' + ddId, '#064635'); }
            if (expCb) { expCb.checked = false; onPillChange(expCb, 'dd-pill-exp-' + ddId, '#064635'); }
            syncDropdownToOrderList(ddId, false, '');
          }
          if (cb.checked && pills) {
            const ddName = row?.querySelector('div[style*="font-weight:600"]')?.textContent?.trim() || 'Dropdown';
            syncDropdownToOrderList(ddId, true, ddName);
          }
        }

        function onPillChange(cb, pillId, activeColor) {
          const span = document.getElementById(pillId);
          if (!span) return;
          var ddId = pillId.replace('dd-pill-req-', '').replace('dd-pill-exp-', '');
          var itemCb = document.querySelector('input[name="selected_dropdowns[' + ddId + '][enabled]"]');
          var isRowChecked = itemCb && itemCb.checked;
          span.style.color = cb.checked ? (isRowChecked ? '#ffffff' : activeColor) : '#9ca3af';
          if (pillId && pillId.startsWith('dd-pill-exp-')) {
            const ddId = pillId.replace('dd-pill-exp-', '');
            const row  = document.querySelector('[data-id="' + ddId + '"]');
            const ddName = row?.querySelector('div[style*="font-weight:600"]')?.textContent?.trim() || 'Dropdown';
            syncDropdownToOrderList(ddId, cb.checked, ddName);
          }
          // Sync wajib antara section dropdown dan urutan export
          if (pillId && pillId.startsWith('dd-pill-req-')) {
            const ddId = pillId.replace('dd-pill-req-', '');
            if (typeof syncRequiredToOrderList === 'function') syncRequiredToOrderList(ddId, cb.checked);
          }
        }

        function syncDropdownToOrderList(ddId, include, label) {
          if (!window.orderList) return;
          const orderId = 'dropdown_' + ddId;
          // Pertahankan is_required dan include_in_export yang sudah ada saat toggle ulang
          const existing = window.orderList.find(f => f.id === orderId);
          const prevRequired = existing ? existing.is_required : false;
          const prevExport = existing ? existing.include_in_export : false; // default tidak masuk export
          window.orderList = window.orderList.filter(f => f.id !== orderId);
          if (include) {
            window.orderList.push({
              id: orderId, label: label, type: 'custom',
              field_id: null, dropdown_id: ddId, enabled: true,
              is_required: prevRequired, include_in_export: prevExport
            });
          }
          if (typeof normalizeOrderListDisabledToBottom === 'function') normalizeOrderListDisabledToBottom();
          if (typeof renderOrderList === 'function') renderOrderList();
        }

        function onFieldTipeChange(selectEl, fieldId) {
          const wrapper = document.getElementById('dropdown-select-' + fieldId);
          if (!wrapper) return;
          wrapper.style.display = selectEl.value === 'dropdown' ? 'block' : 'none';
          const ddSelect = wrapper.querySelector('select');
          if (ddSelect) ddSelect.required = selectEl.value === 'dropdown';
        }

        // ── Dropdown search & show more ──────────────────────────────────────
        window.ddToggleSearchClear = function(input) {
          const iconBtn = document.getElementById('dd-search-icon-btn');
          const clearBtn = document.getElementById('dd-search-clear');
          if (iconBtn) iconBtn.style.display = input.value ? 'none' : 'flex';
          if (clearBtn) clearBtn.style.display = input.value ? 'flex' : 'none';
        };
        window.filterDropdownList = function(val) {
          const q = val.trim().toLowerCase();
          const clearBtn = document.getElementById('dd-search-clear');
          if (clearBtn) clearBtn.style.display = q ? '' : 'none';

          const items = document.querySelectorAll('#dropdown-checklist .dd-item-label');
          const showMoreWrap = document.getElementById('dd-show-more-wrap');
          let visibleCount = 0;

          items.forEach(item => {
            const name = (item.dataset.name || '').toLowerCase();
            const isChecked = item.querySelector('.dd-checkbox')?.checked;
            const matchSearch = !q || name.includes(q);
            const withinLimit = visibleCount < 10;

            if (matchSearch && (isChecked || withinLimit || q)) {
              item.style.display = 'flex';
              if (!isChecked) visibleCount++;
            } else {
              item.style.display = 'none';
            }
          });

          if (showMoreWrap) showMoreWrap.style.display = q ? 'none' : '';
        };

        window.clearDropdownSearch = function() {
          const input = document.getElementById('dd-search-input');
          if (input) { input.value = ''; window.ddToggleSearchClear(input); window.filterDropdownList(''); }
        };

        window.showMoreDropdowns = function() {
          document.querySelectorAll('#dropdown-checklist .dd-item-hidden').forEach(item => {
            item.style.display = 'flex';
            item.classList.remove('dd-item-hidden');
          });
          const wrap = document.getElementById('dd-show-more-wrap');
          if (wrap) wrap.style.display = 'none';
        };

        // Add dynamic field
        window.addField = function() {
          fieldCounter++;
          const fieldsBody = document.getElementById('fields-body');

          // Init toggle color saat label baru muncul (default keduanya tidak dicentang = abu)
          setTimeout(() => {
            const expLabel = document.getElementById('exp-label-' + fieldCounter);
            if (expLabel) {
              const s = expLabel.querySelector('span');
              if (s) s.style.color = '#9ca3af'; // default unchecked
            }
            const reqLabel = document.getElementById('req-label-' + fieldCounter);
            if (reqLabel) {
              const s = reqLabel.querySelector('span');
              if (s) s.style.color = '#9ca3af'; // default unchecked
            }
          }, 20);
          const emptyState = document.getElementById('fields-empty');
          // OPSI A (is_name_field) — saat diaktifkan, tambahkan toggle Nama Tampilan
          // pada template row di bawah.
          
          if (emptyState) {
            emptyState.style.display = 'none';
          }

          const fieldHtml = `
            <div class="field-item" data-field-id="${fieldCounter}" id="field-${fieldCounter}">
              <div class="field-drag">
                <i data-feather="more-vertical"></i>
              </div>
              <div class="field-order-num">${fieldCounter}</div>
              <div class="field-inputs">
                <div class="field-inputs-row">
                  <input type="text" name="fields[${fieldCounter}][label_field]" class="field-input" placeholder="Label (ex: Nomor Polisi)" required />
                  <select name="fields[${fieldCounter}][tipe]" class="field-input field-select" required
                          onchange="onFieldTipeChange(this, ${fieldCounter})">
                    <option value="text">Teks (nama / keterangan)</option>
                    <option value="number">Angka (jumlah / nilai)</option>
                    <option value="decimal_m2">Luas dalam m²</option>
                    <option value="decimal_km">Panjang dalam km</option>
                    <option value="decimal_m">Lebar dalam m</option>
                    <option value="year">Tahun perolehan</option>
                    <option value="month">Bulan dan Tahun</option>
                    <option value="date">Tanggal lengkap</option>
                  </select>
                </div>
                <input type="hidden" name="fields[${fieldCounter}][order]" class="field-order-input" value="999" />
                <div class="field-toggles">
                  <label class="field-required-toggle" id="req-label-${fieldCounter}" title="Centang jika field ini wajib diisi">
                    <input type="checkbox" name="fields[${fieldCounter}][is_required]" value="1"
                           onchange="onFieldToggleChange(this, 'req-label-${fieldCounter}')" />
                    <span>Wajib diisi</span>
                  </label>
                  <label class="field-export-toggle" id="exp-label-${fieldCounter}" title="Centang jika field ini masuk ke export">
                    <input type="checkbox" name="fields[${fieldCounter}][include_in_export]" value="1"
                           onchange="onFieldToggleChange(this, 'exp-label-${fieldCounter}')" />
                    <span>Masuk export</span>
                  </label>
                </div>
              </div>
              <div class="field-actions">
                <button type="button" class="field-action-btn delete" onclick="removeField(${fieldCounter})" title="Hapus Field">
                  <i data-feather="trash-2"></i>
                </button>
              </div>
            </div>
          `;
          
          fieldsBody.insertAdjacentHTML('beforeend', fieldHtml);
          feather.replace();
          updateFieldOrder();
        };

        // Remove field (with confirmation)
        window.removeField = function(id, onRemoved = null) {
          const field = document.getElementById('field-' + id);
          if (!field) return;

          const doRemove = function() {
            field.remove();
            updateFieldOrder();
            checkEmptyFields();
            if (typeof onRemoved === 'function') {
              onRemoved();
            }
          };

          if (typeof AppModal !== 'undefined' && typeof AppModal.confirm === 'function') {
            AppModal.confirm(
              'Konfirmasi Hapus Data Tambahan',
              'Apakah Anda yakin ingin menghapus data tambahan ini?',
              doRemove
            );
          } else if (confirm('Apakah Anda yakin ingin menghapus data tambahan ini?')) {
            doRemove();
          }
        };

        // Update field order numbers
        function updateFieldOrder() {
          const fields = document.querySelectorAll('.field-item');
          fields.forEach((field, idx) => {
            const orderNum = field.querySelector('.field-order-num');
            if (orderNum) {
              orderNum.textContent = idx + 1;
            }
          });
        }

        // Check if fields empty
        function checkEmptyFields() {
          const fields = document.querySelectorAll('.field-item');
          const emptyState = document.getElementById('fields-empty');
          if (fields.length === 0 && emptyState) {
            emptyState.style.display = 'block';
          }
        }

        // ========== ORDER MANAGEMENT ==========
        
        // Fixed fields template dari controller
        const fixedFieldsTemplate = @json($fixedFieldsTemplate ?? []);
        // console.log('[DEBUG] Fixed fields template from controller:', fixedFieldsTemplate);
        // console.log('[DEBUG] fixedFieldsTemplate length:', fixedFieldsTemplate ? fixedFieldsTemplate.length : 0);
        
        // Initialize order list dengan enabled = true untuk semua
         // PENTING: Menggunakan window.orderList agar bisa diakses dari syncOrderToForm()
        window.orderList = (fixedFieldsTemplate && fixedFieldsTemplate.length > 0) 
          ? fixedFieldsTemplate.map((field, idx) => ({
            id: `fixed_${idx + 1}`,
            label: field.label,
            fixed_name: field.fixed_name,
            type: 'fixed',
            enabled: true,
            is_required: true,       // Data Umum: default wajib diisi
            include_in_export: true  // Data Umum: default masuk export
          }))
          : [];
        // console.log('[DEBUG] Initialized orderList:', window.orderList);
        // console.log('[DEBUG] orderList length:', window.orderList.length);

        function normalizeOrderListDisabledToBottom() {
          const enabledFields = window.orderList.filter(f => f.enabled);
          const disabledFields = window.orderList.filter(f => !f.enabled);
          window.orderList = [...enabledFields, ...disabledFields];
        }
        window.normalizeOrderListDisabledToBottom = normalizeOrderListDisabledToBottom;

        // Toggle fixed field enable/disable
        window.toggleFixedField = function(fieldId) {
          const field = window.orderList.find(f => f.id === fieldId);
          if (field && field.type === 'fixed') {
            field.enabled = !field.enabled;
            normalizeOrderListDisabledToBottom();
            
            renderOrderList();
          }
        };

        // Toggle custom field enable/disable (Data Tambahan)
        window.toggleCustomField = function(fieldId) {
          const field = window.orderList.find(f => f.id === fieldId);
          if (field && field.type === 'custom') {
            field.enabled = !field.enabled;
            normalizeOrderListDisabledToBottom();
            renderOrderList();
          }
        };

        // Toggle is_required pada order list item
        window.toggleRequiredField = function(fieldId, isRequired) {
          const field = window.orderList.find(f => f.id === fieldId);
          if (field) {
            field.is_required = isRequired;
            // Sync ke checkbox di section Data Tambahan jika ini custom field
            if (field.type === 'custom') {
              const numericId = String(fieldId).replace('custom_', '');
              const cbInDataTambahan = document.querySelector(`input[name="fields[${numericId}][is_required]"]`);
              if (cbInDataTambahan) cbInDataTambahan.checked = isRequired;
            }
            // Sync ke checkbox "Wajib diisi" di section Field Dropdown jika ini dropdown field
            if (fieldId && String(fieldId).startsWith('dropdown_')) {
              const ddId = String(fieldId).replace('dropdown_', '');
              const reqCb = document.querySelector(`input[name="selected_dropdowns[${ddId}][required]"]`);
              if (reqCb) {
                reqCb.checked = isRequired;
                onPillChange(reqCb, 'dd-pill-req-' + ddId, '#064635');
              }
            }
          }
        };

        // Toggle include_in_export pada order list item
        window.toggleExportField = function(fieldId, includeInExport) {
          const field = window.orderList.find(f => f.id === fieldId);
          if (field) {
            field.include_in_export = includeInExport;
            // Sync ke checkbox di section Data Tambahan jika ini custom field
            if (field.type === 'custom') {
              const numericId = String(fieldId).replace('custom_', '');
              const cbExport = document.querySelector(`input[name="fields[${numericId}][include_in_export]"]`);
              if (cbExport) cbExport.checked = includeInExport;
            }
            // Sync ke checkbox "Masuk export" di section Field Dropdown jika ini dropdown field
            if (fieldId && String(fieldId).startsWith('dropdown_')) {
              const ddId = String(fieldId).replace('dropdown_', '');
              const expCb = document.querySelector(`input[name="selected_dropdowns[${ddId}][include_in_export]"]`);
              if (expCb) {
                expCb.checked = includeInExport;
                onPillChange(expCb, 'dd-pill-exp-' + ddId, '#064635');
              }
            }
            // Re-render agar state checkbox di UI order list konsisten
            renderOrderList();
          }
        };

        // Render order list
        function renderOrderList() {
          normalizeOrderListDisabledToBottom();
          // console.log('[RENDER] renderOrderList called, orderList length:', window.orderList.length);
          const container = document.getElementById('order-list');
          // console.log('[RENDER] Container found:', !!container);
          let emptyState = document.getElementById('order-empty');

          if (window.orderList.length === 0) {
            // console.log('[WARN] OrderList is EMPTY, showing empty state');
            // Recreate empty state if it doesn't exist
            if (!emptyState) {
              container.innerHTML = `
                <div class="order-empty" id="order-empty">
                  <i data-feather="list"></i>
                  <div class="order-empty-text">Urutan field akan muncul di sini</div>
                  <div class="order-empty-hint">Tambahkan data tambahan untuk melihat urutan export</div>
                </div>
              `;
              feather.replace();
            } else {
              emptyState.style.display = 'flex';
            }
            return;
          }

          // console.log('[SUCCESS] Rendering', window.orderList.length, 'items in order list');
          
          // Build unified row list — nomor badge di dalam tiap row agar tinggi otomatis selaras
          const fieldsHtml = window.orderList.map((item, idx) => {
            const isDisabled = !item.enabled;
            const disabledClass = isDisabled ? 'disabled' : '';
            const toggleFunc = item.type === 'fixed' ? 'toggleFixedField' : 'toggleCustomField';
            const checkboxHtml = `<input type="checkbox" class="order-item-checkbox" ${item.enabled ? 'checked' : ''} onchange="${toggleFunc}('${item.id}')" onclick="event.stopPropagation()" />`;
            
            const isFirst = idx === 0;
            const isLast = idx === window.orderList.length - 1;
            const isExport = item.include_in_export !== false; // default true
            const badgeDisabledClass = isDisabled ? 'disabled' : '';
            
            return `
              <div class="order-row">
                <div class="order-number-badge ${badgeDisabledClass}">${idx + 1}</div>
                <div class="order-item ${disabledClass}" data-order-id="${item.id}" draggable="${!isDisabled}">
                  ${checkboxHtml}
                  <div class="order-number">${idx + 1}</div>
                  <div class="order-item-content">
                    <div class="order-item-label">${item.label}</div>
                  </div>
                  <div class="order-item-flags" onclick="event.stopPropagation()">
                    <label class="order-item-required" title="Centang jika field ini wajib diisi">
                      <input type="checkbox" ${item.is_required ? 'checked' : ''} onchange="toggleRequiredField('${item.id}', this.checked)" onclick="event.stopPropagation()" />
                      <span style="color:${item.is_required ? '#064635' : '#9ca3af'}">Wajib diisi</span>
                    </label>
                    <label class="order-item-export" title="Centang jika field ini masuk export">
                      <input type="checkbox" ${isExport ? 'checked' : ''} onchange="toggleExportField('${item.id}', this.checked)" onclick="event.stopPropagation()" />
                      <span style="color:${isExport ? '#064635' : '#9ca3af'}">Masuk export</span>
                    </label>
                  </div>
                  <div class="order-item-actions">
                    <div class="order-move-btns">
                      <button type="button" class="order-move-btn" onclick="moveOrderItem('${item.id}', -1)" ${isFirst || isDisabled ? 'disabled' : ''} title="Pindah ke atas">
                        <i data-feather="chevron-up"></i>
                      </button>
                      <button type="button" class="order-move-btn" onclick="moveOrderItem('${item.id}', 1)" ${isLast || isDisabled ? 'disabled' : ''} title="Pindah ke bawah">
                        <i data-feather="chevron-down"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            `;
          }).join('');
          
          // Single wrapper — nomor terintegrasi dalam tiap row
          container.innerHTML = `<div class="order-rows-wrapper">${fieldsHtml}</div>`;

          // console.log('HTML generated, calling feather.replace()');
          feather.replace();
          // console.log('[SUCCESS] Order list rendered successfully with', orderList.length, 'items');
          initDragAndDrop();
        }

        // Initialize drag and drop
        function initDragAndDrop() {
          const items = document.querySelectorAll('.order-rows-wrapper .order-item');
          let draggedItem = null;

          items.forEach(item => {
            item.addEventListener('dragstart', function(e) {
              draggedItem = this;
              this.style.opacity = '0.4';
              e.dataTransfer.effectAllowed = 'move';
            });

            item.addEventListener('dragend', function(e) {
              this.style.opacity = '1';
              items.forEach(i => i.classList.remove('drag-over'));
            });

            item.addEventListener('dragover', function(e) {
              e.preventDefault();
              if (draggedItem !== this) {
                const rect = this.getBoundingClientRect();
                const midpoint = rect.top + rect.height / 2;
                if (e.clientY < midpoint) {
                  this.parentNode.insertBefore(draggedItem, this);
                } else {
                  this.parentNode.insertBefore(draggedItem, this.nextSibling);
                }
              }
            });

            item.addEventListener('drop', function(e) {
              e.preventDefault();
              e.stopPropagation();
              updateOrderFromDOM();
            });
          });
        }
        window.renderOrderList = renderOrderList;

        // Update orderList dari DOM setelah drag
        function updateOrderFromDOM() {
          const items = document.querySelectorAll('.order-fields-column .order-item');
          const newOrder = [];
          items.forEach(item => {
            const id = item.dataset.orderId;
            const field = window.orderList.find(f => f.id === id);
            if (field) newOrder.push(field);
          });
          window.orderList = newOrder;
          normalizeOrderListDisabledToBottom();
          renderOrderList(); // Re-render untuk update nomor urut
        }

        // Move order item up/down (alternative to drag & drop)
        window.moveOrderItem = function(itemId, direction) {
          const currentIndex = window.orderList.findIndex(f => f.id === itemId);
          if (currentIndex === -1) return;
          
          // Don't allow moving disabled items
          const currentItem = window.orderList[currentIndex];
          if (!currentItem.enabled) return;
          
          const newIndex = currentIndex + direction;
          
          // Check bounds
          if (newIndex < 0 || newIndex >= window.orderList.length) return;
          
          // Don't allow swapping with disabled items (keep them at bottom)
          const targetItem = window.orderList[newIndex];
          if (!targetItem.enabled) return;
          
          // Swap items
          [window.orderList[currentIndex], window.orderList[newIndex]] = [window.orderList[newIndex], window.orderList[currentIndex]];
          
          // Re-render
          renderOrderList();
          
          // Highlight the moved item
          setTimeout(() => {
            const movedItem = document.querySelector(`[data-order-id="${itemId}"]`);
            if (movedItem) {
              // Add highlight class
              movedItem.classList.add('highlighted');
              
              // Scroll into view if needed
              movedItem.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
              
              // Remove highlight after animation
              setTimeout(() => {
                movedItem.classList.remove('highlighted');
              }, 1200);
            }
          }, 50);
        };

        // Add field to order list (dipanggil saat add custom field)
        function addFieldToOrder(fieldId, label) {
          window.orderList.push({
            id: `custom_${fieldId}`,
            label: label || `Field ${fieldId}`,
            type: 'custom',
            field_id: fieldId,
            enabled: true,
            is_required: false,
            include_in_export: false   // default tidak masuk export
          });
          normalizeOrderListDisabledToBottom();
          renderOrderList();
        }

        // Remove field from order list (dipanggil saat remove custom field)
        function removeFieldFromOrder(fieldId) {
          window.orderList = window.orderList.filter(f => f.id !== `custom_${fieldId}`);
          normalizeOrderListDisabledToBottom();
          renderOrderList();
        }

        // Override addField untuk sync dengan order
        const originalAddField = window.addField;
        window.addField = function() {
          originalAddField();
          // Tambahkan ke order list dengan label placeholder
          const newFieldId = fieldCounter;
          addFieldToOrder(newFieldId, `Field Tambahan ${newFieldId}`);
          
          // Listen label input untuk update order list
          setTimeout(() => {
            const labelInput = document.querySelector(`input[name=\"fields[${newFieldId}][label_field]\"]`);
            if (labelInput) {
              labelInput.addEventListener('input', function() {
                const orderField = window.orderList.find(f => f.id === `custom_${newFieldId}`);
                if (orderField) {
                  orderField.label = this.value || `Field Tambahan ${newFieldId}`;
                  renderOrderList();
                }
              });
            }
            // Listen checkbox is_required di Data Tambahan → sync ke Urutan Export
            const reqCb = document.querySelector(`input[name=\"fields[${newFieldId}][is_required]\"]`);
            if (reqCb) {
              reqCb.addEventListener('change', function() {
                const orderField = window.orderList.find(f => f.id === `custom_${newFieldId}`);
                if (orderField) {
                  orderField.is_required = this.checked;
                  renderOrderList();
                }
              });
            }
            // Listen checkbox include_in_export di Data Tambahan → sync ke Urutan Export
            const expCb = document.querySelector(`input[name=\"fields[${newFieldId}][include_in_export]\"]`);
            if (expCb) {
              expCb.addEventListener('change', function() {
                const orderField = window.orderList.find(f => f.id === `custom_${newFieldId}`);
                if (orderField) {
                  orderField.include_in_export = this.checked;
                  renderOrderList();
                }
              });
            }
          }, 100);
        };

        // Override removeField untuk sync dengan order
        const originalRemoveField = window.removeField;
        window.removeField = function(id) {
          originalRemoveField(id, function() {
            removeFieldFromOrder(id);
          });
        };

        // Initialize order list on load
        // console.log('[INIT] Initializing order list...');
        renderOrderList();
        // console.log('[INIT] Order list initialization complete');

        // Samakan alur submit native (mis. tekan Enter) dengan tombol Simpan
        const jenisForm = document.getElementById('jenis-form');
        if (jenisForm) {
          jenisForm.addEventListener('submit', function(e) {
            e.preventDefault();
            confirmSubmit();
          });
        }

        // Initialize
        // console.log('[INIT] Initializing pejabat list...');
        renderPejabatList();
        // console.log('[INIT] All initialization complete!');
      })();

      // Confirm Cancel
      function confirmCancel() {
        if (confirm('Apakah Anda yakin ingin membatalkan? Data yang sudah diisi tidak akan tersimpan.')) {
          window.location.href = "{{ route('jenis-inventaris.index') }}";
        }
      }

      // Sync order list to hidden inputs before form submission
      function syncOrderToForm() {
        // console.log('[SYNC] Starting syncOrderToForm...');
        // console.log('[SYNC] orderList:', window.orderList);
        
        const container = document.getElementById('field-order-container');
        if (!container) {
          console.error('[SYNC] Container field-order-container NOT FOUND!');
          return;
        }
        container.innerHTML = ''; // Clear existing
        
        // Filter only enabled fields
        const enabledFields = window.orderList.filter(f => {
          if (f.type === 'fixed') return f.enabled;
          if (f.type === 'custom') return f.enabled;
          return true;
        });
        
        // console.log('[SYNC] Enabled fields count:', enabledFields.length);
        
        let fixedCount = 0;
        let customCount = 0;
        
        enabledFields.forEach((field, index) => {
          const orderNumber = index + 1;
          
          if (field.type === 'fixed') {
            // For enabled fixed fields, create hidden input
            if (!field.fixed_name) {
              console.error('[SYNC] Fixed field missing fixed_name:', field);
              return;
            }
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = `fixed_fields[${field.fixed_name}]`;
            input.value = orderNumber;
            container.appendChild(input);
            // Also send is_required for fixed fields
            const reqInput = document.createElement('input');
            reqInput.type = 'hidden';
            reqInput.name = `fixed_fields_required[${field.fixed_name}]`;
            reqInput.value = field.is_required ? '1' : '0';
            container.appendChild(reqInput);
            // Send include_in_export for fixed fields
            const expInput = document.createElement('input');
            expInput.type = 'hidden';
            expInput.name = `fixed_fields_export[${field.fixed_name}]`;
            expInput.value = field.include_in_export !== false ? '1' : '0';
            container.appendChild(expInput);
            fixedCount++;
          } else if (field.type === 'custom' && field.field_id) {
            // For custom fields, update the hidden order input in the field item
            const fieldItem = document.getElementById(`field-${field.field_id}`);
            if (fieldItem) {
              const orderInput = fieldItem.querySelector('.field-order-input');
              if (orderInput) {
                orderInput.value = orderNumber;
                customCount++;
              } else {
                console.error('[SYNC] Order input not found for field:', field.field_id);
              }
            } else {
              console.error('[SYNC] Field item not found:', `field-${field.field_id}`);
            }
          } else if (field.type === 'custom' && field.dropdown_id) {
            // Dropdown field dari selected_dropdowns — kirim order via hidden input
            const ddOrderInput = document.createElement('input');
            ddOrderInput.type = 'hidden';
            ddOrderInput.name = `selected_dropdowns[${field.dropdown_id}][order]`;
            ddOrderInput.value = orderNumber;
            container.appendChild(ddOrderInput);
          }
        });
        
        // console.log('[SYNC] Summary: Fixed fields =', fixedCount, ', Custom fields =', customCount);
        // console.log('[SYNC] Container HTML:', container.innerHTML.substring(0, 500));
      }

      // Confirm Submit
      function confirmSubmit() {
        const form = document.getElementById('jenis-form');
        
        if (!form.checkValidity()) {
          form.reportValidity();
          return;
        }

        const nama = document.getElementById('nama').value.trim();
        if (!nama) {
          alert('Nama jenis inventaris wajib diisi!');
          return;
        }

        // Tampilkan konfirmasi DULU, baru sync dan submit setelah user klik Ya
        AppModal.confirm(
          'Konfirmasi Simpan Data',
          'Apakah Anda yakin data jenis inventaris yang dimasukkan sudah benar dan siap disimpan?',
          function() {
            syncOrderToForm();
            if (typeof window.updateHiddenPejabatInputs === 'function') {
              window.updateHiddenPejabatInputs();
            }
            form.submit();
          }
        );
      }

      // Test form data (untuk debug tanpa submit)
      // function testFormData() {
      //   const form = document.getElementById('jenis-form');
        
      //   console.log('[TEST] ===== TESTING FORM DATA (NO SUBMIT) =====');
      //   console.log('[TEST] Syncing order to form...');
        
      //   // Sync order
      //   syncOrderToForm();
        
      //   // Check if hidden inputs created in DOM
      //   const container = document.getElementById('field-order-container');
      //   console.log('[TEST] Container HTML:', container.innerHTML);
      //   console.log('[TEST] Container children count:', container.children.length);
        
      //   console.log('[TEST] Reading form data...');
      //   const formData = new FormData(form);
      //   let fixedFieldsData = [];
      //   let customFieldsData = [];
      //   let allFormData = [];
        
      //   for (let [key, value] of formData.entries()) {
      //     allFormData.push(`${key} = ${value}`);
      //     if (key.includes('fixed_fields')) {
      //       fixedFieldsData.push(`${key} = ${value}`);
      //     }
      //     if (key.includes('fields[') && key.includes('[label_field]')) {
      //       customFieldsData.push(value);
      //     }
      //   }
        
      //   console.log('[TEST] ===== SUMMARY =====');
      //   console.log('[TEST] Fixed Fields Count:', fixedFieldsData.length);
      //   console.log('[TEST] Fixed Fields Data:', fixedFieldsData);
      //   console.log('[TEST] Custom Fields Count:', customFieldsData.length);
      //   console.log('[TEST] Custom Fields Data:', customFieldsData);
      //   console.log('[TEST] ===== ALL FORM DATA =====');
      //   allFormData.forEach(data => console.log('[TEST]', data));
      //   console.log('[TEST] ===== END TEST =====');
        
      //   // Tampilkan alert summary
      //   let summary = '=== DEBUG FORM DATA ===\n\n';
      //   summary += `Container children: ${container.children.length}\n`;
      //   summary += `Fixed Fields: ${fixedFieldsData.length}\n`;
      //   summary += `Custom Fields: ${customFieldsData.length}\n\n`;
      //   if (fixedFieldsData.length === 0) {
      //     summary += '⚠️ WARNING: NO FIXED FIELDS DETECTED!\n';
      //     summary += 'Hidden inputs tidak terbuat di container!\n\n';
      //   }
      //   summary += 'Lihat Console (F12) untuk detail lengkap!';
      //   alert(summary);
      // }


      // kode aneh yang ga kepakai
      //   const fieldItems = document.querySelectorAll('.field-item');
      //   for (const item of fieldItems) {
      //     const namaField = item.querySelector('input[name*="[nama]"]').value.trim();
      //     const labelField = item.querySelector('input[name*="[label]"]').value.trim();
          
      //     if (!namaField || !labelField) {
      //       alert('Semua field dinamis harus memiliki nama dan label!');
      //       return;
      //     }
      //   }

      //   if (confirm('Apakah Anda yakin ingin menyimpan jenis inventaris ini?')) {
      //     const submitEvent = new Event('submit', { bubbles: true, cancelable: true });
      //     form.dispatchEvent(submitEvent);
      //   }
      // }

      window.onRequiresRuanganChange = function(checked) {
        const label = document.getElementById('requires-ruangan-label');
        if (label) {
          label.style.borderColor = checked ? '#519259' : '#e5e7eb';
          label.style.background  = checked ? '#519259' : '#ffffff';
          var allDivs = label.querySelectorAll('div');
          allDivs.forEach(function(d) {
            if (d.style.fontSize === '13px') d.style.color = checked ? '#fff' : '#374151';
            if (d.style.fontSize === '12px') d.style.color = checked ? 'rgba(255,255,255,0.8)' : '#6b7280';
          });
        }
      };
      // Init on load
      document.addEventListener('DOMContentLoaded', function() {
        const cb = document.getElementById('requires_ruangan');
        if (cb) window.onRequiresRuanganChange(cb.checked);
      });

      // ===== ICON & COLOR PICKER =====
      (function() {
        const ICONS = [
          // Transportasi & Kendaraan
          'truck','package','navigation','anchor','compass','send',
          // Bangunan & Properti
          'home','map','map-pin','globe','layout','grid',
          // Alam & Lingkungan
          'droplet','sun','cloud','wind','thermometer','umbrella',
          // Peralatan & Mesin
          'tool','settings','sliders','cpu','hard-drive','server',
          // Elektronik & Teknologi
          'monitor','smartphone','tablet','printer','camera','video',
          // Listrik & Energi
          'zap','battery','power','radio','wifi','bluetooth',
          // Furnitur & Kantor
          'sidebar','briefcase','inbox','archive','clipboard','folder',
          // Dokumen & Data
          'file-text','book-open','book','file','layers','database',
          // Keuangan & Nilai
          'dollar-sign','credit-card','trending-up','bar-chart-2','pie-chart','activity',
          // Orang & Organisasi
          'users','user','user-check','award','shield','key',
          // Umum & Lainnya
          'package','box','tag','grid','list','star',
          'heart','flag','bell','clock','calendar','check-circle',
          'alert-triangle','info','help-circle','eye','search','lock',
          'trash-2','edit','plus-circle','minus-circle','refresh-cw','share-2',
        ];

        const COLOR_PRESETS = [
          '#064635','#6366f1','#0ea5e9','#14b8a6','#10b981',
          '#84cc16','#f59e0b','#f97316','#ef4444','#f43f5e',
          '#ec4899','#a855f7','#8b5cf6','#06b6d4'
        ];

        let selectedIcon  = '';
        let selectedColor = '';

        function renderIconGrid() {
          const grid = document.getElementById('icon-picker-grid');
          if (!grid) return;
          grid.innerHTML = ICONS.map(icon => `
            <button type="button" data-icon="${icon}"
              onclick="selectIcon('${icon}')"
              title="${icon}"
              style="width:100%;aspect-ratio:1;border:2px solid #e5e7eb;border-radius:8px;background:#fff;
                     display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all 0.15s;"
              id="icon-btn-${icon}">
              <i data-feather="${icon}" style="width:18px;height:18px;color:#374151;stroke:#374151;pointer-events:none;"></i>
            </button>
          `).join('');
          if (typeof feather !== 'undefined') feather.replace();
        }

        function renderColorGrid() {
          const grid = document.getElementById('color-preset-grid');
          if (!grid) return;
          grid.innerHTML = COLOR_PRESETS.map(color => `
            <button type="button" data-color="${color}"
              onclick="selectColor('${color}')"
              title="${color}"
              style="width:100%;aspect-ratio:1;border-radius:8px;background:${color};
                     border:3px solid transparent;cursor:pointer;transition:all 0.15s;"
              id="color-btn-${color.replace('#','')}">
            </button>
          `).join('');
        }

        window.selectIcon = function(icon) {
          selectedIcon = icon;
          document.getElementById('custom_icon_input').value = icon;
          // Update button styles
          document.querySelectorAll('#icon-picker-grid button').forEach(btn => {
            const isSelected = btn.dataset.icon === icon;
            btn.style.borderColor = isSelected ? '#064635' : '#e5e7eb';
            btn.style.background  = isSelected ? '#eff6ff' : '#fff';
          });
          updatePreview();
        };

        window.selectColor = function(color) {
          selectedColor = color;
          document.getElementById('custom_color_input').value = color;
          document.getElementById('custom_color_picker').value = color;
          document.getElementById('custom_color_hex').textContent = color;
          // Update button styles
          document.querySelectorAll('#color-preset-grid button').forEach(btn => {
            btn.style.borderColor = btn.dataset.color === color ? '#1f2937' : 'transparent';
          });
          updatePreview();
        };

        window.onCustomColorInput = function(color) {
          selectedColor = color;
          document.getElementById('custom_color_input').value = color;
          document.getElementById('custom_color_hex').textContent = color;
          // Deselect presets
          document.querySelectorAll('#color-preset-grid button').forEach(btn => {
            btn.style.borderColor = 'transparent';
          });
          updatePreview();
        };

        function updatePreview() {
          const box  = document.getElementById('icon-preview-box');
          const icon = document.getElementById('icon-preview-icon');
          if (box && selectedColor) box.style.background = selectedColor;
          if (icon && selectedIcon) {
            icon.setAttribute('data-feather', selectedIcon);
            if (typeof feather !== 'undefined') feather.replace();
          }
        }

        window.onIconModeChange = function(mode) {
          const manualOpts = document.getElementById('manual-icon-options');
          const cardAuto   = document.getElementById('card-auto');
          const cardManual = document.getElementById('card-manual');
          if (!manualOpts) return;

          function styleCard(card, isActive) {
            card.style.borderColor = isActive ? '#519259' : '#e5e7eb';
            card.style.background  = isActive ? '#519259' : '#fff';
            var iconBox = card.children[0];
            if (iconBox && iconBox.tagName === 'DIV') {
              iconBox.style.background = isActive ? 'rgba(255,255,255,0.2)' : '#e5e7eb';
              var svgEl = iconBox.querySelector('svg');
              var iEl   = iconBox.querySelector('i');
              var iconTarget = svgEl || iEl;
              if (iconTarget) {
                iconTarget.style.color  = isActive ? '#fff' : '#6b7280';
                iconTarget.style.stroke = isActive ? '#fff' : '#6b7280';
              }
            }
            var textBox = card.children[1];
            if (textBox && textBox.tagName === 'DIV') {
              var title = textBox.children[0];
              var desc  = textBox.children[1];
              if (title) title.style.color = isActive ? '#fff' : '#374151';
              if (desc)  desc.style.color  = isActive ? 'rgba(255,255,255,0.8)' : '#6b7280';
            }
          }

          styleCard(cardAuto, mode === 'auto');
          styleCard(cardManual, mode === 'manual');

          if (mode === 'manual') {
            manualOpts.style.display = 'block';
            if (!selectedIcon)  { window.selectIcon('layers'); }
            if (!selectedColor) { window.selectColor('#064635'); }
          } else {
            manualOpts.style.display = 'none';
            document.getElementById('custom_icon_input').value  = '';
            document.getElementById('custom_color_input').value = '';
          }
        };

        // Init on DOM ready
        document.addEventListener('DOMContentLoaded', function() {
          renderIconGrid();
          renderColorGrid();
        });
      })();
    </script>
  </body>
</html>
