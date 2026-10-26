<!-- Modal Overlay -->
<div id="modal-overlay" class="modal-overlay" style="display: none;">
  <div class="modal-container">
    <div class="modal-content" id="modal-content">
      <!-- Dynamic content will be inserted here -->
    </div>
  </div>
</div>

<style>
  /* Modal Overlay - SIMA Design System Standard */
  #modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(15, 23, 18, 0.55);
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    z-index: 999999;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 16px;
    box-sizing: border-box;
    animation: modalFadeIn 0.2s cubic-bezier(0.16, 1, 0.3, 1);
  }
  
  @keyframes modalFadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
  }
  
  @keyframes modalSlideUp {
    from {
      opacity: 0;
      transform: translateY(16px) scale(0.96);
    }
    to {
      opacity: 1;
      transform: translateY(0) scale(1);
    }
  }
  
  #modal-overlay .modal-container {
    width: 100%;
    max-width: 480px;
    margin: auto;
    animation: modalSlideUp 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    display: block !important;
    background: transparent !important;
    border: none !important;
    box-shadow: none !important;
    padding: 0 !important;
  }
  
  #modal-overlay #modal-content,
  #modal-overlay .modal-content {
    background: var(--color-surface, #f8faf7) !important;
    background-color: var(--color-surface, #f8faf7) !important;
    border-radius: var(--radius-xl, 20px) !important;
    border: 1px solid var(--color-border, #cfdcc8) !important;
    box-shadow: 0 20px 45px -10px rgba(31, 58, 42, 0.28), 0 0 0 1px rgba(207, 220, 200, 0.5) !important;
    overflow: hidden !important;
    display: block !important;
    width: 100% !important;
    padding: 28px 32px 30px 32px !important;
    box-sizing: border-box !important;
    position: relative;
    color: var(--color-text, #16251b) !important;
  }

  /* Top Drag/Pill Dash Indicator */
  .modal-top-dash {
    width: 42px;
    height: 4.5px;
    border-radius: 9999px;
    background: var(--color-border, #cfdcc8);
    margin: 0 auto 20px auto;
  }
  
  #modal-overlay #modal-content .modal-header,
  #modal-overlay .modal-header {
    display: block !important;
    padding: 0 !important;
    text-align: center !important;
    background: transparent !important;
    border: none !important;
  }
  
  #modal-overlay .modal-icon {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    margin: 0 auto 16px auto;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.2s ease;
  }
  
  #modal-overlay .modal-icon.success {
    background: rgba(63, 125, 58, 0.12);
    color: var(--color-success, #3F7D3A);
    border: 1.5px solid rgba(63, 125, 58, 0.25);
  }
  
  #modal-overlay .modal-icon.warning {
    background: rgba(217, 119, 6, 0.12);
    color: #D97706;
    border: 1.5px solid rgba(217, 119, 6, 0.25);
  }
  
  #modal-overlay .modal-icon.danger {
    background: rgba(166, 61, 47, 0.1);
    color: var(--color-danger, #A63D2F);
    border: 1.5px solid rgba(166, 61, 47, 0.22);
  }

  #modal-overlay .modal-icon.info {
    background: rgba(62, 106, 122, 0.12);
    color: var(--color-info, #3E6A7A);
    border: 1.5px solid rgba(62, 106, 122, 0.25);
  }
  
  #modal-overlay .modal-icon svg,
  #modal-overlay .modal-icon i {
    width: 26px;
    height: 26px;
    stroke-width: 2.2;
    stroke: currentColor;
  }
  
  #modal-overlay .modal-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--color-text, #16251b) !important;
    margin: 0 0 8px 0;
    line-height: 1.35;
    letter-spacing: -0.015em;
    text-align: center;
  }
  
  #modal-overlay .modal-message {
    font-size: 13.5px;
    color: var(--color-text-muted, #546958) !important;
    line-height: 1.55;
    margin: 0 0 20px 0;
    text-align: center;
    font-weight: 400;
  }

  #modal-overlay #modal-content .modal-body,
  #modal-overlay .modal-body {
    padding: 0 !important;
    margin-bottom: 22px;
    background: transparent !important;
    border: none !important;
  }
  
  #modal-overlay #modal-content .modal-footer,
  #modal-overlay .modal-footer {
    display: flex !important;
    padding: 0 !important;
    justify-content: center !important;
    gap: 12px !important;
    border-top: none !important;
    background: transparent !important;
    width: 100% !important;
    box-sizing: border-box !important;
  }
  
  #modal-overlay .modal-footer.single .modal-btn {
    width: 100% !important;
    flex: 1;
  }

  #modal-overlay .modal-input {
    width: 100%;
    padding: 11px 15px;
    border: 1.5px solid var(--color-border, #cfdcc8);
    border-radius: var(--radius-md, 10px);
    font-size: 13.5px;
    color: var(--color-text, #16251b);
    background: var(--color-surface, #f8faf7);
    transition: all 0.2s ease;
    box-sizing: border-box;
    margin-bottom: 6px;
  }

  #modal-overlay .modal-input:focus {
    outline: none;
    background: var(--color-surface, #f8faf7);
    border-color: var(--color-primary, #1f3a2a);
    box-shadow: 0 0 0 3px rgba(31, 58, 42, 0.12);
  }

  #modal-overlay .modal-input-error {
    font-size: 12px;
    color: var(--color-danger, #A63D2F);
    margin-top: 4px;
    min-height: 16px;
    text-align: left;
  }
  
  #modal-overlay .modal-btn {
    flex: 1;
    height: 44px;
    padding: 0 20px;
    border-radius: var(--radius-full, 9999px);
    font-weight: 600;
    font-size: 13.5px;
    border: 1.5px solid transparent;
    cursor: pointer;
    transition: all 0.15s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    justify-content: center;
    font-family: inherit;
    line-height: 1.4;
    text-decoration: none;
    box-sizing: border-box;
  }
  
  #modal-overlay .modal-btn i,
  #modal-overlay .modal-btn svg {
    width: 16px;
    height: 16px;
    stroke-width: 2;
    stroke: currentColor;
    flex-shrink: 0;
  }
  
  #modal-overlay .modal-btn-primary {
    background: var(--color-primary, #1f3a2a);
    color: #ffffff;
    border: 1.5px solid var(--color-primary, #1f3a2a);
  }
  
  #modal-overlay .modal-btn-primary:hover {
    background: #162c1f;
    border-color: #162c1f;
    color: #ffffff;
  }
  
  #modal-overlay .modal-btn-danger {
    background: var(--color-danger, #A63D2F);
    color: #ffffff;
    border: 1.5px solid var(--color-danger, #A63D2F);
  }
  
  #modal-overlay .modal-btn-danger:hover {
    background: #8b3226;
    border-color: #8b3226;
    color: #ffffff;
  }

  #modal-overlay .modal-btn-warning {
    background: var(--color-warning, #B7791F);
    color: #ffffff;
    border: 1.5px solid var(--color-warning, #B7791F);
  }

  #modal-overlay .modal-btn-warning:hover {
    background: #9A6216;
    border-color: #9A6216;
    color: #ffffff;
  }
  
  #modal-overlay .modal-btn-secondary {
    background: var(--color-surface, #f8faf7);
    color: var(--color-text, #16251b);
    border: 1.5px solid var(--color-border, #cfdcc8);
  }
  
  #modal-overlay .modal-btn-secondary:hover {
    background: var(--color-primary-tint, #d5e4d7);
    border-color: var(--color-border-dark, #546958);
    color: var(--color-text, #16251b);
  }
  
  #modal-overlay .modal-btn:active {
    transform: scale(0.98);
  }

  /* Interactive Option Cards (SIMA Modern Selection) */
  .modal-opt-group {
    display: flex;
    flex-direction: column;
    gap: 0;
    width: 100%;
    margin-bottom: 8px;
    border-top: 1px dashed var(--color-border, #cfdcc8);
    border-bottom: 1px dashed var(--color-border, #cfdcc8);
  }

  .modal-opt-card {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 13px 10px;
    border: none;
    border-bottom: 1px dashed var(--color-border, #cfdcc8);
    border-radius: 0;
    background: transparent;
    cursor: pointer;
    transition: background-color 0.15s ease;
    text-align: left;
    user-select: none;
    position: relative;
    box-shadow: none !important;
  }

  .modal-opt-card:last-child {
    border-bottom: none;
  }

  .modal-opt-card:hover {
    background: var(--color-row-hover, #eff5ed);
    border-radius: var(--radius-sm, 8px);
    box-shadow: none !important;
  }

  .modal-opt-card.active {
    background: transparent !important;
    border-color: var(--color-border, #cfdcc8) !important;
    box-shadow: none !important;
  }

  /* Ensure native radio inputs inside option card are completely invisible */
  .modal-opt-card input[type="radio"] {
    position: absolute !important;
    opacity: 0 !important;
    width: 0 !important;
    height: 0 !important;
    pointer-events: none !important;
    margin: 0 !important;
    display: none !important;
  }

  .modal-opt-indicator {
    width: 18px;
    height: 18px;
    border-radius: 50%;
    border: 2px solid var(--color-border-dark, #546958);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    margin-top: 1.5px;
    transition: all 0.15s ease;
    background: transparent;
    box-sizing: border-box;
  }

  .modal-opt-indicator::after {
    content: '';
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: transparent;
    transition: all 0.15s ease;
  }

  .modal-opt-card.active .modal-opt-indicator {
    border-color: var(--color-primary, #1f3a2a);
  }

  .modal-opt-card.active .modal-opt-indicator::after {
    background: var(--color-primary, #1f3a2a);
  }

  .modal-opt-card-title {
    font-size: 13.5px;
    font-weight: 700;
    color: var(--color-text, #16251b);
    margin-bottom: 2px;
  }

  .modal-opt-card.active .modal-opt-card-title {
    color: var(--color-primary, #1f3a2a);
  }

  .modal-opt-card-desc {
    font-size: 12px;
    color: var(--color-text-muted, #546958);
    line-height: 1.4;
  }
  
  /* Responsive */
  @media (max-width: 640px) {
    #modal-overlay #modal-content,
    #modal-overlay .modal-content {
      padding: 22px 20px 24px 20px !important;
      border-radius: 16px !important;
    }
    
    #modal-overlay .modal-title {
      font-size: 17px;
    }
    
    #modal-overlay .modal-message {
      font-size: 13px;
    }
  }
</style>

<script>
  // Modal Functions
  window.AppModal = window.AppModal || {
    overlay: null,
    content: null,
    
    init() {
      this.overlay = document.getElementById('modal-overlay');
      this.content = document.getElementById('modal-content');
      if (!this.overlay || !this.content) return;
      
      // Close on overlay click
      if (this.overlay) {
        this.overlay.addEventListener('click', (e) => {
          if (e.target === this.overlay) {
            if (this._closeOnOverlay !== false) {
              this.close();
            }
          }
        });
      }
      
      // Close on ESC key
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
          if (this._closeOnEsc !== false) {
            this.close();
          }
        }
      });
    },
    
    show(config) {
      if (!this.overlay || !this.content) this.init();
      if (!this.overlay || !this.content) {
        console.error('[AppModal] overlay or content element not found');
        return;
      }
      
      const {
        type = 'success', // success, warning, danger, info
        title = '',
        message = '',
        confirmText = 'OK',
        cancelText = 'Batal',
        showCancel = false,
        onConfirm = null,
        onCancel = null,
        icon = null,
        customHtml = null,
        closeOnOverlay = true,
        closeOnEsc = true
      } = config;

      this._closeOnOverlay = closeOnOverlay;
      this._closeOnEsc = closeOnEsc;

      const customContent = config.customHtml || config.content || null;
      if (customContent) {
        this.content.innerHTML = `
          <div class="modal-top-dash"></div>
          ${customContent}
        `;
        this.overlay.style.display = 'flex';

        if (typeof feather !== 'undefined') {
          try {
            const iconEls = this.content.querySelectorAll('[data-feather]');
            iconEls.forEach(el => {
              const iconName = el.getAttribute('data-feather');
              try {
                const svg = feather.icons[iconName];
                if (svg) {
                  el.outerHTML = svg.toSvg({ class: el.getAttribute('class') || '', style: el.getAttribute('style') || '' });
                }
              } catch(e2) {}
            });
          } catch(e) {}
        }

        this._onConfirm = onConfirm;
        this._onCancel = onCancel;
        document.body.style.overflow = 'hidden';
        return;
      }
      
      // Build icon
      let iconSvg = '';
      if (type === 'success') {
        iconSvg = '<i data-feather="check"></i>';
      } else if (type === 'warning') {
        iconSvg = '<i data-feather="alert-triangle"></i>';
      } else if (type === 'danger') {
        iconSvg = '<i data-feather="trash-2"></i>';
      } else {
        iconSvg = '<i data-feather="info"></i>';
      }
      
      if (icon) iconSvg = icon;
      
      // Build HTML
      const html = `
        <div class="modal-top-dash"></div>
        <div class="modal-header">
          <div class="modal-icon ${type}">
            ${iconSvg}
          </div>
          <h3 class="modal-title">${title}</h3>
          <p class="modal-message">${message}</p>
        </div>
        <div class="modal-footer ${showCancel ? '' : 'single'}">
          ${showCancel ? `
            <button type="button" class="modal-btn modal-btn-secondary" onclick="AppModal.handleCancel()">
              ${cancelText}
            </button>
          ` : ''}
          <button type="button" class="modal-btn modal-btn-${type === 'danger' ? 'danger' : (type === 'warning' ? 'warning' : 'primary')}" onclick="AppModal.handleConfirm()">
            ${confirmText}
          </button>
        </div>
      `;
      
      this.content.innerHTML = html;
      this.overlay.style.display = 'flex';
      
      if (typeof feather !== 'undefined') {
        try {
          const iconEls = this.content.querySelectorAll('[data-feather]');
          iconEls.forEach(el => {
            const iconName = el.getAttribute('data-feather');
            try {
              const svg = feather.icons[iconName];
              if (svg) {
                el.outerHTML = svg.toSvg({ class: el.getAttribute('class') || '', style: el.getAttribute('style') || '' });
              }
            } catch(e2) {}
          });
        } catch(e) {}
      }
      
      this._onConfirm = onConfirm;
      this._onCancel = onCancel;
      document.body.style.overflow = 'hidden';
    },
    
    close() {
      if (this.overlay) {
        this.overlay.style.display = 'none';
        document.body.style.overflow = '';
      }
    },
    
    handleConfirm() {
      let result;
      if (this._onConfirm) {
        result = this._onConfirm();
      }
      if (result === false) return;
      this.close();
    },
    
    handleCancel() {
      if (this._onCancel) {
        this._onCancel();
      }
      this.close();
    },
    
    // Preset methods
    success(title, message, onConfirm = null) {
      if (typeof title === 'object' && title !== null) {
        this.show(Object.assign({ type: 'success', confirmText: 'Selesai', showCancel: false }, title));
        return;
      }
      this.show({
        type: 'success',
        title: title,
        message: message,
        confirmText: 'Selesai',
        showCancel: false,
        onConfirm: onConfirm
      });
    },
    
    error(title, message, onConfirm = null) {
      if (typeof title === 'object' && title !== null) {
        this.show(Object.assign({ type: 'danger', confirmText: 'Tutup', showCancel: false }, title));
        return;
      }
      this.show({
        type: 'danger',
        title: title,
        message: message,
        confirmText: 'Tutup',
        showCancel: false,
        onConfirm: onConfirm
      });
    },

    warning(title, message, onConfirm = null) {
      if (typeof title === 'object' && title !== null) {
        this.show(Object.assign({ type: 'warning', confirmText: 'Mengerti', showCancel: false }, title));
        return;
      }
      this.show({
        type: 'warning',
        title: title,
        message: message,
        confirmText: 'Mengerti',
        showCancel: false,
        onConfirm: onConfirm
      });
    },
    
    confirm(title, message, onConfirm = null, onCancel = null, options = {}) {
      if (typeof title === 'object' && title !== null) {
        const opts = title;
        const isDanger = (opts.type === 'danger') || (opts.confirmClass && opts.confirmClass.includes('danger')) || /hapus|delete|destroy/i.test(opts.title || '');
        this.show({
          type: opts.type || (isDanger ? 'danger' : 'warning'),
          title: opts.title || 'Konfirmasi',
          message: opts.message || '',
          confirmText: opts.confirmText || (isDanger ? 'Hapus' : 'Lanjutkan'),
          cancelText: opts.cancelText || 'Batal',
          showCancel: opts.showCancel !== false,
          onConfirm: opts.onConfirm || null,
          onCancel: opts.onCancel || null
        });
        return;
      }
      const isDanger = (options && options.type === 'danger') || /hapus|delete|destroy/i.test(title || '');
      this.show({
        type: isDanger ? 'danger' : (options && options.type ? options.type : 'warning'),
        title: title,
        message: message,
        confirmText: (options && options.confirmText) || (isDanger ? 'Hapus' : 'Lanjutkan'),
        cancelText: (options && options.cancelText) || 'Batal',
        showCancel: true,
        onConfirm: onConfirm,
        onCancel: onCancel
      });
    },
    
    confirmDelete(itemName, onConfirm = null) {
      this.show({
        type: 'danger',
        title: 'Hapus Data?',
        message: `Apakah Anda yakin ingin menghapus "${itemName}"? Data yang dihapus tidak dapat dipulihkan kembali.`,
        confirmText: 'Hapus',
        cancelText: 'Batal',
        showCancel: true,
        onConfirm: onConfirm
      });
    },

    confirmCancel(options = {}) {
      let onConfirm = null;
      let targetUrl = null;
      let returnKey = null;
      let isEdit = false;
      let message = null;
      let title = null;

      if (typeof options === 'function') {
        onConfirm = options;
      } else if (typeof options === 'string') {
        targetUrl = options;
      } else if (typeof options === 'object' && options !== null) {
        onConfirm = options.onConfirm || null;
        targetUrl = options.targetUrl || options.url || null;
        returnKey = options.returnKey || options.featureKey || null;
        isEdit = typeof options.isEdit !== 'undefined' ? options.isEdit : (window.location.pathname.includes('/edit'));
        message = options.message;
        title = options.title;
      }

      const defaultTitle = 'Konfirmasi Pembatalan';
      const defaultMessage = isEdit 
        ? 'Apakah Anda yakin ingin membatalkan? Perubahan yang belum disimpan akan hilang.'
        : 'Apakah Anda yakin ingin membatalkan? Data yang sudah diisi tidak akan tersimpan.';

      this.show({
        type: 'warning',
        title: title || defaultTitle,
        message: message || defaultMessage,
        confirmText: (options && options.confirmText) || 'Ya, Batalkan',
        cancelText: (options && options.cancelText) || 'Kembali',
        showCancel: true,
        onConfirm: () => {
          if (typeof onConfirm === 'function') {
            onConfirm();
            return;
          }
          if (returnKey) {
            try { sessionStorage.setItem(returnKey, '1'); } catch(e) {}
          }
          if (targetUrl) {
            window.location.href = targetUrl;
          } else if (window.history.length > 1) {
            window.history.back();
          }
        }
      });
    },

    prompt({
      title = 'Input Data',
      message = 'Masukkan nilai yang diminta',
      placeholder = '',
      value = '',
      confirmText = 'Simpan',
      cancelText = 'Batal',
      onConfirm = null,
      onCancel = null,
      closeOnOverlay = false,
      closeOnEsc = false
    } = {}) {
      const inputId = 'app-modal-input';
      const errorId = 'app-modal-input-error';
      const html = `
        <div class="modal-header">
          <div class="modal-icon warning">
            <i data-feather="edit-3"></i>
          </div>
          <h3 class="modal-title">${title}</h3>
          <p class="modal-message">${message}</p>
        </div>
        <div class="modal-body">
          <input
            type="text"
            id="${inputId}"
            class="modal-input"
            placeholder="${placeholder}"
            value="${value}"
            onkeydown="AppModal.handlePromptKey(event)"
            oninput="AppModal.clearPromptError()"
          />
          <div class="modal-input-error" id="${errorId}"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="modal-btn modal-btn-secondary" onclick="AppModal.handleCancel()">
            ${cancelText}
          </button>
          <button type="button" class="modal-btn modal-btn-primary" onclick="AppModal.handleConfirm()">
            ${confirmText}
          </button>
        </div>
      `;

      this._promptInputId = inputId;
      this._promptErrorId = errorId;
      this.show({
        customHtml: html,
        onConfirm: () => {
          const inputEl = document.getElementById(inputId);
          const inputValue = inputEl ? inputEl.value : '';
          if (onConfirm) return onConfirm(inputValue);
        },
        onCancel: onCancel,
        closeOnOverlay: closeOnOverlay,
        closeOnEsc: closeOnEsc
      });

      setTimeout(() => {
        const inputEl = document.getElementById(inputId);
        if (inputEl) inputEl.focus();
      }, 0);
    },

    setPromptError(message) {
      const errorEl = this._promptErrorId ? document.getElementById(this._promptErrorId) : null;
      if (errorEl) {
        errorEl.textContent = message || '';
      }
    },

    clearPromptError() {
      this.setPromptError('');
    },

    handlePromptKey(e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        this.handleConfirm();
      }
    }
  };
  
  // AppNotification Bridge for System-Wide Notifications
  window.AppNotification = window.AppNotification || {
    success(message) {
      window.AppModal.success('Berhasil', message);
    },
    error(message) {
      window.AppModal.error('Gagal', message);
    },
    warning(message) {
      window.AppModal.show({ type: 'warning', title: 'Perhatian', message: message });
    },
    info(message) {
      window.AppModal.show({ type: 'info', title: 'Informasi', message: message });
    }
  };

  // Global helper for cancel buttons across create & edit forms
  window.confirmCancel = function(optionsOrEvent, maybeOptions) {
    let options = {};
    if (optionsOrEvent && typeof optionsOrEvent.preventDefault === 'function') {
      optionsOrEvent.preventDefault();
      optionsOrEvent.stopPropagation();
      const el = optionsOrEvent.currentTarget || optionsOrEvent.target;
      if (el && el.getAttribute) {
        const href = el.getAttribute('href');
        if (href && href !== '#' && !href.startsWith('javascript:')) {
          options.url = href;
        }
      }
      if (maybeOptions && typeof maybeOptions === 'object') {
        Object.assign(options, maybeOptions);
      }
    } else if (typeof optionsOrEvent === 'function') {
      options.onConfirm = optionsOrEvent;
    } else if (typeof optionsOrEvent === 'string') {
      options.url = optionsOrEvent;
    } else if (typeof optionsOrEvent === 'object' && optionsOrEvent !== null) {
      options = optionsOrEvent;
    }

    if (window.AppModal && typeof window.AppModal.confirmCancel === 'function') {
      window.AppModal.confirmCancel(options);
    } else {
      const msg = options.message || (window.location.pathname.includes('/edit') 
        ? 'Apakah Anda yakin ingin membatalkan? Perubahan yang belum disimpan akan hilang.' 
        : 'Apakah Anda yakin ingin membatalkan? Data yang sudah diisi tidak akan tersimpan.');
      if (confirm(msg)) {
        if (typeof options.onConfirm === 'function') {
          options.onConfirm();
        } else if (options.url) {
          window.location.href = options.url;
        } else if (window.history.length > 1) {
          window.history.back();
        }
      }
    }
    return false;
  };

  // Initialize on DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => window.AppModal.init());
  } else {
    window.AppModal.init();
  }
</script>

<!-- GLOBAL SIMA IMAGE VIEWER (Google Drive / Photos style standard) -->
<div class="sima-foto-viewer" id="globalSimaImageViewer" role="dialog" aria-modal="true" aria-label="Penampil Gambar" onclick="if(event.target === this) closeSimaImageViewer()">
  <!-- Topbar: Counter & Close button -->
  <div class="sfv-topbar">
    <div class="sfv-counter" id="globalSfvCounter">
      <span id="globalSfvCounterText"></span>
    </div>
    <button type="button" class="sfv-close-btn" onclick="closeSimaImageViewer()" title="Tutup (Esc)">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="18" y1="6" x2="6" y2="18"></line>
        <line x1="6" y1="6" x2="18" y2="18"></line>
      </svg>
    </button>
  </div>

  <!-- Stage: Main Image & Navigation Arrows -->
  <div class="sfv-stage" onclick="if(event.target === this) closeSimaImageViewer()">
    <button type="button" class="sfv-nav-btn sfv-prev" id="globalSfvPrevBtn" onclick="prevSimaImage(event)" title="Gambar Sebelumnya (Panah Kiri)">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="15 18 9 12 15 6"></polyline>
      </svg>
    </button>

    <img src="" class="sfv-main-img" id="globalSfvMainImg" alt="Preview Gambar">

    <button type="button" class="sfv-nav-btn sfv-next" id="globalSfvNextBtn" onclick="nextSimaImage(event)" title="Gambar Berikutnya (Panah Kanan)">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="9 18 15 12 9 6"></polyline>
      </svg>
    </button>
  </div>

  <!-- Bottom Thumbnail Strip (HANYA DITAMPILKAN JIKA LEBIH DARI 1 GAMBAR) -->
  <div class="sfv-strip" id="globalSfvStrip" style="display:none;"></div>
</div>

<script>
  (function() {
    let globalViewerImages = [];
    let globalViewerCurrentIdx = 0;
    let globalViewerTitle = '';
    let globalOnSwitchCallback = null;

    window.openSimaImageViewer = function(images, startIndex, title, onSwitchCallback) {
      if (!images) return;
      if (typeof images === 'string') {
        globalViewerImages = [images];
      } else if (Array.isArray(images)) {
        globalViewerImages = images.map(item => (typeof item === 'string' ? item : (item.url || item.src || ''))).filter(Boolean);
      } else {
        return;
      }

      if (globalViewerImages.length === 0) return;

      globalViewerCurrentIdx = typeof startIndex === 'number' ? Math.max(0, Math.min(startIndex, globalViewerImages.length - 1)) : 0;
      globalViewerTitle = title || '';
      globalOnSwitchCallback = (typeof onSwitchCallback === 'function') ? onSwitchCallback : null;

      const viewer = document.getElementById('globalSimaImageViewer');
      if (!viewer) return;

      updateGlobalViewerUI();
      viewer.classList.add('open');
      document.body.style.overflow = 'hidden';
    };

    window.closeSimaImageViewer = function() {
      const viewer = document.getElementById('globalSimaImageViewer');
      if (viewer) {
        viewer.classList.remove('open');
        document.body.style.overflow = '';
      }
    };

    window.prevSimaImage = function(e) {
      if (e) e.stopPropagation();
      if (globalViewerCurrentIdx > 0) {
        globalViewerCurrentIdx--;
        updateGlobalViewerUI();
      }
    };

    window.nextSimaImage = function(e) {
      if (e) e.stopPropagation();
      if (globalViewerCurrentIdx < globalViewerImages.length - 1) {
        globalViewerCurrentIdx++;
        updateGlobalViewerUI();
      }
    };

    window.goToSimaImage = function(idx) {
      globalViewerCurrentIdx = idx;
      updateGlobalViewerUI();
    };

    function updateGlobalViewerUI() {
      const img = document.getElementById('globalSfvMainImg');
      const counterText = document.getElementById('globalSfvCounterText');
      const prevBtn = document.getElementById('globalSfvPrevBtn');
      const nextBtn = document.getElementById('globalSfvNextBtn');
      const strip = document.getElementById('globalSfvStrip');

      const count = globalViewerImages.length;
      const currentSrc = globalViewerImages[globalViewerCurrentIdx];

      if (img && currentSrc) {
        img.src = currentSrc;
      }

      // Panggil callback sinkronisasi jika ada
      if (globalOnSwitchCallback) {
        try {
          globalOnSwitchCallback(currentSrc, globalViewerCurrentIdx);
        } catch(err) {}
      }

      // Title & Counter
      if (counterText) {
        if (count > 1) {
          counterText.textContent = `Foto ${globalViewerCurrentIdx + 1} dari ${count}${globalViewerTitle ? ' • ' + globalViewerTitle : ''}`;
        } else {
          counterText.textContent = globalViewerTitle || 'Lihat Gambar';
        }
      }

      // Tombol Prev & Next (Hanya muncul jika > 1 gambar)
      if (prevBtn && nextBtn) {
        if (count > 1) {
          prevBtn.style.display = 'flex';
          nextBtn.style.display = 'flex';
          prevBtn.disabled = (globalViewerCurrentIdx === 0);
          nextBtn.disabled = (globalViewerCurrentIdx === count - 1);
        } else {
          prevBtn.style.display = 'none';
          nextBtn.style.display = 'none';
        }
      }

      // Strip Thumbnail di bawah (HANYA MUNCUL JIKA LEBIH DARI 1 GAMBAR)
      if (strip) {
        if (count > 1) {
          strip.style.display = 'flex';
          strip.innerHTML = globalViewerImages.map((src, i) => `
            <div class="sfv-strip-thumb ${i === globalViewerCurrentIdx ? 'active' : ''}" onclick="goToSimaImage(${i})" title="Foto ${i + 1}">
              <img src="${src}" alt="Thumb ${i + 1}">
            </div>
          `).join('');

          // Auto scroll thumbnail aktif ke tengah
          const activeThumb = strip.querySelector('.sfv-strip-thumb.active');
          if (activeThumb) {
            activeThumb.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
          }
        } else {
          strip.style.display = 'none';
          strip.innerHTML = '';
        }
      }
    }

    // Navigasi keyboard (Esc, Panah Kiri, Panah Kanan)
    document.addEventListener('keydown', function(e) {
      const viewer = document.getElementById('globalSimaImageViewer');
      if (!viewer || !viewer.classList.contains('open')) return;

      if (e.key === 'Escape') {
        closeSimaImageViewer();
      } else if (e.key === 'ArrowLeft') {
        prevSimaImage();
      } else if (e.key === 'ArrowRight') {
        nextSimaImage();
      }
    });
  })();
</script>

