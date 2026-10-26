<!doctype html>
<html lang="en" data-pc-preset="{{ $pc_preset_theme }}" data-pc-theme="{{ $pc_dark_layout }}">
  <head>
    @include('layouts.head-page-meta', ['title' => 'Edit Jenis Inventaris'])
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

      /* Info & Warning box */
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
    @include('layouts.layout-vertical')

    <div class="pc-container">
      <div class="pc-content">
        @include('layouts.breadcrumb', ['breadcrumbItemHome' => 'Master Data', 'breadcrumbItem' => 'Jenis Inventaris', 'breadcrumbItemUrl' => route('jenis-inventaris.index'), 'breadcrumbItemActive' => 'Edit Jenis Inventaris'])

        <div class="grid grid-cols-12 gap-x-6">
          <div class="col-span-12 w-full">
            <div class="card">
              <div class="card-header">
                <div class="flex items-center">
                  <div class="card-header-icon">
                    <i data-feather="layers"></i>
                  </div>
                  <div>
                    <h5 class="mb-0">Edit Jenis Inventaris</h5>
                  </div>
                </div>
              </div>

              <div class="card-body pt-4">
                <form id="jenis-form" action="{{ route('jenis-inventaris.update', $jenis->id) }}" method="POST" class="no-loader" novalidate>
                  @csrf
                  @method('PUT')
                  
                  <!-- Hidden inputs for field order -->
                  <div id="field-order-container"></div>
                  
                  @if($isVirtual ?? false)
                  <div class="info-box mb-4">
                    <i data-feather="info"></i>
                    <div class="info-box-text">
                      <strong>Catatan:</strong> Nama dan deskripsi Buku Inventaris Aset Desa bersifat tetap dan tidak dapat diubah. Yang dapat diubah hanya pejabat yang bertanda tangan.
                    </div>
                  </div>
                  @endif

                  @if($isTidakDitemukan ?? false)
                  <div class="info-box mb-4">
                    <i data-feather="info"></i>
                    <div class="info-box-text">
                      <strong>Catatan:</strong> Ini adalah jenis template. Nama dan struktur field bersifat tetap. Yang dapat diubah hanya deskripsi, ikon, pejabat penandatangan, urutan field, dan status wajib field.
                    </div>
                  </div>
                  @endif
                  
                  <!-- Informasi Dasar -->
                  <div class="form-section">
                    <div class="form-section-title">
                      Informasi Dasar
                    </div>
                    <div class="form-grid">
                      @if($isVirtual ?? false)
                      {{-- Hidden inputs untuk virtual jenis - send value meskipun disabled --}}
                      <input type="hidden" name="nama" value="{{ $jenis->nama }}">
                      <input type="hidden" name="deskripsi" value="{{ $jenis->deskripsi }}">
                      @endif
                      @if($isTidakDitemukan ?? false)
                      {{-- Nama dikunci untuk jenis template, kirim via hidden input --}}
                      <input type="hidden" name="nama" value="{{ $jenis->nama }}">
                      @endif
                      
                      <div class="form-group {{ (!($isVirtual ?? false) && !($isTidakDitemukan ?? false)) ? '' : 'form-full' }}">
                        <label class="form-label" for="nama">
                          Nama Jenis Inventaris <span class="required">*</span>
                        </label>
                        <input type="text" id="nama" name="nama" class="form-control @error('nama') is-invalid @enderror" 
                               placeholder="Contoh: Inventaris Kendaraan" 
                               value="{{ old('nama', $jenis->nama) }}" 
                               @if(($isVirtual ?? false) || ($isTidakDitemukan ?? false)) disabled @else required @endif />
                        @error('nama')
                        <div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>
                        @enderror
                        <p class="form-hint">Nama kategori yang akan muncul di menu dan form</p>
                      </div>
                      <!-- Requires Ruangan -->
                      @if(!($isVirtual ?? false) && !($isTidakDitemukan ?? false))
                      <div class="form-group">
                        @php
                          $reqRuanganChecked = old('requires_ruangan', $jenis->requires_ruangan ?? 0);
                        @endphp
                        <label class="form-label">Konfigurasi Ruangan</label>
                        <label style="display:flex;align-items:center;gap:12px;cursor:pointer;height:46px;box-sizing:border-box;padding:0 16px;background:{{ $reqRuanganChecked ? 'var(--color-primary-tint, #d5e4d7)' : 'var(--color-surface, #f8faf7)' }};border:1.5px solid {{ $reqRuanganChecked ? 'var(--color-primary, #1f3a2a)' : 'var(--color-border, #cfdcc8)' }};border-radius:10px;transition:background-color 0.15s ease, border-color 0.15s ease;color:var(--color-text, #16251b);margin:0;" id="requires-ruangan-label">
                          <input type="checkbox" name="requires_ruangan" id="requires_ruangan" value="1"
                                 {{ $reqRuanganChecked ? 'checked' : '' }}
                                 style="width:18px;height:18px;accent-color:var(--color-primary, #1f3a2a);cursor:pointer;flex-shrink:0;margin:0;"
                                 onchange="onRequiresRuanganChange(this.checked)">
                          <div style="display:flex;flex-direction:column;justify-content:center;min-width:0;">
                            <div style="font-size:13px;font-weight:600;color:var(--color-text, #16251b);line-height:1.25;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">Membutuhkan Data Ruangan</div>
                            <div style="font-size:11px;color:var(--color-text-muted, #546958);line-height:1.2;margin-top:1px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">Field Ruangan akan muncul saat input aset.</div>
                          </div>
                        </label>
                      </div>
                      @endif
                      <div class="form-group form-full">
                        <label class="form-label" for="deskripsi">
                          Deskripsi <span class="required">*</span>
                        </label>
                        <textarea id="deskripsi" name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" 
                                  rows="3" placeholder="Jelaskan jenis inventaris ini..."
                                  @if($isVirtual ?? false) disabled @else required @endif>{{ old('deskripsi', $jenis->deskripsi) }}</textarea>
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

                  @if(!($isVirtual ?? false) && !($isTidakDitemukan ?? false))
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
                  @endif

                  <!-- Section Dropdown — antara Data Tambahan dan Urutan Export -->
                  @if(!($isVirtual ?? false) && !($isTidakDitemukan ?? false))
                  <div class="form-section">
                    <div class="form-section-title">
                      Pilih Dropdown
                    </div>

                    <p class="text-muted text-sm mb-4">Pilih dropdown dari master data yang akan digunakan sebagai field pilihan pada jenis inventaris ini.</p>

                    <div id="dropdown-fields-section">
                        @php
                          $existingDropdownFieldIds = $jenis->fields->where('tipe','dropdown')->pluck('dropdown_id')->filter()->toArray();
                          $existingDropdownRequired = $jenis->fields->where('tipe','dropdown')->pluck('is_required','dropdown_id')->toArray();
                          $hasDropdown = count($existingDropdownFieldIds) > 0;
                        @endphp
                        <label style="display:flex;align-items:center;gap:12px;cursor:pointer;padding:14px 16px;background:{{ $hasDropdown ? 'var(--color-primary-tint, #d5e4d7)' : 'var(--color-surface, #f8faf7)' }};border:1.5px solid {{ $hasDropdown ? 'var(--color-primary, #1f3a2a)' : 'var(--color-border, #cfdcc8)' }};border-radius:10px;transition:background-color 0.15s ease, border-color 0.15s ease;color:var(--color-text, #16251b);" id="needs-dropdown-label">
                          <input type="checkbox" name="needs_dropdown" id="needs_dropdown" value="1"
                                 {{ $hasDropdown ? 'checked' : '' }}
                                 onchange="onNeedsDropdownChange(this.checked)">
                          <div>
                            <div style="font-size:13px;font-weight:600;color:var(--color-text, #16251b);">Jenis inventaris ini membutuhkan field dropdown</div>
                            <div style="font-size:12px;color:var(--color-text-muted, #546958);margin-top:2px;">Jika dicentang, pilih dropdown dari master data yang akan digunakan sebagai field pilihan.</div>
                          </div>
                        </label>

                        <div id="dropdown-list-wrapper" style="display:{{ $hasDropdown ? 'block' : 'none' }};margin-top:14px;">
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
                            <p style="font-size:12px;color:var(--color-text-muted, #546958);margin-bottom:10px;">Centang dropdown yang ingin digunakan, lalu atur apakah wajib diisi atau tidak:</p>
                            <div style="display:flex;flex-direction:column;gap:8px;" id="dropdown-checklist">
                              @foreach($dropdownList as $ddIdx => $dd)
                              @php
                                $isChecked = in_array($dd->id, $existingDropdownFieldIds);
                                $isRequired = $isChecked && ($existingDropdownRequired[$dd->id] ?? 0);
                                $existingField = $jenis->fields->where('tipe','dropdown')->where('dropdown_id',$dd->id)->first();
                                $isExport = $isChecked && ($existingField?->include_in_export ?? 1);
                                $isHidden = $ddIdx >= 10 && !$isChecked;
                              @endphp
                              <div style="display:{{ $isHidden ? 'none' : 'flex' }};align-items:center;gap:12px;padding:12px 16px;background:{{ $isChecked ? 'var(--color-primary-tint, #d5e4d7)' : 'var(--color-surface, #f8faf7)' }};border:1.5px solid {{ $isChecked ? 'var(--color-primary, #1f3a2a)' : 'var(--color-border, #cfdcc8)' }};border-radius:10px;transition:all 0.2s;"
                                    class="dd-item-label {{ $isHidden ? 'dd-item-hidden' : '' }}" data-id="{{ $dd->id }}" data-label="{{ $dd->nama }}" data-name="{{ strtolower($dd->nama) }}">
                                 <input type="checkbox" name="selected_dropdowns[{{ $dd->id }}][enabled]" value="1"
                                        class="dd-checkbox" {{ $isChecked ? 'checked' : '' }}
                                        style="width:18px;height:18px;accent-color:var(--color-primary, #1f3a2a);flex-shrink:0;cursor:pointer;"
                                        onchange="onDropdownItemCheck(this, {{ $dd->id }}, '{{ addslashes($dd->nama) }}')">
                                 <div style="flex:1;cursor:pointer;" onclick="this.previousElementSibling.click()">
                                   <div style="font-size:13px;font-weight:600;color:var(--color-text, #16251b);">{{ $dd->nama }}</div>
                                   <div style="font-size:11px;color:var(--color-text-muted, #546958);margin-top:1px;">{{ $dd->items->pluck('label')->implode(', ') }}</div>
                                 </div>
                                 <div id="dd-pills-{{ $dd->id }}" style="display:{{ $isChecked ? 'flex' : 'none' }};flex-direction:row;align-items:center;gap:16px;flex-shrink:0;">
                                   <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:12px;color:var(--color-text, #16251b);white-space:nowrap;">
                                     <input type="checkbox" name="selected_dropdowns[{{ $dd->id }}][required]" value="1"
                                            class="dd-req-cb" {{ $isRequired ? 'checked' : '' }}
                                            style="width:14px;height:14px;accent-color:var(--color-primary, #1f3a2a);cursor:pointer;margin:0;"
                                            onchange="onPillChange(this, 'dd-pill-req-{{ $dd->id }}', 'var(--color-primary, #1f3a2a)')">
                                     <span id="dd-pill-req-{{ $dd->id }}" style="font-weight:500;color:{{ $isRequired ? 'var(--color-primary, #1f3a2a)' : 'var(--color-text-muted, #546958)' }};">Wajib diisi</span>
                                   </label>
                                   <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:12px;color:var(--color-text, #16251b);white-space:nowrap;">
                                     <input type="checkbox" name="selected_dropdowns[{{ $dd->id }}][include_in_export]" value="1"
                                            class="dd-exp-cb" {{ $isExport ? 'checked' : '' }}
                                            style="width:14px;height:14px;accent-color:var(--color-primary, #1f3a2a);cursor:pointer;margin:0;"
                                            onchange="onPillChange(this, 'dd-pill-exp-{{ $dd->id }}', 'var(--color-primary, #1f3a2a)')">
                                     <span id="dd-pill-exp-{{ $dd->id }}" style="font-weight:500;color:{{ $isExport ? 'var(--color-primary, #1f3a2a)' : 'var(--color-text-muted, #546958)' }};">Masuk export</span>
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
                  @endif

                  <!-- Urutan Field untuk Export -->
                  @if($isVirtual ?? false)
                  <!-- Urutan Field untuk Export (Buku Inventaris Aset Desa - read-only statis) -->
                  <div class="order-section">
                    <div class="order-section-title">
                      Urutan Data untuk Export
                    
                    </div>
                    <div class="order-hint">
                      <i data-feather="info"></i>
                      <span>Kolom export untuk Buku Inventaris Aset Desa bersifat tetap dan tidak dapat diubah.</span>
                    </div>
                    <div style="overflow-x:auto;margin-top:12px;">
                      <table style="width:100%;border-collapse:collapse;font-size:13px;border:1.5px solid var(--color-border, #cfdcc8);border-radius:10px;overflow:hidden;background:var(--color-surface, #f8faf7);">
                        <thead>
                          <tr style="background:var(--color-primary-tint, #d5e4d7);border-bottom:1.5px solid var(--color-border, #cfdcc8);">
                            <th style="padding:10px 14px;text-align:left;font-weight:600;color:var(--color-text, #16251b);width:80px;">URUTAN</th>
                            <th style="padding:10px 14px;text-align:left;font-weight:600;color:var(--color-text, #16251b);">LABEL</th>
                            <th style="padding:10px 14px;text-align:left;font-weight:600;color:var(--color-text, #16251b);width:140px;">KATEGORI</th>
                          </tr>
                        </thead>
                        <tbody>
                          @php
                            $bukuFields = [
                              ['label' => 'Jenis Barang'],
                              ['label' => 'Kode Barang'],
                              ['label' => 'Nama / Identitas Barang'],
                              ['label' => 'Asal Usul Barang', 'sub' => 'APBDes / Perolehan Lain yang Sah / Kekayaan Asli Desa'],
                              ['label' => 'Tahun Perolehan'],
                              ['label' => 'Nilai Aset (Rp)'],
                              ['label' => 'Pengguna'],
                              ['label' => 'Keterangan'],
                            ];
                          @endphp
                          @foreach($bukuFields as $i => $field)
                          <tr style="border-bottom:1px solid var(--color-border, #cfdcc8);">
                            <td style="padding:12px 14px;color:var(--color-text, #16251b);font-weight:500;">{{ $i + 1 }}</td>
                            <td style="padding:12px 14px;">
                              <span style="font-weight:600;color:var(--color-text, #16251b);">{{ $field['label'] }}</span>
                              @if(!empty($field['sub']))
                                <br><span style="font-size:11px;color:var(--color-text-muted, #546958);">{{ $field['sub'] }}</span>
                              @endif
                            </td>
                            <td style="padding:12px 14px;color:var(--color-primary, #1f3a2a);font-size:12px;font-weight:600;">Data Umum</td>
                          </tr>
                          @endforeach
                        </tbody>
                      </table>
                    </div>
                  </div>
                  @else
                  <!-- Interactive Order section untuk jenis biasa DAN template (isTidakDitemukan) -->
                  <div class="order-section">
                    <div class="order-section-title">
                      Urutan Data untuk Export
                    
                    </div>
                    <div class="order-hint">
                      <i data-feather="info"></i>
                      <span>Atur urutan kolom yang akan muncul saat export Excel/PDF. Drag & drop untuk mengubah urutan{{ ($isTidakDitemukan ?? false) ? ' dan tandai field yang wajib diisi' : '' }}.</span>
                    </div>
                    <div class="order-list" id="order-list">
                      <div class="order-empty" id="order-empty">
                        <i data-feather="list"></i>
                        <div class="order-empty-text">Urutan field akan muncul di sini</div>
                        <div class="order-empty-hint">{{ ($isTidakDitemukan ?? false) ? 'Field pakem akan muncul di sini untuk diurutkan' : 'Tambahkan data tambahan untuk melihat urutan export' }}</div>
                      </div>
                    </div>
                  </div>
                  @endif

                  <!-- Form Actions -->
                  <div class="form-actions">
                    <a href="{{ route('jenis-inventaris.index') }}" class="btn-cancel" onclick="return confirmCancel(event, { returnKey: 'jenis_inventaris_returning', isEdit: true })">
                      <i data-feather="x"></i>
                      Batal
                    </a>
                    <button type="button" class="btn-submit" onclick="confirmSubmit()">
                      <i data-feather="save"></i>
                      Update Jenis
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
    @include('layouts.modals')
    @include('layouts.footer-js')

    <script>
      // Blok fungsi dropdown versi lama (duplikat) sudah dihapus.
      // Implementasi yang dipakai ada di blok script utama di bawah (versi const).
    </script>

    <script>
      (function(){
        // Raw dropdown data from database
        const rawDropdownList = @json($dropdownList ?? []);

        // Real pejabat data from database
        const rawPejabatList = @json($pejabatsData ?? []);
        const pejabatList = (Array.isArray(rawPejabatList) ? rawPejabatList : Object.values(rawPejabatList || {})).map(p => ({
          ...p,
          id: Number(p.id)
        }));

        const selectedPejabats = [];
        let pejabatChanged = false; // Track jika user ubah pejabat
        // Pre-selected pejabats from database
        const rawPreSelectedPejabats = @json($preSelectedData ?? []);
        const preSelectedPejabats = Array.isArray(rawPreSelectedPejabats)
          ? rawPreSelectedPejabats
          : Object.values(rawPreSelectedPejabats || {});
        preSelectedPejabats.forEach(p => {
          const pejId = Number(p.id);
          const pej = pejabatList.find(pj => pj.id === pejId);
          if (pej) selectedPejabats.push(pej);
        });
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
          pejabatChanged = true; // Mark pejabat as changed
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
          pejabatChanged = true;
          renderPejabatList();
          updateHiddenPejabatInputs();
        };

        // Update hidden inputs for form submission
        window.updateHiddenPejabatInputs = function() {
          const form = document.getElementById('jenis-form');
          // Remove old hidden inputs
          const oldInputs = form.querySelectorAll('input[name="default_pejabats[]"]');
          oldInputs.forEach(input => input.remove());
          
          // Remove old pejabat change flag
          const oldFlag = form.querySelector('input[name="pejabat_changed"]');
          if (oldFlag) oldFlag.remove();
          
          // Only send pejabat if user changed it
          if (pejabatChanged) {
            // Add flag to indicate pejabat was explicitly changed
            const flagInput = document.createElement('input');
            flagInput.type = 'hidden';
            flagInput.name = 'pejabat_changed';
            flagInput.value = '1';
            form.appendChild(flagInput);
            
            // Add new hidden inputs for selected pejabats
            selectedPejabats.forEach(pejabat => {
              const input = document.createElement('input');
              input.type = 'hidden';
              input.name = 'default_pejabats[]';
              input.value = pejabat.id;
              form.appendChild(input);
            });
          }
        };

        // Daftar dropdown dari master data
        const dropdownOptions = `{!! \App\Models\Dropdown::orderBy('nama')->get(['id','nama'])->map(fn($d) => '<option value="'.$d->id.'">'.$d->nama.'</option>')->implode('') !!}`;

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
                // Default Masuk export = true jika baru dicentang
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

        function onPillChange(cb, pillId, activeColor) {
          const span = document.getElementById(pillId);
          if (!span) return;
          span.style.color = cb.checked ? 'var(--color-primary, #1f3a2a)' : 'var(--color-text-muted, #546958)';
          span.style.fontWeight = cb.checked ? '600' : '500';
          if (pillId && pillId.startsWith('dd-pill-exp-')) {
            var ddId = pillId.replace('dd-pill-exp-', '');
            syncExportToOrderList(ddId, cb.checked);
          }
          // Sync "Wajib diisi" dari section dropdown → order list
          if (pillId && pillId.startsWith('dd-pill-req-')) {
            var ddId = pillId.replace('dd-pill-req-', '');
            syncRequiredToOrderList(ddId, cb.checked);
          }
        }

        function syncDropdownToOrderList(ddId, include, label, isReq, isExp) {
          if (!window.orderList) return;
          const orderId = 'dropdown_' + ddId;
          // Pertahankan is_required dan include_in_export yang sudah ada saat toggle ulang
          const existing = window.orderList.find(f => f.id === orderId);
          const prevRequired = isReq !== undefined ? isReq : (existing ? existing.is_required : false);
          const prevExport = isExp !== undefined ? isExp : (existing !== undefined ? existing.include_in_export : true);
          const finalLabel = label || (existing ? existing.label : 'Dropdown');

          window.orderList = window.orderList.filter(f => f.id !== orderId);
          if (include) {
            window.orderList.push({
              id: orderId, label: finalLabel, type: 'custom', field_id: null, dropdown_id: parseInt(ddId),
              enabled: true, is_required: prevRequired, include_in_export: prevExport
            });
          }
          if (typeof normalizeOrderListDisabledToBottom === 'function') normalizeOrderListDisabledToBottom();
          if (typeof renderOrderList === 'function') renderOrderList();
        }

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

        // Sync warna teks label toggle sesuai status checkbox (pakem dropdown)
        window.onFieldToggleChange = function(cb, labelId) {
          const label = document.getElementById(labelId);
          if (label) {
            const span = label.querySelector('span');
            if (span) {
              span.style.color = cb.checked ? 'var(--color-primary, #1f3a2a)' : 'var(--color-text-muted, #546958)';
              span.style.fontWeight = cb.checked ? '600' : '500';
            }
          }
        };

        // Expose ke window agar bisa dipanggil dari atribut onchange HTML
        // (fungsi berada di dalam IIFE, jadi harus di-expose eksplisit)
        window.onNeedsDropdownChange = onNeedsDropdownChange;
        window.onDropdownItemCheck   = onDropdownItemCheck;
        window.onPillChange          = onPillChange;
        window.syncDropdownToOrderList = syncDropdownToOrderList;

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

        // ── Dropdown search & show more ──────────────────────────────────────
        let _ddSearchTimer = null;
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

          // Sembunyikan tombol "Tampilkan X lainnya" saat searching
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
        window.addField = function(existingField = null) {
          fieldCounter++;
          const fieldsBody = document.getElementById('fields-body');
          const emptyState = document.getElementById('fields-empty');
          
          if (emptyState) {
            emptyState.style.display = 'none';
          }

          const fieldId = existingField ? existingField.id : null;
          const labelValue = existingField ? existingField.label : '';
          const tipeValue = existingField ? existingField.tipe : 'text';
          const isRequired = existingField ? (existingField.is_required == 1 || existingField.is_required === true) : false;
          const isExport = existingField ? (existingField.include_in_export == 1 || existingField.include_in_export === true || existingField.include_in_export === undefined) : false;
          const orderValue = existingField ? existingField.order : 999;
          const displayCounter = existingField ? existingField.id : fieldCounter;
          const existingDropdownId = existingField ? (existingField.dropdown_id || '') : '';
          const showDropdownSelect = tipeValue === 'dropdown';

          const reqColor = isRequired ? 'var(--color-primary, #1f3a2a)' : 'var(--color-text-muted, #546958)';
          const expColor = isExport ? 'var(--color-primary, #1f3a2a)' : 'var(--color-text-muted, #546958)';

          const fieldHtml = `
            <div class="field-item" data-field-id="${displayCounter}" id="field-${displayCounter}">
              <div class="field-drag">
                <i data-feather="more-vertical"></i>
              </div>
              <div class="field-order-num">${displayCounter}</div>
              <div class="field-inputs">
                <div class="field-inputs-row">
                  ${fieldId ? `<input type="hidden" name="fields[${displayCounter}][id]" value="${fieldId}" />` : ''}
                  <input type="text" name="fields[${displayCounter}][label_field]" class="field-input" placeholder="Label (ex: Nomor Polisi)" value="${labelValue}" required />
                  <select name="fields[${displayCounter}][tipe]" class="field-input field-select" required
                          onchange="onFieldTipeChange(this, ${displayCounter})">
                    <option value="text" ${tipeValue === 'text' ? 'selected' : ''}>Teks (nama / keterangan)</option>
                    <option value="number" ${tipeValue === 'number' ? 'selected' : ''}>Angka (jumlah / nilai)</option>
                    <option value="decimal_m2" ${tipeValue === 'decimal_m2' ? 'selected' : ''}>Luas dalam m²</option>
                    <option value="decimal_km" ${tipeValue === 'decimal_km' ? 'selected' : ''}>Panjang dalam km</option>
                    <option value="decimal_m" ${tipeValue === 'decimal_m' ? 'selected' : ''}>Lebar dalam m</option>
                    <option value="year" ${tipeValue === 'year' ? 'selected' : ''}>Tahun perolehan</option>
                    <option value="month" ${tipeValue === 'month' ? 'selected' : ''}>Bulan dan Tahun</option>
                    <option value="date" ${tipeValue === 'date' ? 'selected' : ''}>Tanggal lengkap</option>
                  </select>
                </div>
                <input type="hidden" name="fields[${displayCounter}][order]" class="field-order-input" value="${orderValue}" />
                <div class="field-toggles">
                  <label class="field-required-toggle" id="req-label-edit-${displayCounter}"
                         style="color:${reqColor}; font-weight:${isRequired ? '600' : '500'};" title="Centang jika field ini wajib diisi">
                    <input type="checkbox" name="fields[${displayCounter}][is_required]" value="1" ${isRequired ? 'checked' : ''}
                           onchange="onFieldToggleChange(this, 'req-label-edit-${displayCounter}')" />
                    <span>Wajib diisi</span>
                  </label>
                  <label class="field-export-toggle" id="exp-label-edit-${displayCounter}"
                         style="color:${expColor}; font-weight:${isExport ? '600' : '500'};" title="Centang jika field ini masuk ke export">
                    <input type="checkbox" name="fields[${displayCounter}][include_in_export]" value="1" ${isExport ? 'checked' : ''}
                           onchange="onFieldToggleChange(this, 'exp-label-edit-${displayCounter}')" />
                    <span>Masuk export</span>
                  </label>
                </div>
              </div>
              <div class="field-actions">
                <button type="button" class="field-action-btn delete" onclick="removeField(${displayCounter})" title="Hapus Field">
                  <i data-feather="trash-2"></i>
                </button>
              </div>
            </div>
          `;
          
          fieldsBody.insertAdjacentHTML('beforeend', fieldHtml);
          const addedItem = document.getElementById(`field-${displayCounter}`);
          if (addedItem) {
            const sel = addedItem.querySelector('.field-select');
            if (sel) adjustFieldSelectWidth(sel);
          }
          feather.replace();
          updateFieldOrder();
        };

        // Remove field (with confirmation + warning jika ada aset yang menggunakan field ini)
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

          // Cek usage count dari data yang sudah di-preload saat halaman dimuat
          const hiddenId = field.querySelector('input[name*="[id]"]');
          const dbFieldId = hiddenId ? hiddenId.value : null;
          const usageCount = dbFieldId ? (fieldUsageCounts[dbFieldId] || 0) : 0;

          const extraMsg = usageCount > 0
            ? `<br><br>Field ini sudah digunakan oleh <strong>${usageCount} aset</strong>. Data field pada aset tersebut akan hilang jika dihapus.`
            : '';
          const msg = 'Apakah Anda yakin ingin menghapus data tambahan ini?' + extraMsg;

          if (typeof AppModal !== 'undefined' && typeof AppModal.confirm === 'function') {
            AppModal.confirm('Konfirmasi Hapus Data Tambahan', msg, doRemove);
          } else if (confirm(usageCount > 0
            ? `Field ini digunakan oleh ${usageCount} aset. Data akan hilang. Yakin hapus?`
            : 'Apakah Anda yakin ingin menghapus data tambahan ini?')) {
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
            emptyState.style.display = 'flex';
          }
        }

        // ========== ORDER MANAGEMENT ==========
        
        // Fixed fields template dari controller
        const fixedFieldsTemplate = @json($fixedFieldsTemplate ?? []);
        // console.log('[DEBUG] Fixed fields template from controller:', fixedFieldsTemplate);
        // console.log('[DEBUG] fixedFieldsTemplate length:', fixedFieldsTemplate ? fixedFieldsTemplate.length : 0);
        
        // Custom fields array for editing
        const customFields = [];
        
        // Load ALL existing fields from database (both fixed and custom)
        const existingFields = @json($jenis->fields ? $jenis->fields->sortBy('order')->values() : []);

        // Pre-load usage count per field (dihitung di server saat render halaman)
        @php
            $fieldUsageCountsData = [];
            if ($jenis->fields) {
                foreach ($jenis->fields as $f) {
                    if ($f->field_type === 'custom') {
                        // Custom field: hitung dari inventaris_values
                        $fieldUsageCountsData[$f->id] = \App\Models\InventarisValue::where('jenis_inventaris_data_id', $f->id)
                            ->whereNotNull('value')->where('value','!=','')->where('value','!=','-')->count();
                    }
                }
            }
            // Fixed field: hitung dari kolom inventaris langsung
            $fixedFieldUsageData = [];
            $fixedColumns = ['nama_barang','kode_barang','nup','tahun_perolehan','nilai_perolehan','keterangan','kondisi','asal_usul','pengguna'];
            foreach ($fixedColumns as $col) {
                $fixedFieldUsageData[$col] = \App\Models\Inventaris::where('jenis_inventaris_id', $jenis->id)
                    ->whereNotNull($col)->where($col,'!=','')->count();
            }
        @endphp
        const fieldUsageCounts = @json($fieldUsageCountsData);
        const fixedFieldUsageCounts = @json($fixedFieldUsageData);
        
        // console.log('[EDIT-DEBUG] existingFields from database:', existingFields);
        // console.log('[EDIT-DEBUG] existingFields length:', existingFields.length);
        
        // Initialize empty orderList with GLOBAL scope (like create.blade.php)
        window.orderList = [];
        
        // Track which fixed fields are in database
        const savedFixedFields = new Set();
        
        // Process ALL fields from database
        existingFields.forEach((field) => {
          // Skip jenis_barang — sudah digabung ke nama_barang sebagai "Jenis Barang / Nama Barang"
          if (field.field_type === 'fixed' && field.fixed_field_name === 'jenis_barang') return;
          if (field.nama_field === 'jenis_barang') return;

          if (field.field_type === 'fixed') {
            // Ganti label nama_barang menjadi "Jenis Barang / Nama Barang"
            const displayLabel = field.fixed_field_name === 'nama_barang'
              ? 'Jenis Barang / Nama Barang'
              : field.label_field;
            savedFixedFields.add(field.fixed_field_name);
            window.orderList.push({
              id: field.fixed_field_name,
              fixed_name: field.fixed_field_name,
              label: displayLabel,
              type: 'fixed',
              enabled: true,
              order: field.order,
              is_required: field.is_required ? true : false,
              include_in_export: field.include_in_export !== 0 && field.include_in_export !== false
            });
            // console.log('[EDIT-DEBUG] Added FIXED field to orderList:', field.label_field, 'enabled: true', 'order:', field.order);
          } else {
            // Custom field — handle dropdown field secara khusus
            const isDropdownField = field.tipe === 'dropdown' && field.dropdown_id;
            const orderId = isDropdownField ? 'dropdown_' + field.dropdown_id : 'custom_' + field.id;
            let fieldLabel = field.label_field;
            if (isDropdownField) {
              const matchedDropdown = rawDropdownList.find(d => String(d.id) === String(field.dropdown_id));
              if (matchedDropdown && matchedDropdown.nama) {
                fieldLabel = matchedDropdown.nama;
              }
            }
            window.orderList.push({
              id: orderId,
              label: fieldLabel,
              type: 'custom',
              enabled: true,
              field_id: isDropdownField ? null : field.id,
              dropdown_id: isDropdownField ? field.dropdown_id : null,
              order: field.order,
              tipe: field.tipe,
              is_required: field.is_required,
              include_in_export: field.include_in_export !== 0 && field.include_in_export !== false
            });
            
            // Also add to customFields array (hanya non-dropdown)
            if (!isDropdownField) {
              customFields.push({
                id: field.id,
                label: field.label_field,
                tipe: field.tipe,
                is_required: field.is_required,
                include_in_export: field.include_in_export !== 0 && field.include_in_export !== false,
                order: field.order
              });
            }
          }
        });
        
        // console.log('[EDIT-DEBUG] Saved fixed fields count:', savedFixedFields.size);
        // console.log('[EDIT-DEBUG] orderList after processing DB fields:', window.orderList.length);
        
        // FALLBACK: If NO fixed fields in database (old data), populate with all default fixed fields enabled
        if (savedFixedFields.size === 0 && fixedFieldsTemplate && fixedFieldsTemplate.length > 0) {
          fixedFieldsTemplate.forEach((template) => {
            window.orderList.push({
              id: template.id,
              fixed_name: template.fixed_name || template.id,
              label: template.label,
              type: 'fixed',
              enabled: true,
              order: template.order || 999,
              is_required: false
            });
            savedFixedFields.add(template.id);
          });
        } else {
          // Add any fixed fields NOT in database (disabled) with default order
          if (fixedFieldsTemplate && fixedFieldsTemplate.length > 0) {
            fixedFieldsTemplate.forEach((template) => {
              if (!savedFixedFields.has(template.id)) {
                window.orderList.push({
                  id: template.id,
                  fixed_name: template.fixed_name || template.id,
                  label: template.label,
                  type: 'fixed',
                  enabled: false,
                  order: template.order + 1000,
                  is_required: false
                });
              }
            });
          }
        }
        
        // console.log('[DEBUG] Initialized orderList:', window.orderList);
        // console.log('[DEBUG] orderList length:', window.orderList.length);
        // console.log('[DEBUG] Saved fixed fields:', Array.from(savedFixedFields));

        // Sort orderList by saved order
        window.orderList.sort((a, b) => a.order - b.order);

        function normalizeOrderListDisabledToBottom() {
          const enabledFields = window.orderList.filter(f => f.enabled);
          const disabledFields = window.orderList.filter(f => !f.enabled);
          window.orderList = [...enabledFields, ...disabledFields];
        }
        window.normalizeOrderListDisabledToBottom = normalizeOrderListDisabledToBottom;

        // Toggle fixed field enable/disable
        window.toggleFixedField = function(fieldId) {
          const field = window.orderList.find(f => f.id === fieldId);
          if (!field || field.type !== 'fixed') return;

          if (field.enabled) {
            // Akan di-disable — cari DB field ID dan usage count
            const dbField = existingFields.find(f => f.fixed_field_name === fieldId || f.fixed_field_name === field.fixed_name);
            const dbFieldId = dbField ? dbField.id : null;
            // Fixed field: pakai fixedFieldUsageCounts (hitung dari kolom inventaris langsung)
            const fixedName = field.fixed_name || fieldId;
            const usageCount = fixedFieldUsageCounts[fixedName] || 0;

            const doToggle = function() {
              field.enabled = false;
              normalizeOrderListDisabledToBottom();
              renderOrderList();
            };

            if (usageCount > 0) {
              const msg = `Field <strong>"${field.label}"</strong> sudah digunakan oleh <strong>${usageCount} aset</strong>.<br><br>Jika dinonaktifkan, field tidak akan muncul di form dan export, tapi data yang sudah ada tidak dihapus.`;
              if (typeof AppModal !== 'undefined' && typeof AppModal.confirm === 'function') {
                AppModal.confirm('Nonaktifkan Field Data Umum?', msg, doToggle);
              } else if (confirm(`Field "${field.label}" sudah digunakan oleh ${usageCount} aset. Yakin nonaktifkan?`)) {
                doToggle();
              }
            } else {
              doToggle();
            }
          } else {
            // Re-enable: langsung tanpa warning
            field.enabled = true;
            normalizeOrderListDisabledToBottom();
            renderOrderList();
          }
        };

        // Toggle custom field enable/disable (Data Tambahan)
        window.toggleCustomField = function(fieldId) {
          // console.log('[TOGGLE-DEBUG] toggleCustomField called with fieldId:', fieldId);
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
            if (field.type === 'custom') {
              const numericId = String(fieldId).replace('custom_', '');
              const cbInDataTambahan = document.querySelector(`input[name="fields[${numericId}][is_required]"]`);
              if (cbInDataTambahan) cbInDataTambahan.checked = isRequired;
            }
            // Sync ke checkbox "Wajib diisi" di section Field Dropdown
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

        // Toggle include_in_export pada order list item
        window.toggleExportField = function(fieldId, includeInExport) {
          const field = window.orderList.find(f => f.id === fieldId);
          if (field) {
            field.include_in_export = includeInExport;
            // Sync ke checkbox include_in_export di section Data Tambahan jika ini custom field
            if (field.type === 'custom' && !String(fieldId).startsWith('dropdown_')) {
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
                onPillChange(expCb, 'dd-pill-exp-' + ddId, 'var(--color-primary, #1f3a2a)');
              }
            }
            // Re-render agar state checkbox di UI order list konsisten
            renderOrderList();
          }
        };

        // Render order list
        function renderOrderList() {
          const container = document.getElementById('order-list');
          if (!container) return; // Guard: order-list tidak ada untuk Buku Inventaris Aset Desa
          normalizeOrderListDisabledToBottom();
          // console.log('[RENDER] renderOrderList called, orderList length:', window.orderList.length);
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
          
          // Single wrapper — nomor terintegrasi dalam tiap row
          container.innerHTML = `<div class="order-rows-wrapper">${fieldsHtml}</div>`;

          // console.log('HTML generated, calling feather.replace()');
          feather.replace();
          // console.log('[SUCCESS] Order list rendered successfully with', window.orderList.length, 'items');
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
          const items = document.querySelectorAll('.order-rows-wrapper .order-item');
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
            include_in_export: false  // default tidak masuk export untuk field baru
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
        window.addField = function(existingField = null) {
          originalAddField(existingField);
          
          // HANYA tambahkan ke order list jika ini field BARU (bukan existing field)
          if (!existingField) {
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
          }
          // Jika existingField, sudah ada di orderList dari database, jangan tambah lagi
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

        // POPULATE EXISTING CUSTOM FIELDS TO FORM
        // console.log('[INIT] Populating existing custom fields to form...', customFields.length, 'fields');
        customFields.forEach((field) => {
          // console.log('[INIT] Adding existing field:', field.label, 'ID:', field.id);
          addField({
            id: field.id,
            label: field.label,
            tipe: field.tipe,
            is_required: field.is_required,
            order: field.order
          });
        });
        document.querySelectorAll('.field-inputs-row .field-select').forEach(sel => adjustFieldSelectWidth(sel));
        // console.log('[INIT] Custom fields populated successfully');

        // Add form submission handler to sync order before submit
        const form = document.getElementById('jenis-form');
        if (form) {
          form.addEventListener('submit', function(e) {
            e.preventDefault();
            // console.log('[FORM-SUBMIT] Form submit event captured');
            
            // Call confirmSubmit which will sync the order and pejabat
            confirmSubmit();
          });
        }

        // Initialize
        // console.log('[INIT] Initializing pejabat list...');
        renderPejabatList();
        // console.log('[INIT] All initialization complete!');
      })();

      // Confirm Cancel
      function confirmCancel(e, extraOpts) {
        if (e && typeof e.preventDefault === 'function') e.preventDefault();
        AppModal.confirmCancel(Object.assign({
          url: "{{ route('jenis-inventaris.index') }}",
          returnKey: 'jenis_inventaris_returning',
          isEdit: true
        }, extraOpts || {}));
        return false;
      }

      // Sync order list to hidden inputs before form submission
      function syncOrderToForm() {
        const container = document.getElementById('field-order-container');
        container.innerHTML = ''; // Clear existing
        
        // console.log('[SYNC-DEBUG] Starting syncOrderToForm...');
        // console.log('[SYNC-DEBUG] orderList:', window.orderList);
        
        // Filter only enabled fields dari orderList (source of truth)
        const enabledFields = window.orderList.filter(f => {
          if (f.type === 'fixed') return f.enabled;
          if (f.type === 'custom') return f.enabled;
          return true;
        });
        // console.log('[SYNC-DEBUG] enabledFields count:', enabledFields.length);
        // console.log('[SYNC-DEBUG] enabledFields:', enabledFields);
        
        // Collect ENABLED custom field IDs
        const enabledCustomFieldIds = new Set();
        const allCustomFieldIds = new Set();
        
        // console.log('[SYNC-DEBUG] Processing window.orderList to collect custom field IDs...');
        window.orderList.forEach(f => {
          if (f.type === 'custom') {
            // console.log('[SYNC-DEBUG] Custom field found - id:', f.id, 'field_id:', f.field_id, 'enabled:', f.enabled);
            if (f.field_id) {
              allCustomFieldIds.add(f.field_id);
              if (f.enabled) {
                enabledCustomFieldIds.add(f.field_id);
              }
            }
          }
        });
        
        // console.log('[SYNC-DEBUG] allCustomFieldIds (from orderList):', Array.from(allCustomFieldIds));
        // console.log('[SYNC-DEBUG] enabledCustomFieldIds:', Array.from(enabledCustomFieldIds));
        
        // Create hidden inputs for enabled fields
        enabledFields.forEach((field, index) => {
          const orderNumber = index + 1;
          
          if (field.type === 'fixed') {
            // For enabled fixed fields, create hidden input for order
            // Gunakan fixed_name (string key seperti 'nama_barang') bukan field.id (integer)
            const fixedKey = field.fixed_name || field.id;
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = `fixed_fields[${fixedKey}]`;
            input.value = orderNumber;
            container.appendChild(input);
            // Also send is_required for fixed fields
            const reqInput = document.createElement('input');
            reqInput.type = 'hidden';
            reqInput.name = `fixed_fields_required[${fixedKey}]`;
            reqInput.value = field.is_required ? '1' : '0';
            container.appendChild(reqInput);
            // Send include_in_export for fixed fields
            const expInput = document.createElement('input');
            expInput.type = 'hidden';
            expInput.name = `fixed_fields_export[${fixedKey}]`;
            expInput.value = field.include_in_export !== false ? '1' : '0';
            container.appendChild(expInput);
          } else if (field.type === 'custom' && field.field_id) {
            // For custom fields, update the hidden order input in the field item
            const fieldItem = document.getElementById(`field-${field.field_id}`);
            if (fieldItem) {
              const orderInput = fieldItem.querySelector('.field-order-input');
              if (orderInput) {
                orderInput.value = orderNumber;
              }
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
        
        // Determine which custom fields are DISABLED (not in enabledCustomFieldIds)
        // Source: allCustomFieldIds from orderList
        const deletedCustomFields = Array.from(allCustomFieldIds).filter(id => !enabledCustomFieldIds.has(id));
        // console.log('[SYNC-DEBUG] DISABLED custom fields to delete:', deletedCustomFields);
        
        // Send disabled custom fields if any
        if (deletedCustomFields.length > 0) {
          deletedCustomFields.forEach(fieldId => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'deleted_custom_fields[]';
            input.value = fieldId;
            container.appendChild(input);
            // console.log('[SYNC-DEBUG] Added deleted_custom_fields[]:', fieldId);
          });
        }
        // console.log('[SYNC-DEBUG] Container HTML:', container.innerHTML);
      }

      // Confirm Submit
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

        // Tampilkan konfirmasi DULU, baru sync dan submit setelah user klik Ya
        AppModal.confirm(
          'Update Jenis Inventaris',
          'Apakah Anda yakin ingin update data jenis inventaris ini?',
          function() {
            // Sync order dan pejabat SETELAH user konfirmasi
            syncOrderToForm();
            if (typeof window.updateHiddenPejabatInputs === 'function') {
              window.updateHiddenPejabatInputs();
            }
            form.submit();
          }
        );
      }
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
          label.style.borderColor = checked ? 'var(--color-primary, #1f3a2a)' : 'var(--color-border, #cfdcc8)';
          label.style.background  = checked ? 'var(--color-primary-tint, #d5e4d7)' : 'var(--color-surface, #f8faf7)';
        }
      };

      // Init on load
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
