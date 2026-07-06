<script src="{{ asset('js/flatpickr-ko.js') }}"></script>
<script>
document.addEventListener('input', function (e) {
    var el = e.target;
    if (!el || el.type !== 'number') return;
    if (!el.closest('.fi-fo-date-time-picker-time-inputs')) return;
    if (el.value.length > 2) el.value = el.value.slice(0, 2);
}, true);
</script>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard@v1.3.9/dist/web/static/pretendard.min.css">

<style>
/* ═══════════════════════════════════════════════════════
   PAC-RUN ADMIN — Design System Variables
   레이아웃/타이포(Archivo+Pretendard, flat 스타일)는 REVIEW와 동일,
   색상은 CREW 고유 브랜드(pac-yellow/pac-black) 유지
═══════════════════════════════════════════════════════ */
:root {
    --adm-side:      #1A1212;
    --adm-side-2:    #231818;
    --adm-line:      #2D2020;
    --adm-accent:    #E5AD16;
    --adm-accent-d:  #C99813;
    --adm-accent-ink: #1A1212;
    --adm-accent2:   #E80043;
    --adm-ink:       #1A1D24;
    --adm-muted:     #9CA3AF;
    --adm-muted-2:   #6B7280;
    --adm-muted-3:   #565D6B;
    --adm-bg:        #F1F2F5;
    --adm-border:    #E5E7EB;
}

/* ═══════════════════════════════════════════════════════
   Base Font
═══════════════════════════════════════════════════════ */
.fi, .fi * {
    font-family: 'Pretendard', -apple-system, sans-serif;
    -webkit-font-smoothing: antialiased;
}

/* ═══════════════════════════════════════════════════════
   Page background
═══════════════════════════════════════════════════════ */
.fi-main {
    background: var(--adm-bg) !important;
    max-width: 100% !important;
}

.fi-main-ctn {
    width: 100% !important;
}

/* ═══════════════════════════════════════════════════════
   Sidebar Container
═══════════════════════════════════════════════════════ */
.fi-sidebar {
    background: var(--adm-side) !important;
    border-right: none !important;
    box-shadow: none !important;
}

/* ═══════════════════════════════════════════════════════
   Sidebar Header — Brand
═══════════════════════════════════════════════════════ */
.fi-sidebar-header,
.fi-sidebar-header-ctn {
    background: var(--adm-side) !important;
    border-bottom: none !important;
    padding: 1.25rem 1.25rem 1rem !important;
}

.fi-sidebar-header a,
.fi-sidebar-header span,
.fi-sidebar-header-logo-ctn a,
.fi-sidebar-header-logo-ctn span {
    font-family: 'Archivo', sans-serif !important;
    font-weight: 700 !important;
    font-size: 1.1rem !important;
    letter-spacing: -0.01em !important;
    color: #fff !important;
    line-height: 1 !important;
    display: block !important;
}

/* Sub-label: ADMIN CONSOLE */
.fi-sidebar-header-logo-ctn::after {
    content: 'ADMIN CONSOLE';
    display: block;
    font-family: 'Archivo', sans-serif;
    font-size: 0.65rem;
    font-weight: 600;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--adm-muted-2);
    margin-top: 0.15rem;
}

/* ═══════════════════════════════════════════════════════
   Navigation Groups
═══════════════════════════════════════════════════════ */
.fi-sidebar-group {
    padding: 0 0.75rem !important;
    margin-bottom: 1.5rem !important;
}

.fi-sidebar-group-label {
    font-family: 'Archivo', sans-serif !important;
    font-size: 0.62rem !important;
    font-weight: 700 !important;
    letter-spacing: 0.14em !important;
    text-transform: uppercase !important;
    color: var(--adm-muted-3) !important;
    padding: 0.6rem 0.6rem 0.3rem !important;
}

.fi-sidebar-group-label::before {
    content: none !important;
}

/* ═══════════════════════════════════════════════════════
   Navigation Items — Base
═══════════════════════════════════════════════════════ */
.fi-sidebar-item-btn {
    border-radius: 7px !important;
    transition: all 0.15s ease !important;
    margin: 1px 0 !important;
    border-left: 2px solid transparent !important;
}

.fi-sidebar-item-label {
    font-size: 0.84rem !important;
    font-weight: 500 !important;
    color: #B0B5BE !important;
    transition: color 0.15s ease !important;
}

.fi-sidebar-item-icon {
    color: #6B7280 !important;
    transition: color 0.15s ease !important;
}

/* ── Hover ── */
.fi-sidebar-item-btn:hover {
    background-color: var(--adm-side-2) !important;
}
.fi-sidebar-item-btn:hover .fi-sidebar-item-label {
    color: #fff !important;
}
.fi-sidebar-item-btn:hover .fi-sidebar-item-icon {
    color: #fff !important;
}
.fi-sidebar-item-btn::before {
    content: none !important;
}

