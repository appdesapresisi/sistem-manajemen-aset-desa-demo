/**
 * SIMA Standard Searchable Dropdown Engine
 * Standar Acuan Visual & Interaksi: Dropdown Pusat Ekspor
 */
(function () {
  'use strict';

  function shouldExclude(select) {
    if (!select || select.dataset.simaEnhanced === 'true' || select.dataset.noEnhance === 'true') {
      return true;
    }
    const id = (select.id || '').toLowerCase();
    const name = (select.name || '').toLowerCase();
    const cls = (select.className || '').toLowerCase();

    // 1. Exclude: dropdown kode barang
    if (id.includes('kode_barang') || name.includes('kode_barang') || id.includes('kodebarang') || name.includes('kodebarang')) {
      return true;
    }

    // 2. Exclude: yang khusus untuk tanggal, bulan, tahun
    const dateKeywords = ['tanggal', 'tgl', 'bulan', 'tahun', 'year', 'month', 'date'];
    for (let i = 0; i < dateKeywords.length; i++) {
      if (id.includes(dateKeywords[i]) || name.includes(dateKeywords[i])) {
        return true;
      }
    }

    // 3. Exclude: perPage pagination
    if (id === 'perpageselect' || cls.includes('per-page-select') || name === 'per_page') {
      return true;
    }

    // 4. Exclude: flatpickr or hidden
    if (cls.includes('flatpickr') || select.type === 'hidden' || select.offsetParent === null && select.hasAttribute('hidden')) {
      return true;
    }

    // 5. Exclude if already inside an enhanced dropdown
    if (select.closest('.searchable-dropdown')) {
      return true;
    }

    return false;
  }

  function parseOptionText(opt, select) {
    if (select) {
      const selectName = (select.name || '').toLowerCase();
      const selectId = (select.id || '').toLowerCase();
      if (selectName.includes('kondisi') || selectId.includes('kondisi')) {
        // Data kondisi selalu to the point tanpa subtitle deskripsi (B / KB / RB)
        const cleaned = opt.text.replace(/\s*\([A-Za-z0-9\s]+\)\s*$/, '').trim();
        return { title: cleaned, desc: '' };
      }
    }
    if (opt.dataset.desc) {
      return { title: opt.text.trim(), desc: opt.dataset.desc.trim() };
    }
    const raw = opt.text.trim();
    return { title: raw, desc: '' };
  }

  function enhanceSelect(select) {
    if (shouldExclude(select)) return;
    select.dataset.simaEnhanced = 'true';

    // Sembunyikan select bawaan tetapi tetap pertahankan form value & validity
    select.style.setProperty('display', 'none', 'important');

    // Container dropdown
    const container = document.createElement('div');
    container.className = 'searchable-dropdown sima-enhanced-dropdown';
    if (select.id) container.id = select.id + '-container';

    // Tombol trigger
    const trigger = document.createElement('button');
    trigger.type = 'button';
    trigger.className = 'dropdown-trigger';
    if (select.id) trigger.id = select.id + '-trigger';

    const labelSpan = document.createElement('span');
    labelSpan.className = 'dropdown-selected-label';

    const caretSvg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    caretSvg.setAttribute('class', 'dropdown-trigger-caret');
    caretSvg.setAttribute('width', '16');
    caretSvg.setAttribute('height', '16');
    caretSvg.setAttribute('viewBox', '0 0 24 24');
    caretSvg.setAttribute('fill', 'none');
    caretSvg.setAttribute('stroke', '#5E6C5A');
    caretSvg.setAttribute('stroke-width', '2');
    caretSvg.setAttribute('stroke-linecap', 'round');
    caretSvg.setAttribute('stroke-linejoin', 'round');
    const polyline = document.createElementNS('http://www.w3.org/2000/svg', 'polyline');
    polyline.setAttribute('points', '6 9 12 15 18 9');
    caretSvg.appendChild(polyline);

    trigger.appendChild(labelSpan);
    trigger.appendChild(caretSvg);

    // Panel menu dropdown (Acuan: Pusat Ekspor)
    const menu = document.createElement('div');
    menu.className = 'dropdown-menu-panel';
    if (select.id) menu.id = select.id + '-menu';

    // Wrapper pencarian
    const searchWrap = document.createElement('div');
    searchWrap.className = 'dropdown-search-wrapper';

    const searchIconSvg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    searchIconSvg.setAttribute('width', '14');
    searchIconSvg.setAttribute('height', '14');
    searchIconSvg.setAttribute('viewBox', '0 0 24 24');
    searchIconSvg.setAttribute('fill', 'none');
    searchIconSvg.setAttribute('stroke', '#5E6C5A');
    searchIconSvg.setAttribute('stroke-width', '2');
    searchIconSvg.setAttribute('stroke-linecap', 'round');
    searchIconSvg.setAttribute('stroke-linejoin', 'round');
    const scircle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
    scircle.setAttribute('cx', '11');
    scircle.setAttribute('cy', '11');
    scircle.setAttribute('r', '8');
    const sline = document.createElementNS('http://www.w3.org/2000/svg', 'line');
    sline.setAttribute('x1', '21');
    sline.setAttribute('y1', '21');
    sline.setAttribute('x2', '16.65');
    sline.setAttribute('y2', '16.65');
    searchIconSvg.appendChild(scircle);
    searchIconSvg.appendChild(sline);

    const searchInput = document.createElement('input');
    searchInput.type = 'text';
    searchInput.className = 'dropdown-search-input';
    searchInput.placeholder = 'Cari...';
    searchInput.autocomplete = 'off';

    searchWrap.appendChild(searchIconSvg);
    searchWrap.appendChild(searchInput);

    // List opsi
    const optionsList = document.createElement('ul');
    optionsList.className = 'dropdown-options-list';

    menu.appendChild(searchWrap);
    menu.appendChild(optionsList);

    container.appendChild(trigger);
    container.appendChild(menu);

    select.parentNode.insertBefore(container, select.nextSibling);

    function isPlaceholderOption(optVal, text) {
      if (!optVal || optVal === 'all' || optVal === 'semua') return true;
      const t = (text || '').trim();
      return t.startsWith('Semua ') || t === 'Semua' || t.startsWith('--') || t.startsWith('Pilih ') || t === 'Pilih';
    }

    function buildOptions() {
      optionsList.innerHTML = '';
      const options = Array.from(select.options);
      let selectedText = '';
      let isPlaceholder = false;

      // Jika jumlah opsi <= 5, searchbox bisa tetap ada atau opsional, tapi tetap disediakan agar konsisten
      options.forEach(function (opt) {
        if (opt.disabled && !opt.value) {
          if (opt.selected || !select.value) {
            selectedText = opt.text.trim();
            isPlaceholder = true;
          }
          return;
        }

        const li = document.createElement('li');
        li.className = 'dropdown-option-item';
        li.dataset.value = opt.value;
        li.dataset.search = (opt.text || '').toLowerCase();

        const parsed = parseOptionText(opt, select);

        const textDiv = document.createElement('div');
        textDiv.className = 'dropdown-option-text';

        const nameDiv = document.createElement('div');
        nameDiv.className = 'dropdown-option-name';
        nameDiv.textContent = parsed.title;
        textDiv.appendChild(nameDiv);

        if (parsed.desc) {
          const descDiv = document.createElement('div');
          descDiv.className = 'dropdown-option-desc';
          descDiv.textContent = parsed.desc;
          textDiv.appendChild(descDiv);
        }

        li.appendChild(textDiv);

        const isCurrentlySelected = opt.value === select.value || (opt.selected && !selectedText);
        if (isCurrentlySelected) {
          li.classList.add('selected');
          selectedText = parsed.title;
          isPlaceholder = isPlaceholderOption(opt.value, parsed.title);
        }

        li.addEventListener('click', function (e) {
          e.stopPropagation();
          optionsList.querySelectorAll('.dropdown-option-item').forEach(function (el) {
            el.classList.remove('selected');
          });
          li.classList.add('selected');

          select.value = opt.value;
          select.dispatchEvent(new Event('change', { bubbles: true }));
          select.dispatchEvent(new Event('input', { bubbles: true }));

          const isMuted = isPlaceholderOption(opt.value, parsed.title);
          updateLabel(parsed.title, isMuted);
          closeDropdown();
        });

        optionsList.appendChild(li);
      });

      if (!selectedText && options.length > 0) {
        const curOpt = select.options[select.selectedIndex];
        if (curOpt) {
          selectedText = parseOptionText(curOpt, select).title;
          isPlaceholder = isPlaceholderOption(curOpt.value, selectedText);
        } else {
          selectedText = 'Pilih';
          isPlaceholder = true;
        }
      }

      updateLabel(selectedText || 'Pilih', isPlaceholder);
    }

    function updateLabel(text, isMuted) {
      const isMutedState = isMuted || isPlaceholderOption(select.value, text);
      if (isMutedState) {
        labelSpan.style.color = 'var(--color-text-muted, #5E6C5A)';
        labelSpan.style.fontSize = '13px';
        labelSpan.style.fontWeight = '500';
      } else {
        labelSpan.style.color = 'var(--color-text, #1F3320)';
        labelSpan.style.fontSize = '13.5px';
        labelSpan.style.fontWeight = '600';
      }
      labelSpan.textContent = text;
    }

    function closeDropdown() {
      menu.classList.remove('show');
      container.classList.remove('open');
    }

    function openDropdown() {
      document.querySelectorAll('.dropdown-menu-panel.show').forEach(function (m) {
        if (m !== menu) m.classList.remove('show');
      });
      document.querySelectorAll('.searchable-dropdown.open').forEach(function (c) {
        if (c !== container) c.classList.remove('open');
      });

      menu.classList.add('show');
      container.classList.add('open');
      searchInput.value = '';
      filterOptions('');
      setTimeout(function () {
        searchInput.focus();
      }, 40);
    }

    function filterOptions(query) {
      const q = (query || '').toLowerCase().trim();
      let hasMatch = false;
      optionsList.querySelectorAll('.dropdown-option-item').forEach(function (item) {
        const s = item.dataset.search || '';
        const match = !q || s.indexOf(q) !== -1;
        item.style.display = match ? 'flex' : 'none';
        if (match) hasMatch = true;
      });

      let emptyMsg = optionsList.querySelector('.dropdown-empty-search');
      if (!hasMatch) {
        if (!emptyMsg) {
          emptyMsg = document.createElement('li');
          emptyMsg.className = 'dropdown-empty-search';
          emptyMsg.style.cssText = 'padding:16px;text-align:center;color:#9ca3af;font-size:12px;list-style:none;';
          emptyMsg.textContent = 'Tidak ada pilihan yang cocok.';
          optionsList.appendChild(emptyMsg);
        }
      } else if (emptyMsg) {
        emptyMsg.remove();
      }
    }

    trigger.addEventListener('click', function (e) {
      e.stopPropagation();
      if (menu.classList.contains('show')) {
        closeDropdown();
      } else {
        openDropdown();
      }
    });

    searchInput.addEventListener('input', function () {
      filterOptions(this.value);
    });

    // Close saat klik di luar
    document.addEventListener('click', function (e) {
      if (!container.contains(e.target)) {
        closeDropdown();
      }
    });

    // Sinkronisasi jika select value diubah via JS
    select.addEventListener('change', function () {
      const curOpt = select.options[select.selectedIndex];
      if (curOpt) {
        optionsList.querySelectorAll('.dropdown-option-item').forEach(function (item) {
          if (item.dataset.value === curOpt.value) {
            item.classList.add('selected');
          } else {
            item.classList.remove('selected');
          }
        });
        const parsedTitle = parseOptionText(curOpt, select).title;
        const isOptPlaceholder = isPlaceholderOption(curOpt.value, parsedTitle);
        updateLabel(parsedTitle, isOptPlaceholder);
      }
    });

    buildOptions();

    // Rebuild jika anak <option> dari <select> berubah secara dinamis
    const optObserver = new MutationObserver(function () {
      buildOptions();
    });
    optObserver.observe(select, { childList: true, subtree: true });
  }

  // Fungsi upgrade global
  window.initSimaDropdowns = function () {
    document.querySelectorAll('select').forEach(enhanceSelect);
  };

  // Jalankan saat DOM siap
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', window.initSimaDropdowns);
  } else {
    window.initSimaDropdowns();
  }

  // Observer untuk menangkap field select yang di-generate dinamis
  var bodyObserver = new MutationObserver(function (mutations) {
    var hasNewSelect = false;
    for (var i = 0; i < mutations.length; i++) {
      var added = mutations[i].addedNodes;
      for (var j = 0; j < added.length; j++) {
        var node = added[j];
        if (node.nodeType === 1) {
          if (node.tagName === 'SELECT' || (node.querySelector && node.querySelector('select'))) {
            hasNewSelect = true;
            break;
          }
        }
      }
      if (hasNewSelect) break;
    }
    if (hasNewSelect) {
      window.initSimaDropdowns();
    }
  });

  if (document.body) {
    bodyObserver.observe(document.body, { childList: true, subtree: true });
  } else {
    document.addEventListener('DOMContentLoaded', function () {
      bodyObserver.observe(document.body, { childList: true, subtree: true });
    });
  }
})();

