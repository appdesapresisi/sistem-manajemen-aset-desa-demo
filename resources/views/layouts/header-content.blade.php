<!-- [Topbar Shell] start -->
@php
    $authUser = auth()->user();
    $notifItems = \App\Models\Notification::where('user_id', $authUser?->id)
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();
    $notifUnread = \App\Models\Notification::where('user_id', $authUser?->id)
        ->where('is_read', false)
        ->count();
    $notifTotal = \App\Models\Notification::where('user_id', $authUser?->id)->count();
    $notifSystem = \App\Models\Notification::where('user_id', $authUser?->id)
        ->where(function($q) {
            $q->where('type', 'system')->orWhere('type', 'general')->orWhereNull('type');
        })->count();
@endphp

<!-- Left Section: Mobile Toggle & Page Context -->
<div class="topbar-left me-auto" style="display: flex; align-items: center; gap: 12px; height: 100%;">
  <div class="pc-mob-drp">
    <ul class="inline-flex" style="list-style: none; margin: 0; padding: 0; display: flex; align-items: center;">
      <!-- Menu collapse icon desktop -->
      <li class="pc-h-item pc-sidebar-collapse">
        <a href="#" class="btn-sima-icon" id="sidebar-hide" title="Sembunyikan Sidebar">
          <i data-feather="menu" style="width: 18px; height: 18px;"></i>
        </a>
      </li>
      <!-- Menu mobile popup -->
      <li class="pc-h-item pc-sidebar-popup">
        <a href="#" class="btn-sima-icon" id="mobile-collapse" title="Buka Menu">
          <i data-feather="menu" style="width: 18px; height: 18px;"></i>
        </a>
      </li>
    </ul>
  </div>
</div>

<!-- Search Box with Integrated Command Palette (Inline on Desktop, Expandable to Left on Mobile) -->
<div class="topbar-search-wrapper" id="global-search-container" style="position: relative;">
  <div class="topbar-search-box">
    <i data-feather="search" style="width: 15px; height: 15px; color: var(--color-text-muted); flex-shrink: 0;"></i>
    <input type="text" class="topbar-search-input" placeholder="Cari data aset, menu, card..." id="global-search-input" autocomplete="off" spellcheck="false">
    <span class="topbar-search-shortcut" style="background: transparent !important; border: none !important; padding: 0 !important; font-size: 11.5px; color: var(--color-text-muted, #546958); font-weight: 500; user-select: none;">Ctrl+K</span>
    <!-- Mobile Search Close Button -->
    <button type="button" id="mobile-search-close" class="mobile-search-close-btn" title="Tutup Pencarian" style="display: none; background: transparent; border: none; cursor: pointer; padding: 2px 4px; color: var(--color-text-muted); align-items: center; justify-content: center; border-radius: var(--radius-full);">
      <i data-feather="x" style="width: 14px; height: 14px;"></i>
    </button>
  </div>

  <!-- Dropdown Command Palette -->
  <div id="global-search-dropdown" class="global-search-dropdown-panel" style="display: none;">
    <div style="padding: 10px 14px; border-bottom: 1px dashed var(--color-border); display: flex; justify-content: space-between; align-items: center; font-size: 11px; color: var(--color-text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em;">
      <span id="global-search-status-text">Pencarian & Navigasi Cepat</span>
      <div class="global-search-shortcuts-hint" style="display: flex; gap: 8px; font-size: 10.5px; text-transform: none; font-weight: 500;">
        <span><kbd style="padding: 1px 4px; background: var(--color-bg); border: 1px solid var(--color-border); border-radius: 3px; font-family: inherit;">↑↓</kbd> Pilih</span>
        <span><kbd style="padding: 1px 4px; background: var(--color-bg); border: 1px solid var(--color-border); border-radius: 3px; font-family: inherit;">↵</kbd> Buka</span>
        <span><kbd style="padding: 1px 4px; background: var(--color-bg); border: 1px solid var(--color-border); border-radius: 3px; font-family: inherit;">Esc</kbd> Tutup</span>
      </div>
    </div>
    <div id="global-search-results-list" style="max-height: 380px; overflow-y: auto; padding: 6px 0;"></div>
  </div>
</div>

