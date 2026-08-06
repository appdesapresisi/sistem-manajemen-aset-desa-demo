      /* Signature method switcher */
      .signature-method-toggle {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px;
        margin-bottom: 12px;
      }
      .signature-method-option {
        display: flex;
      }
      .signature-method-option input {
        display: none;
      }
      .signature-method-option span {
        display: flex;
        flex: 1;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 12px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        background: #fff;
        color: #374151;
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
        border-color: #064635;
        background: #eef2ff;
        color: #1f2937;
      }

      /* Draw signature */
      .signature-draw-section {
        margin-bottom: 12px;
      }
      .signature-draw-wrapper {
        border: 1px solid #d1d5db;
        border-radius: 12px;
        background: #fff;
        overflow: hidden;
      }
      .signature-canvas {
        width: 100%;
        height: 220px;
        display: block;
        cursor: crosshair;
        touch-action: none;
        background: #fff;
      }
      .signature-draw-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        margin-top: 10px;
      }
      .btn-clear-signature {
        border: 1px solid #d1d5db;
        background: #fff;
        color: #374151;
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 12px;
        font-weight: 500;
        cursor: pointer;
      }
      .btn-clear-signature:hover {
        background: #f3f4f6;
      }
      .signature-draw-hint {
        font-size: 12px;
        color: #6b7280;
      }
