@php
  $hasSessionFlash = session('success') || session('error') || session('warning') || session('info');
  $hasValidationErrors = isset($errors) && $errors->any();
@endphp

@if($hasSessionFlash || $hasValidationErrors)
<div id="toast-notification" class="toast-notification">
  <div class="toast-content 
    @if(session('success')) toast-success
    @elseif(session('error') || $hasValidationErrors) toast-error
    @elseif(session('warning')) toast-warning
    @elseif(session('info')) toast-info
    @endif
  ">
    {{-- Status Icon Circle --}}
    <div class="toast-icon-circle">
      @if(session('success'))
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="20 6 9 17 4 12"></polyline>
        </svg>
      @elseif(session('error') || $hasValidationErrors)
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
      @elseif(session('warning'))
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
          <line x1="12" y1="8" x2="12" y2="13"></line>
          <line x1="12" y1="17" x2="12.01" y2="17"></line>
        </svg>
      @elseif(session('info'))
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
          <line x1="12" y1="16" x2="12" y2="11"></line>
          <line x1="12" y1="7" x2="12.01" y2="7"></line>
        </svg>
      @endif
    </div>

    {{-- Message Body --}}
    <div class="toast-message">
      <h6 class="toast-title">
        @if(session('success')) Berhasil
        @elseif(session('error') || $hasValidationErrors) Terjadi Kesalahan
        @elseif(session('warning')) Perhatian
        @elseif(session('info')) Informasi
        @endif
      </h6>
      <p class="toast-text">
        {{ session('success') ?? session('error') ?? ($hasValidationErrors ? $errors->first() : null) ?? session('warning') ?? session('info') }}
      </p>
    </div>

    {{-- Close Button --}}
    <button class="toast-close" type="button" aria-label="Tutup notifikasi">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="18" y1="6" x2="6" y2="18"></line>
        <line x1="6" y1="6" x2="18" y2="18"></line>
      </svg>
    </button>

    {{-- Countdown Progress Bar --}}
    <div class="toast-progress-track">
      <div id="toast-progress-bar" class="toast-progress-bar"></div>
    </div>
  </div>
</div>
@endif