/**
 * SIMA Global Reset Filter Animation Engine
 * Menangani animasi putaran 360 derajat counter-clockwise pada icon tombol Reset Filter di seluruh sistem
 */
(function () {
  'use strict';

  function triggerResetFilterAnimation(btn) {
    if (!btn) return;
    const icon = btn.querySelector('svg, i');
    if (!icon) return;

    // Reset class agar animasi dapat dipicu kembali jika diklik beruntun
    icon.classList.remove('is-spinning-reset');
    btn.classList.remove('is-spinning');
    btn.classList.remove('is-animating');

    // Force DOM reflow untuk me-restart CSS keyframe animation
    void icon.offsetWidth;

    icon.classList.add('is-spinning-reset');
    btn.classList.add('is-spinning');
    btn.classList.add('is-animating');

    setTimeout(function () {
      icon.classList.remove('is-spinning-reset');
      btn.classList.remove('is-spinning');
      btn.classList.remove('is-animating');
    }, 650);
  }

  // Delegasi event klik pada seluruh tombol reset filter di seluruh sistem
  // Menggunakan capture phase agar animasi terpicu seketika saat tombol ditekan
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('button[id*="reset-filter"], .btn-reset-filter, button[onclick*="resetFilter"], button[onclick*="resetActivityFilter"], button[title*="Reset Filter"], button[title*="Reset semua filter"]');
    if (btn) {
      triggerResetFilterAnimation(btn);
    }
  }, true);
})();

