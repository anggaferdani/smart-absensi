<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>@yield('title')</title>
    <link href="{{ asset('tabler/dist/css/tabler.min.css?1692870487') }}" rel="stylesheet"/>
    <link href="{{ asset('tabler/dist/css/tabler-flags.min.css?1692870487') }}" rel="stylesheet"/>
    <link href="{{ asset('tabler/dist/css/tabler-payments.min.css?1692870487') }}" rel="stylesheet"/>
    <link href="{{ asset('tabler/dist/css/tabler-vendors.min.css?1692870487') }}" rel="stylesheet"/>
    <link href="{{ asset('tabler/dist/css/demo.min.css?1692870487') }}" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css">
    <style>
      @import url('https://rsms.me/inter/inter.css');
      :root {
        --tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
      }
      body {
        font-feature-settings: "cv03", "cv04", "cv11";
      }
      ::-webkit-resizer {
        display: none;
      }

      /* =====================================================
         GLOBAL EXPORT TOAST SYSTEM
         ===================================================== */
      #export-toast-container {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 99999;
        display: flex;
        flex-direction: column;
        gap: 10px;
        max-width: 340px;
      }
      .export-toast {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0,0,0,.15);
        padding: 14px 16px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
        border-left: 4px solid #ccc;
        animation: toastIn .25s ease;
      }
      .export-toast.toast-success { border-left-color: #2fb344; }
      .export-toast.toast-error   { border-left-color: #d63939; }
      .export-toast-icon { font-size: 18px; margin-top: 1px; flex-shrink: 0; }
      .export-toast.toast-success .export-toast-icon { color: #2fb344; }
      .export-toast.toast-error   .export-toast-icon { color: #d63939; }
      .export-toast-body { flex: 1; font-size: 13px; line-height: 1.5; }
      .export-toast-title { font-weight: 700; margin-bottom: 2px; font-size: 14px; }
      .export-toast-close {
        background: none; border: none; cursor: pointer;
        color: #aaa; font-size: 16px; padding: 0; line-height: 1;
        flex-shrink: 0; margin-top: 1px;
      }
      .export-toast-close:hover { color: #333; }
      @keyframes toastIn {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0); }
      }
    </style>
  </head>
  <body>
    <script src="{{ asset('tabler/dist/js/demo-theme.min.js?1692870487') }}"></script>

    {{-- Global toast container — muncul di semua halaman --}}
    <div id="export-toast-container"></div>

    <div class="page">
      <!-- Sidebar -->
      @include('templates.subtemplates.sidebar')
      <!-- Navbar -->
      @include('templates.subtemplates.navbar')
      <div class="page-wrapper">
        <!-- Page header -->
        <div class="page-header d-print-none">
          @yield('header')
        </div>
        <!-- Page body -->
        <div class="page-body">
          @yield('content')
        </div>
        @include('templates.subtemplates.footer')
      </div>
    </div>

    <script src="{{ asset('tabler/dist/libs/apexcharts/dist/apexcharts.min.js?1692870487') }}" defer></script>
    <script src="{{ asset('tabler/dist/libs/jsvectormap/dist/js/jsvectormap.min.js?1692870487') }}" defer></script>
    <script src="{{ asset('tabler/dist/libs/jsvectormap/dist/maps/world.js?1692870487') }}" defer></script>
    <script src="{{ asset('tabler/dist/libs/jsvectormap/dist/maps/world-merc.js?1692870487') }}" defer></script>
    <script src="{{ asset('tabler/dist/js/tabler.min.js?1692870487') }}" defer></script>
    <script src="{{ asset('tabler/dist/js/demo.min.js?1692870487') }}" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script type="text/javascript">
      $(document).ready(function () {
        $('form').on('submit', function () {
          $.LoadingOverlay("show");
          setTimeout(function () {
            $.LoadingOverlay("hide");
          }, 100000);
        });
      });
    </script>

    {{-- =====================================================
         GLOBAL TOAST SCRIPT
         Letakkan di sini supaya tersedia di semua halaman.
         Fungsi showToast / removeToast bisa dipanggil dari
         @push('scripts') halaman manapun.
         ===================================================== --}}
    <script>
      const LS_ACTIVE_TOASTS = 'absen_active_toasts';

      function _getActiveToasts() {
        try { return JSON.parse(localStorage.getItem(LS_ACTIVE_TOASTS) || '[]'); } catch (e) { return []; }
      }

      function _saveActiveToast(toast) {
        const toasts = _getActiveToasts();
        toasts.push(toast);
        localStorage.setItem(LS_ACTIVE_TOASTS, JSON.stringify(toasts));
      }

      function _removeActiveToast(toastId) {
        const toasts = _getActiveToasts().filter(t => t.id !== toastId);
        localStorage.setItem(LS_ACTIVE_TOASTS, JSON.stringify(toasts));
      }

      /**
       * Tampilkan toast notification.
       *
       * @param {string}      type        'success' | 'error'
       * @param {string}      title       Judul bold
       * @param {string}      message     Isi pesan (boleh HTML)
       * @param {string|null} downloadUrl Jika ada, tampilkan tombol Download
       * @param {string|null} deleteUrl   Endpoint untuk hapus file di server saat X diklik
       * @param {string|null} persistedId ID dari localStorage saat restore (internal)
       */
      function showToast(type, title, message, downloadUrl = null, deleteUrl = null, persistedId = null) {
        const container = document.getElementById('export-toast-container');
        if (!container) return;

        const id  = persistedId || ('toast-' + Date.now() + '-' + Math.random().toString(36).slice(2, 7));
        const cls = type === 'success' ? 'toast-success' : 'toast-error';
        const ico = type === 'success'
          ? '<i class="fa-solid fa-circle-check"></i>'
          : '<i class="fa-solid fa-circle-xmark"></i>';

        const colorMap = { success: '#2fb344', error: '#d63939' };
        const dlBtn = downloadUrl
          ? `<a href="${downloadUrl}" target="_blank"
               style="display:inline-flex;align-items:center;gap:5px;margin-top:6px;
                      padding:4px 12px;background:${colorMap[type] ?? '#2fb344'};color:#fff;
                      border-radius:5px;text-decoration:none;font-size:12px;font-weight:600">
               <i class="fa-solid fa-download"></i> Download
             </a>`
          : '';

        // encode deleteUrl ke data attribute supaya aman dari quote injection
        const deleteAttr = deleteUrl ? `data-delete-url="${deleteUrl}"` : '';

        container.insertAdjacentHTML('beforeend', `
          <div id="${id}" class="export-toast ${cls}">
            <div class="export-toast-icon">${ico}</div>
            <div class="export-toast-body">
              <div class="export-toast-title">${title}</div>
              <div>${message}</div>
              ${dlBtn}
            </div>
            <button class="export-toast-close" onclick="removeToast('${id}')" title="Tutup" ${deleteAttr}>
              <i class="fa-solid fa-times"></i>
            </button>
          </div>`);

        // Persist ke localStorage (skip jika ini adalah restore)
        if (!persistedId) {
          _saveActiveToast({ id, type, title, message, downloadUrl, deleteUrl });
        }
      }

      /**
       * Tutup toast: hapus dari DOM, localStorage, dan file di server (jika ada deleteUrl).
       */
      function removeToast(id) {
        const el = document.getElementById(id);
        const deleteUrl = el?.querySelector('.export-toast-close')?.dataset?.deleteUrl || null;

        if (el) el.remove();
        _removeActiveToast(id);

        // Hapus file dari server storage
        if (deleteUrl) {
          fetch(deleteUrl, {
            method: 'DELETE',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                              || '{{ csrf_token() }}',
              'Accept': 'application/json',
            },
          }).catch(() => {}); // fire-and-forget, gagal pun tidak masalah
        }
      }

      // Restore toasts yang belum di-close saat halaman load
      document.addEventListener('DOMContentLoaded', function () {
        _getActiveToasts().forEach(t => {
          showToast(t.type, t.title, t.message, t.downloadUrl, t.deleteUrl, t.id);
        });
      });
    </script>

    @stack('scripts')
  </body>
</html>