/* ── Active ── */
.fi-sidebar-item.fi-active .fi-sidebar-item-btn {
    background: var(--adm-side-2) !important;
    border-left-color: var(--adm-accent) !important;
    box-shadow: none !important;
}
.fi-sidebar-item.fi-active .fi-sidebar-item-label {
    color: #fff !important;
    font-weight: 600 !important;
}
.fi-sidebar-item.fi-active .fi-sidebar-item-icon {
    color: #fff !important;
}

/* ═══════════════════════════════════════════════════════
   Sidebar Footer
═══════════════════════════════════════════════════════ */
.fi-sidebar-footer {
    background: var(--adm-side) !important;
    border-top: 1px solid var(--adm-line) !important;
}
.fi-sidebar-footer,
.fi-sidebar-footer * {
    color: var(--adm-muted) !important;
    font-size: 0.8rem !important;
}
.fi-sidebar-footer button:hover *,
.fi-sidebar-footer a:hover * {
    color: #fff !important;
}

/* ═══════════════════════════════════════════════════════
   Topbar
═══════════════════════════════════════════════════════ */
.fi-topbar {
    background: #fff !important;
    border-bottom: 1px solid var(--adm-border) !important;
    box-shadow: none !important;
    padding-top: 0 !important;
}

/* ═══════════════════════════════════════════════════════
   Breadcrumbs
═══════════════════════════════════════════════════════ */
.fi-breadcrumbs ol li {
    font-size: 0.78rem !important;
    color: var(--adm-muted-2) !important;
}
.fi-breadcrumbs ol li:last-child {
    color: var(--adm-ink) !important;
    font-weight: 600 !important;
}
.fi-breadcrumbs ol li a:hover {
    color: var(--adm-accent) !important;
}

/* ═══════════════════════════════════════════════════════
   Page Heading
═══════════════════════════════════════════════════════ */
.fi-header-heading {
    font-family: 'Archivo', sans-serif !important;
    font-weight: 700 !important;
    font-size: 1.4rem !important;
    letter-spacing: -0.01em !important;
    color: var(--adm-ink) !important;
    text-transform: none !important;
}
.fi-header-subheading {
    font-size: 0.83rem !important;
    color: var(--adm-muted-2) !important;
    margin-top: 2px !important;
}

/* ═══════════════════════════════════════════════════════
   Sections / Cards
═══════════════════════════════════════════════════════ */
.fi-section {
    border: 1px solid var(--adm-border) !important;
    border-radius: 10px !important;
    box-shadow: none !important;
}
.fi-section-header {
    border-bottom: 1px solid #F0F0F0 !important;
    padding: 1rem 1.25rem 0.75rem !important;
}
.fi-section-header-heading {
    font-family: 'Archivo', sans-serif !important;
    font-weight: 700 !important;
    font-size: 0.92rem !important;
    letter-spacing: 0 !important;
    text-transform: none !important;
    color: var(--adm-ink) !important;
}
.fi-section-header-description {
    font-size: 0.78rem !important;
    color: var(--adm-muted) !important;
}

/* ═══════════════════════════════════════════════════════
   Table
═══════════════════════════════════════════════════════ */
.fi-ta {
    border: 1px solid var(--adm-border) !important;
    border-radius: 10px !important;
    overflow: hidden !important;
    box-shadow: none !important;
}

.fi-ta-header-row {
    background: #F9FAFB !important;
}
.fi-ta-header-cell {
    font-family: 'Archivo', sans-serif !important;
    font-size: 0.72rem !important;
    font-weight: 700 !important;
    letter-spacing: 0.04em !important;
    text-transform: uppercase !important;
    color: var(--adm-muted-2) !important;
    border-bottom: 1px solid var(--adm-border) !important;
    padding: 0.7rem 1.1rem !important;
}
.fi-ta-header-cell:first-child {
    border-left: none !important;
}

.fi-ta-header-cell button {
    color: var(--adm-muted-2) !important;
    gap: 4px !important;
}
.fi-ta-header-cell button:hover {
    color: var(--adm-accent) !important;
}

.fi-ta-row {
    transition: background 0.1s ease !important;
    border-bottom: 1px solid #F1F2F5 !important;
}
.fi-ta-row:hover td {
    background: #FAFBFC !important;
}
.fi-ta-row:last-child {
    border-bottom: none !important;
}

.fi-ta-row:nth-child(even) td {
    background: #FAFAFA !important;
}
.fi-ta-row:nth-child(even):hover td {
    background: #FAFBFC !important;
}