<!-- Right Section: Notifications & Profile -->
<div class="topbar-actions ms-auto" style="display: flex; align-items: center; gap: 10px;">

  <!-- Mobile Search Trigger Icon (visible only on responsive screens, positioned left of bell) -->
  <button type="button" class="btn-sima-icon mobile-search-trigger" id="mobile-search-toggle" title="Cari Data Aset / Menu" aria-label="Buka Pencarian">
    <i data-feather="search" style="width: 17px; height: 17px;"></i>
  </button>

  <!-- Notification Dropdown -->
  <div class="dropdown pc-h-item" style="position: relative;">
    <a id="notif-bell-link" class="btn-sima-icon dropdown-toggle arrow-none" data-pc-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false" style="position: relative;" title="Notifikasi">
      <i data-feather="bell" style="width: 17px; height: 17px;"></i>
      @if($notifUnread > 0)
      <span id="notif-badge" style="position: absolute; top: 4px; right: 4px; width: 8px; height: 8px; border-radius: 50%; background: var(--color-danger, #A63D2F); border: 2px solid var(--color-surface, #f8faf7);"></span>
      @endif
    </a>

    <!-- Notification Dropdown Menu (SIMA System Standard) -->
    <div class="dropdown-menu dropdown-notification dropdown-menu-end pc-h-dropdown notif-popup-panel modal-sima-box" style="display: none; width: 380px; max-width: 92vw; padding: 0; margin-top: 10px; border-radius: var(--radius-lg); box-shadow: var(--shadow-float); overflow: hidden;">
      
      {{-- 1. Header with Title & Refresh --}}
      <div class="notif-popup-header" style="padding: 16px 18px 10px 18px; display: flex; justify-content: space-between; align-items: center;">
        <span style="font-size: 15px; font-weight: 700; color: var(--color-text, #16251b); letter-spacing: -0.01em;">Notifikasi</span>
        <button type="button" id="btn-notif-refresh" class="notif-header-icon-btn" title="Segarkan notifikasi" style="background: none; border: none; cursor: pointer; color: var(--color-text-muted, #546958); padding: 4px; border-radius: 6px; display: flex; align-items: center; justify-content: center; transition: all 0.2s ease;">
          <i data-feather="rotate-cw" style="width: 15px; height: 15px;"></i>
        </button>
      </div>

      {{-- 2. System Standard Tabs (Centered 50/50 Symmetrical Layout) --}}
      <div class="notif-popup-tabs-wrapper" style="padding: 0 18px; border-bottom: 1px dashed var(--color-border); display: flex;">
        <button type="button" class="notif-tab-btn active" data-tab="all" style="flex: 1; display: inline-flex; justify-content: center; align-items: center; text-align: center;">
          <span>Semua</span>
          <span class="notif-tab-badge" id="tab-badge-all">{{ $notifTotal }}</span>
        </button>
        <button type="button" class="notif-tab-btn" data-tab="unread" style="flex: 1; display: inline-flex; justify-content: center; align-items: center; text-align: center;">
          <span>Belum Dibaca</span>
          <span class="notif-tab-badge" id="tab-badge-unread">{{ $notifUnread }}</span>
        </button>
      </div>

      {{-- 3. Notification List Body (Clean Neutral Warm Styling, No Green Domination) --}}
      <div id="notif-dropdown-body" style="max-height: 350px; overflow-y: auto; padding: 0;">
        @forelse($notifItems as $notif)
          @php
            $isUnread = !$notif->is_read;
          @endphp
          <div class="notif-item-row {{ $isUnread ? 'is-unread' : '' }}" style="padding: 13px 18px; border-bottom: 1px dashed var(--color-border); background: transparent; transition: background 0.15s ease;">
            <a href="{{ route('notifications.read', $notif->id) }}" style="text-decoration: none; color: inherit; display: block;">
              <div style="display: flex; gap: 12px; align-items: flex-start;">
                {{-- Clean Direct Icon --}}
                <i data-feather="{{ $notif->icon ?? 'bell' }}" style="width: 16px; height: 16px; color: var(--color-text-muted, #546958); margin-top: 2px; flex-shrink: 0;"></i>
                
                {{-- Text Content --}}
                <div style="flex: 1; min-width: 0;">
                  <div style="font-size: 13px; font-weight: {{ $isUnread ? '600' : '500' }}; color: {{ $isUnread ? 'var(--color-text, #16251b)' : 'var(--color-text-muted, #546958)' }}; line-height: 1.35; margin-bottom: 3px;">
                    {{ $notif->title }}
                  </div>
                  @if($notif->body)
                  <div style="font-size: 12px; color: var(--color-text-muted, #546958); line-height: 1.4; margin-bottom: 6px;">
                    {{ $notif->body }}
                  </div>
                  @endif

                  {{-- Time Row --}}
                  <div style="font-size: 11px; color: var(--color-text-muted, #546958); font-weight: 500;">
                    {{ $notif->created_at ? ($notif->created_at->diffForHumans(null, true) . ' lalu') : '' }}
                  </div>
                </div>
              </div>
            </a>
          </div>
        @empty
          <div style="text-align: center; padding: 36px 18px; color: var(--color-text-muted, #546958); display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px;">
            <i data-feather="inbox" style="width: 36px; height: 36px; stroke-width: 1.5; color: var(--color-border-dark, #546958);"></i>
            <p style="margin: 0; font-size: 13px; font-weight: 500; color: var(--color-text-muted, #546958);">Belum ada notifikasi baru</p>
          </div>
        @endforelse
      </div>

      {{-- 4. Footer Divider (Single Clean Dashed Border) --}}
      <div style="padding: 0 18px;">
        <div style="border-top: 1px dashed var(--color-border); height: 0; width: 100%;"></div>
      </div>
      <div class="notif-popup-footer" style="padding: 12px 18px; background: var(--color-surface, #f8faf7); display: flex; justify-content: space-between; align-items: center;">
        <button type="button" id="btn-mark-all-read" style="background: none; border: none; cursor: pointer; font-size: 12px; color: var(--color-primary, #1f3a2a); font-weight: 600; padding: 0; text-decoration: underline; text-underline-offset: 3px; transition: color 0.15s ease;">
          Tandai semua dibaca
        </button>
        <a href="{{ route('notifications.index') }}" class="btn-notif-center" style="font-size: 12px; color: var(--color-text-on-primary, #eaf1e7); font-weight: 600; text-decoration: none; border: 1px solid var(--color-primary, #1f3a2a); background: var(--color-primary, #1f3a2a); padding: 6px 16px; border-radius: var(--radius-full); transition: all 0.15s ease; display: inline-flex; align-items: center; justify-content: center;">
          Pusat Notifikasi
        </a>
      </div>
    </div>
  </div>

  <!-- User Profile Dropdown -->
  @auth
  <div class="dropdown pc-h-item header-user-profile" style="position: relative;">
    <a class="dropdown-toggle arrow-none" data-pc-toggle="dropdown" href="#" role="button" aria-haspopup="false" data-pc-auto-close="outside" aria-expanded="false" style="text-decoration: none; display: flex; align-items: center; gap: 8px;">
      <div class="avatar-sima" style="width: 36px; height: 36px;">
        {{ $authUser->initials ?? substr($authUser->nama ?? $authUser->username ?? 'U', 0, 2) }}
      </div>
    </a>
    <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown modal-sima-box" style="display: none; width: 290px; min-width: 290px; padding: 0; margin-top: 10px; border-radius: var(--radius-lg); box-shadow: var(--shadow-float);">
      <div style="padding: 18px 20px; border-bottom: 1px dashed var(--color-border); background: var(--color-surface);">
        <div style="font-size: 15px; font-weight: 700; color: var(--color-text); line-height: 1.3;">
          {{ $authUser->nama ?? $authUser->name ?? $authUser->username }}
        </div>
        <div style="font-size: 12.5px; color: var(--color-text-muted); margin-top: 4px; font-weight: 500;">
          {{ $authUser->role_name_display ?: ($authUser->username ? '@'.$authUser->username : '') }}
        </div>
      </div>
      <div style="padding: 10px 12px 14px 12px; display: flex; flex-direction: column; gap: 4px;">
        <a href="{{ route('profile') }}" class="dropdown-item">
          <i data-feather="user"></i>
          <span>Profil Pengguna</span>
        </a>
        <a href="{{ route('profile.password') }}" class="dropdown-item">
          <i data-feather="lock"></i>
          <span>Ganti Kata Sandi</span>
        </a>
        <hr class="dropdown-divider sima-divider" />
        <a href="{{ route('logout.get') }}" class="dropdown-item text-danger">
          <i data-feather="log-out"></i>
          <span>Keluar Sistem</span>
        </a>
      </div>
    </div>
  </div>
  @endauth

</div>

