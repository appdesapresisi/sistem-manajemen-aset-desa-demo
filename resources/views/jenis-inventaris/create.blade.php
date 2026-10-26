<!doctype html>
<html lang="en" data-pc-preset="{{ $pc_preset_theme }}" data-pc-theme="{{ $pc_dark_layout }}">
  <head>
    @include('layouts.head-page-meta', ['title' => 'Tambah Jenis Inventaris'])
    @include('layouts.head-css')
    <style>
      /* Form Styling - SIMA Standard */
      .form-section {
        background: transparent;
        border: none;
        padding: 0;
        margin-bottom: 24px;
      }
      .form-section + .form-section {
        margin-top: 24px;
        padding-top: 24px;
        border-top: 1px dashed var(--color-border);
      }
      .form-section-title {
        font-size: 14.5px;
        font-weight: 700;
        color: var(--color-primary);
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
      }
      .form-section-title i,
      .form-section-title svg {
        color: var(--color-primary);
        width: 16px;
        height: 16px;
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
        font-weight: 600;
        font-size: 13.5px;
        color: var(--color-text);
        margin-bottom: 8px;
        display: block;
      }
      .form-label .required {
        color: #D9383A;
        font-weight: 700;
        margin-left: 2px;
      }
      .form-label .optional {
        color: var(--color-text-muted);
        font-weight: 400;
        font-size: 12px;
        margin-left: 4px;
      }
      .form-control, textarea.form-control {
        width: 100%;
        height: 46px;
        padding: 0 16px;
        border: 1.5px solid var(--color-border);
        border-radius: 10px;
        font-size: 14px;
        transition: border-color 0.2s ease;
        background: var(--color-surface);
        color: var(--color-text);
        box-shadow: none !important;
      }
      textarea.form-control {
        height: auto;
        min-height: 80px;
        padding: 12px 16px;
      }
      .form-control:focus, textarea.form-control:focus {
        outline: none !important;
        border-color: var(--color-primary) !important;
        box-shadow: none !important;
      }
      .form-control::placeholder, textarea.form-control::placeholder {
        color: var(--color-text-muted);
        opacity: 0.65;
      }
      .form-hint {
        font-size: 12px;
        color: var(--color-text-muted);
        margin-top: 6px;
      }
      .form-full {
        grid-column: 1 / -1;
      }

      /* Card Header */
      .card .card-header,
      .card-header {
        background-color: transparent !important;
        padding: 32px 24px 0 24px !important;
        border: none !important;
        border-bottom: none !important;
      }
      .card .card-body,
      .card-body {
        padding-bottom: 0 !important;
      }
      .card .card-header > div,
      .card-header > div,
      .card-header-inner,
      .card-header > .flex {
        border-bottom: 1px dashed var(--color-border) !important;
        padding-bottom: 20px !important;
        width: 100% !important;
      }
      .card-header-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        min-width: 44px;
        border-radius: 12px;
        background: var(--color-primary);
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

      /* Pejabat Multi-select */
      .pejabat-select-area {
        border: 1.5px solid var(--color-border);
        border-radius: 12px;
        background: var(--color-surface);
        overflow: hidden;
      }
      .pejabat-select-header {
        background: var(--color-surface);
        padding: 14px 18px;
        border-bottom: 1px dashed var(--color-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
      }
      .pejabat-select-header-title {
        font-size: 13.5px;
        font-weight: 600;
        color: var(--color-text);
      }
      .pejabat-count {
        font-size: 12px;
        background: var(--color-primary);
        color: #fff;
        padding: 3px 10px;
        border-radius: var(--radius-full);
        font-weight: 600;
      }
      .pejabat-list {
        max-height: 240px;
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: var(--color-border) transparent;
      }
      .pejabat-list::-webkit-scrollbar {
        width: 5px;
      }
      .pejabat-list::-webkit-scrollbar-track {
        background: transparent;
      }
      .pejabat-list::-webkit-scrollbar-thumb {
        background-color: var(--color-border);
        border-radius: var(--radius-full);
      }
      .pejabat-list::-webkit-scrollbar-thumb:hover {
        background-color: var(--color-text-muted);
      }
      .pejabat-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 18px;
        border-bottom: 1px solid var(--color-border);
        cursor: pointer;
        transition: all 0.15s;
        background: var(--color-surface);
      }
      .pejabat-item:last-child {
        border-bottom: none;
      }
      .pejabat-item:hover {
        background: var(--color-row-hover);
      }
      .pejabat-item.selected {
        background: var(--color-primary-tint);
      }
      .pejabat-item input[type="checkbox"] {
        cursor: pointer;
      }
      .pejabat-info {
        flex: 1;
      }
      .pejabat-nama {
        font-size: 14px;
        font-weight: 600;
        color: var(--color-text);
      }
      .pejabat-jabatan {
        font-size: 12px;
        color: var(--color-text-muted);
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
        background: var(--color-primary);
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
        width: 18px;
        height: 14px;
        border: none !important;
        background: transparent !important;
        box-shadow: none !important;
        padding: 0 !important;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: color 0.15s ease, transform 0.15s ease;
      }
      .order-btn:hover {
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        transform: scale(1.18);
      }
      .order-btn i,
      .order-btn svg {
        width: 13px;
        height: 13px;
        color: var(--color-text-muted);
        stroke: var(--color-text-muted);
        transition: color 0.15s ease, stroke 0.15s ease;
      }
      .order-btn:hover i,
      .order-btn:hover svg {
        color: var(--color-primary);
        stroke: var(--color-primary);
      }

      /* Dynamic Fields Section */
      .fields-container {
        border: 1.5px solid var(--color-border);
        border-radius: 12px;
        background: var(--color-surface);
        overflow: hidden;
      }
      .fields-header {
        background: var(--color-surface);
        border-bottom: 1px dashed var(--color-border);
        padding: 14px 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
      }
      .fields-header-title {
        font-size: 13.5px;
        font-weight: 600;
        color: var(--color-text);
        display: flex;
        align-items: center;
        gap: 8px;
      }
      .fields-header-title i,
      .fields-header-title svg {
        width: 16px;
        height: 16px;
        color: var(--color-primary);
        stroke: var(--color-primary);
      }
      .btn-add-field {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        background: var(--color-primary);
        border: none;
        border-radius: var(--radius-full) !important;
        color: #fff;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
      }
      .btn-add-field:hover {
        background: var(--color-primary-hover);
        color: #fff;
      }
      .btn-add-field i,
      .btn-add-field svg {
        width: 14px;
        height: 14px;
        stroke: #fff;
        color: #fff;
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
        color: var(--color-text-muted);
      }
      .fields-empty-icon {
        width: 48px;
        height: 48px;
        margin-bottom: 12px;
        background: var(--color-primary-tint);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
      }
      .fields-empty-icon i {
        width: 24px;
        height: 24px;
        color: var(--color-primary);
      }
      .fields-empty-text {
        font-size: 14px;
        margin-bottom: 4px;
        font-weight: 600;
        color: var(--color-text);
      }
      .fields-empty-hint {
        font-size: 12px;
        color: var(--color-text-muted);
      }
      .field-item {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        padding: 16px 14px;
        border-bottom: 1px solid var(--color-border);
        background: var(--color-surface);
        transition: all 0.15s;
      }
      .field-item:hover {
        background: var(--color-row-hover);
      }
      .field-item:last-child {
        border-bottom: none;
      }
      .field-drag {
        display: none;
      }
      .field-order-num {
        width: 24px;
        min-width: 24px;
        height: 42px;
        background: transparent !important;
        border-radius: 0 !important;
        border: none !important;
        box-shadow: none !important;
        color: var(--color-text-muted, #546958) !important;
        font-size: 13.5px !important;
        font-weight: 600 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        flex-shrink: 0 !important;
        margin-top: 0 !important;
        user-select: none;
      }
      .field-inputs {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 6px;
        min-width: 0;
      }
      .field-inputs-row {
        display: flex;
        flex-direction: row;
        gap: 8px;
        align-items: center;
        width: 100%;
        position: relative;
      }
      .field-inputs-row .field-input:not(.field-select) {
        flex: 1 1 auto !important;
        min-width: 180px !important;
        width: 100% !important;
        height: 42px !important;
        min-height: 42px !important;
        max-height: 42px !important;
        padding: 0 14px !important;
        border: 1px solid var(--color-border) !important;
        border-radius: var(--radius-sm, 8px) !important;
        font-size: 13.5px !important;
        background: var(--color-surface) !important;
        color: var(--color-text) !important;
        box-sizing: border-box !important;
        line-height: 40px !important;
        transition: border-color 0.15s ease !important;
      }
      .field-inputs-row .field-input:not(.field-select):focus {
        outline: none !important;
        border-color: var(--color-primary) !important;
        box-shadow: none !important;
      }
      .field-inputs-row .field-select {
        flex: 0 0 170px !important;
        width: 170px !important;
        min-width: 150px !important;
        max-width: 180px !important;
        height: 42px !important;
        min-height: 42px !important;
        max-height: 42px !important;
        padding: 0 32px 0 12px !important;
        line-height: 40px !important;
        font-size: 13px !important;
        background-color: var(--color-surface) !important;
        border: 1px solid var(--color-border) !important;
        border-radius: var(--radius-sm, 8px) !important;
        color: var(--color-text) !important;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%235E6C5A' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E") !important;
        background-repeat: no-repeat !important;
        background-position: right 12px center !important;
        background-size: 14px 14px !important;
        cursor: pointer !important;
        appearance: none !important;
        -webkit-appearance: none !important;
        -moz-appearance: none !important;
        box-sizing: border-box !important;
        transition: border-color 0.15s ease !important;
      }
      .field-inputs-row .field-select:focus {
        outline: none !important;
        border-color: var(--color-primary) !important;
        box-shadow: none !important;
      }
      .field-toggles {
        display: flex;
        flex-direction: row;
        gap: 16px;
        align-items: center;
        justify-content: flex-end;
        width: 100%;
        margin-left: auto;
        padding-top: 2px;
      }
      .field-required-toggle,
      .field-export-toggle {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        font-weight: 500;
        color: var(--color-text);
        cursor: pointer;
        user-select: none;
        white-space: nowrap;
      }
      .field-required-toggle input,
      .field-export-toggle input {
        width: 14px;
        height: 14px;
        margin: 0;
        accent-color: var(--color-primary);
        cursor: pointer;
      }
      .field-actions {
        display: flex;
        align-items: center;
        gap: 4px;
        flex-shrink: 0;
        height: 42px;
        margin-top: 0;
      }
      .field-action-btn {
        width: 36px;
        height: 36px;
        border: none;
        border-radius: var(--radius-sm, 6px);
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
        display: flex;
        width: 100%;
        box-sizing: border-box;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        border-radius: 8px;
        background: var(--color-primary-tint);
        border: 1px solid var(--color-border);
        color: var(--color-text);
        font-size: 12px;
        font-weight: 500;
        line-height: 1.45;
        margin-top: 14px;
      }
      .info-box i,
      .info-box svg {
        color: var(--color-primary);
        stroke: var(--color-primary);
        flex-shrink: 0;
        width: 14px;
        height: 14px;
      }

      /* Search box di form-section mengikuti ukuran standar index manajemen aset */
      .form-section .topbar-search-box {
        width: 280px !important;
        max-width: 100% !important;
        height: 38px !important;
        min-height: 38px !important;
        padding: 0 14px !important;
        margin-bottom: 14px;
        box-sizing: border-box !important;
      }
      .form-section .topbar-search-box:focus-within {
        width: 360px !important;
        max-width: 100% !important;
      }
      .info-box-text {
        font-size: 12px;
        color: var(--color-text);
        line-height: 1.45;
        font-weight: 500;
        flex: 1;
      }
      .info-box-text strong {
        font-weight: 600;
        color: var(--color-text);
      }

      /* Order Section */
      .order-section {
        background: transparent;
        border: none;
        padding: 0;
        margin-bottom: 24px;
        margin-top: 24px;
        padding-top: 24px;
        border-top: 1px dashed var(--color-border);
      }
      .order-section-title {
        font-size: 14.5px;
        font-weight: 700;
        color: var(--color-primary);
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
      }
      .order-section-title i,
      .order-section-title svg {
        width: 16px;
        height: 16px;
        color: var(--color-primary);
      }
      .order-hint {
        display: flex;
        width: 100%;
        box-sizing: border-box;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        border-radius: 8px;
        background: var(--color-primary-tint);
        border: 1px solid var(--color-border);
        color: var(--color-text);
        font-size: 12px;
        font-weight: 500;
        line-height: 1.4;
        margin-bottom: 16px;
      }
      .order-hint i,
      .order-hint svg {
        color: var(--color-primary);
        stroke: var(--color-primary);
        flex-shrink: 0;
        width: 14px;
        height: 14px;
      }
      .order-hint span {
        font-size: 12px;
        color: var(--color-text);
        flex: 1;
      }
      .order-list {
        background: var(--color-surface);
        border: 1.5px solid var(--color-border);
        border-radius: 12px;
        padding: 16px;
        min-height: 240px;
      }
      .order-rows-wrapper {
        display: flex;
        flex-direction: column;
        gap: 8px;
        width: 100%;
      }
      .order-item {
        width: 100%;
        min-width: 0;
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        padding-right: 52px;
        background: var(--color-surface);
        border: 1.5px solid var(--color-border);
        border-radius: 8px;
        color: var(--color-text);
        transition: all 0.2s;
        min-height: 52px;
        position: relative;
      }
      .order-number {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 20px;
        color: var(--color-text-muted);
        font-size: 13px;
        font-weight: 700;
        font-variant-numeric: tabular-nums;
        flex-shrink: 0;
      }
      .order-item:hover {
        background: var(--color-row-hover);
      }
      .order-item.highlighted {
        border-color: var(--color-primary);
        background: var(--color-primary-tint);
      }
      .order-item.sortable-ghost {
        opacity: 0.4;
        background: var(--color-row-hover);
      }
      .order-drag-handle {
        color: var(--color-text-muted);
        display: flex;
        align-items: center;
      }
      .order-item-content {
        flex: 1;
        display: flex;
        align-items: center;
        gap: 10px;
      }
      .order-item-label {
        flex: 1;
        font-size: 13.5px;
        font-weight: 600;
        color: var(--color-text);
      }
      .order-item-badge {
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        flex-shrink: 0;
      }
      .order-item-badge.fixed {
        background: var(--color-primary-tint);
        color: var(--color-primary);
        border: 1px solid var(--color-primary);
        display: flex;
        align-items: center;
        gap: 4px;
      }
      .order-item-badge.custom {
        background: #dbeafe;
        color: #1e40af;
        border: 1px solid #93c5fd;
      }
      .order-item.disabled {
        opacity: 0.5;
        background: var(--color-row-hover);
      }
      .order-item.disabled .order-item-label {
        text-decoration: line-through;
        color: var(--color-text-muted);
      }
      .order-item-checkbox {
        width: 18px;
        height: 18px;
        accent-color: var(--color-primary);
        cursor: pointer;
      }
      .order-item-required,
      .order-item-export {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 12px;
        font-weight: 500;
        color: var(--color-text-muted);
        white-space: nowrap;
        cursor: pointer;
        user-select: none;
      }
      .order-item-required input[type="checkbox"],
      .order-item-export input[type="checkbox"] {
        width: 14px;
        height: 14px;
        accent-color: var(--color-primary);
        cursor: pointer;
        margin: 0;
      }
      .order-item-flags {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 16px;
        margin-right: 52px;
        flex-shrink: 0;
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
        gap: 2px;
      }
      .order-move-btn {
        width: 24px;
        height: 20px;
        border: none;
        background: transparent;
        border-radius: 4px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: color 0.15s, transform 0.15s;
        flex-shrink: 0;
        padding: 0;
        color: var(--color-text-muted);
      }
      .order-move-btn:hover:not(:disabled) {
        background: transparent;
        border: none;
        color: var(--color-primary);
        transform: scale(1.15);
      }
      .order-move-btn:disabled {
        cursor: not-allowed;
        opacity: 0.25;
      }
      .order-move-btn i {
        width: 15px;
        height: 15px;
        color: currentColor;
      }
      .order-empty {
        text-align: center;
        padding: 60px 20px;
        color: var(--color-text-muted);
      }
      .order-empty i {
        width: 48px;
        height: 48px;
        margin-bottom: 12px;
        opacity: 0.5;
        color: var(--color-text-muted);
      }
      .order-empty-text {
        font-size: 14px;
        margin-bottom: 4px;
        font-weight: 600;
        color: var(--color-text);
      }
      .order-empty-hint {
        font-size: 12px;
        color: var(--color-text-muted);
      }
      /* Buttons */
      .form-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        padding-top: 24px;
        border-top: none;
        margin-top: 24px;
        margin-bottom: 32px;
      }
      .btn-cancel {
        height: 46px;
        padding: 0 20px;
        border: 1.5px solid var(--color-border);
        background: var(--color-surface);
        color: var(--color-text);
        border-radius: var(--radius-full);
        font-weight: 500;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: none !important;
      }
      .btn-cancel:hover {
        background: var(--color-row-hover);
        border-color: var(--color-border);
        color: var(--color-text);
      }
      .btn-submit {
        height: 46px;
        padding: 0 24px;
        border: none;
        background: var(--color-primary);
        color: #fff;
        border-radius: var(--radius-full);
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        box-shadow: none !important;
        transition: background-color 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
      }
      .btn-submit:hover {
        background: var(--color-primary-hover);
        color: #fff;
      }
      .btn-cancel i,
      .btn-cancel svg {
        width: 16px;
        height: 16px;
      }
      .btn-submit i,
      .btn-submit svg {
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
        .fields-header {
          flex-direction: column;
          align-items: flex-start;
          gap: 10px;
        }
        .btn-add-field {
          width: 100%;
          justify-content: center;
        }
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
        .field-inputs-row {
          flex-direction: column;
          align-items: stretch;
        }
        .field-inputs-row .field-input:not(.field-select) {
          width: 100% !important;
          flex: 1 1 100% !important;
        }
        .field-inputs-row .field-select {
          width: 100% !important;
          max-width: 100% !important;
          flex: 1 1 100% !important;
        }
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
        .order-list {
          grid-template-columns: 1fr;
          gap: 8px;
          padding: 8px;
        }
        .order-numbers-column {
          display: none;
        }
        .order-number {
          display: inline-flex !important;
          min-width: 20px;
          background: transparent;
          border: none;
          color: var(--color-text-muted);
          font-size: 13px;
          font-weight: 700;
          font-variant-numeric: tabular-nums;
          align-items: center;
          justify-content: center;
          flex-shrink: 0;
        }
        .order-drag-handle {
          display: none;
        }
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
        @include('layouts.breadcrumb', ['breadcrumbItemHome' => 'Master Data', 'breadcrumbItem' => 'Jenis Inventaris', 'breadcrumbItemUrl' => route('jenis-inventaris.index'), 'breadcrumbItemActive' => 'Tambah Jenis Inventaris'])

        <div class="grid grid-cols-12 gap-x-6">
          <div class="col-span-12 w-full">
            <div class="card">
              <div class="card-header">
                <div class="flex items-center">
                  <div class="card-header-icon">
                    <i data-feather="layers"></i>
                  </div>
                  <div>
                    <h5 class="mb-0">Tambah Jenis Inventaris Baru</h5>
                  </div>
                </div>
              </div>

              <div class="card-body" style="padding-top: 14px !important;">
                <form id="jenis-form" action="{{ route('jenis-inventaris.store') }}" method="POST" class="no-loader" novalidate>
                  @csrf
                  
                  <!-- Hidden inputs for field order -->
                  <div id="field-order-container"></div>

                  <!-- Warning Box: Diletakkan di atas Informasi Dasar -->
                  <div id="template-name-warning" class="warning-box" style="display:none;margin-top:0;margin-bottom:20px;">
                    <i data-feather="alert-triangle"></i>
                    <span><strong>Perhatian:</strong> Jenis inventaris dengan nama ini sudah ada sebagai template sistem dan tidak dapat dibuat ulang.</span>
                  </div>
                  
                  <!-- Informasi Dasar -->
                  <div class="form-section">
                    <div class="form-section-title">
                      Informasi Dasar
                    </div>

                    <div class="form-grid">
                      <div class="form-group">
                        <label class="form-label" for="nama">
                          Nama Jenis Inventaris <span class="required">*</span>
                        </label>
                        <input type="text" id="nama" name="nama" class="form-control @error('nama') is-invalid @enderror" placeholder="Contoh: Inventaris Kendaraan" value="{{ old('nama') }}" required oninput="checkTemplateName(this.value)" />
                        @error('nama')
                        <div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>
                        @enderror
                        <p class="form-hint">Nama kategori yang akan muncul di menu dan form</p>
                      </div>
                      <div class="form-group">
                        <label class="form-label">Konfigurasi Ruangan</label>
                        <label style="display:flex;align-items:center;gap:12px;cursor:pointer;height:46px;box-sizing:border-box;padding:0 16px;background:{{ old('requires_ruangan') ? 'var(--color-primary-tint, #d5e4d7)' : 'var(--color-surface, #f8faf7)' }};border:1.5px solid {{ old('requires_ruangan') ? 'var(--color-primary, #1f3a2a)' : 'var(--color-border, #cfdcc8)' }};border-radius:10px;transition:background-color 0.15s ease, border-color 0.15s ease;margin:0;" id="requires-ruangan-label">
                          <input type="checkbox" name="requires_ruangan" id="requires_ruangan" value="1"
                                 {{ old('requires_ruangan') ? 'checked' : '' }}
                                 style="width:18px;height:18px;accent-color:var(--color-primary, #1f3a2a);cursor:pointer;flex-shrink:0;margin:0;"
                                 onchange="onRequiresRuanganChange(this.checked)">
                          <div style="display:flex;flex-direction:column;justify-content:center;min-width:0;">
                            <div style="font-size:13px;font-weight:600;color:var(--color-text, #16251b);line-height:1.25;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">Membutuhkan Data Ruangan</div>
                            <div style="font-size:11px;color:var(--color-text-muted, #546958);line-height:1.2;margin-top:1px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">Field Ruangan akan muncul saat input aset.</div>
                          </div>
                        </label>
                      </div>
                      <div class="form-group form-full">
                        <label class="form-label" for="deskripsi">
                          Deskripsi <span class="required">*</span>
                        </label>
                        <textarea required id="deskripsi" name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" rows="4" placeholder="Jelaskan jenis inventaris ini...">{{ old('deskripsi') }}</textarea>
                        @error('deskripsi')
                        <div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>
                        @enderror
                        <p class="form-hint">Deskripsi singkat tentang jenis inventaris ini</p>
                      </div>
                    </div>
                  </div>

                  <!-- Default Pejabat Penandatangan -->
                  <div class="form-section">
                    <div class="form-section-title">
                      Pejabat yang bertanda tangan
                    </div>
                    <p class="text-muted text-sm mb-4">Pilih pejabat yang akan menjadi penandatangan default saat export inventaris jenis ini. Urutan menentukan posisi tanda tangan.</p>

                    <!-- Search box pejabat (SIMA Standard / Manajemen Aset UI) -->
                    <div class="topbar-search-box">
                      <i data-feather="search" style="width:15px;height:15px;color:var(--color-text-muted);flex-shrink:0;"></i>
                      <input type="text" id="pejabat-search-input" class="topbar-search-input" placeholder="Cari pejabat..."
                             oninput="pejabatToggleSearchClear(this);filterPejabatList(this.value)" autocomplete="off" />
                      <span class="topbar-search-shortcut" id="pejabat-search-clear" onclick="clearPejabatSearch()" style="cursor:pointer;display:none;color:var(--color-danger);font-size:12px;font-weight:600;padding-left:6px;">Batal</span>
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

                    <div class="info-box" style="margin-top:14px;">
                      <i data-feather="info" style="color:var(--color-primary, #1f3a2a);stroke:var(--color-primary, #1f3a2a);"></i>
                      <div class="info-box-text">
                        <strong>Catatan:</strong> Pejabat yang sudah dipilih disini bisa diubah lagi saat diperlukan. Urutan pejabat dipilih akan menentukan posisi tanda tangan pada dokumen export.
                      </div>
                    </div>
                  </div>

                  <!-- Data Dinamis -->
                  <div class="form-section">
                    <div class="form-section-title">
                      Pilih Data Tambahan
                    </div>

                    <p class="text-muted text-sm mb-4">Tambahkan data khusus sesuai kebutuhan jenis inventaris ini. Contoh: untuk Kendaraan bisa tambah "Nomor Polisi", "Merk", "Tipe", dll.</p>

                    <div class="fields-container">
                      <div class="fields-header">
                        <span class="fields-header-title">
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
                      Pilih Dropdown
                    </div>

                    <p class="text-muted text-sm mb-4">Pilih dropdown dari master data yang akan digunakan sebagai field pilihan pada jenis inventaris ini.</p>

                    <div id="dropdown-fields-section">
                        <label style="display:flex;align-items:center;gap:12px;cursor:pointer;padding:14px 16px;background:{{ old('needs_dropdown') ? 'var(--color-primary-tint, #d5e4d7)' : 'var(--color-surface, #f8faf7)' }};border:1.5px solid {{ old('needs_dropdown') ? 'var(--color-primary, #1f3a2a)' : 'var(--color-border, #cfdcc8)' }};border-radius:10px;transition:background-color 0.15s ease, border-color 0.15s ease;" id="needs-dropdown-label">
                          <input type="checkbox" name="needs_dropdown" id="needs_dropdown" value="1"
                                 {{ old('needs_dropdown') ? 'checked' : '' }}
                                 onchange="onNeedsDropdownChange(this.checked)">
                          <div>
                            <div style="font-size:13.5px;font-weight:600;color:var(--color-text, #16251b);">Jenis inventaris ini membutuhkan field dropdown</div>
                            <div style="font-size:12px;color:var(--color-text-muted, #546958);margin-top:2px;">Jika dicentang, pilih dropdown dari master data yang akan digunakan sebagai field pilihan pada jenis inventaris ini.</div>
                          </div>
                        </label>

                        <div id="dropdown-list-wrapper" style="display:none;margin-top:14px;">
                          @if($dropdownList->isEmpty())
                            <div class="warning-box" style="margin-bottom:12px;">
                              <i data-feather="alert-triangle"></i>
                              <span>Belum ada dropdown di master data. <a href="{{ route('dropdown.create') }}" target="_blank" style="color:var(--color-primary, #1f3a2a);font-weight:600;margin-left:4px;text-decoration:underline;text-underline-offset:3px;">Buat sekarang →</a></span>
                            </div>
                          @else
                            <!-- Search box dropdown (SIMA Standard / Manajemen Aset UI) -->
                            <div class="topbar-search-box" style="margin-bottom:10px;">
                              <i data-feather="search" style="width:15px;height:15px;color:var(--color-text-muted);flex-shrink:0;"></i>
                              <input type="text" id="dd-search-input" class="topbar-search-input" placeholder="Cari dropdown..."
                                     oninput="ddToggleSearchClear(this);filterDropdownList(this.value)" autocomplete="off" />
                              <span class="topbar-search-shortcut" id="dd-search-clear" onclick="clearDropdownSearch()" style="cursor:pointer;display:none;color:var(--color-danger);font-size:12px;font-weight:600;padding-left:6px;">Batal</span>
                            </div>
                            <p style="font-size:12px;color:var(--color-text-muted, #546958);margin-bottom:10px;">Centang dropdown yang ingin digunakan, lalu atur opsinya:</p>
                            <div style="display:flex;flex-direction:column;gap:8px;" id="dropdown-checklist">
                              @foreach($dropdownList as $ddIdx => $dd)
                              @php $isHiddenCreate = $ddIdx >= 10; @endphp
                              <div style="display:{{ $isHiddenCreate ? 'none' : 'flex' }};align-items:center;gap:12px;padding:12px 16px;background:var(--color-surface, #f8faf7);border:1.5px solid var(--color-border, #cfdcc8);border-radius:10px;transition:all 0.2s;"
                                   class="dd-item-label {{ $isHiddenCreate ? 'dd-item-hidden' : '' }}" data-id="{{ $dd->id }}" data-label="{{ $dd->nama }}" data-name="{{ strtolower($dd->nama) }}">
                                <input type="checkbox" name="selected_dropdowns[{{ $dd->id }}][enabled]" value="1"
                                       class="dd-checkbox"
                                       style="width:18px;height:18px;accent-color:var(--color-primary, #1f3a2a);flex-shrink:0;cursor:pointer;"
                                       onchange="onDropdownItemCheck(this, {{ $dd->id }}, '{{ addslashes($dd->nama) }}')">
                                <div style="flex:1;">
                                  <div style="font-size:13.5px;font-weight:600;color:var(--color-text, #16251b);">{{ $dd->nama }}</div>
                                  <div style="font-size:11.5px;color:var(--color-text-muted, #546958);margin-top:1px;">{{ $dd->items->pluck('label')->implode(', ') }}</div>
                                </div>
                                <div id="dd-pills-{{ $dd->id }}" style="display:none;flex-direction:row;align-items:center;gap:16px;flex-shrink:0;">
                                  <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:12px;color:var(--color-text, #16251b);white-space:nowrap;">
                                    <input type="checkbox" name="selected_dropdowns[{{ $dd->id }}][required]" value="1"
                                           class="dd-req-cb"
                                           style="width:14px;height:14px;accent-color:var(--color-primary, #1f3a2a);cursor:pointer;margin:0;"
                                           onchange="onPillChange(this, 'dd-pill-req-{{ $dd->id }}', 'var(--color-primary, #1f3a2a)')">
                                    <span id="dd-pill-req-{{ $dd->id }}" style="font-weight:500;color:var(--color-text-muted, #546958);">Wajib diisi</span>
                                  </label>
                                  <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:12px;color:var(--color-text, #16251b);white-space:nowrap;">
                                    <input type="checkbox" name="selected_dropdowns[{{ $dd->id }}][include_in_export]" value="1"
                                           class="dd-exp-cb"
                                           style="width:14px;height:14px;accent-color:var(--color-primary, #1f3a2a);cursor:pointer;margin:0;"
                                           onchange="onPillChange(this, 'dd-pill-exp-{{ $dd->id }}', 'var(--color-primary, #1f3a2a)')">
                                    <span id="dd-pill-exp-{{ $dd->id }}" style="font-weight:500;color:var(--color-text-muted, #546958);">Masuk export</span>
                                  </label>
                                </div>
                              </div>
                              @endforeach
                            </div>
                            @if($dropdownList->count() > 10)
                            <div id="dd-show-more-wrap" style="margin-top:8px;text-align:center;">
                              <button type="button" id="dd-show-more-btn" onclick="showMoreDropdowns()"
                                      style="font-size:12px;color:var(--color-primary, #1f3a2a);font-weight:600;background:var(--color-primary-tint, #d5e4d7);border:1px solid var(--color-border, #cfdcc8);cursor:pointer;padding:6px 16px;border-radius:var(--radius-full);">
                                Tampilkan {{ $dropdownList->count() - 10 }} lainnya ↓
                              </button>
                            </div>
                            @endif
                          @endif
                        </div>
                    </div>
                  </div>

                  <!-- Urutan Field untuk Export -->
                  <div class="order-section">
                    <div class="order-section-title">
                      Urutan Data untuk Export
                    
                    </div>
                    <div class="order-hint">
                      <i data-feather="info"></i>
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
                    <a href="{{ route('jenis-inventaris.index') }}" class="btn-cancel" onclick="return confirmCancel(event, { returnKey: 'jenis_inventaris_returning', isEdit: false })">
                      <i data-feather="x"></i>
                      Batal
                    </a>
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
          warning.style.display = 'flex';
          if (submitBtn) submitBtn.disabled = true;
          if (typeof feather !== 'undefined') feather.replace();
        } else {
          warning.style.display = 'none';
          if (submitBtn) submitBtn.disabled = false;
        }
      }

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
              <div style="padding: 20px; text-align: center; color: var(--color-text-muted, #546958); font-size: 13.5px;">
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
          if (clearBtn) {
            clearBtn.style.display = input.value.trim().length > 0 ? 'inline-block' : 'none';
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
          const oldInputs = form.querySelectorAll('input[name="default_pejabats[]"]');
          oldInputs.forEach(input => input.remove());
          
          selectedPejabats.forEach(pejabat => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'default_pejabats[]';
            input.value = pejabat.id;
            form.appendChild(input);
          });
        }

        window.updateHiddenPejabatInputs = updateHiddenPejabatInputs;

        // Dropdown functions
        function onNeedsDropdownChange(checked) {
          const wrapper = document.getElementById('dropdown-list-wrapper');
          const label   = document.getElementById('needs-dropdown-label');
          if (wrapper) wrapper.style.display = checked ? 'block' : 'none';
          if (label) {
            label.style.borderColor = checked ? 'var(--color-primary, #1f3a2a)' : 'var(--color-border, #cfdcc8)';
            label.style.background  = checked ? 'var(--color-primary-tint, #d5e4d7)' : 'var(--color-surface, #f8faf7)';
          }
          if (!checked) {
            document.querySelectorAll('.dd-checkbox').forEach(cb => {
              cb.checked = false;
              const ddId = cb.closest('[data-id]')?.dataset?.id;
              if (ddId) onDropdownItemCheck(cb, ddId);
            });
          }
        }
        window.onNeedsDropdownChange = onNeedsDropdownChange;

        function onDropdownItemCheck(cb, ddId, ddName) {
          const pills = document.getElementById('dd-pills-' + ddId);
          const row   = cb.closest('[data-id]');
          const finalDdName = ddName || row?.getAttribute('data-label') || 'Dropdown';

          if (pills) pills.style.display = cb.checked ? 'flex' : 'none';
          if (row) {
            row.style.borderColor = cb.checked ? 'var(--color-primary, #1f3a2a)' : 'var(--color-border, #cfdcc8)';
            row.style.background  = cb.checked ? 'var(--color-primary-tint, #d5e4d7)' : 'var(--color-surface, #f8faf7)';
            
            if (pills) {
              var reqSpan = document.getElementById('dd-pill-req-' + ddId);
              var expSpan = document.getElementById('dd-pill-exp-' + ddId);
              var reqCb = pills.querySelector('.dd-req-cb');
              var expCb = pills.querySelector('.dd-exp-cb');
              
              if (cb.checked) {
                if (expCb && !expCb.checked) {
                  expCb.checked = true;
                }
              }
              if (reqSpan && reqCb) {
                reqSpan.style.color = reqCb.checked ? 'var(--color-primary, #1f3a2a)' : 'var(--color-text-muted, #546958)';
                reqSpan.style.fontWeight = reqCb.checked ? '600' : '500';
              }
              if (expSpan && expCb) {
                expSpan.style.color = expCb.checked ? 'var(--color-primary, #1f3a2a)' : 'var(--color-text-muted, #546958)';
                expSpan.style.fontWeight = expCb.checked ? '600' : '500';
              }
            }
          }
          if (!cb.checked && pills) {
            const reqCb = pills.querySelector('.dd-req-cb');
            const expCb = pills.querySelector('.dd-exp-cb');
            if (reqCb) { reqCb.checked = false; }
            if (expCb) { expCb.checked = false; }
            syncDropdownToOrderList(ddId, false, '');
          }
          if (cb.checked) {
            const reqCb = pills ? pills.querySelector('.dd-req-cb') : null;
            const expCb = pills ? pills.querySelector('.dd-exp-cb') : null;
            const isReq = reqCb ? reqCb.checked : false;
            const isExp = expCb ? expCb.checked : true;
            syncDropdownToOrderList(ddId, true, finalDdName, isReq, isExp);
          }
        }
        window.onDropdownItemCheck = onDropdownItemCheck;

        function onPillChange(cb, pillId, activeColor) {
          const span = document.getElementById(pillId);
          if (!span) return;
          span.style.color = cb.checked ? 'var(--color-primary, #1f3a2a)' : 'var(--color-text-muted, #546958)';
          span.style.fontWeight = cb.checked ? '600' : '500';
          if (pillId && pillId.startsWith('dd-pill-exp-')) {
            var ddId = pillId.replace('dd-pill-exp-', '');
            syncExportToOrderList(ddId, cb.checked);
          }
          if (pillId && pillId.startsWith('dd-pill-req-')) {
            var ddId = pillId.replace('dd-pill-req-', '');
            syncRequiredToOrderList(ddId, cb.checked);
          }
        }
        window.onPillChange = onPillChange;

        function syncDropdownToOrderList(ddId, include, label, isReq, isExp) {
          if (!window.orderList) return;
          const orderId = 'dropdown_' + ddId;
          const existing = window.orderList.find(f => f.id === orderId);
          const prevRequired = isReq !== undefined ? isReq : (existing ? existing.is_required : false);
          const prevExport = isExp !== undefined ? isExp : (existing !== undefined ? existing.include_in_export : true);
          const finalLabel = label || (existing ? existing.label : 'Dropdown');

          window.orderList = window.orderList.filter(f => f.id !== orderId);
          if (include) {
            window.orderList.push({
              id: orderId, label: finalLabel, type: 'custom',
              field_id: null, dropdown_id: parseInt(ddId), enabled: true,
              is_required: prevRequired, include_in_export: prevExport
            });
          }
          if (typeof normalizeOrderListDisabledToBottom === 'function') normalizeOrderListDisabledToBottom();
          if (typeof renderOrderList === 'function') renderOrderList();
        }
        window.syncDropdownToOrderList = syncDropdownToOrderList;

        function syncRequiredToOrderList(ddId, isRequired) {
          if (!window.orderList) return;
          const orderId = 'dropdown_' + ddId;
          const item = window.orderList.find(f => f.id === orderId);
          if (item) {
            item.is_required = isRequired;
            if (typeof renderOrderList === 'function') renderOrderList();
          }
        }
        window.syncRequiredToOrderList = syncRequiredToOrderList;

        function syncExportToOrderList(ddId, includeInExport) {
          if (!window.orderList) return;
          const orderId = 'dropdown_' + ddId;
          const item = window.orderList.find(f => f.id === orderId);
          if (item) {
            item.include_in_export = includeInExport;
            if (typeof renderOrderList === 'function') renderOrderList();
          }
        }
        window.syncExportToOrderList = syncExportToOrderList;

        function adjustFieldSelectWidth(selectEl) {
          if (!selectEl) return;
          selectEl.style.width = '170px';
          selectEl.style.minWidth = '150px';
          selectEl.style.maxWidth = '180px';
          selectEl.style.flex = '0 0 170px';
        }
        window.adjustFieldSelectWidth = adjustFieldSelectWidth;

        function onFieldTipeChange(selectEl, fieldId) {
          adjustFieldSelectWidth(selectEl);
          const wrapper = document.getElementById('dropdown-select-' + fieldId);
          if (!wrapper) return;
          wrapper.style.display = selectEl.value === 'dropdown' ? 'block' : 'none';
          const ddSelect = wrapper.querySelector('select');
          if (ddSelect) ddSelect.required = selectEl.value === 'dropdown';
        }
        window.onFieldTipeChange = onFieldTipeChange;

        // Dropdown search & show more
        window.ddToggleSearchClear = function(input) {
          const clearBtn = document.getElementById('dd-search-clear');
          if (clearBtn) {
            clearBtn.style.display = input.value.trim().length > 0 ? 'inline-block' : 'none';
          }
        };

        window.filterDropdownList = function(val) {
          const q = val.trim().toLowerCase();
          const clearBtn = document.getElementById('dd-search-clear');
          if (clearBtn) clearBtn.style.display = q ? 'inline-block' : 'none';

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

          setTimeout(() => {
            const expLabel = document.getElementById('exp-label-' + fieldCounter);
            if (expLabel) {
              const s = expLabel.querySelector('span');
              if (s) s.style.color = 'var(--color-text-muted, #546958)';
            }
            const reqLabel = document.getElementById('req-label-' + fieldCounter);
            if (reqLabel) {
              const s = reqLabel.querySelector('span');
              if (s) s.style.color = 'var(--color-text-muted, #546958)';
            }
          }, 20);

          const emptyState = document.getElementById('fields-empty');
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
          const addedItem = document.getElementById(`field-${fieldCounter}`);
          if (addedItem) {
            const sel = addedItem.querySelector('.field-select');
            if (sel) adjustFieldSelectWidth(sel);
          }
          feather.replace();
          updateFieldOrder();
        };

        // Remove field
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
        const fixedFieldsTemplate = @json($fixedFieldsTemplate ?? []);
        
        window.orderList = (fixedFieldsTemplate && fixedFieldsTemplate.length > 0) 
          ? fixedFieldsTemplate.map((field, idx) => ({
            id: `fixed_${idx + 1}`,
            label: field.label,
            fixed_name: field.fixed_name,
            type: 'fixed',
            enabled: true,
            is_required: true,
            include_in_export: true
          }))
          : [];

        function normalizeOrderListDisabledToBottom() {
          const enabledFields = window.orderList.filter(f => f.enabled);
          const disabledFields = window.orderList.filter(f => !f.enabled);
          window.orderList = [...enabledFields, ...disabledFields];
        }
        window.normalizeOrderListDisabledToBottom = normalizeOrderListDisabledToBottom;

        window.toggleFixedField = function(fieldId) {
          const field = window.orderList.find(f => f.id === fieldId);
          if (field && field.type === 'fixed') {
            field.enabled = !field.enabled;
            normalizeOrderListDisabledToBottom();
            renderOrderList();
          }
        };

        window.toggleCustomField = function(fieldId) {
          const field = window.orderList.find(f => f.id === fieldId);
          if (field && field.type === 'custom') {
            field.enabled = !field.enabled;
            normalizeOrderListDisabledToBottom();
            renderOrderList();
          }
        };

        window.toggleRequiredField = function(fieldId, isRequired) {
          const field = window.orderList.find(f => f.id === fieldId);
          if (field) {
            field.is_required = isRequired;
            if (field.type === 'custom') {
              const numericId = String(fieldId).replace('custom_', '');
              const cbInDataTambahan = document.querySelector(`input[name="fields[${numericId}][is_required]"]`);
              if (cbInDataTambahan) cbInDataTambahan.checked = isRequired;
            }
            if (fieldId && String(fieldId).startsWith('dropdown_')) {
              const ddId = String(fieldId).replace('dropdown_', '');
              const reqCb = document.querySelector(`input[name="selected_dropdowns[${ddId}][required]"]`);
              if (reqCb) {
                reqCb.checked = isRequired;
                onPillChange(reqCb, 'dd-pill-req-' + ddId, 'var(--color-primary, #1f3a2a)');
              }
            }
          }
        };

        window.toggleExportField = function(fieldId, includeInExport) {
          const field = window.orderList.find(f => f.id === fieldId);
          if (field) {
            field.include_in_export = includeInExport;
            if (field.type === 'custom') {
              const numericId = String(fieldId).replace('custom_', '');
              const cbExport = document.querySelector(`input[name="fields[${numericId}][include_in_export]"]`);
              if (cbExport) cbExport.checked = includeInExport;
            }
            if (fieldId && String(fieldId).startsWith('dropdown_')) {
              const ddId = String(fieldId).replace('dropdown_', '');
              const expCb = document.querySelector(`input[name="selected_dropdowns[${ddId}][include_in_export]"]`);
              if (expCb) {
                expCb.checked = includeInExport;
                onPillChange(expCb, 'dd-pill-exp-' + ddId, 'var(--color-primary, #1f3a2a)');
              }
            }
            renderOrderList();
          }
        };

        // Render order list
        function renderOrderList() {
          normalizeOrderListDisabledToBottom();
          const container = document.getElementById('order-list');
          let emptyState = document.getElementById('order-empty');

          if (window.orderList.length === 0) {
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
          
          const fieldsHtml = window.orderList.map((item, idx) => {
            const isDisabled = !item.enabled;
            const disabledClass = isDisabled ? 'disabled' : '';
            const toggleFunc = item.type === 'fixed' ? 'toggleFixedField' : 'toggleCustomField';
            const checkboxHtml = `<input type="checkbox" class="order-item-checkbox" ${item.enabled ? 'checked' : ''} onchange="${toggleFunc}('${item.id}')" onclick="event.stopPropagation()" />`;
            
            const isFirst = idx === 0;
            const isLast = idx === window.orderList.length - 1;
            const isExport = item.include_in_export !== false;
            const badgeDisabledClass = isDisabled ? 'disabled' : '';
            
            return `
              <div class="order-item ${disabledClass}" data-order-id="${item.id}" draggable="${!isDisabled}">
                ${checkboxHtml}
                <div class="order-number">${idx + 1}</div>
                <div class="order-item-content">
                  <div class="order-item-label">${item.label}</div>
                </div>
                <div class="order-item-flags" onclick="event.stopPropagation()">
                  <label class="order-item-required" title="Centang jika field ini wajib diisi">
                    <input type="checkbox" ${item.is_required ? 'checked' : ''} onchange="toggleRequiredField('${item.id}', this.checked)" onclick="event.stopPropagation()" />
                    <span style="color:${item.is_required ? 'var(--color-primary, #1f3a2a)' : 'var(--color-text-muted, #546958)'}; font-weight:${item.is_required ? '600' : '500'};">Wajib diisi</span>
                  </label>
                  <label class="order-item-export" title="Centang jika field ini masuk export">
                    <input type="checkbox" ${isExport ? 'checked' : ''} onchange="toggleExportField('${item.id}', this.checked)" onclick="event.stopPropagation()" />
                    <span style="color:${isExport ? 'var(--color-primary, #1f3a2a)' : 'var(--color-text-muted, #546958)'}; font-weight:${isExport ? '600' : '500'};">Masuk export</span>
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
            `;
          }).join('');
          
          container.innerHTML = `<div class="order-rows-wrapper">${fieldsHtml}</div>`;
          feather.replace();
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

        function updateOrderFromDOM() {
          const items = document.querySelectorAll('.order-rows-wrapper .order-item');
          const newOrder = [];
          items.forEach(item => {
            const id = item.dataset.orderId;
            const field = window.orderList.find(f => f.id === id);
            if (field) newOrder.push(field);
          });
          window.orderList = newOrder;
          normalizeOrderListDisabledToBottom();
          renderOrderList();
        }

        window.moveOrderItem = function(itemId, direction) {
          const currentIndex = window.orderList.findIndex(f => f.id === itemId);
          if (currentIndex === -1) return;
          
          const currentItem = window.orderList[currentIndex];
          if (!currentItem.enabled) return;
          
          const newIndex = currentIndex + direction;
          if (newIndex < 0 || newIndex >= window.orderList.length) return;
          
          const targetItem = window.orderList[newIndex];
          if (!targetItem.enabled) return;
          
          [window.orderList[currentIndex], window.orderList[newIndex]] = [window.orderList[newIndex], window.orderList[currentIndex]];
          renderOrderList();
          
          setTimeout(() => {
            const movedItem = document.querySelector(`[data-order-id="${itemId}"]`);
            if (movedItem) {
              movedItem.classList.add('highlighted');
              movedItem.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
              setTimeout(() => {
                movedItem.classList.remove('highlighted');
              }, 1200);
            }
          }, 50);
        };

        function addFieldToOrder(fieldId, label) {
          window.orderList.push({
            id: `custom_${fieldId}`,
            label: label || `Field ${fieldId}`,
            type: 'custom',
            field_id: fieldId,
            enabled: true,
            is_required: false,
            include_in_export: false
          });
          normalizeOrderListDisabledToBottom();
          renderOrderList();
        }

        function removeFieldFromOrder(fieldId) {
          window.orderList = window.orderList.filter(f => f.id !== `custom_${fieldId}`);
          normalizeOrderListDisabledToBottom();
          renderOrderList();
        }

        const originalAddField = window.addField;
        window.addField = function() {
          originalAddField();
          const newFieldId = fieldCounter;
          addFieldToOrder(newFieldId, `Field Tambahan ${newFieldId}`);
          
          setTimeout(() => {
            const labelInput = document.querySelector(`input[name="fields[${newFieldId}][label_field]"]`);
            if (labelInput) {
              labelInput.addEventListener('input', function() {
                const orderField = window.orderList.find(f => f.id === `custom_${newFieldId}`);
                if (orderField) {
                  orderField.label = this.value || `Field Tambahan ${newFieldId}`;
                  renderOrderList();
                }
              });
            }
            const reqCb = document.querySelector(`input[name="fields[${newFieldId}][is_required]"]`);
            if (reqCb) {
              reqCb.addEventListener('change', function() {
                const orderField = window.orderList.find(f => f.id === `custom_${newFieldId}`);
                if (orderField) {
                  orderField.is_required = this.checked;
                  renderOrderList();
                }
              });
            }
            const expCb = document.querySelector(`input[name="fields[${newFieldId}][include_in_export]"]`);
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

        const originalRemoveField = window.removeField;
        window.removeField = function(id) {
          originalRemoveField(id, function() {
            removeFieldFromOrder(id);
          });
        };

        renderOrderList();

        const jenisForm = document.getElementById('jenis-form');
        if (jenisForm) {
          jenisForm.addEventListener('submit', function(e) {
            e.preventDefault();
            confirmSubmit();
          });
        }

        renderPejabatList();
      })();

      function onFieldToggleChange(cb, labelId) {
        var label = document.getElementById(labelId);
        if (label) {
          var span = label.querySelector('span');
          if (span) span.style.color = cb.checked ? 'var(--color-primary, #1f3a2a)' : 'var(--color-text-muted, #546958)';
        }
      }
      window.onFieldToggleChange = onFieldToggleChange;

      function syncOrderToForm() {
        const container = document.getElementById('field-order-container');
        if (!container) return;
        container.innerHTML = '';
        
        const enabledFields = window.orderList.filter(f => {
          if (f.type === 'fixed') return f.enabled;
          if (f.type === 'custom') return f.enabled;
          return true;
        });
        
        enabledFields.forEach((field, index) => {
          const orderNumber = index + 1;
          
          if (field.type === 'fixed') {
            if (!field.fixed_name) return;
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = `fixed_fields[${field.fixed_name}]`;
            input.value = orderNumber;
            container.appendChild(input);
            
            const reqInput = document.createElement('input');
            reqInput.type = 'hidden';
            reqInput.name = `fixed_fields_required[${field.fixed_name}]`;
            reqInput.value = field.is_required ? '1' : '0';
            container.appendChild(reqInput);
            
            const expInput = document.createElement('input');
            expInput.type = 'hidden';
            expInput.name = `fixed_fields_export[${field.fixed_name}]`;
            expInput.value = field.include_in_export !== false ? '1' : '0';
            container.appendChild(expInput);
          } else if (field.type === 'custom' && field.field_id) {
            const fieldItem = document.getElementById(`field-${field.field_id}`);
            if (fieldItem) {
              const orderInput = fieldItem.querySelector('.field-order-input');
              if (orderInput) orderInput.value = orderNumber;
            }
          } else if (field.type === 'custom' && field.dropdown_id) {
            const ddOrderInput = document.createElement('input');
            ddOrderInput.type = 'hidden';
            ddOrderInput.name = `selected_dropdowns[${field.dropdown_id}][order]`;
            ddOrderInput.value = orderNumber;
            container.appendChild(ddOrderInput);
          }
        });
      }

      function confirmSubmit() {
        const form = document.getElementById('jenis-form');
        
        if (typeof window.simaValidateForm === 'function') {
          if (!window.simaValidateForm(form)) return;
        } else if (!form.checkValidity()) {
          const firstInvalid = form.querySelector(':invalid');
          if (firstInvalid) {
            const target = (firstInvalid.offsetParent !== null) ? firstInvalid : (firstInvalid.closest('.form-group') || firstInvalid);
            if (target && typeof target.scrollIntoView === 'function') {
              target.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            if (typeof firstInvalid.focus === 'function') {
              try { firstInvalid.focus(); } catch(err) {}
            }
          }
          return;
        }

        const namaEl = document.getElementById('nama');
        const nama = namaEl ? namaEl.value.trim() : '';
        if (!nama) {
          if (namaEl) {
            namaEl.style.borderColor = '#dc2626';
            namaEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
            namaEl.focus();
          }
          AppModal.error('Validasi Form', 'Nama jenis inventaris wajib diisi!');
          return;
        }
        if (namaEl) namaEl.style.borderColor = '';

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

      window.onRequiresRuanganChange = function(checked) {
        const label = document.getElementById('requires-ruangan-label');
        if (label) {
          label.style.borderColor = checked ? 'var(--color-primary, #1f3a2a)' : 'var(--color-border, #cfdcc8)';
          label.style.background  = checked ? 'var(--color-primary-tint, #d5e4d7)' : 'var(--color-surface, #f8faf7)';
        }
      };

      document.addEventListener('DOMContentLoaded', function() {
        const cb = document.getElementById('requires_ruangan');
        if (cb) window.onRequiresRuanganChange(cb.checked);
      });

      document.addEventListener('DOMContentLoaded', function() {
        try { sessionStorage.setItem('jenis_inventaris_returning', '1'); } catch(e) {}
      });
    </script>
  </body>
</html>