.fi-ta-cell {
    padding: 0.75rem 1.1rem !important;
    font-size: 0.84rem !important;
    color: var(--adm-ink) !important;
    vertical-align: middle !important;
}

.fi-ta-empty-state-icon {
    color: #CCC !important;
}
.fi-ta-empty-state-heading {
    font-family: 'Archivo', sans-serif !important;
    font-weight: 700 !important;
    letter-spacing: 0 !important;
    text-transform: none !important;
}

/* ═══════════════════════════════════════════════════════
   Buttons
═══════════════════════════════════════════════════════ */
.fi-btn-color-primary {
    background: var(--adm-accent) !important;
    color: var(--adm-accent-ink) !important;
    font-family: 'Pretendard', sans-serif !important;
    font-weight: 600 !important;
    letter-spacing: 0 !important;
    text-transform: none !important;
    border: none !important;
    box-shadow: none !important;
    transition: background 0.15s ease !important;
}
.fi-btn-color-primary:hover {
    background: var(--adm-accent-d) !important;
    box-shadow: none !important;
    transform: none !important;
}

.fi-btn-color-gray {
    font-weight: 500 !important;
    transition: all 0.15s ease !important;
}

/* ═══════════════════════════════════════════════════════
   Inputs / Forms
═══════════════════════════════════════════════════════ */
.fi-input,
.fi-fo-field-wrp input,
.fi-fo-field-wrp textarea,
.fi-fo-field-wrp select {
    border-color: #D1D5DB !important;
    border-radius: 7px !important;
    transition: border-color 0.15s ease, box-shadow 0.15s ease !important;
    font-size: 0.85rem !important;
}
.fi-input:focus,
.fi-fo-field-wrp input:focus,
.fi-fo-field-wrp textarea:focus {
    border-color: var(--adm-accent) !important;
    box-shadow: 0 0 0 3px rgba(229,173,22,.15) !important;
    outline: none !important;
}

.fi-fo-field-wrp-label label {
    font-family: 'Pretendard', sans-serif !important;
    font-weight: 600 !important;
    font-size: 0.8rem !important;
    letter-spacing: 0 !important;
    text-transform: none !important;
    color: #374151 !important;
}

.fi-fo-field-wrp-label .fi-fo-label-required-indicator {
    color: var(--adm-accent2) !important;
}

.fi-fo-helper-text {
    font-size: 0.76rem !important;
    color: var(--adm-muted) !important;
}

/* ═══════════════════════════════════════════════════════
   Badges
═══════════════════════════════════════════════════════ */
.fi-badge {
    font-family: 'Pretendard', sans-serif !important;
    font-size: 0.72rem !important;
    font-weight: 600 !important;
    letter-spacing: 0 !important;
    text-transform: none !important;
    border-radius: 5px !important;
}

/* ═══════════════════════════════════════════════════════
   Stats Overview Widget
═══════════════════════════════════════════════════════ */
.fi-wi-stats-overview-stat {
    border: 1px solid var(--adm-border) !important;
    border-radius: 10px !important;
    overflow: hidden !important;
    position: relative !important;
    transition: box-shadow 0.2s ease !important;
}
.fi-wi-stats-overview-stat::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    background: var(--adm-accent);
}
.fi-wi-stats-overview-stat-value {
    font-family: 'Archivo', sans-serif !important;
    font-size: 1.9rem !important;
    color: var(--adm-ink) !important;
    letter-spacing: -0.01em !important;
    line-height: 1 !important;
}
.fi-wi-stats-overview-stat-label {
    font-family: 'Archivo', sans-serif !important;
    font-size: 0.72rem !important;
    font-weight: 700 !important;
    letter-spacing: 0.1em !important;
    text-transform: uppercase !important;
    color: var(--adm-muted-2) !important;
}
.fi-wi-stats-overview-stat-description {
    font-size: 0.78rem !important;
    color: var(--adm-muted) !important;
}

/* ═══════════════════════════════════════════════════════
   Pagination
═══════════════════════════════════════════════════════ */
.fi-pagination {
    border-top: 1px solid #F0F0F0 !important;
    padding: 0.75rem 1rem !important;
}
.fi-pagination-item-btn[aria-current="page"] {
    background: var(--adm-accent) !important;
    color: var(--adm-accent-ink) !important;
    font-weight: 600 !important;
    border-color: var(--adm-accent) !important;
}
.fi-pagination-item-btn {
    transition: all 0.15s ease !important;
}
.fi-pagination-item-btn:hover {
    border-color: var(--adm-accent) !important;
    color: var(--adm-accent-d) !important;
}