<style>
  .notif-popup-tabs-wrapper {
    display: flex;
    width: 100%;
    padding: 0 18px;
    border-bottom: 1px dashed var(--color-border);
  }
  .notif-tab-btn {
    flex: 1 1 50%;
    border: none;
    background: transparent;
    padding: 10px 8px 11px 8px;
    cursor: pointer;
    display: inline-flex;
    justify-content: center;
    align-items: center;
    gap: 7px;
    font-size: 13px;
    font-weight: 500;
    color: var(--color-text-muted, #546958);
    border-bottom: 2px solid transparent;
    text-align: center;
    transition: all 0.15s ease;
  }
  .notif-tab-btn:hover {
    color: var(--color-text, #16251b);
  }
  .notif-tab-btn.active {
    color: var(--color-primary, #1f3a2a) !important;
    font-weight: 600 !important;
    border-bottom: 2px solid var(--color-primary, #1f3a2a) !important;
    background: transparent !important;
  }
  .notif-tab-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 18px;
    height: 18px;
    padding: 0 6px;
    font-size: 11px;
    font-weight: 600;
    border-radius: var(--radius-full, 9999px);
    background: var(--color-bg, #eaf1e7);
    color: var(--color-text-muted, #546958);
    border: 1px solid var(--color-border, #cfdcc8);
    line-height: 1;
    transition: all 0.15s ease;
  }
  .notif-tab-btn[data-tab="unread"]:not(.active) .notif-tab-badge {
    background: var(--color-warning-soft, #F4E7C3);
    color: var(--color-warning, #B7791F);
    border-color: transparent;
  }
  .notif-tab-btn.active .notif-tab-badge {
    background: var(--color-primary-tint, #d5e4d7) !important;
    color: var(--color-primary, #1f3a2a) !important;
    border-color: transparent !important;
    font-weight: 600 !important;
  }
  .notif-item-row:hover {
    background: var(--color-row-hover, #eff5ed) !important;
  }
  .notif-item-row:last-child {
    border-bottom: none !important;
  }
  .notif-header-icon-btn:hover {
    background: rgba(0, 0, 0, 0.06) !important;
    color: var(--color-text, #16251b) !important;
  }
  .btn-notif-center:hover {
    background: var(--color-primary-hover, #162c1f) !important;
    border-color: var(--color-primary-hover, #162c1f) !important;
    color: var(--color-text-on-primary, #eaf1e7) !important;
  }
  #btn-mark-all-read {
    outline: none !important;
    transition: all 0.2s ease !important;
  }
  #btn-mark-all-read:hover {
    color: var(--color-primary-hover, #162c1f) !important;
  }
  #btn-mark-all-read:focus-visible {
    outline: 1.5px dashed var(--color-primary, #1f3a2a) !important;
    outline-offset: 3px;
    border-radius: 3px;
  }
  .spin-animation {
    animation: simaSpin 0.75s linear infinite;
  }
  @keyframes simaSpin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }
</style>

<!-- Notification Polling & Real-time Filter Script -->
<script>
  (function () {
    const dropdownUrl = "{{ route('notifications.dropdown') }}";
    const readUrlTemplate = "{{ route('notifications.read', ['id' => '__ID__']) }}";
    const markAllUrl = "{{ route('notifications.mark-all') }}";
    const csrfToken = "{{ csrf_token() }}";
    const pollIntervalMs = 15000;

    let activeFilter = 'all';
    let cachedNotifications = [];

    function escapeHtml(value) {
      return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/\"/g, '&quot;')
        .replace(/'/g, '&#039;');
    }

    function renderBadge(unread) {
      const bell = document.getElementById('notif-bell-link');
      if (!bell) return;

      let badge = document.getElementById('notif-badge');
      if (unread > 0) {
        if (!badge) {
          badge = document.createElement('span');
          badge.id = 'notif-badge';
          badge.style.position = 'absolute';
          badge.style.top = '4px';
          badge.style.right = '4px';
          badge.style.width = '8px';
          badge.style.height = '8px';
          badge.style.borderRadius = '50%';
          badge.style.background = 'var(--color-danger, #A63D2F)';
          badge.style.border = '2px solid var(--color-surface, #f8faf7)';
          bell.appendChild(badge);
        }
      } else if (badge) {
        badge.remove();
      }
    }

    function getIconMeta(iconType) {
      iconType = (iconType || 'bell').toLowerCase();
      const isDanger = ['trash', 'trash-2', 'x-circle', 'alert-circle', 'alert-triangle', 'user-x', 'x'].includes(iconType);
      
      const bg = isDanger ? 'var(--color-danger-soft, #F3DAD3)' : 'var(--color-bg, #eaf1e7)';
      const color = isDanger ? 'var(--color-danger, #A63D2F)' : 'var(--color-text-muted, #546958)';
      return { icon: iconType, bg: bg, color: color };
    }

    function buildItemHtml(item) {
      const id = String(item.id ?? '');
      const href = readUrlTemplate.replace('__ID__', id);
      const isUnread = !item.is_read;
      const meta = getIconMeta(item.icon);
      const title = escapeHtml(item.title || '-');
      const body = item.body ? `<div style="font-size: 12px; color: var(--color-text-muted, #546958); line-height: 1.4; margin-bottom: 6px;">${escapeHtml(item.body)}</div>` : '';
      const time = escapeHtml(item.time ? (item.time.endsWith('lalu') ? item.time : item.time + ' lalu') : '');
      return `
        <div class="notif-item-row ${isUnread ? 'is-unread' : ''}" style="padding: 13px 18px; border-bottom: 1px dashed var(--color-border); background: transparent; transition: background 0.15s ease;">
          <a href="${href}" style="text-decoration: none; color: inherit; display: block;">
            <div style="display: flex; gap: 12px; align-items: flex-start;">
              <i data-feather="${meta.icon}" style="width: 16px; height: 16px; color: var(--color-text-muted, #546958); margin-top: 2px; flex-shrink: 0;"></i>
              <div style="flex: 1; min-width: 0;">
                <div style="font-size: 13px; font-weight: ${isUnread ? '600' : '500'}; color: ${isUnread ? 'var(--color-text, #16251b)' : 'var(--color-text-muted, #546958)'}; line-height: 1.35; margin-bottom: 3px;">
                  ${title}
                </div>
                ${body}
                <div style="font-size: 11px; color: var(--color-text-muted, #546958); font-weight: 500;">
                  ${time}
                </div>
              </div>
            </div>
          </a>
        </div>
      `;
    }

    function renderList() {
      const bodyEl = document.getElementById('notif-dropdown-body');
      if (!bodyEl) return;

      let filtered = cachedNotifications;
      if (activeFilter === 'unread') {
        filtered = cachedNotifications.filter(n => !n.is_read);
      }

      if (filtered.length === 0) {
        bodyEl.innerHTML = `
          <div style="text-align: center; padding: 36px 18px; color: var(--color-text-muted, #546958);">
            <i data-feather="bell-off" style="width: 28px; height: 28px; margin: 0 auto 10px; display: block; opacity: 0.5; color: var(--color-text-muted, #546958);"></i>
            <div style="font-size: 13px; font-weight: 600; color: var(--color-text, #16251b);">Tidak ada notifikasi</div>
          </div>
        `;
      } else {
        bodyEl.innerHTML = filtered.map(buildItemHtml).join('');
      }

      if (window.feather && typeof window.feather.replace === 'function') {
        window.feather.replace();
      }
    }

    function updateTabBadges(unread, total) {
      const bAll = document.getElementById('tab-badge-all');
      const bUnread = document.getElementById('tab-badge-unread');

      if (bAll) bAll.textContent = total !== undefined ? total : cachedNotifications.length;
      if (bUnread) bUnread.textContent = unread !== undefined ? unread : cachedNotifications.filter(n => !n.is_read).length;
    }

    async function refreshNotifications(isManual = false) {
      if (!isManual && document.hidden) return;
      const refreshBtn = document.getElementById('btn-notif-refresh');
      if (isManual && refreshBtn) {
        refreshBtn.classList.add('spin-animation');
      }

      try {
        const res = await fetch(dropdownUrl, {
          method: 'GET',
          headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
          cache: 'no-store',
          credentials: 'same-origin'
        });
        if (!res.ok) return;

        const data = await res.json();
        const unread = Number(data.unread || 0);
        const total = Number(data.total !== undefined ? data.total : (data.items || []).length);
        cachedNotifications = Array.isArray(data.items) ? data.items : [];

        renderBadge(unread);
        updateTabBadges(unread, total);
        renderList();
      } catch (e) {
      } finally {
        if (isManual && refreshBtn) {
          setTimeout(() => refreshBtn.classList.remove('spin-animation'), 600);
        }
      }
    }

    async function markAllAsRead() {
      const markAllBtn = document.getElementById('btn-mark-all-read');
      if (markAllBtn && markAllBtn.disabled) return;

      if (markAllBtn) {
        markAllBtn.disabled = true;
        markAllBtn.style.opacity = '0.6';
      }

      const unreadRows = document.querySelectorAll('.notif-item-row.is-unread');
      unreadRows.forEach(row => {
        row.style.transition = 'all 0.3s ease';
        row.style.backgroundColor = 'var(--color-primary-tint, #d5e4d7)';
      });

      try {
        const res = await fetch(markAllUrl, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        });

        if (res.ok) {
          unreadRows.forEach(row => {
            row.style.backgroundColor = 'transparent';
            row.classList.remove('is-unread');
          });

          cachedNotifications.forEach(n => n.is_read = true);
          renderBadge(0);
          updateTabBadges(0);

          if (activeFilter === 'unread') {
            unreadRows.forEach(row => {
              row.style.opacity = '0';
              row.style.transform = 'translateY(-6px)';
            });
            setTimeout(() => {
              renderList();
            }, 300);
          } else {
            setTimeout(() => {
              renderList();
            }, 350);
          }

          if (markAllBtn) {
            markAllBtn.innerHTML = '✓ Semua telah dibaca';
            markAllBtn.style.color = 'var(--color-success, #3F7D3A)';
            markAllBtn.style.textDecoration = 'none';
            setTimeout(() => {
              markAllBtn.disabled = false;
              markAllBtn.style.opacity = '1';
              markAllBtn.innerHTML = 'Tandai semua dibaca';
              markAllBtn.style.color = 'var(--color-primary, #1f3a2a)';
              markAllBtn.style.textDecoration = 'underline';
              markAllBtn.style.textUnderlineOffset = '3px';
            }, 2000);
          }
        } else if (markAllBtn) {
          markAllBtn.disabled = false;
          markAllBtn.style.opacity = '1';
        }
      } catch (e) {
        if (markAllBtn) {
          markAllBtn.disabled = false;
          markAllBtn.style.opacity = '1';
        }
      }
    }

    document.addEventListener('DOMContentLoaded', function () {
      // Tab click events
      const tabBtns = document.querySelectorAll('.notif-tab-btn');
      tabBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
          e.preventDefault();
          tabBtns.forEach(b => b.classList.remove('active'));
          this.classList.add('active');

          activeFilter = this.getAttribute('data-tab') || 'all';
          renderList();
        });
      });

      // Refresh button
      const refreshBtn = document.getElementById('btn-notif-refresh');
      if (refreshBtn) {
        refreshBtn.addEventListener('click', function(e) {
          e.preventDefault();
          refreshNotifications(true);
        });
      }

      // Mark all read button
      const markAllBtn = document.getElementById('btn-mark-all-read');
      if (markAllBtn) {
        markAllBtn.addEventListener('click', function(e) {
          e.preventDefault();
          markAllAsRead();
        });
      }

      refreshNotifications();
      setInterval(refreshNotifications, pollIntervalMs);

      // Keyboard Shortcut ⌘K / Ctrl+K
      document.addEventListener('keydown', function (e) {
        if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
          e.preventDefault();
          const searchInput = document.getElementById('global-search-input');
          if (searchInput) {
            searchInput.focus();
            searchInput.select();
          }
        }
      });
    });
  })();
</script>

<!-- Global Search Command Palette Styles & Focusing Engine -->
<style>
  .global-search-dropdown-panel {
    position: absolute;
    top: calc(100% + 8px);
    right: 0;
    width: 480px;
    max-width: 92vw;
    background: var(--color-surface, #f8faf7);
    border: 1px solid var(--color-border, #cfdcc8);
    border-radius: var(--radius-lg, 12px);
    box-shadow: 0 16px 40px rgba(31, 58, 42, 0.16);
    z-index: 1060;
    overflow: hidden;
    text-align: left;
  }
  @media (max-width: 991.98px) {
    .global-search-dropdown-panel {
      position: fixed !important;
      left: 16px !important;
      right: 16px !important;
      top: 68px !important;
      width: auto !important;
      max-width: none !important;
      box-sizing: border-box !important;
      z-index: 1065 !important;
    }
  }
  .global-search-group-header {
    padding: 8px 14px 4px 14px;
    font-size: 10.5px;
    font-weight: 700;
    color: var(--color-text-muted, #546958);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    display: flex;
    align-items: center;
    gap: 6px;
  }
  .global-search-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 8px 14px;
    cursor: pointer;
    text-decoration: none !important;
    color: var(--color-text, #16251b) !important;
    transition: background 0.12s ease;
    user-select: none;
  }
  .global-search-item:hover,
  .global-search-item.is-selected {
    background: var(--color-primary-tint, #E8EFE5) !important;
  }
  .global-search-item-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: var(--color-bg, #EDF1DE);
    color: var(--color-primary, #1f3a2a);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }
  .global-search-item-icon svg,
  .global-search-item-icon i {
    width: 15px !important;
    height: 15px !important;
    color: var(--color-primary, #1f3a2a) !important;
    stroke: var(--color-primary, #1f3a2a) !important;
  }
  .global-search-item:hover .global-search-item-icon,
  .global-search-item.is-selected .global-search-item-icon {
    background: var(--color-primary, #1f3a2a) !important;
    color: #ffffff !important;
  }
  .global-search-item:hover .global-search-item-icon svg,
  .global-search-item:hover .global-search-item-icon i,
  .global-search-item.is-selected .global-search-item-icon svg,
  .global-search-item.is-selected .global-search-item-icon i {
    color: #ffffff !important;
    stroke: #ffffff !important;
  }
  .global-search-item-title {
    font-size: 13px;
    font-weight: 600;
    color: var(--color-text, #16251b);
    line-height: 1.3;
  }
  .global-search-item-desc {
    font-size: 11.5px;
    color: var(--color-text-muted, #546958);
    line-height: 1.3;
    margin-top: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 270px;
  }
  .global-search-item-badge {
    margin-left: auto;
    font-size: 11px;
    font-weight: 600;
    padding: 3px 8px;
    border-radius: 6px;
    background: var(--color-bg, #EDF1DE);
    color: var(--color-text-muted, #546958);
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
    gap: 4px;
  }
  .global-search-item:hover .global-search-item-badge,
  .global-search-item.is-selected .global-search-item-badge {
    background: var(--color-primary, #1f3a2a) !important;
    color: #ffffff !important;
  }

  /* High-visibility clean focus ring & pulse */
  @keyframes simaTargetPulse {
    0% {
      box-shadow: 0 0 0 0 rgba(31, 58, 42, 0.7), 0 0 0 0 rgba(31, 58, 42, 0.4);
      outline: 2.5px solid var(--color-primary, #1f3a2a);
      outline-offset: 4px;
      transform: translateY(-2px);
    }
    30% {
      box-shadow: 0 0 0 12px rgba(31, 58, 42, 0.22), 0 8px 24px rgba(31, 58, 42, 0.25);
      outline: 2.5px solid var(--color-primary, #1f3a2a);
      outline-offset: 4px;
      transform: translateY(-3px);
    }
    60% {
      box-shadow: 0 0 0 22px rgba(31, 58, 42, 0), 0 6px 18px rgba(31, 58, 42, 0.15);
      outline: 2.5px solid var(--color-primary, #1f3a2a);
      outline-offset: 3px;
      transform: translateY(-1px);
    }
    100% {
      box-shadow: none;
      outline: 2.5px solid transparent;
      outline-offset: 0;
      transform: translateY(0);
    }
  }

  .sima-search-highlight-target {
    animation: simaTargetPulse 2.5s cubic-bezier(0.2, 0.8, 0.2, 1) forwards !important;
    scroll-margin-top: 96px !important;
    border-color: var(--color-primary, #1f3a2a) !important;
    position: relative;
    z-index: 20;
    transition: all 0.3s ease;
  }
</style>

<script>
  (function () {
    // 1. Focus Target Handler (Scroll & Highlight Pulse)
    window.simaFocusTarget = function (targetId) {
      if (!targetId) return false;
      const cleanId = targetId.replace(/^#/, '');
      const el = document.getElementById(cleanId);
      if (!el) return false;

      // Smooth scroll to target, centering in viewport
      el.scrollIntoView({ behavior: 'smooth', block: 'center' });

      // Apply highlight pulse animation
      el.classList.remove('sima-search-highlight-target');
      void el.offsetWidth; // Force DOM reflow
      el.classList.add('sima-search-highlight-target');

      // Update URL hash smoothly without jump
      try {
        if (history.replaceState) {
          history.replaceState(null, null, '#' + cleanId);
        }
      } catch (e) {}

      // Remove highlight after animation finishes
      setTimeout(function () {
        el.classList.remove('sima-search-highlight-target');
      }, 2600);

      return true;
    };

    // 2. Hash Listener on Page Load (Handles Cross-Page Jumps)
    document.addEventListener('DOMContentLoaded', function () {
      if (window.location.hash) {
        const hash = window.location.hash.substring(1);
        if (hash) {
          setTimeout(function () {
            window.simaFocusTarget(hash);
          }, 400);
        }
      }
    });

    // 3. Global Catalog of Sections & Pages
    const globalCatalog = [
      // --- DASHBOARD SECTIONS ---
      {
        id: 'section-aset-per-jenis',
        title: 'Aset per Jenis Inventaris',
        desc: 'Distribusi jumlah unit & total nilai per jenis inventaris',
        page: 'Dashboard',
        url: '{{ route("dashboard") }}',
        icon: 'layers',
        keywords: ['aset per jenis inventaris', 'jenis inventaris', 'kategori', 'unit', 'total nilai', 'kib a', 'kib b']
      },
      {
        id: 'section-rentang-nilai',
        title: 'Aset menurut Rentang Nilai',
        desc: 'Penggolongan aset berdasarkan nominal harga perolehan',
        page: 'Dashboard',
        url: '{{ route("dashboard") }}',
        icon: 'dollar-sign',
        keywords: ['rentang nilai', 'di atas 50 juta', '10-50 juta', 'harga aset', 'nilai aset']
      },
      {
        id: 'section-nilai-per-kategori',
        title: 'Nilai Aset per Kategori',
        desc: 'Grafik donut dan proporsi nilai aset per klasifikasi',
        page: 'Dashboard',
        url: '{{ route("dashboard") }}',
        icon: 'pie-chart',
        keywords: ['nilai aset per kategori', 'donut chart', 'grafik nilai', 'kategori']
      },
      {
        id: 'section-kelengkapan-data',
        title: 'Kelengkapan Data Aset',
        desc: 'Indikator kelengkapan NUP, foto, barcode, dan ruangan',
        page: 'Dashboard',
        url: '{{ route("dashboard") }}',
        icon: 'check-circle',
        keywords: ['kelengkapan data aset', 'nup', 'foto aset', 'barcode', 'ruangan aset']
      },
      {
        id: 'section-status-buku',
        title: 'Status Buku Aset',
        desc: 'Ringkasan Buku Inventaris, Penghapusan, dan Format Laporan KIB',
        page: 'Dashboard',
        url: '{{ route("dashboard") }}',
        icon: 'book',
        keywords: ['status buku aset', 'buku inventaris', 'penghapusan', 'format laporan kib']
      },
      {
        id: 'section-kondisi-aset',
        title: 'Kondisi Aset',
        desc: 'Statistik kondisi Baik, Rusak Ringan, dan Rusak Berat',
        page: 'Dashboard',
        url: '{{ route("dashboard") }}',
        icon: 'activity',
        keywords: ['kondisi aset', 'kondisi fisik', 'baik', 'rusak ringan', 'rusak berat']
      },
      {
        id: 'section-asal-aset',
        title: 'Asal Aset',
        desc: 'Sumber perolehan dari APBDes, Kekayaan Asli Desa, Bantuan',
        page: 'Dashboard',
        url: '{{ route("dashboard") }}',
        icon: 'archive',
        keywords: ['asal aset', 'asal usul perolehan', 'apbdes', 'kekayaan asli', 'bantuan pemerintah']
      },
      {
        id: 'section-tren-perolehan',
        title: 'Tahun Perolehan Aset',
        desc: 'Grafik historis dan akumulasi perolehan aset per tahun',
        page: 'Dashboard',
        url: '{{ route("dashboard") }}',
        icon: 'bar-chart-2',
        keywords: ['tahun perolehan aset', 'tren perolehan', 'grafik tahun', 'perolehan aset']
      },
      {
        id: 'section-sebaran-ruangan',
        title: 'Sebaran Ruangan',
        desc: 'Distribusi persentase aset di setiap ruangan kantor desa',
        page: 'Dashboard',
        url: '{{ route("dashboard") }}',
        icon: 'home',
        keywords: ['sebaran ruangan', 'ruangan kantor', 'lokasi aset', 'terisi aset']
      },
      {
        id: 'section-aktivitas-terbaru',
        title: 'Aktivitas Terbaru',
        desc: 'Log aktivitas mutasi dan audit sistem terkini',
        page: 'Dashboard',
        url: '{{ route("dashboard") }}',
        icon: 'clock',
        keywords: ['aktivitas terbaru', 'log aktivitas', 'riwayat', 'audit trail']
      },
      {
        id: 'section-aset-terbaru',
        title: 'Aset yang Baru Ditambahkan',
        desc: 'Tabel 10 aset terakhir yang didaftarkan ke sistem',
        page: 'Dashboard',
        url: '{{ route("dashboard") }}',
        icon: 'table',
        keywords: ['aset yang baru ditambahkan', '10 aset terbaru', 'tabel aset baru', 'aset terakhir']
      },
      {
        id: 'section-ringkasan-kpi',
        title: 'Ringkasan Statistik KPI',
        desc: '5 Kartu metrik utama aset, nilai perolehan, ruangan, dan pejabat',
        page: 'Dashboard',
        url: '{{ route("dashboard") }}',
        icon: 'grid',
        keywords: ['ringkasan statistik kpi', 'kpi aset', 'total aset', 'total nilai']
      },

      // --- PENGHAPUSAN ASET SECTIONS ---
      {
        id: 'section-stat-penghapusan',
        title: 'Ringkasan Statistik Penghapusan',
        desc: 'Metrik total aset dihapus, nilai mutasi, dan jenis sering dihapus',
        page: 'Penghapusan Aset',
        url: '{{ route("penghapusan-aset.index") }}',
        icon: 'trash-2',
        keywords: ['ringkasan statistik penghapusan', 'mutasi berkurang', 'total dihapus']
      },
      {
        id: 'section-tabs-penghapusan',
        title: 'Tab Aset Aktif & Riwayat Penghapusan',
        desc: 'Pengalihan tampilan aset aktif vs arsip berita acara penghapusan',
        page: 'Penghapusan Aset',
        url: '{{ route("penghapusan-aset.index") }}',
        icon: 'folder',
        keywords: ['tab penghapusan', 'aset aktif siap dihapus', 'riwayat penghapusan']
      },
      {
        id: 'section-tabel-aktif',
        title: 'Tabel Aset Siap Dihapus',
        desc: 'Daftar aset aktif yang dapat dipilih untuk diajukan penghapusan',
        page: 'Penghapusan Aset',
        url: '{{ route("penghapusan-aset.index") }}',
        icon: 'list',
        keywords: ['tabel aset siap dihapus', 'usulan penghapusan', 'hapus aset', 'daftar aktif']
      },
      {
        id: 'section-tabel-riwayat',
        title: 'Tabel Riwayat Aset Dihapus',
        desc: 'Arsip berita acara mutasi berkurang dan status SK penghapusan',
        page: 'Penghapusan Aset',
        url: '{{ route("penghapusan-aset.index") }}',
        icon: 'archive',
        keywords: ['tabel riwayat aset dihapus', 'berita acara', 'sk penghapusan', 'arsip mutasi']
      },

      // --- BUKU INVENTARIS SECTIONS ---
      {
        id: 'section-stat-inventaris',
        title: 'Ringkasan Statistik Inventaris',
        desc: 'Total aset inventaris desa beserta kondisi fisik lengkap',
        page: 'Buku Inventaris',
        url: '{{ route("manajemen-aset.index") }}',
        icon: 'box',
        keywords: ['ringkasan statistik inventaris', 'total inventaris', 'statistik aset']
      },
      {
        id: 'section-tabs-kondisi',
        title: 'Tab Filter Kondisi Aset',
        desc: 'Tab cepat untuk memfilter Baik, Rusak Ringan, atau Rusak Berat',
        page: 'Buku Inventaris',
        url: '{{ route("manajemen-aset.index") }}',
        icon: 'filter',
        keywords: ['tab filter kondisi aset', 'filter kondisi', 'rusak berat', 'rusak ringan']
      },
      {
        id: 'section-tabel-inventaris',
        title: 'Tabel Buku Inventaris Aset',
        desc: 'Tabel master buku inventaris dengan filter KIB, ruangan, dan pencarian',
        page: 'Buku Inventaris',
        url: '{{ route("manajemen-aset.index") }}',
        icon: 'list',
        keywords: ['tabel buku inventaris aset', 'daftar aset', 'buku inventaris', 'master aset']
      },

      // --- MASTER DATA SECTIONS ---
      {
        id: 'section-tabel-ruangan',
        title: 'Tabel Data Ruangan',
        desc: 'Daftar seluruh ruangan kantor desa dan kapasitas asetnya',
        page: 'Data Ruangan',
        url: '{{ route("ruangan.index") }}',
        icon: 'home',
        keywords: ['tabel data ruangan', 'daftar ruangan', 'lokasi ruangan']
      },
      {
        id: 'section-tabel-pejabat',
        title: 'Tabel Pejabat Penatausaha',
        desc: 'Daftar pejabat penatausaha pengurus barang dan penandatangan KIB',
        page: 'Data Pejabat',
        url: '{{ route("pejabat.index") }}',
        icon: 'award',
        keywords: ['tabel pejabat penatausaha', 'pengurus barang', 'ttd pejabat', 'sk pejabat']
      },
      {
        id: 'section-tabel-users',
        title: 'Tabel Manajemen Pengguna',
        desc: 'Daftar akun pengguna, hak akses peran, dan status akun',
        page: 'Kelola Pengguna',
        url: '{{ route("users.index") }}',
        icon: 'users',
        keywords: ['tabel manajemen pengguna', 'daftar user', 'akun', 'hak akses']
      }
    ];

    // Main Feature Menus
    const featureMenus = [
      { title: 'Dashboard Utama', desc: 'Ringkasan eksekutif dan analitik visual aset desa', url: '{{ route("dashboard") }}', icon: 'grid', keywords: ['dashboard', 'utama', 'beranda', 'home', 'ringkasan'] },
      { title: 'Buku Inventaris Aset', desc: 'Pengelolaan data aset, tambah inventaris, dan cetak QR', url: '{{ route("manajemen-aset.index") }}', icon: 'box', keywords: ['buku inventaris', 'aset', 'manajemen aset', 'inventaris', 'kib'] },
      { title: 'Penghapusan Aset', desc: 'Mutasi berkurang aset rusak berat dan berita acara', url: '{{ route("penghapusan-aset.index") }}', icon: 'trash-2', keywords: ['penghapusan aset', 'mutasi berkurang', 'hapus', 'ba'] },
      { title: 'Data Ruangan', desc: 'Master ruangan dan pemetaan penempatan barang inventaris', url: '{{ route("ruangan.index") }}', icon: 'home', keywords: ['ruangan', 'data ruangan', 'lokasi'] },
      { title: 'Data Pejabat', desc: 'Pejabat penatausaha barang dan penandatangan dokumen KIB', url: '{{ route("pejabat.index") }}', icon: 'award', keywords: ['pejabat', 'penatausaha', 'kepala desa', 'sekretaris'] },
      { title: 'Jenis Inventaris', desc: 'Klasifikasi barang inventaris sesuai kodefikasi Permendagri', url: '{{ route("jenis-inventaris.index") }}', icon: 'layers', keywords: ['jenis inventaris', 'klasifikasi', 'kib a', 'kib b', 'kib c', 'kib d', 'kib e', 'kib f'] },
      { title: 'Pusat Ekspor & KIB', desc: 'Cetak dan unduh Buku Inventaris, KIB A–F, KIR ke Excel/PDF', url: '{{ route("export-data.index") }}', icon: 'file-text', keywords: ['ekspor data', 'export', 'cetak kib', 'laporan aset', 'unduh excel'] },
      { title: 'Riwayat & Log Aktivitas', desc: 'Log audit dan rekaman seluruh perubahan data dalam sistem', url: '{{ route("dashboard.activities") }}', icon: 'clock', keywords: ['aktivitas', 'log', 'riwayat', 'audit', 'history'] },
      { title: 'Kelola Pengguna', desc: 'Manajemen akun pengguna sistem dan hak akses jabatan', url: '{{ route("users.index") }}', icon: 'users', keywords: ['users', 'pengguna', 'kelola user', 'akun'] },
      { title: 'Profil Saya', desc: 'Pengaturan profil dan penggantian kata sandi akun', url: '{{ route("profile") }}', icon: 'user', keywords: ['profil', 'profile', 'akun saya', 'password'] }
    ];

    // 4. Command Palette Interaction Controller
    document.addEventListener('DOMContentLoaded', function () {
      const searchBoxWrap = document.getElementById('global-search-container');
      const searchInput = document.getElementById('global-search-input');
      const searchDropdown = document.getElementById('global-search-dropdown');
      const resultsList = document.getElementById('global-search-results-list');
      const statusText = document.getElementById('global-search-status-text');

      if (!searchInput || !searchDropdown || !resultsList) return;

      let selectedIndex = -1;
      let renderedItems = [];

      function showDropdown() {
        searchDropdown.style.display = 'block';
        renderResults(searchInput.value.trim());
      }

      function hideDropdown() {
        searchDropdown.style.display = 'none';
        selectedIndex = -1;
      }

      function isElementOnCurrentPage(id) {
        return !!document.getElementById(id);
      }

      function renderResults(query) {
        const q = query.toLowerCase();
        resultsList.innerHTML = '';
        renderedItems = [];
        selectedIndex = -1;

        const inPageMatches = [];
        const otherPageMatches = [];
        const menuMatches = [];

        // Check Catalog Sections
        globalCatalog.forEach(item => {
          const matchTitle = item.title.toLowerCase().includes(q);
          const matchDesc = item.desc.toLowerCase().includes(q);
          const matchPage = item.page.toLowerCase().includes(q);
          const matchKeyword = item.keywords.some(k => k.includes(q));

          if (!q || matchTitle || matchDesc || matchPage || matchKeyword) {
            if (isElementOnCurrentPage(item.id)) {
              inPageMatches.push({ ...item, isCurrentPage: true });
            } else {
              otherPageMatches.push({ ...item, isCurrentPage: false });
            }
          }
        });

        // Also dynamically detect any extra [data-search-title] on current page not in catalog
        const dynamicSections = document.querySelectorAll('[data-search-title]');
        dynamicSections.forEach(sec => {
          const id = sec.id;
          if (!id) return;
          const title = sec.getAttribute('data-search-title') || id;
          const matchTitle = title.toLowerCase().includes(q);
          if ((!q || matchTitle) && !inPageMatches.some(m => m.id === id)) {
            inPageMatches.push({
              id: id,
              title: title,
              desc: 'Fokus ke bagian kartu ini pada halaman saat ini',
              page: 'Halaman Ini',
              icon: 'crosshair',
              isCurrentPage: true
            });
          }
        });

        // Check Menus
        featureMenus.forEach(menu => {
          const matchTitle = menu.title.toLowerCase().includes(q);
          const matchDesc = menu.desc.toLowerCase().includes(q);
          const matchKeyword = menu.keywords.some(k => k.includes(q));

          if (!q || matchTitle || matchDesc || matchKeyword) {
            menuMatches.push(menu);
          }
        });

        let html = '';

        // Group 1: In-Page Card Targets (Focus screen directly!)
        if (inPageMatches.length > 0) {
          inPageMatches.forEach(item => {
            const idx = renderedItems.length;
            renderedItems.push({ type: 'in-page', data: item });
            html += `
              <div class="global-search-item" data-index="${idx}" onclick="window.simaSelectSearchResult(${idx})">
                <div class="global-search-item-icon">
                  <i data-feather="${item.icon || 'crosshair'}" style="width: 15px; height: 15px;"></i>
                </div>
                <div style="min-width: 0; flex: 1;">
                  <div class="global-search-item-title">${item.title}</div>
                  <div class="global-search-item-desc">${item.desc}</div>
                </div>
                <span class="global-search-item-badge">Fokus Card ↵</span>
              </div>
            `;
          });
        }

        // Group 2: Features & Menus
        if (menuMatches.length > 0) {
          menuMatches.slice(0, q ? 5 : 4).forEach(menu => {
            const idx = renderedItems.length;
            renderedItems.push({ type: 'menu', data: menu });
            html += `
              <div class="global-search-item" data-index="${idx}" onclick="window.simaSelectSearchResult(${idx})">
                <div class="global-search-item-icon">
                  <i data-feather="${menu.icon || 'link'}" style="width: 15px; height: 15px;"></i>
                </div>
                <div style="min-width: 0; flex: 1;">
                  <div class="global-search-item-title">${menu.title}</div>
                  <div class="global-search-item-desc">${menu.desc}</div>
                </div>
                <span class="global-search-item-badge">Buka Menu ↵</span>
              </div>
            `;
          });
        }

        // Group 3: Other Pages' Sections
        if (otherPageMatches.length > 0 && q) {
          otherPageMatches.slice(0, 5).forEach(item => {
            const idx = renderedItems.length;
            renderedItems.push({ type: 'other-page', data: item });
            html += `
              <div class="global-search-item" data-index="${idx}" onclick="window.simaSelectSearchResult(${idx})">
                <div class="global-search-item-icon">
                  <i data-feather="${item.icon || 'layers'}" style="width: 15px; height: 15px;"></i>
                </div>
                <div style="min-width: 0; flex: 1;">
                  <div class="global-search-item-title">${item.title} <span style="font-size: 11px; font-weight: 500; color: var(--color-text-muted);">(${item.page})</span></div>
                  <div class="global-search-item-desc">${item.desc}</div>
                </div>
                <span class="global-search-item-badge">Buka & Fokus ↵</span>
              </div>
            `;
          });
        }

        // Group 4: Live Inventory Search Query Action
        if (q) {
          const idxInventaris = renderedItems.length;
          renderedItems.push({
            type: 'asset-search',
            data: {
              url: '{{ route("manajemen-aset.index") }}?search=' + encodeURIComponent(query),
              title: `Cari "${query}" di Buku Inventaris`
            }
          });
          html += `
            <div class="global-search-item" data-index="${idxInventaris}" onclick="window.simaSelectSearchResult(${idxInventaris})">
              <div class="global-search-item-icon" style="background: var(--color-primary-tint); color: var(--color-primary);">
                <i data-feather="search" style="width: 15px; height: 15px;"></i>
              </div>
              <div style="min-width: 0; flex: 1;">
                <div class="global-search-item-title">Cari "<strong>${escapeHtml(query)}</strong>" di Buku Inventaris</div>
                <div class="global-search-item-desc">Filter tabel aset berdasarkan nama barang, kode, atau NUP</div>
              </div>
              <span class="global-search-item-badge">Cari Aset ↵</span>
            </div>
          `;

          const idxPenghapusan = renderedItems.length;
          renderedItems.push({
            type: 'asset-search',
            data: {
              url: '{{ route("penghapusan-aset.index") }}?search=' + encodeURIComponent(query),
              title: `Cari "${query}" di Penghapusan Aset`
            }
          });
          html += `
            <div class="global-search-item" data-index="${idxPenghapusan}" onclick="window.simaSelectSearchResult(${idxPenghapusan})">
              <div class="global-search-item-icon">
                <i data-feather="trash-2" style="width: 15px; height: 15px;"></i>
              </div>
              <div style="min-width: 0; flex: 1;">
                <div class="global-search-item-title">Cari "<strong>${escapeHtml(query)}</strong>" di Penghapusan Aset</div>
                <div class="global-search-item-desc">Cek status aset mutasi berkurang dan berita acara</div>
              </div>
              <span class="global-search-item-badge">Cek Mutasi ↵</span>
            </div>
          `;
        }

        if (renderedItems.length === 0) {
          html = `
            <div style="padding: 24px 16px; text-align: center; color: var(--color-text-muted);">
              <i data-feather="alert-circle" style="width: 24px; height: 24px; margin-bottom: 8px;"></i>
              <div style="font-size: 13px; font-weight: 600; color: var(--color-text);">Tidak ada hasil yang cocok</div>
              <div style="font-size: 12px; margin-top: 2px;">Coba gunakan kata kunci nama menu, nama bagian, atau kode aset.</div>
            </div>
          `;
        }

        resultsList.innerHTML = html;

        if (statusText) {
          statusText.textContent = q ? `Hasil Pencarian: "${query}"` : 'Navigasi Cepat & Bagian Halaman';
        }

        // Auto-select first item if searching
        if (renderedItems.length > 0) {
          selectedIndex = 0;
          updateSelectedVisual();
        }

        // Re-run feather icons inside dropdown
        if (window.feather) {
          feather.replace();
        }
      }

      function updateSelectedVisual() {
        const items = resultsList.querySelectorAll('.global-search-item');
        items.forEach((item, idx) => {
          if (idx === selectedIndex) {
            item.classList.add('is-selected');
            item.scrollIntoView({ block: 'nearest' });
          } else {
            item.classList.remove('is-selected');
          }
        });
      }

      window.simaSelectSearchResult = function (index) {
        if (index < 0 || index >= renderedItems.length) return;
        const target = renderedItems[index];

        hideDropdown();
        searchInput.blur();

        if (target.type === 'in-page') {
          // In-Page target: Smoothly focus and highlight
          window.simaFocusTarget(target.data.id);
        } else if (target.type === 'other-page') {
          // Other page section: Navigate with hash
          window.location.href = target.data.url + '#' + target.data.id;
        } else if (target.type === 'menu') {
          // Menu navigation
          window.location.href = target.data.url;
        } else if (target.type === 'asset-search') {
          // Redirect to asset search
          window.location.href = target.data.url;
        }
      };

      function escapeHtml(str) {
        return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
      }

      // Event Listeners for Search Input
      searchInput.addEventListener('focus', function () {
        showDropdown();
      });

      searchInput.addEventListener('input', function () {
        showDropdown();
      });

      searchInput.addEventListener('keydown', function (e) {
        if (searchDropdown.style.display === 'none') {
          if (e.key === 'ArrowDown' || e.key === 'Enter') {
            showDropdown();
            return;
          }
        }

        if (e.key === 'ArrowDown') {
          e.preventDefault();
          if (renderedItems.length > 0) {
            selectedIndex = (selectedIndex + 1) % renderedItems.length;
            updateSelectedVisual();
          }
        } else if (e.key === 'ArrowUp') {
          e.preventDefault();
          if (renderedItems.length > 0) {
            selectedIndex = (selectedIndex - 1 + renderedItems.length) % renderedItems.length;
            updateSelectedVisual();
          }
        } else if (e.key === 'Enter') {
          e.preventDefault();
          if (selectedIndex >= 0 && selectedIndex < renderedItems.length) {
            window.simaSelectSearchResult(selectedIndex);
          } else if (searchInput.value.trim().length > 0) {
            // Default Enter fallback: search in Buku Inventaris
            window.location.href = '{{ route("manajemen-aset.index") }}?search=' + encodeURIComponent(searchInput.value.trim());
          }
        } else if (e.key === 'Escape') {
          e.preventDefault();
          hideDropdown();
          searchInput.blur();
          if (window.closeMobileSearch) {
            window.closeMobileSearch();
          }
        }
      });

      // Mobile Search Expand / Collapse Handlers
      const mobileSearchToggle = document.getElementById('mobile-search-toggle');
      const mobileSearchClose = document.getElementById('mobile-search-close');
      const pcHeader = document.querySelector('.pc-header');

      window.openMobileSearch = function () {
        if (pcHeader) pcHeader.classList.add('is-search-expanded');
        if (searchInput) {
          searchInput.focus();
          showDropdown();
        }
      };

      window.closeMobileSearch = function () {
        if (pcHeader) pcHeader.classList.remove('is-search-expanded');
        if (searchInput) {
          searchInput.blur();
        }
        hideDropdown();
      };

      if (mobileSearchToggle) {
        mobileSearchToggle.addEventListener('click', function (e) {
          e.preventDefault();
          e.stopPropagation();
          if (pcHeader && pcHeader.classList.contains('is-search-expanded')) {
            window.closeMobileSearch();
          } else {
            window.openMobileSearch();
          }
        });
      }

      if (mobileSearchClose) {
        mobileSearchClose.addEventListener('click', function (e) {
          e.preventDefault();
          e.stopPropagation();
          window.closeMobileSearch();
        });
      }

      // Close dropdown & mobile search when clicking outside
      document.addEventListener('click', function (e) {
        if (searchBoxWrap && !searchBoxWrap.contains(e.target)) {
          hideDropdown();
        }
        if (pcHeader && pcHeader.classList.contains('is-search-expanded')) {
          if (searchBoxWrap && !searchBoxWrap.contains(e.target) && mobileSearchToggle && !mobileSearchToggle.contains(e.target)) {
            window.closeMobileSearch();
          }
        }
      });
    });

    // =========================================================================
    // BULLETPROOF SIDEBAR TOGGLE (DESKTOP & MOBILE DRAWER)
    // =========================================================================
    let _lastSidebarToggleTime = 0;
    window.toggleSidebar = function (e) {
      if (e) {
        if (e.preventDefault) e.preventDefault();
        if (e.stopPropagation) e.stopPropagation();
      }
      const now = Date.now();
      if (now - _lastSidebarToggleTime < 280) return;
      _lastSidebarToggleTime = now;

      const sidebar = document.querySelector('.pc-sidebar');
      if (!sidebar) return;

      const isMobile = window.innerWidth < 1024;
      if (isMobile) {
        if (sidebar.classList.contains('mob-sidebar-active')) {
          window.closeMobileSidebar();
        } else {
          window.openMobileSidebar();
        }
      } else {
        sidebar.classList.toggle('pc-sidebar-hide');
      }
    };

    window.openMobileSidebar = function () {
      const sidebar = document.querySelector('.pc-sidebar');
      if (!sidebar) return;
      sidebar.classList.add('mob-sidebar-active');

      // Add background dimming overlay if not exists
      let overlay = document.querySelector('.pc-menu-overlay');
      if (!overlay) {
        overlay = document.createElement('div');
        overlay.className = 'pc-menu-overlay';
        document.body.appendChild(overlay);
        overlay.addEventListener('click', function () {
          window.closeMobileSidebar();
        });
      }
      document.body.classList.add('mob-sidebar-open');
    };

    window.closeMobileSidebar = function () {
      const sidebar = document.querySelector('.pc-sidebar');
      if (sidebar) {
        sidebar.classList.remove('mob-sidebar-active');
      }
      const overlay = document.querySelector('.pc-menu-overlay');
      if (overlay) {
        overlay.remove();
      }
      document.body.classList.remove('mob-sidebar-open');
    };

    // Delegated click handler on document: works across all layouts, timing & SVG replacements
    document.addEventListener('click', function (e) {
      // 1. Sidebar open/close triggers (hamburger buttons)
      const toggleBtn = e.target.closest('#mobile-collapse, #sidebar-hide, .pc-sidebar-popup a, .pc-sidebar-collapse a');
      if (toggleBtn) {
        e.preventDefault();
        e.stopPropagation();
        window.toggleSidebar(e);
        return;
      }

      // 2. Mobile drawer close button inside sidebar header
      const closeBtn = e.target.closest('#mobile-sidebar-close');
      if (closeBtn) {
        e.preventDefault();
        window.closeMobileSidebar();
        return;
      }

      // 3. Auto-close mobile drawer when tapping a leaf navigation item
      if (window.innerWidth < 1024) {
        const navLink = e.target.closest('.pc-sidebar .pc-link');
        if (navLink && !navLink.closest('.pc-hasmenu')) {
          window.closeMobileSidebar();
        }
      }
    });
  })();
</script>
<!-- [Topbar Shell] end -->