<style>
  /* Toast Notification Container */
  .toast-notification {
    position: fixed;
    top: 24px;
    right: 24px;
    z-index: 99999;
    animation: toastSlideIn 0.4s cubic-bezier(0.16, 1, 0.3, 1);
  }
  
  .toast-content {
    position: relative;
    display: flex;
    align-items: flex-start;
    gap: 14px;
    min-width: 320px;
    max-width: 420px;
    padding: 16px 18px 18px 18px;
    border-radius: var(--radius-lg, 16px);
    background: var(--color-surface, #f8faf7);
    border: 1px solid var(--color-border, #cfdcc8);
    box-shadow: var(--shadow-float, 0 8px 24px rgba(31, 58, 42, 0.12));
    overflow: hidden;
    transition: transform 0.2s ease;
  }

  .toast-content:hover {
    transform: translateY(-2px);
  }
  
  /* Status Types & Colors (SIMA Design System) */
  .toast-success .toast-icon-circle {
    background-color: var(--color-success, #3F7D3A);
    color: var(--color-surface, #f8faf7);
  }
  .toast-success .toast-progress-bar {
    background-color: var(--color-success, #3F7D3A);
  }
  
  .toast-error .toast-icon-circle {
    background-color: var(--color-danger, #A63D2F);
    color: var(--color-surface, #f8faf7);
  }
  .toast-error .toast-progress-bar {
    background-color: var(--color-danger, #A63D2F);
  }
  
  .toast-warning .toast-icon-circle {
    background-color: var(--color-warning, #B7791F);
    color: var(--color-surface, #f8faf7);
  }
  .toast-warning .toast-progress-bar {
    background-color: var(--color-warning, #B7791F);
  }
  
  .toast-info .toast-icon-circle {
    background-color: var(--color-info, #3E6A7A);
    color: var(--color-surface, #f8faf7);
  }
  .toast-info .toast-progress-bar {
    background-color: var(--color-info, #3E6A7A);
  }
  
  /* Icon Circle Badge */
  .toast-icon-circle {
    width: 24px;
    height: 24px;
    min-width: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    margin-top: 1px;
  }
  
  /* Message Content */
  .toast-message {
    flex: 1;
    min-width: 0;
  }
  
  .toast-title {
    margin: 0 0 3px 0;
    font-size: 14px;
    font-weight: 600;
    color: var(--color-text, #16251b);
    line-height: 1.35;
    letter-spacing: -0.01em;
  }
  
  .toast-text {
    margin: 0;
    font-size: 13px;
    color: var(--color-text-muted, #546958);
    line-height: 1.45;
    word-break: break-word;
  }
  
  /* Close Button */
  .toast-close {
    background: transparent;
    border: none;
    color: var(--color-text-muted, #546958);
    cursor: pointer;
    padding: 3px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    transition: color 0.15s ease;
    flex-shrink: 0;
    margin-top: -1px;
    margin-right: -4px;
  }
  .toast-close:hover {
    color: var(--color-text, #16251b);
  }
  
  /* Progress Countdown Bar */
  .toast-progress-track {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 3px;
    background: var(--color-border, #cfdcc8);
    overflow: hidden;
  }
  
  .toast-progress-bar {
    height: 100%;
    width: 100%;
    transform-origin: left;
    animation: toastCountdown 5s linear forwards;
  }
  
  .toast-notification:hover .toast-progress-bar {
    animation-play-state: paused;
  }
  
  @keyframes toastCountdown {
    from {
      width: 100%;
    }
    to {
      width: 0%;
    }
  }
  
  /* Slide Animations */
  @keyframes toastSlideIn {
    from {
      transform: translateX(110%);
      opacity: 0;
    }
    to {
      transform: translateX(0);
      opacity: 1;
    }
  }
  
  @keyframes toastSlideOut {
    from {
      transform: translateX(0);
      opacity: 1;
    }
    to {
      transform: translateX(110%);
      opacity: 0;
    }
  }
  
  .toast-notification.hiding {
    animation: toastSlideOut 0.25s cubic-bezier(0.4, 0, 1, 1) forwards;
  }
  
  /* Responsive */
  @media (max-width: 640px) {
    .toast-notification {
      top: 16px;
      right: 16px;
      left: 16px;
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
  
  var DURATION = 5000;
  
  function initToastInstance(toastEl) {
    if (!toastEl) return;
    
    var startTime = Date.now();
    var remainingTime = DURATION;
    var dismissTimer = null;
    var isClosed = false;

    function closeNow() {
      if (isClosed) return;
      isClosed = true;
      if (dismissTimer) clearTimeout(dismissTimer);
      
      toastEl.classList.add('hiding');
      setTimeout(function() {
        if (toastEl && toastEl.parentNode) {
          toastEl.parentNode.removeChild(toastEl);
        }
      }, 280);
    }

    toastEl._closeNow = closeNow;
    window.closeToastNow = closeNow;

    function startTimer() {
      startTime = Date.now();
      dismissTimer = setTimeout(function() {
        closeNow();
      }, remainingTime);
    }

    function pauseTimer() {
      if (dismissTimer) {
        clearTimeout(dismissTimer);
        dismissTimer = null;
      }
      remainingTime -= (Date.now() - startTime);
      if (remainingTime < 0) remainingTime = 0;
    }

    // Start auto-dismiss countdown
    startTimer();

    // Pause on hover, resume on mouseleave
    toastEl.addEventListener('mouseenter', pauseTimer);
    toastEl.addEventListener('mouseleave', function() {
      if (!isClosed && remainingTime > 0) {
        startTimer();
      }
    });

    // Close button inside toastEl
    var closeBtn = toastEl.querySelector('.toast-close');
    if (closeBtn) {
      closeBtn.onclick = function(e) {
        e.preventDefault();
        closeNow();
      };
    }
  }

  // Handle server-rendered toast on page load
  var initialToast = document.getElementById('toast-notification');
  if (initialToast) {
    initToastInstance(initialToast);
  }

  // Global showToast API
  window.showToast = function(message, type, title) {
    type = type || 'success';
    var titles = {
      success: 'Berhasil',
      error: 'Terjadi Kesalahan',
      warning: 'Perhatian',
      info: 'Informasi'
    };
    var displayTitle = title || titles[type] || 'Notifikasi';

    var icons = {
      success: '<polyline points="20 6 9 17 4 12"></polyline>',
      error: '<line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line>',
      warning: '<line x1="12" y1="8" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line>',
      info: '<line x1="12" y1="16" x2="12" y2="11"></line><line x1="12" y1="7" x2="12.01" y2="7"></line>'
    };
    var iconSvg = icons[type] || icons.success;

    // Remove any existing toast
    var existing = document.getElementById('toast-notification');
    if (existing) {
      existing.remove();
    }

    var toast = document.createElement('div');
    toast.id = 'toast-notification';
    toast.className = 'toast-notification';
    toast.innerHTML = 
      '<div class="toast-content toast-' + type + '">' +
        '<div class="toast-icon-circle">' +
          '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">' +
            iconSvg +
          '</svg>' +
        '</div>' +
        '<div class="toast-message">' +
          '<h6 class="toast-title">' + displayTitle + '</h6>' +
          '<p class="toast-text">' + message + '</p>' +
        '</div>' +
        '<button class="toast-close" type="button" aria-label="Tutup notifikasi">' +
          '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' +
            '<line x1="18" y1="6" x2="6" y2="18"></line>' +
            '<line x1="6" y1="6" x2="18" y2="18"></line>' +
          '</svg>' +
        '</button>' +
        '<div class="toast-progress-track">' +
          '<div id="toast-progress-bar" class="toast-progress-bar"></div>' +
        '</div>' +
      '</div>';

    document.body.appendChild(toast);
    initToastInstance(toast);
  };

  // Check for pending toast in sessionStorage (e.g. after import finish & reload)
  function checkPendingToast() {
    try {
      var pendingToast = sessionStorage.getItem('pending_toast');
      if (pendingToast) {
        sessionStorage.removeItem('pending_toast');
        var parsed = JSON.parse(pendingToast);
        if (parsed && parsed.message) {
          setTimeout(function() {
            window.showToast(parsed.message, parsed.type || 'success', parsed.title || null);
          }, 150);
        }
      }
    } catch (e) {
      console.error('Error restoring pending toast:', e);
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', checkPendingToast);
  } else {
    checkPendingToast();
  }
})();
</script>