/* ═══════════════════════════════════════════════════════
   Tabs
═══════════════════════════════════════════════════════ */
.fi-tabs-item-btn {
    font-family: 'Pretendard', sans-serif !important;
    font-weight: 600 !important;
    letter-spacing: 0 !important;
    text-transform: none !important;
    font-size: 0.85rem !important;
    transition: all 0.15s ease !important;
}
.fi-tabs-item-btn[aria-selected="true"],
.fi-tabs-item-btn.fi-active {
    color: var(--adm-accent) !important;
    border-bottom-color: var(--adm-accent) !important;
    border-bottom-width: 2px !important;
}

/* ═══════════════════════════════════════════════════════
   Modals
═══════════════════════════════════════════════════════ */
.fi-modal-window {
    border-radius: 12px !important;
    box-shadow: 0 20px 60px rgba(0,0,0,.15) !important;
}
.fi-modal-header {
    border-bottom: 1px solid #F0F0F0 !important;
    padding: 1.1rem 1.5rem !important;
}
.fi-modal-heading {
    font-family: 'Archivo', sans-serif !important;
    font-weight: 700 !important;
    font-size: 1.1rem !important;
    letter-spacing: 0 !important;
    text-transform: none !important;
    color: var(--adm-ink) !important;
}
.fi-modal-footer {
    border-top: 1px solid #F0F0F0 !important;
    padding: 0.9rem 1.5rem !important;
}

/* ═══════════════════════════════════════════════════════
   Notifications / Alerts
═══════════════════════════════════════════════════════ */
.fi-no-notification {
    border-radius: 10px !important;
    border: 1px solid rgba(0,0,0,.08) !important;
    box-shadow: 0 8px 24px rgba(0,0,0,.12) !important;
}
.fi-no-notification-title {
    font-family: 'Archivo', sans-serif !important;
    font-weight: 700 !important;
    letter-spacing: 0 !important;
    text-transform: none !important;
}

/* ═══════════════════════════════════════════════════════
   Dropdown Menu
═══════════════════════════════════════════════════════ */
.fi-dropdown-panel {
    border-radius: 10px !important;
    border: 1px solid var(--adm-border) !important;
    box-shadow: 0 8px 24px rgba(0,0,0,.1) !important;
    overflow: hidden !important;
}
.fi-dropdown-header {
    font-family: 'Archivo', sans-serif !important;
    font-weight: 700 !important;
    font-size: 0.72rem !important;
    letter-spacing: 0.1em !important;
    text-transform: uppercase !important;
    color: var(--adm-muted) !important;
    padding: 0.6rem 0.85rem !important;
}
.fi-dropdown-list-item-btn {
    font-size: 0.83rem !important;
    transition: all 0.12s ease !important;
}
.fi-dropdown-list-item-btn:hover {
    background: #FAFBFC !important;
    color: var(--adm-accent) !important;
}

/* ═══════════════════════════════════════════════════════
   Filters bar
═══════════════════════════════════════════════════════ */
.fi-ta-filters-dropdown-trigger {
    font-family: 'Pretendard', sans-serif !important;
    font-weight: 600 !important;
    letter-spacing: 0 !important;
    text-transform: none !important;
    font-size: 0.83rem !important;
}

/* ═══════════════════════════════════════════════════════
   Search input in table
═══════════════════════════════════════════════════════ */
.fi-ta-search-field input {
    border-radius: 7px !important;
    border: 1px solid #D1D5DB !important;
    font-size: 0.83rem !important;
    transition: border-color 0.15s ease, box-shadow 0.15s ease !important;
}
.fi-ta-search-field input:focus {
    border-color: var(--adm-accent) !important;
    box-shadow: 0 0 0 3px rgba(229,173,22,.15) !important;
}

/* ═══════════════════════════════════════════════════════
   Rich Editor
═══════════════════════════════════════════════════════ */
.fi-fo-rich-editor trix-editor {
    min-height: 320px;
    font-size: 0.9rem !important;
    line-height: 1.65 !important;
}

/* ═══════════════════════════════════════════════════════
   Loading overlay
═══════════════════════════════════════════════════════ */
[wire\:loading] {
    opacity: 0.7 !important;
    transition: opacity 0.15s ease !important;
}

/* ═══════════════════════════════════════════════════════
   Scrollbar styling (sidebar)
═══════════════════════════════════════════════════════ */
.fi-sidebar::-webkit-scrollbar {
    width: 4px;
}
.fi-sidebar::-webkit-scrollbar-track {
    background: transparent;
}
.fi-sidebar::-webkit-scrollbar-thumb {
    background: #2A2D36;
    border-radius: 4px;
}
.fi-sidebar::-webkit-scrollbar-thumb:hover {
    background: var(--adm-accent);
}
</style>
