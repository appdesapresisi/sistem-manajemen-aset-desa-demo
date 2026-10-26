      /* Signature method switcher */
      .signature-method-toggle {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
        margin-bottom: 12px;
      }
      .signature-method-option {
        display: flex;
      }
      .signature-method-option input,
      .signature-method-option input[type="radio"] {
        display: none !important;
        position: absolute !important;
        opacity: 0 !important;
        width: 0 !important;
        height: 0 !important;
        pointer-events: none !important;
        visibility: hidden !important;
      }
      .signature-method-option span {
        display: flex;
        flex: 1;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 14px;
        border: 1.5px solid var(--color-border);
        border-radius: 10px;
        background: var(--color-surface);
        color: var(--color-text);
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        text-align: center;
      }
      @media (max-width: 480px) {
        .signature-method-toggle {
          grid-template-columns: 1fr;
        }
      }
      .signature-method-option span i {
        width: 14px;
        height: 14px;
      }
      .signature-method-option input:checked + span {
        border-color: var(--color-primary);
        background: var(--color-row-hover);
        color: var(--color-primary);
        font-weight: 700;
      }

      /* Draw signature */
      .signature-draw-section {
        margin-bottom: 12px;
      }
      .signature-draw-wrapper {
        border: 1.5px solid var(--color-border, #cfdcc8) !important;
        border-radius: 10px !important;
        background-color: var(--color-surface, #f8faf7) !important;
        overflow: hidden;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
      }
      .signature-draw-wrapper:hover,
      .signature-draw-wrapper:focus-within {
        border-color: var(--color-primary, #1f3a2a) !important;
      }
      .signature-canvas {
        width: 100%;
        height: 220px;
        display: block;
        cursor: crosshair;
        touch-action: none;
        background-color: var(--color-surface, #f8faf7) !important;
      }
      .signature-draw-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        margin-top: 10px;
      }
      .btn-clear-signature {
        border: 1.5px solid var(--color-border);
        background: var(--color-surface);
        color: var(--color-text);
        border-radius: var(--radius-full);
        padding: 8px 16px;
        font-size: 12px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
      }
      .btn-clear-signature:hover {
        background: var(--color-row-hover);
        color: var(--color-primary);
      }
      .signature-draw-hint {
        font-size: 12px;
        color: var(--color-text-muted);
      }
