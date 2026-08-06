<script>
	document.documentElement.setAttribute('dir', 'ltr');
</script>
<!-- [Font] Family -->
<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600&display=swap" rel="stylesheet" />
<!-- Tailwind CDN - quick start (development/testing). Replace with compiled CSS for production. -->
<script src="https://cdn.tailwindcss.com"></script>
<!-- [phosphor Icons] https://phosphoricons.com/ -->
<link rel="stylesheet" href="{{ asset('assets/fonts/phosphor/duotone/style.css') }}" />
<!-- [Tabler Icons] https://tablericons.com -->
<link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}" />
<!-- [Feather Icons] https://feathericons.com -->
<link rel="stylesheet" href="{{ asset('assets/fonts/feather.css') }}" />
<!-- [Font Awesome Icons] https://fontawesome.com/icons -->
<link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome.css') }}" />
<!-- [Material Icons] https://fonts.google.com/icons -->
<link rel="stylesheet" href="{{ asset('assets/fonts/material.css') }}" />
<!-- SimpleBar CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/simplebar@6.2.5/dist/simplebar.min.css">
<!-- [Template CSS Files] -->
<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="main-style-link" />

<style>
	/* ===== COLOR SYSTEM OVERRIDE ===== */
	:root {
		--color-primary: #064635;
		--color-secondary: #519259;
		--color-primary-hover: #053a2c;
		--color-primary-light: rgba(6, 70, 53, 0.1);
		--color-bg: #f4faf5;
	}

	/* System background */
	body {
		background-color: #f4faf5 !important;
	}

	/* Global focus style */
	input:not([type="checkbox"]):not([type="radio"]):not([type="range"]):focus,
	select:focus,
	textarea:focus,
	.form-control:focus,
	.form-select:focus,
	.form-input:focus,
	.filter-select:focus,
	.page-jump-input:focus,
	.modal-input:focus,
	.field-input:focus {
		border-color: #064635 !important;
		box-shadow: 0 0 0 3px rgba(6, 70, 53, 0.1) !important;
		outline: none !important;
	}

	/* Search bar internal input */
	.search-box input[type="text"][id]:focus,
	.search-box input[type="text"][placeholder]:focus,
	.search-box input[type="search"][id]:focus,
	.search-box input[type="search"][placeholder]:focus {
		border-color: transparent !important;
		box-shadow: none !important;
		outline: none !important;
	}

	/* Global select dropdown styling */
	select.form-select,
	select.form-control.form-select,
	select.field-select {
		-webkit-appearance: none !important;
		-moz-appearance: none !important;
		appearance: none !important;
		background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%239ca3af' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E") !important;
		background-repeat: no-repeat !important;
		background-position: right 16px center !important;
		background-size: 16px 16px !important;
		padding-top: 10px !important;
		padding-bottom: 10px !important;
		padding-left: 16px !important;
		padding-right: 40px !important;
	}

	/* Premium Page Entry Transition */
	.pc-container {
		opacity: 0;
		transform: translateY(12px);
		animation: premiumPageFadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
		animation-delay: 0.1s;
	}

	@keyframes premiumPageFadeIn {
		to {
			opacity: 1;
			transform: translateY(0);
		}
	}

	/* Breadcrumb hover */
	.breadcrumb .breadcrumb-item:not(:last-child) a:hover {
		color: #064635 !important;
	}

	/* Footer */
	.pc-footer {
		position: relative;
		z-index: 1 !important;
	}
	.pc-footer p,
	.pc-footer span,
	.pc-footer a {
		color: #064635 !important;
	}
	.pc-footer a:hover {
		color: #053a2c !important;
	}

	.pc-container {
		position: relative;
		z-index: 100 !important;
	}
	.pc-sidebar {
		overflow: visible !important;
	}
	.pc-sidebar .navbar-wrapper {
		display: flex !important;
		flex-direction: column !important;
		height: 100vh !important;
		overflow: hidden !important;
	}
	.pc-sidebar .m-header {
		flex-shrink: 0 !important;
	}
	.pc-sidebar .navbar-content {
		flex: 1 !important;
		min-height: 0 !important;
		overflow-y: auto !important;
		height: auto !important;
	}
	.pc-sidebar .navbar-content::-webkit-scrollbar {
		width: 4px;
	}
	.pc-sidebar .navbar-content::-webkit-scrollbar-track {
		background: transparent;
	}
	.pc-sidebar .navbar-content::-webkit-scrollbar-thumb {
		background: rgba(255,255,255,0.2);
		border-radius: 4px;
	}

	/* ===== SIDEBAR COLOR OVERRIDE ===== */
	.pc-sidebar {
		background-color: #064635 !important;
		--tw-shadow: none !important;
		box-shadow: none !important;
	}

	/* Sidebar text — default putih */
	.pc-sidebar .pc-navbar .pc-link,
	.pc-sidebar .pc-navbar .pc-mtext {
		color: #ffffff !important;
	}
	.pc-sidebar .pc-navbar .pc-link i,
	.pc-sidebar .pc-navbar .pc-link svg {
		color: #ffffff !important;
	}
	/* Hover — background secondary, text tetap putih, kotak flat (radius 0) */
	.pc-sidebar .pc-navbar .pc-item:hover > .pc-link {
		background-color: #519259 !important;
		border-radius: 0px !important;
		color: #ffffff !important;
	}
	.pc-sidebar .pc-navbar .pc-item:hover > .pc-link::after {
		border-radius: 0px !important;
	}
	.pc-sidebar .pc-navbar .pc-item:hover > .pc-link i,
	.pc-sidebar .pc-navbar .pc-item:hover > .pc-link svg {
		color: #ffffff !important;
	}
	/* Active state — background secondary, text tetap putih, kotak flat (radius 0) */
	.pc-sidebar .pc-navbar > .pc-item.active > .pc-link {
		background-color: #519259 !important;
		border-radius: 0px !important;
		color: #ffffff !important;
		font-weight: 600;
		border-left: none !important;
	}
	.pc-sidebar .pc-navbar > .pc-item.active > .pc-link::after {
		border-radius: 0px !important;
	}
	.pc-sidebar .pc-navbar > .pc-item.active > .pc-link i,
	.pc-sidebar .pc-navbar > .pc-item.active > .pc-link svg {
		color: #ffffff !important;
	}
	/* Caption headers */
	.pc-sidebar .pc-navbar .pc-caption {
		color: rgba(255,255,255,0.5) !important;
		font-size: 11px !important;
	}

	/* Breadcrumb title — warna primary */
	.page-header .page-header-title h5,
	.page-header .page-header-title h4,
	.page-header .page-header-title h3 {
		color: #064635 !important;
	}

	/* Sidebar & Main Layout responsive fixes */
	@media (min-width: 1024px) {
		.pc-sidebar {
			left: 0px;
			width: 264px !important;
			transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1), left 0.2s cubic-bezier(0.4, 0, 0.2, 1), width 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
		}
		.pc-sidebar.pc-sidebar-hide {
			transform: translate3d(-264px, 0, 0) !important;
		}
		.pc-container {
			margin-left: 264px;
		}
		.pc-footer {
			margin-left: 264px;
		}
		.pc-header {
			left: 264px;
		}

		.pc-sidebar.pc-sidebar-hide ~ .pc-container,
		.pc-sidebar.pc-sidebar-hide ~ .pc-footer {
			margin-left: 0px;
		}
		.pc-sidebar.pc-sidebar-hide ~ .pc-header {
			left: 0px;
		}
	}

	@media not all and (min-width: 1024px) {
		.pc-sidebar {
			left: -264px;
		}
		.pc-sidebar.mob-sidebar-active {
			left: 0px;
		}
		.pc-container {
			margin-left: 0px;
		}
		.pc-footer {
			margin-left: 0px;
		}
		.pc-header {
			left: 0px;
		}
	}

	/* ===== GLOBAL COLOR OVERRIDES (replace #064635 everywhere) ===== */
	/* Accent color for checkboxes */
	input[type="checkbox"], input[type="radio"] {
		accent-color: #064635 !important;
	}

	/* Buttons & links that use system primary color */
	.btn-edit, .btn-primary-custom {
		background: linear-gradient(135deg, #519259 0%, #064635 100%) !important;
		color: #fff !important;
		border: none !important;
	}
	.btn-edit:hover, .btn-primary-custom:hover {
		background: linear-gradient(135deg, #519259 0%, #064635 100%) !important;
		filter: brightness(0.92);
	}

	/* Submit/save buttons — gradient */
	.btn-save, .btn-submit,
	button[type="submit"].btn-edit,
	button[type="submit"][style*="background: #064635"],
	button[type="submit"][style*="background:#064635"],
	input[type="submit"][style*="background: #064635"],
	input[type="submit"][style*="background:#064635"] {
		background: linear-gradient(135deg, #519259 0%, #064635 100%) !important;
		border: none !important;
		color: #fff !important;
	}

	/* Page header */
	.page-header-card, .dashboard-header {
		background: #064635 !important;
		box-shadow: none !important;
	}

	/* Pagination active */
	.page-btn.active {
		background: #064635 !important;
		color: #fff !important;
	}

	/* Pagination arrow buttons (prev/next) and non-active — bg hijau tipis */
	.page-btn:not(.active) {
		background: #f4faf5 !important;
	}

	/* Stat card decorative circle — semua warna secondary */
	.stat-card::before {
		background: #519259 !important;
	}

	/* Search focus */
	.search-box:focus-within {
		border-color: #064635 !important;
		box-shadow: 0 0 0 3px rgba(6, 70, 53, 0.1) !important;
	}
	.search-box:focus-within button i,
	.search-box:focus-within button svg {
		color: #064635 !important;
		stroke: #064635 !important;
	}
	/* Exclude X (clear) button — tetap merah */
	.search-box:focus-within button[style*="color:#ef4444"] i,
	.search-box:focus-within button[style*="color:#ef4444"] svg,
	.search-box:focus-within #clear-search-btn i,
	.search-box:focus-within #clear-search-btn svg {
		color: #ef4444 !important;
		stroke: #ef4444 !important;
	}

	/* Password toggle eye icon — primary saat field focus */
	.password-wrapper:focus-within .password-toggle,
	.password-wrapper:focus-within .password-toggle i,
	.password-wrapper:focus-within .password-toggle svg {
		color: #064635 !important;
		stroke: #064635 !important;
	}
	.form-input-wrapper:focus-within .password-toggle,
	.form-input-wrapper:focus-within .password-toggle i,
	.form-input-wrapper:focus-within .password-toggle svg {
		color: #064635 !important;
		stroke: #064635 !important;
	}

	/* Search box & filter button — bg hijau tipis sistem */
	.search-box {
		background: #ffffff !important;
		border: 1px solid #d1d5db !important;
	}
	.filter-btn {
		background: #ffffff !important;
		border: 1px solid #d1d5db !important;
		color: #4b5563 !important;
	}

	/* Global: replace all #f9fafb backgrounds with system green tint */
	table thead th,
	.modern-table thead th,
	.aset-table thead th,
	.nilai-inventory-table thead th,
	.kondisi-inventory-table thead th,
	.pending-table thead th {
		background: #f4faf5 !important;
	}
	/* Table row hover */
	table tbody tr:hover td,
	.modern-table tbody tr:hover td,
	.aset-table tbody tr:hover td {
		background: #e6f4ea !important;
	}

	/* Filter button hover */
	.filter-btn:hover {
		background: #064635 !important;
		border-color: #064635 !important;
		color: #fff !important;
	}

	/* Card headers */
	.d-card-header {
		color: #064635 !important;
	}
	/* Info/alert box icons — warna harus match text */
	.alert-info i, .alert-info svg,
	.info-box-blue i, .info-box-blue svg,
	div[style*="border: 1px solid #bfdbfe"] i,
	div[style*="border:1px solid #bfdbfe"] i,
	div[style*="border: 1px solid #93c5fd"] i,
	div[style*="border:1px solid #93c5fd"] i {
		color: #1e40af !important;
		stroke: #1e40af !important;
	}
	/* CRUD page header title (h5 next to icon) */
	.card-header-icon + div h5,
	.card-header-icon ~ div h5,
	.header-wrapper h5,
	.pc-content > div > div > div > div h5.mb-1 {
		color: #064635 !important;
	}

	/* Badge/pill with system color */
	.d-card-header span[style*="background:#064635"],
	.d-card-header span[style*="background: #064635"] {
		background: #064635 !important;
	}

	/* Pagination info strong */
	.pagination-info strong {
		color: #064635 !important;
	}

	/* Add button & any action primary buttons using gradient */
	.btn-add,
	.add-btn,
	.btn-primary-custom,
	[class*="btn"][style*="background: #064635"],
	[class*="btn"][style*="background:#064635"] {
		background: linear-gradient(135deg, #064635 0%, #519259 100%) !important;
		border: none !important;
		color: #ffffff !important;
		box-shadow: 0 4px 12px rgba(6, 70, 53, 0.2) !important;
		transition: all 0.3s ease !important;
	}
	.btn-add:hover,
	.add-btn:hover,
	.btn-primary-custom:hover,
	[class*="btn"][style*="background: #064635"]:hover,
	[class*="btn"][style*="background:#064635"]:hover {
		background: linear-gradient(135deg, #053a2c 0%, #46804d 100%) !important;
		transform: translateY(-2px) !important;
		box-shadow: 0 6px 20px rgba(6, 70, 53, 0.3) !important;
		color: #ffffff !important;
	}

	/* Spinner border */
	.table-loading .spinner {
		border-top-color: #064635 !important;
	}

	/* Links */
	a[style*="color:#064635"], a[style*="color: #064635"] {
		color: #064635 !important;
	}

	/* Header navbar icon hover & active states - no background wrapper, only secondary color for icon */
	.pc-header .pc-h-item .pc-head-link:hover,
	.pc-header .pc-h-item .pc-head-link:active,
	.pc-header .pc-h-item .pc-head-link.show,
	.pc-header .pc-h-item .pc-head-link.active,
	.pc-header .pc-h-item .pc-head-link[aria-expanded="true"],
	.pc-header .pc-h-item.dropdown.show .pc-head-link,
	.pc-header .pc-h-item.show .pc-head-link,
	.pc-header .pc-h-item:focus-within .pc-head-link {
		background: transparent !important;
		background-color: transparent !important;
		border-color: transparent !important;
		box-shadow: none !important;
		color: #519259 !important;
	}

	/* Target the pseudo-element background to keep it completely transparent */
	.pc-header .pc-h-item .pc-head-link:hover::after,
	.pc-header .pc-h-item .pc-head-link:active::after,
	.pc-header .pc-h-item .pc-head-link.show::after,
	.pc-header .pc-h-item .pc-head-link.active::after,
	.pc-header .pc-h-item .pc-head-link[aria-expanded="true"]::after,
	.pc-header .pc-h-item.dropdown.show .pc-head-link::after,
	.pc-header .pc-h-item.show .pc-head-link::after,
	.pc-header .pc-h-item:focus-within .pc-head-link::after {
		background: transparent !important;
		background-color: transparent !important;
		border-color: transparent !important;
		color: transparent !important;
		--tw-scale-x: 0 !important;
		--tw-scale-y: 0 !important;
	}

	.pc-header .pc-h-item .pc-head-link:hover svg,
	.pc-header .pc-h-item .pc-head-link:active svg,
	.pc-header .pc-h-item .pc-head-link.show svg,
	.pc-header .pc-h-item .pc-head-link.active svg,
	.pc-header .pc-h-item .pc-head-link[aria-expanded="true"] svg,
	.pc-header .pc-h-item.dropdown.show .pc-head-link svg,
	.pc-header .pc-h-item.show .pc-head-link svg,
	.pc-header .pc-h-item:focus-within .pc-head-link svg,
	.pc-header .pc-h-item .pc-head-link:hover i,
	.pc-header .pc-h-item .pc-head-link:active i,
	.pc-header .pc-h-item .pc-head-link.show i,
	.pc-header .pc-h-item .pc-head-link.active i,
	.pc-header .pc-h-item .pc-head-link[aria-expanded="true"] i,
	.pc-header .pc-h-item.dropdown.show .pc-head-link i,
	.pc-header .pc-h-item.show .pc-head-link i,
	.pc-header .pc-h-item:focus-within .pc-head-link i {
		color: #519259 !important;
		stroke: #519259 !important;
		fill: none !important;
	}

	.dropdown-user-profile .dropdown-item {
		transition: all 0.2s ease;
		border-radius: 0px !important;
	}
	.dropdown-user-profile .dropdown-item:hover {
		background-color: #519259 !important;
		color: #ffffff !important;
	}
	.dropdown-user-profile .dropdown-item:hover svg,
	.dropdown-user-profile .dropdown-item:hover .pc-icon,
	.dropdown-user-profile .dropdown-item:hover span {
		color: #ffffff !important;
		fill: #ffffff !important;
	}

	.pc-header .dropdown-menu.dropdown-user-profile,
	.pc-header .dropdown-menu.dropdown-notification,
	.pc-header .pc-h-dropdown.dropdown-user-profile,
	.pc-header .pc-h-dropdown.dropdown-notification,
	.dropdown-user-profile,
	.dropdown-notification {
		border-radius: 16px !important;
		overflow: hidden !important;
	}

	/* ===== GLOBAL TABLE HEADER STYLE (LIGHT GREEN BG + PRIMARY TEXT) ===== */
	table thead,
	.table thead,
	.modern-table thead,
	.jenis-inventory-table thead,
	.nilai-inventory-table thead,
	.kondisi-inventory-table thead,
	.pending-table thead {
		background-color: #f4faf5 !important;
	}
	table thead th,
	.table thead th,
	.modern-table thead th,
	.jenis-inventory-table thead th,
	.nilai-inventory-table thead th,
	.kondisi-inventory-table thead th,
	.pending-table thead th {
		background-color: #f4faf5 !important;
		color: #064635 !important;
	}
	table thead th a,
	.table thead th a,
	.modern-table thead th a,
	table thead th i,
	.table thead th i,
	.modern-table thead th i,
	table thead th svg,
	.table thead th svg,
	.modern-table thead th svg {
		color: #064635 !important;
		stroke: #064635 !important;
	}

	/* ===== GLOBAL BULK ACTION BAR STYLE ===== */
	#bulk-bar {
		background-color: #f4faf5 !important;
		background: #f4faf5 !important;
		border: none !important;
		border-bottom: 1px solid #e5e7eb !important;
		border-radius: 0 !important;
		margin: 0 !important;
		padding: 10px 20px !important;
	}
	#bulk-bar > span {
		color: #064635 !important;
	}
	/* Bulk delete button — samakan dengan btn-hapus-semua */
	#bulk-bar button[onclick*="bulkDelete"],
	#bulk-bar button[onclick*="DeleteConfirm"] {
		background: #fee2e2 !important;
		color: #dc2626 !important;
		border: 1px solid #fca5a5 !important;
		border-radius: 12px !important;
		padding: 8px 16px !important;
		font-weight: 600 !important;
	}
	#bulk-bar button[onclick*="bulkDelete"]:hover,
	#bulk-bar button[onclick*="DeleteConfirm"]:hover {
		background: #fecaca !important;
	}

	/* ===== HEADER NAVBAR (TOPBAR) COLOR OVERRIDE ===== */
	.pc-header {
		background-color: rgba(244, 250, 245, 0.8) !important;
		backdrop-filter: blur(8px) !important;
		-webkit-backdrop-filter: blur(8px) !important;
		border-bottom: none !important;
	}

	/* ===== GLOBAL STRIPED TABLE ROWS STYLE ===== */
	table tbody tr:nth-child(even),
	.table tbody tr:nth-child(even),
	.modern-table tbody tr:nth-child(even) {
		background-color: #f4faf5 !important;
	}
	table tbody tr:hover,
	.table tbody tr:hover,
	.modern-table tbody tr:hover {
		background-color: #e6efe9 !important;
	}

	/* Data card icon — seragam secondary di semua index */
	.data-card-icon {
		background: #519259 !important;
		border-radius: 12px !important;
	}
	.data-card-icon svg,
	.data-card-icon i {
		color: #fff !important;
		stroke: #fff !important;
	}

	/* ===== GLOBAL PAGINATION BAR PADDING OVERRIDE ===== */
	.pagination-bar {
		padding-top: 32px !important;
		padding-bottom: 32px !important;
		border-top: none !important;
	}
	.dash-pagination-bar {
		padding-top: 24px !important;
		padding-bottom: 24px !important;
	}

	/* Data card title — warna primary di semua index */
	.data-card-title h5,
	.data-card-title h4 {
		color: #064635 !important;
	}

	/* Filter button — default state (sebelum hover/aktif) tetap seperti semula */
	.filter-btn {
		background: #ffffff !important;
		border: 1px solid #d1d5db !important;
		color: #4b5563 !important;
		border-radius: 12px !important;
	}
	.filter-btn i,
	.filter-btn svg {
		color: #4b5563 !important;
		stroke: #4b5563 !important;
	}
	/* Filter btn hover — bg secondary, icon putih */
	.filter-btn:hover {
		background: #519259 !important;
		border-color: #519259 !important;
		color: #fff !important;
	}
	.filter-btn:hover i,
	.filter-btn:hover svg,
	.filter-btn.show i,
	.filter-btn.show svg {
		color: #fff !important;
		stroke: #fff !important;
	}
	/* Filter btn active/open state — bg secondary, icon putih */
	.filter-btn[aria-expanded="true"],
	.filter-btn.active,
	.filter-btn.show {
		background: #519259 !important;
		border-color: #519259 !important;
		color: #fff !important;
	}
	.filter-btn[aria-expanded="true"] i,
	.filter-btn[aria-expanded="true"] svg,
	.filter-btn.active i,
	.filter-btn.active svg,
	.filter-btn.show i,
	.filter-btn.show svg {
		color: #fff !important;
		stroke: #fff !important;
	}

	/* Filter dropdown panel — more padding */
	.filter-btn + .dropdown-menu,
	.dropdown-menu[style*="min-width: 240px"] {
		padding: 20px !important;
		border-radius: 12px !important;
	}

	/* Terapkan Filter button inside dropdown — gradient */
	.dropdown-menu button[onclick*="applyFilter"],
	.dropdown-menu button[onclick*="applyFilters"] {
		background: linear-gradient(135deg, #519259 0%, #064635 100%) !important;
		border: none !important;
		border-radius: 12px !important;
		color: #fff !important;
	}
</style>
