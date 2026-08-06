{{-- Toast Notification Component --}}
@if(session('success') || session('error') || session('warning') || session('info'))
<div id="toast-notification" class="toast-notification">
  <div class="toast-content 
    @if(session('success')) toast-success
    @elseif(session('error')) toast-error
    @elseif(session('warning')) toast-warning
    @elseif(session('info')) toast-info
    @endif
  ">
    <div class="toast-icon">
      @if(session('success'))
        <i data-feather="check-circle"></i>
      @elseif(session('error'))
        <i data-feather="x-circle"></i>
      @elseif(session('warning'))
        <i data-feather="alert-triangle"></i>
      @elseif(session('info'))
        <i data-feather="info"></i>
      @endif
    </div>
    <div class="toast-message">
      <h6 class="toast-title">
        @if(session('success')) Berhasil!
        @elseif(session('error')) Gagal!
        @elseif(session('warning')) Perhatian!
        @elseif(session('info')) Informasi
        @endif
      </h6>
      <p class="toast-text">
        {{ session('success') ?? session('error') ?? session('warning') ?? session('info') }}
      </p>
    </div>
    <button class="toast-close" onclick="window.closeToastNow()" type="button">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="18" y1="6" x2="6" y2="18"></line>
        <line x1="6" y1="6" x2="18" y2="18"></line>
      </svg>
    </button>
  </div>
</div>

<style>
  /* Toast Notification Styles */
  .toast-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    animation: slideInRight 0.4s ease-out;
  }
  
  .toast-content {
    background: white;
    border-radius: 12px;
    padding: 16px 20px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 350px;
    max-width: 450px;
    border-left: 4px solid;
    position: relative;
  }
  
  /* Toast Types */
  .toast-success {
    border-left-color: #10b981;
    background: linear-gradient(to right, #ecfdf5 0%, white 100%);
  }
  .toast-success .toast-icon {
    color: #10b981;
  }
  
  .toast-error {
    border-left-color: #ef4444;
    background: linear-gradient(to right, #fef2f2 0%, white 100%);
  }
  .toast-error .toast-icon {
    color: #ef4444;
  }
  
  .toast-warning {
    border-left-color: #f59e0b;
    background: linear-gradient(to right, #fffbeb 0%, white 100%);
  }
  .toast-warning .toast-icon {
    color: #f59e0b;
  }
  
  .toast-info {
    border-left-color: #3b82f6;
    background: linear-gradient(to right, #eff6ff 0%, white 100%);
  }
  .toast-info .toast-icon {
    color: #3b82f6;
  }
  
  .toast-icon {
    flex-shrink: 0;
    width: 24px;
    height: 24px;
  }
  .toast-icon i {
    width: 24px;
    height: 24px;
  }
  
  .toast-message {
    flex: 1;
  }
  
  .toast-title {
    margin: 0 0 4px 0;
    font-size: 14px;
    font-weight: 600;
    color: #1f2937;
  }
  
  .toast-text {
    margin: 0;
    font-size: 13px;
    color: #6b7280;
    line-height: 1.4;
  }
  
  .toast-close {
    background: none;
    border: none;
    color: #9ca3af;
    cursor: pointer;
    padding: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
    transition: all 0.2s;
  }
  .toast-close:hover {
    background: rgba(0, 0, 0, 0.05);
    color: #6b7280;
  }
  .toast-close svg {
    width: 16px;
    height: 16px;
  }
  
  /* Animations */
  @keyframes slideInRight {
    from {
      transform: translateX(400px);
      opacity: 0;
    }
    to {
      transform: translateX(0);
      opacity: 1;
    }
  }
  
  @keyframes slideOutRight {
    from {
      transform: translateX(0);
      opacity: 1;
    }
    to {
      transform: translateX(400px);
      opacity: 0;
    }
  }
  
  .toast-notification.hiding {
    animation: slideOutRight 0.3s ease-in forwards;
  }
  
  /* Responsive */
  @media (max-width: 640px) {
    .toast-notification {
      top: 10px;
      right: 10px;
      left: 10px;
    }
    .toast-content {
      min-width: auto;
      max-width: none;
    }
  }
</style>

<script>
(function() {
  'use strict';
  
  // Define close function globally
  window.closeToastNow = function() {
    var toast = document.getElementById('toast-notification');
    if (toast) {
      toast.classList.add('hiding');
      setTimeout(function() {
        if (toast && toast.parentNode) {
          toast.parentNode.removeChild(toast);
        }
      }, 300);
    }
  };
  
  // Initialize feather icons for toast icon
  if (typeof feather !== 'undefined') {
    feather.replace();
  }
  
  // Auto dismiss after 5 seconds
  setTimeout(function() {
    window.closeToastNow();
  }, 5000);
})();
</script>
@endif
