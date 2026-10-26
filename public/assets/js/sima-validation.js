/**
 * SIMA Universal Form Validation Standard (Slick UX Pattern)
 * Mengacu pada standar resmi Design.md & .agents/rules/form-inputs.md
 * - Border merah (#E53935)
 * - Label / judul merah (#D9383A)
 * - Ikon SVG segitiga peringatan + teks merah
 * - Instant Typing Recovery: saat user mulai mengetik / memilih opsi, semua status error seketika hilang
 * - Empty submit handling: klik submit tanpa isi langsung memicu status error visual & auto-focus
 */
(function() {
  'use strict';

  // SVG Segitiga Peringatan Standar (Preserved icon style)
  const SVG_WARNING_ICON = '<svg class="sima-error-icon" width="14" height="14" viewBox="0 0 16 16" style="flex-shrink:0;margin-top:2px;" aria-hidden="true">' +
    '<path fill="#D9383A" d="M7.134 1.603a1 1 0 0 1 1.732 0l6.062 10.5A1 1 0 0 1 14.062 13.6H1.938a1 1 0 0 1-.866-1.497l6.062-10.5z"/>' +
    '<path stroke="#FFFFFF" stroke-width="1.3" stroke-linecap="round" d="M8 5.5v3.2M8 11.2h.01"/>' +
    '</svg>';

  // Mendapatkan elemen label terkait input
  function getFieldLabelElement(field) {
    if (!field) return null;
    if (field.id) {
      try {
        const explicitLabel = document.querySelector(`label[for="${CSS.escape(field.id)}"]`);
        if (explicitLabel) return explicitLabel;
      } catch(e) {}
    }
    const wrapper = field.closest('.form-group, .kezak-field, .mb-3, .form-section-item, .col-span-12, .col-span-6');
    if (wrapper) {
      return wrapper.querySelector('.form-label, .kezak-label, label');
    }
    return null;
  }

  // Mendapatkan teks nama field yang bersih untuk pesan error
  function getFieldLabelText(field) {
    const labelEl = getFieldLabelElement(field);
    if (!labelEl) {
      return field.getAttribute('placeholder') || field.name || 'Kolom ini';
    }
    // Salin teks tanpa asterisk required
    let clone = labelEl.cloneNode(true);
    clone.querySelectorAll('.required, .req, .optional, span, i, svg').forEach(el => el.remove());
    let text = clone.textContent.trim().replace(/[*:]/g, '').trim();
    return text || 'Kolom ini';
  }

  // Menampilkan state error pada field
  window.simaShowFieldError = function(field, customMessage) {
    if (!field || field.type === 'hidden') return;

    // 1. Tambah class error pada input
    field.classList.add('is-invalid', 'error');

    // 2. Hubungkan ke searchable select / custom dropdown jika ada
    const wrapper = field.closest('.form-group, .kezak-field, .mb-3, .form-section-item, .col-span-12, .col-span-6') || field.parentElement;
    if (wrapper) {
      wrapper.classList.add('has-error');
      
      const dropdownTrigger = wrapper.querySelector('.dropdown-trigger, .ss-trigger');
      if (dropdownTrigger) {
        dropdownTrigger.classList.add('is-invalid', 'error');
      }

      // 3. Ubah warna label menjadi merah
      const labelEl = getFieldLabelElement(field);
      if (labelEl) {
        labelEl.classList.add('error', 'is-invalid');
      }

      // 4. Warning Text di bawah field
      let errorContainer = wrapper.querySelector('.invalid-feedback-custom, .error-text, .kezak-field-error, .invalid-feedback');
      
      const labelText = getFieldLabelText(field);
      let errorMsg = customMessage || (field.validity && field.validity.valueMissing ? `${labelText} wajib diisi.` : (field.validationMessage || `${labelText} tidak valid.`));
      
      if (!errorContainer) {
        // Buat elemen warning jika belum ada di template
        errorContainer = document.createElement('div');
        errorContainer.className = 'invalid-feedback-custom sima-dynamic-feedback has-svg-icon';
        errorContainer.style.display = 'flex';
        errorContainer.innerHTML = SVG_WARNING_ICON + `<span>${errorMsg}</span>`;
        
        // Sisipkan setelah input atau setelah dropdown container secara aman
        const insertAfterTarget = wrapper.querySelector('.searchable-dropdown, .searchable-select, .password-wrapper, .kezak-input-box') || field;
        if (insertAfterTarget && typeof insertAfterTarget.after === 'function') {
          insertAfterTarget.after(errorContainer);
        } else if (insertAfterTarget && insertAfterTarget.parentNode) {
          insertAfterTarget.parentNode.insertBefore(errorContainer, insertAfterTarget.nextSibling);
        } else {
          wrapper.appendChild(errorContainer);
        }
      } else {
        errorContainer.style.display = 'flex';
        errorContainer.classList.add('show', 'has-svg-icon');
        
        // Anti Double Icon: Cek apakah errorContainer sudah memiliki icon (svg atau i)
        const existingIcon = errorContainer.querySelector('svg, i, .sima-error-icon');
        const textSpan = errorContainer.querySelector('span, .kezak-error-text');
        
        if (existingIcon && textSpan) {
          textSpan.textContent = errorMsg;
        } else if (existingIcon && !textSpan) {
          Array.from(errorContainer.childNodes).forEach(node => {
            if (node !== existingIcon && !existingIcon.contains(node)) {
              node.remove();
            }
          });
          const span = document.createElement('span');
          span.textContent = errorMsg;
          errorContainer.appendChild(span);
        } else if (textSpan) {
          if (!errorContainer.querySelector('.sima-error-icon')) {
            errorContainer.insertAdjacentHTML('afterbegin', SVG_WARNING_ICON);
          }
          textSpan.textContent = errorMsg;
        } else {
          errorContainer.innerHTML = SVG_WARNING_ICON + `<span>${errorMsg}</span>`;
        }
      }
    }
  };

  // Menghapus state error pada field (Recovery)
  window.simaClearFieldError = function(field) {
    if (!field) return;

    field.classList.remove('is-invalid', 'error');

    const wrapper = field.closest('.form-group, .kezak-field, .mb-3, .form-section-item, .col-span-12, .col-span-6') || field.parentElement;
    if (wrapper) {
      // Selalu bersihkan status error dari label field ini
      const labelEl = getFieldLabelElement(field);
      if (labelEl) {
        labelEl.classList.remove('error', 'is-invalid');
      }

      // Cek apakah masih ada field input/select/textarea lain di dalam wrapper yang invalid
      const otherInputs = wrapper.querySelectorAll('input.is-invalid, input.error, select.is-invalid, select.error, textarea.is-invalid, textarea.error, .ss-trigger.is-invalid, .dropdown-trigger.is-invalid');
      if (otherInputs.length === 0) {
        wrapper.classList.remove('has-error');
      }

      const dropdownTrigger = wrapper.querySelector('.dropdown-trigger, .ss-trigger');
      if (dropdownTrigger) {
        dropdownTrigger.classList.remove('is-invalid', 'error');
        dropdownTrigger.style.borderColor = '';
      }

      // Sembunyikan atau hapus pesan error
      const errorContainers = wrapper.querySelectorAll('.invalid-feedback-custom, .error-text, .kezak-field-error, .invalid-feedback, .text-danger');
      errorContainers.forEach(container => {
        if (container.classList.contains('sima-dynamic-feedback')) {
          container.remove();
        } else {
          container.style.display = 'none';
          container.classList.remove('show');
        }
      });
    }
  };

  // 1. Instant Typing Recovery: saat mengetik atau mengubah nilai
  document.addEventListener('input', function(e) {
    if (e.target && (e.target.matches('input, textarea') || e.target.classList.contains('form-control') || e.target.classList.contains('kezak-input'))) {
      simaClearFieldError(e.target);
    }
  }, true);

  document.addEventListener('change', function(e) {
    if (e.target && (e.target.matches('select, input[type="file"], input[type="checkbox"], input[type="radio"]') || e.target.classList.contains('form-select'))) {
      simaClearFieldError(e.target);
    }
  }, true);

  // Helper Universal: Menemukan elemen paling atas (topmost) di layar berdasarkan koordinat vertikal dokumen
  window.simaFindTopmostElement = function(elements) {
    if (!elements || elements.length === 0) return null;
    const list = Array.from(elements).filter(el => el && typeof el.getBoundingClientRect === 'function');
    if (list.length === 0) return null;
    list.sort((a, b) => {
      const topA = a.getBoundingClientRect().top + window.scrollY;
      const topB = b.getBoundingClientRect().top + window.scrollY;
      return topA - topB;
    });
    return list[0];
  };

  // Helper Global untuk Scroll Halus & Fokus ke Elemen Invalid (Selalu Topmost Hierarchy)
  window.simaScrollToField = function(field) {
    if (!field) return;

    // Cari container/wrapper form-group terdekat agar scroll terpusat dan estetis
    let scrollTarget = field;
    if (field.offsetParent === null || (typeof field.getBoundingClientRect === 'function' && field.getBoundingClientRect().height === 0)) {
      scrollTarget = field.closest('.form-group, .kezak-field, .mb-3, .form-section-item, .col-span-12, .col-span-6, .form-section') || field;
    } else {
      scrollTarget = field.closest('.form-group, .kezak-field, .mb-3, .form-section-item') || field;
    }

    if (scrollTarget && typeof scrollTarget.scrollIntoView === 'function') {
      scrollTarget.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    // Tentukan elemen yang akan difokuskan (input, select, textarea, trigger)
    let focusTarget = field;
    if (field.classList && field.classList.contains('form-group')) {
      focusTarget = field.querySelector('input:not([type="hidden"]), select, textarea, .ss-trigger, .dropdown-trigger, button') || field;
    }

    // Fokuskan input tanpa membatalkan smooth scroll Chromium (preventScroll: true)
    if (focusTarget && typeof focusTarget.focus === 'function' && !focusTarget.disabled) {
      try {
        focusTarget.focus({ preventScroll: true });
      } catch (err) {
        try { focusTarget.focus(); } catch (e2) {}
      }
    }
  };

  // 2. Intercept Event 'invalid' HTML5 (Suppresses ugly native browser popups & triggers smooth scroll to TOPMOST field)
  let invalidBatchElements = [];
  let invalidScrollTimeout = null;

  document.addEventListener('invalid', function(e) {
    if (e.target && typeof e.target.checkValidity === 'function') {
      // Cegah popup bubble tooltip bawaan browser
      e.preventDefault();
      
      const field = e.target;
      const labelText = getFieldLabelText(field);
      let message = `${labelText} wajib diisi.`;
      
      if (field.validity) {
        if (field.validity.valueMissing) {
          message = (field.tagName === 'SELECT' || field.classList.contains('form-select'))
            ? `${labelText} wajib dipilih.`
            : `${labelText} wajib diisi.`;
        } else if (field.validity.patternMismatch && field.title) {
          message = field.title;
        } else if (field.validity.tooShort) {
          message = `${labelText} minimal ${field.minLength} karakter.`;
        } else if (field.validity.typeMismatch && field.type === 'email') {
          message = 'Format email tidak valid.';
        } else if (field.validationMessage) {
          message = field.validationMessage;
        }
      }

      simaShowFieldError(field, message);

      if (!invalidBatchElements.includes(field)) {
        invalidBatchElements.push(field);
      }

      clearTimeout(invalidScrollTimeout);
      invalidScrollTimeout = setTimeout(function() {
        if (invalidBatchElements.length > 0) {
          const topmost = window.simaFindTopmostElement(invalidBatchElements);
          if (topmost) {
            window.simaScrollToField(topmost);
          }
          invalidBatchElements = [];
        }
      }, 40);
    }
  }, true);

  // 3. Form Validation Helper Global
  window.simaValidateForm = function(form) {
    if (!form) return true;

    const fields = Array.from(form.querySelectorAll('input:not([type="hidden"]):not([type="button"]):not([type="submit"]), textarea, select'));
    const invalidList = [];
    let isValid = true;

    for (let field of fields) {
      if (field.disabled) continue;

      // Lewati field yang berada di dalam section tersembunyi (display: none)
      const isVisible = field.offsetParent !== null || (field._flatpickr && field._flatpickr.altInput && field._flatpickr.altInput.offsetParent !== null);
      if (!isVisible) continue;

      let isFieldInvalid = false;
      let msg = '';
      const labelText = getFieldLabelText(field);

      if (typeof field.checkValidity === 'function' && !field.checkValidity()) {
        isFieldInvalid = true;
        msg = `${labelText} wajib diisi.`;
        if (field.validity && field.validity.valueMissing) {
          msg = (field.tagName === 'SELECT' || field.classList.contains('form-select'))
            ? `${labelText} wajib dipilih.`
            : `${labelText} wajib diisi.`;
        } else if (field.validity && field.validity.patternMismatch && field.title) {
          msg = field.title;
        } else if (field.validity && field.validity.tooShort) {
          msg = `${labelText} minimal ${field.minLength} karakter.`;
        } else if (field.validationMessage) {
          msg = field.validationMessage;
        }
      } else if (field.hasAttribute('required')) {
        const val = (field.value || '').trim();
        if (!val) {
          isFieldInvalid = true;
          msg = (field.tagName === 'SELECT' || field.classList.contains('form-select'))
            ? `${labelText} wajib dipilih.`
            : `${labelText} wajib diisi.`;
        }
      }

      if (isFieldInvalid) {
        isValid = false;
        simaShowFieldError(field, msg);
        invalidList.push(field);
      }
    }

    if (!isValid && invalidList.length > 0) {
      const topmost = window.simaFindTopmostElement(invalidList);
      if (topmost) {
        window.simaScrollToField(topmost);
      }
      return false;
    }

    return true;
  };

  // 4. Intercept Klik Submit Button pada fase Capture agar Scroll Halus & Penandaan Error Berjalan Seketika
  document.addEventListener('click', function(e) {
    const btn = e.target.closest('button[type="submit"], input[type="submit"]');
    if (!btn || btn.type === 'button') return;

    const form = btn.closest('form');
    if (!form || form.noValidate) return;

    // Periksa apakah form memiliki elemen yang tidak valid secara native
    if (typeof form.checkValidity === 'function' && !form.checkValidity()) {
      e.preventDefault();
      e.stopPropagation();
      e.stopImmediatePropagation();

      window.simaValidateForm(form);
      return false;
    }
  }, true);

  // 5. Perlindungan terhadap Submit Langsung
  document.addEventListener('submit', function(e) {
    const form = e.target;
    if (form && !form.noValidate && form.tagName === 'FORM') {
      if (!window.simaValidateForm(form)) {
        e.preventDefault();
        e.stopImmediatePropagation();
        return false;
      }
    }
  }, false);

  // 5. Inisialisasi awal saat halaman dimuat: jika ada class is-invalid bawaan Blade (@error)
  document.addEventListener('DOMContentLoaded', function() {
    const serverInvalidInputs = document.querySelectorAll('.is-invalid, .error');
    serverInvalidInputs.forEach(field => {
      const wrapper = field.closest('.form-group, .kezak-field, .mb-3, .form-section-item, .col-span-12, .col-span-6');
      if (wrapper) {
        wrapper.classList.add('has-error');
        const labelEl = getFieldLabelElement(field);
        if (labelEl) labelEl.classList.add('error', 'is-invalid');
        const errorContainer = wrapper.querySelector('.invalid-feedback-custom, .error-text, .kezak-field-error, .invalid-feedback');
        if (errorContainer) {
          errorContainer.style.display = 'flex';
          errorContainer.classList.add('show');
        }
      }
    });
  });

})();
