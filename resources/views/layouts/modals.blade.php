<!-- Modal Overlay -->
<div id="modal-overlay" class="modal-overlay" style="display: none;">
  <div class="modal-container">
    <div class="modal-content" id="modal-content">
      <!-- Dynamic content will be inserted here -->
    </div>
  </div>
</div>

<style>
  /* Modal Overlay */
  .modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(4px);
    z-index: 999999;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: fadeIn 0.2s ease;
  }
  
  @keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
  }
  
  @keyframes slideDown {
    from {
      opacity: 0;
      transform: translateY(-20px) scale(0.95);
    }
    to {
      opacity: 1;
      transform: translateY(0) scale(1);
    }
  }
  
  .modal-container {
    width: 90%;
    max-width: 480px;
    animation: slideDown 0.3s ease;
  }
  
  .modal-content {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    overflow: hidden;
  }
  
  /* Override Bootstrap modal classes yang conflict — scoped ke dalam modal-overlay kita */
  #modal-overlay #modal-content .modal-header,
  .modal-overlay #modal-content .modal-header {
    display: block !important;
    padding: 24px 24px 20px 24px !important;
    text-align: center !important;
    border-bottom: none !important;
    flex-direction: unset !important;
    align-items: unset !important;
    justify-content: unset !important;
  }
  
  #modal-overlay #modal-content .modal-footer,
  .modal-overlay #modal-content .modal-footer {
    display: flex !important;
    padding: 20px 24px 24px 24px !important;
    justify-content: center !important;
    border-top: none !important;
    background: transparent !important;
    flex-wrap: nowrap !important;
    align-items: center !important;
  }

  #modal-overlay #modal-content,
  .modal-overlay #modal-content {
    background: #fff !important;
    border-radius: 16px !important;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3) !important;
    overflow: hidden !important;
    display: block !important;
    border: none !important;
    min-width: 300px;
  }

  #modal-overlay .modal-container,
  .modal-overlay .modal-container {
    width: 90%;
    max-width: 480px;
    animation: slideDown 0.3s ease;
    display: block !important;
  }
  
  /* Modal Header */
  .modal-header {
    padding: 24px 24px 20px 24px;
    text-align: center;
  }
  
  .modal-icon {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    margin: 0 auto 16px;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  .modal-icon.success {
    background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
  }
  
  .modal-icon.warning {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
  }
  
  .modal-icon.danger {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
  }
  
  .modal-icon svg,
  .modal-icon i {
    width: 40px;
    height: 40px;
  }
  
  .modal-icon.success svg,
  .modal-icon.success i {
    color: #16a34a;
    stroke: #16a34a;
  }
  
  .modal-icon.warning svg,
  .modal-icon.warning i {
    color: #ea580c;
    stroke: #ea580c;
  }
  
  .modal-icon.danger svg,
  .modal-icon.danger i {
    color: #dc2626;
    stroke: #dc2626;
  }
  
  .modal-title {
    font-size: 22px;
    font-weight: 700;
    color: #1f2937;
    margin: 0 0 8px 0;
  }
  
  .modal-message {
    font-size: 15px;
    color: #6b7280;
    line-height: 1.6;
    margin: 0;
  }
  
  /* Modal Footer */
  .modal-footer {
    padding: 20px 24px 24px 24px;
    display: flex;
    gap: 12px;
    justify-content: center;
  }
  
  .modal-footer.single {
    justify-content: center;
  }

  .modal-body {
    padding: 0 24px 20px 24px;
  }

  .modal-input {
    width: 100%;
    padding: 12px 14px;
    border: 1px solid #d1d5db;
    border-radius: 10px;
    font-size: 14px;
    transition: all 0.2s ease;
  }

  .modal-input:focus {
    outline: none;
    border-color: #064635;
    box-shadow: 0 0 0 3px rgba(6, 70, 53, 0.1);
  }

  .modal-input-error {
    font-size: 12px;
    color: #dc2626;
    margin-top: 6px;
    min-height: 16px;
  }
  
  .modal-btn {
    padding: 12px 28px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 15px;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    min-width: 120px;
    justify-content: center;
  }
  
  .modal-btn-primary {
    background: linear-gradient(135deg, #064635 0%, #519259 100%);
    color: #fff;
    box-shadow: 0 4px 12px rgba(6, 70, 53, 0.3);
  }
  
  .modal-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(6, 70, 53, 0.4);
  }
  
  .modal-btn-danger {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: #fff;
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
  }
  
  .modal-btn-danger:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(239, 68, 68, 0.4);
  }
  
  .modal-btn-secondary {
    background: #fff;
    color: #374151;
    border: 2px solid #e5e7eb;
  }
  
  .modal-btn-secondary:hover {
    background: #f4faf5;
    border-color: #d1d5db;
  }
  
  .modal-btn:active {
    transform: scale(0.98);
  }
  
  /* Responsive */
  @media (max-width: 640px) {
    .modal-container {
      width: 95%;
      margin: 20px;
    }
    
    .modal-title {
      font-size: 20px;
    }
    
    .modal-message {
      font-size: 14px;
    }
    
    .modal-footer {
      flex-direction: column;
    }
    
    .modal-btn {
      width: 100%;
    }
  }
