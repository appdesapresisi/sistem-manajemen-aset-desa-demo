<!-- [Mobile Media Block] start -->
<div class="me-auto pc-mob-drp">
  <ul class="inline-flex *:min-h-header-height *:inline-flex *:items-center">
    <!-- ======= Menu collapse Icon ===== -->
    <li class="pc-h-item pc-sidebar-collapse max-lg:hidden lg:inline-flex">
      <a href="#" class="pc-head-link ltr:!ml-0 rtl:!mr-0" id="sidebar-hide">
        <i data-feather="menu"></i>
      </a>
    </li>
    <li class="pc-h-item pc-sidebar-popup lg:hidden">
      <a href="#" class="pc-head-link ltr:!ml-0 rtl:!mr-0" id="mobile-collapse">
        <i data-feather="menu"></i>
      </a>
    </li>
  </ul>
</div>
<!-- [Mobile Media Block end] -->
<div class="ms-auto">
  <ul class="inline-flex *:min-h-header-height *:inline-flex *:items-center">

    @php
        $authUser = auth()->user();
        // Notifikasi dari tabel notifications milik user ini
        $notifItems = \App\Models\Notification::where('user_id', $authUser?->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        $notifUnread = \App\Models\Notification::where('user_id', $authUser?->id)
            ->where('is_read', false)
            ->count();
        $notifTotal = \App\Models\Notification::where('user_id', $authUser?->id)->count();
    @endphp
    <li class="dropdown pc-h-item">
      <a id="notif-bell-link" class="pc-head-link dropdown-toggle arrow-none me-0" data-pc-toggle="dropdown" href="#" role="button"
        aria-haspopup="false" aria-expanded="false">
        <i data-feather="bell"></i>
        @if($notifUnread > 0)
        <span id="notif-badge" class="badge bg-danger-500 text-white rounded-full z-10 absolute right-0 top-0" style="background:#ef4444!important;">{{ $notifUnread > 99 ? '99+' : $notifUnread }}</span>
        @endif
      </a>
      <div class="dropdown-menu dropdown-notification dropdown-menu-end pc-h-dropdown p-2">
        <div class="dropdown-header flex items-center justify-between py-4 px-5 border-b border-gray-100">
          <h5 class="m-0" style="color:#064635;">Notifikasi</h5>
          <div id="notif-mark-all-wrap" style="display:flex;gap:10px;align-items:center;{{ $notifUnread > 0 ? '' : 'display:none;' }}">
            @if($notifUnread > 0)
            <button type="button" id="btn-mark-all-read" style="background:none;border:none;cursor:pointer;font-size:12px;color:#064635;font-weight:600;padding:0;">Tandai dibaca</button>
            @endif
          </div>
        </div>
        <div id="notif-dropdown-body" class="dropdown-body header-notification-scroll relative py-2 px-0"
          style="max-height: calc(100vh - 215px)">
          @forelse($notifItems as $notif)
          <a href="{{ route('notifications.read', $notif->id) }}" class="block py-3 px-5 border-b border-gray-100" style="text-decoration:none;">
            <div>
              <div class="flex gap-3" style="align-items:center;">
                <div class="grow" style="min-width:0;">
                  <div style="font-size:13px;font-weight:{{ !$notif->is_read ? '600' : '500' }};color:{{ !$notif->is_read ? '#064635' : '#1f2937' }};white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $notif->title }}</div>
                  @if($notif->body)
                  <div style="font-size:11px;color:#6b7280;margin-top:2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $notif->body }}</div>
                  @endif
                </div>
                <div style="font-size:10px;color:#9ca3af;white-space:nowrap;flex-shrink:0;">{{ $notif->created_at?->diffForHumans(null, true) }}</div>
              </div>
            </div>
          </a>
          @empty
          <div style="text-align:center;padding:32px 16px;color:#9ca3af;">
            <i data-feather="bell-off" style="width:32px;height:32px;margin:0 auto 8px;display:block;"></i>
            <div style="font-size:13px;font-weight:500;">Tidak ada notifikasi</div>
          </div>
          @endforelse
        </div>
        @if($notifTotal > 3)
        <div id="notif-view-all-wrap" class="py-2 px-3" style="border-top:1px solid #f3f4f6;">
          <a href="{{ route('notifications.index') }}" style="font-size:12px;color:#064635;font-weight:600;text-decoration:underline; text-underline-offset:2px;">
            Lihat semua notifikasi &rarr;
          </a>
        </div>
        @else
        <div id="notif-view-all-wrap" class="py-2 px-3" style="border-top:1px solid #f3f4f6; display:none;">
          <a href="{{ route('notifications.index') }}" style="font-size:12px;color:#064635;font-weight:600;text-decoration:underline; text-underline-offset:2px;">
            Lihat semua notifikasi &rarr;
          </a>
        </div>
        @endif
      </div>
    </li>
    <li class="dropdown pc-h-item header-user-profile">
      <a class="pc-head-link dropdown-toggle arrow-none me-0" data-pc-toggle="dropdown" href="#" role="button"
        aria-haspopup="false" data-pc-auto-close="outside" aria-expanded="false">
        <i data-feather="user"></i>
      </a>
      <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown p-2 overflow-hidden">
        <div class="dropdown-header flex items-center justify-between py-4 px-5" style="background-color: #ffffff; border-bottom: 1px solid #f1f5f9;">
          <div class="flex mb-1 items-center">
            <div class="shrink-0">
              <div class="w-10 h-10 rounded-full flex items-center justify-center" style="background-color: #519259;">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              </div>
            </div>
            <div class="grow ms-3">
              <h6 class="mb-1" style="color: #064635; font-weight: 600;">{{ auth()->user()->nama ?? auth()->user()->username }}</h6>
              <span style="color: #64748b; font-size: 12px;">{{ auth()->user()->username }}</span>
            </div>
          </div>
        </div>
        <div class="dropdown-body py-4 px-5">
          <div class="profile-notification-scroll position-relative" style="max-height: calc(100vh - 225px)">
            <a href="{{ route('profile') }}" class="dropdown-item">
              <span>
                <svg class="pc-icon text-muted me-2 inline-block">
                  <use xlink:href="#custom-user"></use>
                </svg>
                <span>Informasi Saya</span>
              </span>
            </a>
            <a href="{{ route('profile.password') }}" class="dropdown-item">
              <span>
                <svg class="pc-icon text-muted me-2 inline-block">
                  <use xlink:href="#custom-lock-outline"></use>
                </svg>
                <span>Ganti Password</span>
              </span>
            </a>
            <div class="grid mt-7 mb-3">
              <a href="{{ route('logout.get') }}" class="btn flex items-center justify-center w-full text-white" style="background: linear-gradient(135deg, #064635 0%, #519259 100%); border: none;">
                <svg class="pc-icon me-2 w-[22px] h-[22px]">
                  <use xlink:href="#custom-logout-1-outline"></use>
                </svg>
                Logout
              </a>
            </div>
          </div>
        </div>
      </div>
    </li>
  </ul>
</div>

<script>
  (function () {
    const dropdownUrl = "{{ route('notifications.dropdown') }}";
    const readUrlTemplate = "{{ route('notifications.read', ['id' => '__ID__']) }}";
    const pollIntervalMs = 10000;

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
        const text = unread > 99 ? '99+' : String(unread);
        if (!badge) {
          badge = document.createElement('span');
          badge.id = 'notif-badge';
          badge.className = 'badge bg-danger-500 text-white rounded-full z-10 absolute right-0 top-0';
          badge.style.background = '#ef4444';
          badge.style.setProperty('background', '#ef4444', 'important');
          bell.appendChild(badge);
        }
        badge.textContent = text;
      } else if (badge) {
        badge.remove();
      }
    }

    function renderMarkAll(unread) {
      const wrap = document.getElementById('notif-mark-all-wrap');
      if (!wrap) return;
      wrap.style.display = unread > 0 ? 'flex' : 'none';
    }

    function renderViewAll(totalItems) {
      const wrap = document.getElementById('notif-view-all-wrap');
      if (!wrap) return;
      wrap.style.display = totalItems > 3 ? 'block' : 'none';
    }

    function buildItemHtml(item) {
      const id = String(item.id ?? '');
      const href = readUrlTemplate.replace('__ID__', id);
      const isUnread = !item.is_read;
      const titleStyle = isUnread ? 'font-weight:600;color:#064635;' : 'font-weight:500;color:#1f2937;';
      const title = escapeHtml(item.title || '-');
      const body = item.body ? `<div style="font-size:11px;color:#6b7280;margin-top:2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${escapeHtml(item.body)}</div>` : '';
      const time = escapeHtml(item.time || '');

      return `<a href="${href}" class="block py-3 px-5 border-b border-gray-100" style="text-decoration:none;">
        <div>
          <div class="flex gap-3" style="align-items:center;">
            <div class="grow" style="min-width:0;">
              <div style="font-size:13px;${titleStyle}white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${title}</div>
              ${body}
            </div>
            <div style="font-size:10px;color:#9ca3af;white-space:nowrap;flex-shrink:0;">${time}</div>
          </div>
        </div>
      </a>`;
    }

    function buildEmptyHtml() {
      return `<div style="text-align:center;padding:32px 16px;color:#9ca3af;">
        <i data-feather="bell-off" style="width:32px;height:32px;margin:0 auto 8px;display:block;"></i>
        <div style="font-size:13px;font-weight:500;">Tidak ada notifikasi</div>
      </div>`;
    }

    async function refreshNotifications() {
      if (document.hidden) return;

      try {
        const res = await fetch(dropdownUrl, {
          method: 'GET',
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          },
          cache: 'no-store',
          credentials: 'same-origin'
        });
        if (!res.ok) return;

        const data = await res.json();
        const unread = Number(data.unread || 0);
        const items = Array.isArray(data.items) ? data.items : [];

        renderBadge(unread);
        renderMarkAll(unread);
        renderViewAll(items.length);

        const bodyEl = document.getElementById('notif-dropdown-body');
        if (bodyEl) {
          bodyEl.innerHTML = items.length ? items.map(buildItemHtml).join('') : buildEmptyHtml();
        }

        if (window.feather && typeof window.feather.replace === 'function') {
          window.feather.replace();
        }
      } catch (e) {
        // Silent fail: UI will retry on next interval
      }
    }

    document.addEventListener('DOMContentLoaded', function () {
      refreshNotifications();
      setInterval(refreshNotifications, pollIntervalMs);
      document.addEventListener('visibilitychange', function () {
        if (!document.hidden) refreshNotifications();
      });

      // Tandai semua dibaca — AJAX, tanpa reload halaman
      document.addEventListener('click', function (e) {
        const btn = e.target.closest('#btn-mark-all-read');
        if (!btn) return;
        e.preventDefault();

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content
          || '{{ csrf_token() }}';

        btn.disabled = true;
        btn.style.opacity = '0.5';

        fetch("{{ route('notifications.mark-all') }}", {
          method: 'POST',
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
          },
          credentials: 'same-origin',
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            // Hapus badge angka di lonceng
            const badge = document.getElementById('notif-badge');
            if (badge) badge.remove();

            // Sembunyikan tombol "Tandai dibaca"
            const wrap = document.getElementById('notif-mark-all-wrap');
            if (wrap) wrap.style.display = 'none';

            // Update semua item di dropdown: hilangkan border kiri unread
            const body = document.getElementById('notif-dropdown-body');
            if (body) {
              body.querySelectorAll('a.card').forEach(card => {
                card.style.borderLeft = '3px solid transparent';
              });
            }
          }
        })
        .catch(() => {})
        .finally(() => {
          btn.disabled = false;
          btn.style.opacity = '';
        });
      });
    });
  })();
</script>