/**
 * SIMA Export Dropdowns Engine
 * Membuka/menutup menu format Ekspor Data (Excel & PDF) secara independen dan instan.
 */
window.toggleExportMenu = function(btn, e) {
  if (e) {
    e.preventDefault();
    e.stopPropagation();
  }
  const wrapper = btn ? btn.closest('.sima-dropdown-wrapper') : null;
  if (!wrapper) return;
  const menu = wrapper.querySelector('.export-dropdown-menu');
  if (!menu) return;

  const isVisible = menu.style.display === 'block';

  // Tutup dropdown ekspor lainnya
  document.querySelectorAll('.export-dropdown-menu').forEach(function(m) {
    m.style.display = 'none';
  });

  if (!isVisible) {
    menu.style.display = 'block';
    
    // Auto-adjust positioning so it NEVER clips off screen (left or right)
    const rect = menu.getBoundingClientRect();
    if (rect.left < 8) {
      menu.style.left = '0';
      menu.style.right = 'auto';
    } else if (rect.right > window.innerWidth - 8) {
      menu.style.right = '0';
      menu.style.left = 'auto';
    }
  }
};

document.addEventListener('click', function(e) {
  if (!e.target.closest('.sima-dropdown-wrapper')) {
    document.querySelectorAll('.export-dropdown-menu').forEach(function(m) {
      m.style.display = 'none';
    });
  }
});