</style>

<script>
  // Modal Functions
  const AppModal = {
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
        type = 'success', // success, warning, danger
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

      if (customHtml) {
        this.content.innerHTML = customHtml;
        // Force inline style
        const containerCustom = this.content.parentElement;
        if (containerCustom) {
          containerCustom.style.cssText = 'width: 90%; max-width: 480px; display: block !important;';
        }
        this.content.style.cssText = 'background: #fff !important; border-radius: 16px !important; box-shadow: 0 20px 60px rgba(0,0,0,0.3) !important; overflow: hidden !important; display: block !important; border: none !important;';
        this.overlay.style.cssText = 'display: flex !important; position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; background: rgba(0,0,0,0.6) !important; backdrop-filter: blur(4px) !important; z-index: 999999 !important; align-items: center !important; justify-content: center !important;';

        this._onConfirm = onConfirm;
        this._onCancel = onCancel;
        document.body.style.overflow = 'hidden';
        return;
      }
      
      // Build icon
      let iconSvg = '';
      if (type === 'success') {
        iconSvg = '<i data-feather="check-circle"></i>';
      } else if (type === 'warning') {
        iconSvg = '<i data-feather="alert-triangle"></i>';
      } else if (type === 'danger') {
        iconSvg = '<i data-feather="alert-circle"></i>';
      }
      
      if (icon) iconSvg = icon;
      
      // Build HTML
      const html = `
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
          <button type="button" class="modal-btn modal-btn-${type === 'danger' ? 'danger' : 'primary'}" onclick="AppModal.handleConfirm()">
            ${confirmText}
          </button>
        </div>
      `;
      
      this.content.innerHTML = html;
      // Force inline style pada container modal untuk override semua framework CSS
      const container = this.content.parentElement; // .modal-container
      if (container) {
        container.style.cssText = 'width: 90%; max-width: 480px; animation: slideDown 0.3s ease; display: block !important;';
      }
      this.content.style.cssText = 'background: #fff !important; border-radius: 16px !important; box-shadow: 0 20px 60px rgba(0,0,0,0.3) !important; overflow: hidden !important; display: block !important; border: none !important;';
      // Gunakan inline style untuk menghindari Tailwind/Bootstrap override
      this.overlay.style.cssText = 'display: flex !important; position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; background: rgba(0,0,0,0.6) !important; backdrop-filter: blur(4px) !important; z-index: 999999 !important; align-items: center !important; justify-content: center !important;';
      
      // Replace feather icons — hanya di dalam modal content, bukan seluruh DOM
      // Hindari feather.replace() global karena bisa sangat lambat jika ada banyak elemen
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
      
      // Store callbacks
      this._onConfirm = onConfirm;
      this._onCancel = onCancel;
      
      // Prevent body scroll
      document.body.style.overflow = 'hidden';
    },
    
    close() {
      if (this.overlay) {
        this.overlay.style.display = 'none';
        this.overlay.style.cssText = 'display: none;';
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
      this.show({
        type: 'success',
        title: title,
        message: message,
        confirmText: 'OK',
        showCancel: false,
        onConfirm: onConfirm
      });
    },
    
    error(title, message, onConfirm = null) {
      this.show({
        type: 'danger',
        title: title,
        message: message,
        confirmText: 'OK',
        showCancel: false,
        onConfirm: onConfirm
      });
    },
    
    confirm(title, message, onConfirm = null, onCancel = null) {
      this.show({
        type: 'warning',
        title: title,
        message: message,
        confirmText: 'Ya, Lanjutkan',
        cancelText: 'Batal',
        showCancel: true,
        onConfirm: onConfirm,
        onCancel: onCancel
      });
    },
    
    confirmDelete(itemName, onConfirm = null) {
      this.show({
        type: 'danger',
        title: 'Konfirmasi Hapus',
        message: `Apakah Anda yakin ingin menghapus ${itemName}? Data yang dihapus tidak dapat dikembalikan.`,
        confirmText: 'Ya, Hapus',
        cancelText: 'Batal',
        showCancel: true,
        onConfirm: onConfirm
      });
    },

    prompt({
      title = 'Input',
      message = 'Masukkan nilai',
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
  
  // Initialize on DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => AppModal.init());
  } else {
    AppModal.init();
  }
  
  // Check for success/error messages from session
  @if(!($disableModalSessionFeedback ?? false))
  document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
      AppModal.success('Berhasil!', '{{ session('success') }}');
    @endif
    
    @if(session('error'))
      AppModal.error('Terjadi Kesalahan', '{{ session('error') }}');
    @endif
    
    @if($errors->any())
      AppModal.error('Validasi Gagal', '{{ $errors->first() }}');
    @endif
  });
  @endif
</script>
