<script>
  (function(){
    if (window.initSignaturePadField) {
      return;
    }

    window.initSignaturePadField = function(options) {
      const settings = Object.assign({
        methodName: 'signature_method',
        defaultMethod: 'draw',
        requireSignature: false,
        requiredUploadMessage: 'Silakan upload file tanda tangan elektronik terlebih dahulu.',
        requiredDrawMessage: 'Silakan gambar tanda tangan terlebih dahulu.',
        processErrorMessage: 'Gagal memproses gambar tanda tangan. Silakan coba lagi.',
        drawSectionId: 'draw-signature-section',
        uploadSectionId: 'upload-signature-section',
        signatureErrorId: 'signature-method-error',
        signatureCanvasId: 'signature-canvas',
        clearButtonId: 'clear-signature',
        ttdInputId: 'ttd_path',
        uploadAreaId: 'upload-area',
        filePreviewId: 'file-preview',
        previewImgId: 'preview-img',
        previewNameId: 'preview-name',
        previewSizeId: 'preview-size',
        removeFileButtonId: 'remove-file'
      }, options || {});

      const methodSelector = 'input[name="' + settings.methodName + '"]';
      const methodInputs = document.querySelectorAll(methodSelector);
      const drawSection = document.getElementById(settings.drawSectionId);
      const uploadSection = document.getElementById(settings.uploadSectionId);
      const signatureError = document.getElementById(settings.signatureErrorId);
      const signatureCanvas = document.getElementById(settings.signatureCanvasId);
      const clearSignatureBtn = document.getElementById(settings.clearButtonId);
      const ttdInput = document.getElementById(settings.ttdInputId);
      const uploadArea = document.getElementById(settings.uploadAreaId);
      const filePreview = document.getElementById(settings.filePreviewId);
      const previewImg = document.getElementById(settings.previewImgId);
      const previewName = document.getElementById(settings.previewNameId);
      const previewSize = document.getElementById(settings.previewSizeId);
      const removeFileBtn = document.getElementById(settings.removeFileButtonId);

      if (!ttdInput || !uploadArea || !filePreview || !previewImg || !previewName || !previewSize) {
        return {
          prepareForSubmit: async function() { return true; },
          clearError: function() {}
        };
      }

      let isDrawing = false;
      let hasSignature = false;
      let canvasCtx = null;
      let canvasRatio = 1;
      const initialUploadHasFile = uploadArea.classList.contains('has-file');

      function shouldRequireSignature() {
        if (typeof settings.requireSignature === 'function') {
          return !!settings.requireSignature();
        }
        return !!settings.requireSignature;
      }

      function setSignatureError(message) {
        if (!signatureError) return;
        signatureError.textContent = message;
        signatureError.style.display = message ? 'block' : 'none';
      }

      function getSelectedSignatureMethod() {
        const checked = document.querySelector(methodSelector + ':checked');
        return checked ? checked.value : settings.defaultMethod;
      }

      function refreshUploadAreaState() {
        const hasNewFile = ttdInput.files && ttdInput.files.length > 0;
        if (hasNewFile || (initialUploadHasFile && !hasNewFile)) {
          uploadArea.classList.add('has-file');
        } else {
          uploadArea.classList.remove('has-file');
        }
      }

      function applySignatureMethodState() {
        const method = getSelectedSignatureMethod();
        const showDraw = method === 'draw';

        if (drawSection) {
          drawSection.style.display = showDraw ? 'block' : 'none';
        }
        if (uploadSection) {
          uploadSection.style.display = showDraw ? 'none' : 'block';
        }

        if (showDraw) {
          filePreview.classList.remove('show');
          uploadArea.classList.remove('has-file');
        } else {
          refreshUploadAreaState();
        }

        setSignatureError('');
        // If the draw section becomes visible after being hidden (e.g. shown by other scripts),
        // ensure canvas is properly sized and initialized.
        if (showDraw) {
          ensureCanvasSetupWhenVisible();
        }
      }

      function ensureCanvasSetupWhenVisible() {
        if (!signatureCanvas) return;
        // Try to initialize the canvas when it becomes visible (handles cases where the
        // containing element was display:none during initial setup). Poll briefly.
        let attempts = 0;
        const maxAttempts = 20; // ~3 seconds with 150ms interval
        const iv = setInterval(function() {
          attempts++;
          const rect = signatureCanvas.getBoundingClientRect();
          if (rect.width > 0 && rect.height > 0) {
            setupCanvas();
            clearInterval(iv);
            return;
          }
          if (attempts >= maxAttempts) {
            clearInterval(iv);
          }
        }, 150);
      }

      function setupCanvas() {
        if (!signatureCanvas) return;
        const rect = signatureCanvas.getBoundingClientRect();
        const dpr = window.devicePixelRatio || 1;
        canvasRatio = dpr;

        signatureCanvas.width = Math.max(1, Math.floor(rect.width * dpr));
        signatureCanvas.height = Math.max(1, Math.floor(rect.height * dpr));

        canvasCtx = signatureCanvas.getContext('2d');
        canvasCtx.setTransform(1, 0, 0, 1, 0, 0);
        canvasCtx.scale(dpr, dpr);
        canvasCtx.lineJoin = 'round';
        canvasCtx.lineCap = 'round';
        canvasCtx.lineWidth = 2.4;
        canvasCtx.strokeStyle = '#16251b';
      }

      function clearCanvasSignature() {
        if (!canvasCtx || !signatureCanvas) return;
        canvasCtx.clearRect(0, 0, signatureCanvas.width / canvasRatio, signatureCanvas.height / canvasRatio);
        hasSignature = false;
      }

      function getPoint(e) {
        const rect = signatureCanvas.getBoundingClientRect();
        return {
          x: e.clientX - rect.left,
          y: e.clientY - rect.top
        };
      }

      function startDraw(e) {
        if (!canvasCtx || getSelectedSignatureMethod() !== 'draw') return;
        e.preventDefault();
        const p = getPoint(e);
        isDrawing = true;
        hasSignature = true;
        canvasCtx.beginPath();
        canvasCtx.moveTo(p.x, p.y);
        setSignatureError('');
      }

      function drawMove(e) {
        if (!isDrawing || !canvasCtx || getSelectedSignatureMethod() !== 'draw') return;
        e.preventDefault();
        const p = getPoint(e);
        canvasCtx.lineTo(p.x, p.y);
        canvasCtx.stroke();
      }

      function endDraw() {
        if (!isDrawing || !canvasCtx) return;
        isDrawing = false;
        canvasCtx.closePath();
      }

      function formatFileSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
      }

      function handleFileSelect(files) {
        if (!(files && files[0])) return;

        const file = files[0];
        const validTypes = ['image/png', 'image/jpeg', 'image/jpg'];
        if (!validTypes.includes(file.type)) {
          alert('Format file tidak didukung! Gunakan PNG atau JPG.');
          ttdInput.value = '';
          return;
        }

        if (file.size > 5 * 1024 * 1024) {
          alert('Ukuran file terlalu besar! Maksimal 5MB.');
          ttdInput.value = '';
          return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
          previewImg.src = e.target.result;
        };
        reader.readAsDataURL(file);

        previewName.textContent = file.name;
        previewSize.textContent = formatFileSize(file.size);
        uploadArea.classList.add('has-file');
        filePreview.classList.add('show');

        if (window.feather && typeof window.feather.replace === 'function') {
          window.feather.replace();
        }
      }

      async function prepareForSubmit() {
        const method = getSelectedSignatureMethod();
        const required = shouldRequireSignature();

        setSignatureError('');

        if (method === 'upload') {
          const hasFile = ttdInput.files && ttdInput.files.length > 0;
          if (!hasFile && required) {
            setSignatureError(settings.requiredUploadMessage);
            return false;
          }
          return true;
        }

        if (!hasSignature) {
          if (required) {
            setSignatureError(settings.requiredDrawMessage);
            return false;
          }
          return true;
        }

        const blob = await new Promise((resolve) => signatureCanvas.toBlob(resolve, 'image/png'));
        if (!blob) {
          setSignatureError(settings.processErrorMessage);
          return false;
        }

        const filename = 'ttd_drawn_' + Date.now() + '.png';
        const file = new File([blob], filename, { type: 'image/png' });
        const transfer = new DataTransfer();
        transfer.items.add(file);
        ttdInput.files = transfer.files;

        return true;
      }

      // Sync ttd_type hidden input setiap kali pilihan metode berubah
      function syncTtdType() {
        const method = getSelectedSignatureMethod();
        const ttdTypeInput = document.getElementById('ttd_type_input');
        if (ttdTypeInput) {
          ttdTypeInput.value = (method === 'upload') ? 'elektronik' : 'gambar';
        }
      }

      methodInputs.forEach(function(input) {
        input.addEventListener('change', applySignatureMethodState);
        input.addEventListener('change', syncTtdType);
      });

      // Set nilai awal saat halaman dibuka
      syncTtdType();

      ttdInput.addEventListener('change', function() {
        handleFileSelect(this.files);
        setSignatureError('');
      });

      uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.style.borderColor = '#1f3a2a';
        this.style.background = '#f0f4ff';
      });

      uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.style.borderColor = '#d1d5db';
        this.style.background = '#fff';
      });

      uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        this.style.borderColor = '#d1d5db';
        this.style.background = '#fff';
        const files = e.dataTransfer.files;
        if (files.length > 0) {
          ttdInput.files = files;
          handleFileSelect(files);
        }
      });

      if (removeFileBtn) {
        removeFileBtn.addEventListener('click', function() {
          ttdInput.value = '';
          previewImg.src = '';
          filePreview.classList.remove('show');
          refreshUploadAreaState();
          if (window.feather && typeof window.feather.replace === 'function') {
            window.feather.replace();
          }
        });
      }

      if (signatureCanvas) {
        setupCanvas();
        window.addEventListener('resize', setupCanvas);
        signatureCanvas.addEventListener('pointerdown', startDraw);
        signatureCanvas.addEventListener('pointermove', drawMove);
        window.addEventListener('pointerup', endDraw);
        window.addEventListener('pointercancel', endDraw);
      }

      if (clearSignatureBtn) {
        clearSignatureBtn.addEventListener('click', function() {
          clearCanvasSignature();
        });
      }

      applySignatureMethodState();

      return {
        prepareForSubmit: prepareForSubmit,
        clearError: function() {
          setSignatureError('');
        },
        resetDraw: clearCanvasSignature
        ,
        ensureSetup: ensureCanvasSetupWhenVisible
      };
    };
  })();
</script>
