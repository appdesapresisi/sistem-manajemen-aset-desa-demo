<!-- [ Pre-loader ] start -->
<div class="loader-bg" style="
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(5px);
    z-index: 99999;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    transition: opacity 0.4s ease, visibility 0.4s ease;
">
  <!-- Spinner Wrapper -->
  <div style="
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 16px;
  ">
    <!-- Spinner -->
    <div class="spinner-container" style="
        position: relative;
        width: 64px;
        height: 64px;
    ">
      <div style="
          position: absolute;
          width: 100%;
          height: 100%;
          border: 4px solid rgba(6, 70, 53, 0.1);
          border-radius: 50%;
          box-sizing: border-box;
      "></div>
      <div class="spinner-core" style="
          position: absolute;
          width: 100%;
          height: 100%;
          border: 4px solid transparent;
          border-top-color: #1f3a2a;
          border-radius: 50%;
          box-sizing: border-box;
          animation: spin-loader 1s linear infinite;
      "></div>
    </div>
    <!-- Loading Text -->
    <div class="loader-text" style="
        font-family: 'Inter', sans-serif;
        font-size: 15px;
        font-weight: 600;
        color: #1f3a2a;
        letter-spacing: 0.5px;
        animation: pulse-loader 1.5s ease-in-out infinite;
    ">
      Memproses data, mohon tunggu...
    </div>
  </div>
</div>

<style>
  @keyframes spin-loader {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }
  @keyframes pulse-loader {
    0%, 100% { opacity: 0.6; }
    50% { opacity: 1; }
  }
  
  /* Support dark mode theme if configured */
  [data-pc-theme="dark"] .loader-bg {
    background: rgba(18, 18, 18, 0.8) !important;
  }
  [data-pc-theme="dark"] .loader-bg .loader-text {
    color: #e2e8f0 !important;
  }
  [data-pc-theme="dark"] .spinner-core {
    border-top-color: #519259 !important;
  }
  [data-pc-theme="dark"] .spinner-container > div:first-child {
    border-color: rgba(255, 255, 255, 0.1) !important;
  }
</style>

<script>
  window.AppLoader = {
    _element: null,
    getElement() {
      if (!this._element) {
        this._element = document.querySelector('.loader-bg');
      }
      return this._element;
    },
    show() {
      const loader = this.getElement();
      if (loader) {
        loader.style.transition = 'opacity 0.6s cubic-bezier(0.16, 1, 0.3, 1), backdrop-filter 0.6s cubic-bezier(0.16, 1, 0.3, 1)';
        loader.style.display = 'flex';
        // force reflow
        loader.offsetHeight;
        loader.style.opacity = '1';
        loader.style.backdropFilter = 'blur(5px)';
        loader.style.pointerEvents = 'all';
      }
    },
    hide() {
      const loader = this.getElement();
      if (loader) {
        loader.style.transition = 'opacity 0.6s cubic-bezier(0.16, 1, 0.3, 1), backdrop-filter 0.6s cubic-bezier(0.16, 1, 0.3, 1)';
        loader.style.opacity = '0';
        loader.style.backdropFilter = 'blur(0px)';
        loader.style.pointerEvents = 'none';
        setTimeout(() => {
          if (loader.style.opacity === '0') {
            loader.style.display = 'none';
          }
        }, 600);
      }
    }
  };

  // Intercept standard form submissions globally
  document.addEventListener('submit', function (e) {
    // Wait for other handlers to potentially call preventDefault()
    setTimeout(() => {
      if (e.defaultPrevented) return;
      const form = e.target;
      // Do not intercept if form targets a new window or is explicitly marked as no-loader
      if (form && !form.getAttribute('target') && !form.classList.contains('no-loader')) {
        window.AppLoader.show();
      }
    }, 0);
  });

  // Intercept programmatic form.submit() calls globally
  (function() {
    const originalSubmit = HTMLFormElement.prototype.submit;
    HTMLFormElement.prototype.submit = function () {
      if (!this.getAttribute('target') && !this.classList.contains('no-loader')) {
        window.AppLoader.show();
      }
      originalSubmit.apply(this, arguments);
    };
  })();

  // Auto-dismiss initial page loader as soon as DOM is interactive/loaded
  function autoDismissInitialLoader() {
    try {
      if (window.AppLoader) {
        window.AppLoader.hide();
      }
    } catch(e) {}
  }

  if (document.readyState === 'complete' || document.readyState === 'interactive') {
    autoDismissInitialLoader();
  } else {
    document.addEventListener('DOMContentLoaded', autoDismissInitialLoader);
    window.addEventListener('load', autoDismissInitialLoader);
  }

  // Safety fallback: pastikan loader disembunyikan maksimal dalam 2 detik
  setTimeout(autoDismissInitialLoader, 2000);
</script>
<!-- [ Pre-loader ] End -->
