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
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow+Condensed:ital,wght@0,400;0,600;0,700;0,800;0,900;1,700&family=Noto+Sans+KR:wght@300;400;500;700&display=swap" rel="stylesheet">

<style>
/* ═══════════════════════════════════════════════════════
   PAC-RUN ADMIN — Design System Variables
═══════════════════════════════════════════════════════ */
:root {
    --pac-black:    #1A1212;
    --pac-black-2:  #211818;
    --pac-black-3:  #2C1F1F;
    --pac-yellow:   #E5AD16;
    --pac-yellow-d: #B8880D;
    --pac-yellow-l: #F0C040;
    --pac-pink:     #E80043;
    --pac-muted:    #8A7A6A;
    --pac-border:   rgba(229,173,22,.12);
    --pac-border-2: rgba(229,173,22,.25);
    --sidebar-w:    16rem;
}

/* ═══════════════════════════════════════════════════════
   Base Font
═══════════════════════════════════════════════════════ */
.fi, .fi * {
    font-family: 'Noto Sans KR', sans-serif;
    -webkit-font-smoothing: antialiased;
}

/* ═══════════════════════════════════════════════════════
   Top accent stripe — the finish line
═══════════════════════════════════════════════════════ */
body::before {
    content: '';
    position: fixed;
    top: 0; left: 0; right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--pac-yellow) 0%, var(--pac-yellow-l) 40%, var(--pac-pink) 100%);
    z-index: 9999;
}

/* ═══════════════════════════════════════════════════════
   Sidebar Container
═══════════════════════════════════════════════════════ */
.fi-sidebar {
    background: var(--pac-black) !important;
    border-right: 1px solid var(--pac-border) !important;
    box-shadow: 4px 0 24px rgba(0,0,0,.4) !important;
}

/* ═══════════════════════════════════════════════════════
   Sidebar Header — Brand
═══════════════════════════════════════════════════════ */
.fi-sidebar-header,
.fi-sidebar-header-ctn {
    background: var(--pac-black) !important;
    border-bottom: 1px solid var(--pac-border-2) !important;
    padding: 1.25rem 1rem !important;
    position: relative !important;
    overflow: hidden !important;
}

/* Decorative diagonal stripe behind brand */
.fi-sidebar-header::after {
    content: '';
    position: absolute;
    top: -40px; right: -20px;
    width: 80px; height: 120px;
    background: repeating-linear-gradient(
        -55deg,
        transparent, transparent 4px,
        rgba(229,173,22,.06) 4px, rgba(229,173,22,.06) 8px
    );
    pointer-events: none;
}

/* Brand name text */
.fi-sidebar-header a,
.fi-sidebar-header span,
.fi-sidebar-header-logo-ctn a,
.fi-sidebar-header-logo-ctn span {
    font-family: 'Bebas Neue', sans-serif !important;
    font-size: 1.4rem !important;
    letter-spacing: 0.08em !important;
    color: var(--pac-yellow) !important;
    text-shadow: 0 0 20px rgba(229,173,22,.3) !important;
    line-height: 1 !important;
    display: block !important;
}

/* Sub-label: ADMIN PANEL */
.fi-sidebar-header-logo-ctn::after {
    content: 'ADMIN PANEL';
    display: block;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 0.58rem;
    font-weight: 700;
    letter-spacing: 0.22em;
    color: var(--pac-muted);
    margin-top: 2px;
}

/* ═══════════════════════════════════════════════════════
   Navigation Groups
═══════════════════════════════════════════════════════ */
.fi-sidebar-group {
    padding: 0 0.5rem !important;
    margin-bottom: 0.25rem !important;
}

.fi-sidebar-group-label {
    font-family: 'Barlow Condensed', sans-serif !important;
    font-size: 0.62rem !important;
    font-weight: 700 !important;
    letter-spacing: 0.18em !important;
    text-transform: uppercase !important;
    color: var(--pac-muted) !important;
    padding: 0.6rem 0.75rem 0.3rem !important;
    display: flex !important;
    align-items: center !important;
    gap: 0.4rem !important;
}

/* Yellow pip before group label */
.fi-sidebar-group-label::before {
    content: '';
    display: inline-block;
    width: 12px; height: 2px;
    background: var(--pac-yellow);
    border-radius: 1px;
    flex-shrink: 0;
}

/* ═══════════════════════════════════════════════════════
   Navigation Items — Base
═══════════════════════════════════════════════════════ */
.fi-sidebar-item-btn {
    border-radius: 6px !important;
    transition: all 0.18s ease !important;
    position: relative !important;
    margin: 1px 0 !important;
    overflow: hidden !important;
}

.fi-sidebar-item-label {
    font-size: 0.82rem !important;
    font-weight: 500 !important;
    color: #8A8080 !important;
    transition: color 0.18s ease !important;
}

.fi-sidebar-item-icon {
    color: #5A5050 !important;
    transition: color 0.18s ease !important;
}

/* ── Hover ── */
.fi-sidebar-item-btn:hover {
    background-color: rgba(229,173,22,.08) !important;
}
.fi-sidebar-item-btn:hover .fi-sidebar-item-label {
    color: #D4A832 !important;
}
.fi-sidebar-item-btn:hover .fi-sidebar-item-icon {
    color: #D4A832 !important;
}

/* Hover left accent bar */
.fi-sidebar-item-btn::before {
    content: '';
    position: absolute;
    left: 0; top: 20%; bottom: 20%;
    width: 2px;
    background: var(--pac-yellow);
    border-radius: 0 2px 2px 0;
    opacity: 0;
    transition: opacity 0.18s ease, top 0.18s ease, bottom 0.18s ease;
}
.fi-sidebar-item-btn:hover::before {
    opacity: 0.6;
}

/* ── Active ── */
.fi-sidebar-item.fi-active .fi-sidebar-item-btn {
    background: linear-gradient(135deg, #E5AD16 0%, #C99210 100%) !important;
    box-shadow: 0 2px 12px rgba(229,173,22,.35) !important;
}
.fi-sidebar-item.fi-active .fi-sidebar-item-btn::before {
    opacity: 0;
}
.fi-sidebar-item.fi-active .fi-sidebar-item-label {
    color: var(--pac-black) !important;
    font-weight: 700 !important;
}
.fi-sidebar-item.fi-active .fi-sidebar-item-icon {
    color: var(--pac-black) !important;
}

/* ═══════════════════════════════════════════════════════
   Sidebar Footer
═══════════════════════════════════════════════════════ */
.fi-sidebar-footer {
    background: var(--pac-black) !important;
    border-top: 1px solid var(--pac-border) !important;
}
.fi-sidebar-footer,
.fi-sidebar-footer * {
    color: var(--pac-muted) !important;
    font-size: 0.8rem !important;
}
.fi-sidebar-footer button:hover *,
.fi-sidebar-footer a:hover * {
    color: var(--pac-yellow) !important;
}

/* ═══════════════════════════════════════════════════════
   Topbar
═══════════════════════════════════════════════════════ */
.fi-topbar {
    background: rgba(255,255,255,0.97) !important;
    border-bottom: 1px solid #EBEBEB !important;
    backdrop-filter: blur(8px) !important;
    box-shadow: 0 1px 8px rgba(0,0,0,.05) !important;
    padding-top: 3px !important; /* offset for body::before stripe */
}

/* ═══════════════════════════════════════════════════════
   Breadcrumbs
═══════════════════════════════════════════════════════ */
.fi-breadcrumbs ol li {
    font-size: 0.78rem !important;
    color: #888 !important;
}
.fi-breadcrumbs ol li:last-child {
    color: var(--pac-black) !important;
    font-weight: 600 !important;
}
.fi-breadcrumbs ol li a:hover {
    color: var(--pac-yellow-d) !important;
}

/* ═══════════════════════════════════════════════════════
   Page Heading
═══════════════════════════════════════════════════════ */
.fi-header-heading {
    font-family: 'Barlow Condensed', sans-serif !important;
    font-weight: 800 !important;
    font-size: 2rem !important;
    letter-spacing: 0.02em !important;
    color: var(--pac-black) !important;
    text-transform: uppercase !important;
}
.fi-header-subheading {
    font-size: 0.83rem !important;
    color: #888 !important;
    margin-top: 2px !important;
}

/* ═══════════════════════════════════════════════════════
   Sections / Cards
═══════════════════════════════════════════════════════ */
.fi-section {
    border: 1px solid #E8E8E8 !important;
    border-radius: 10px !important;
    box-shadow: 0 1px 4px rgba(0,0,0,.04) !important;
    transition: box-shadow 0.2s ease !important;
}
.fi-section:hover {
    box-shadow: 0 2px 12px rgba(0,0,0,.06) !important;
}
.fi-section-header {
    border-bottom: 1px solid #F0F0F0 !important;
    padding: 1rem 1.25rem 0.75rem !important;
}
.fi-section-header-heading {
    font-family: 'Barlow Condensed', sans-serif !important;
    font-weight: 700 !important;
    font-size: 0.92rem !important;
    letter-spacing: 0.08em !important;
    text-transform: uppercase !important;
    color: var(--pac-black) !important;
}
.fi-section-header-description {
    font-size: 0.78rem !important;
    color: #999 !important;
}

/* ═══════════════════════════════════════════════════════
   Table
═══════════════════════════════════════════════════════ */
.fi-ta {
    border: 1px solid #EBEBEB !important;
    border-radius: 10px !important;
    overflow: hidden !important;
    box-shadow: 0 1px 6px rgba(0,0,0,.04) !important;
}

/* Table header */
.fi-ta-header-row {
    background: var(--pac-black) !important;
}
.fi-ta-header-cell {
    font-family: 'Barlow Condensed', sans-serif !important;
    font-size: 0.7rem !important;
    font-weight: 700 !important;
    letter-spacing: 0.12em !important;
    text-transform: uppercase !important;
    color: #ACA0A0 !important;
    border-bottom: 2px solid var(--pac-yellow) !important;
    padding: 0.7rem 1rem !important;
}
.fi-ta-header-cell:first-child {
    border-left: 3px solid var(--pac-yellow) !important;
}

/* Column sort button */
.fi-ta-header-cell button {
    color: #ACA0A0 !important;
    gap: 4px !important;
}
.fi-ta-header-cell button:hover {
    color: var(--pac-yellow) !important;
}

/* Table rows */
.fi-ta-row {
    transition: background 0.12s ease !important;
    border-bottom: 1px solid #F5F5F5 !important;
}
.fi-ta-row:hover td {
    background: #FDFBF5 !important;
}
.fi-ta-row:last-child {
    border-bottom: none !important;
}

/* Striped rows */
.fi-ta-row:nth-child(even) td {
    background: #FAFAFA !important;
}
.fi-ta-row:nth-child(even):hover td {
    background: #FDFBF5 !important;
}

/* Table cells */
.fi-ta-cell {
    padding: 0.65rem 1rem !important;
    font-size: 0.83rem !important;
    color: #333 !important;
    vertical-align: middle !important;
}

/* Table action buttons (row level) */
.fi-ta-row-actions {
    display: flex !important;
    gap: 4px !important;
}

/* Table empty state */
.fi-ta-empty-state-icon {
    color: #CCC !important;
}
.fi-ta-empty-state-heading {
    font-family: 'Barlow Condensed', sans-serif !important;
    font-weight: 700 !important;
    letter-spacing: 0.06em !important;
    text-transform: uppercase !important;
}

/* ═══════════════════════════════════════════════════════
   Buttons
═══════════════════════════════════════════════════════ */
/* Primary button */
.fi-btn-color-primary {
    background: linear-gradient(135deg, var(--pac-yellow) 0%, var(--pac-yellow-d) 100%) !important;
    color: var(--pac-black) !important;
    font-family: 'Barlow Condensed', sans-serif !important;
    font-weight: 700 !important;
    letter-spacing: 0.06em !important;
    text-transform: uppercase !important;
    border: none !important;
    box-shadow: 0 2px 8px rgba(229,173,22,.3) !important;
    transition: all 0.2s ease !important;
}
.fi-btn-color-primary:hover {
    background: linear-gradient(135deg, var(--pac-yellow-l) 0%, var(--pac-yellow) 100%) !important;
    box-shadow: 0 4px 16px rgba(229,173,22,.45) !important;
    transform: translateY(-1px) !important;
}
.fi-btn-color-primary:active {
    transform: translateY(0) !important;
}

/* Danger button */
.fi-btn-color-danger {
    transition: all 0.15s ease !important;
}
.fi-btn-color-danger:hover {
    transform: translateY(-1px) !important;
}

/* Success button */
.fi-btn-color-success {
    transition: all 0.15s ease !important;
}

/* Gray / outline buttons */
.fi-btn-color-gray {
    font-weight: 600 !important;
    transition: all 0.15s ease !important;
}

/* ═══════════════════════════════════════════════════════
   Inputs / Forms
═══════════════════════════════════════════════════════ */
.fi-input,
.fi-fo-field-wrp input,
.fi-fo-field-wrp textarea,
.fi-fo-field-wrp select {
    border-color: #E0E0E0 !important;
    border-radius: 7px !important;
    transition: border-color 0.15s ease, box-shadow 0.15s ease !important;
    font-size: 0.85rem !important;
}
.fi-input:focus,
.fi-fo-field-wrp input:focus,
.fi-fo-field-wrp textarea:focus {
    border-color: var(--pac-yellow) !important;
    box-shadow: 0 0 0 3px rgba(229,173,22,.12) !important;
    outline: none !important;
}

/* Form label */
.fi-fo-field-wrp-label label {
    font-family: 'Barlow Condensed', sans-serif !important;
    font-weight: 700 !important;
    font-size: 0.78rem !important;
    letter-spacing: 0.08em !important;
    text-transform: uppercase !important;
    color: #555 !important;
}

/* Required asterisk */
.fi-fo-field-wrp-label .fi-fo-label-required-indicator {
    color: var(--pac-pink) !important;
}

/* Helper text */
.fi-fo-helper-text {
    font-size: 0.76rem !important;
    color: #AAA !important;
}

/* ═══════════════════════════════════════════════════════
   Badges
═══════════════════════════════════════════════════════ */
.fi-badge {
    font-family: 'Barlow Condensed', sans-serif !important;
    font-size: 0.7rem !important;
    font-weight: 700 !important;
    letter-spacing: 0.08em !important;
    text-transform: uppercase !important;
    border-radius: 4px !important;
}

/* ═══════════════════════════════════════════════════════
   Stats Overview Widget
═══════════════════════════════════════════════════════ */
.fi-wi-stats-overview-stat {
    border: 1px solid #EBEBEB !important;
    border-radius: 10px !important;
    overflow: hidden !important;
    position: relative !important;
    transition: transform 0.2s ease, box-shadow 0.2s ease !important;
}
.fi-wi-stats-overview-stat:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 6px 20px rgba(0,0,0,.08) !important;
}
/* Yellow top border on stat cards */
.fi-wi-stats-overview-stat::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    background: var(--pac-yellow);
}
.fi-wi-stats-overview-stat-value {
    font-family: 'Bebas Neue', sans-serif !important;
    font-size: 2.2rem !important;
    color: var(--pac-black) !important;
    letter-spacing: 0.03em !important;
    line-height: 1 !important;
}
.fi-wi-stats-overview-stat-label {
    font-family: 'Barlow Condensed', sans-serif !important;
    font-size: 0.72rem !important;
    font-weight: 700 !important;
    letter-spacing: 0.12em !important;
    text-transform: uppercase !important;
    color: var(--pac-muted) !important;
}
.fi-wi-stats-overview-stat-description {
    font-size: 0.78rem !important;
    color: #AAA !important;
}

/* ═══════════════════════════════════════════════════════
   Pagination
═══════════════════════════════════════════════════════ */
.fi-pagination {
    border-top: 1px solid #F0F0F0 !important;
    padding: 0.75rem 1rem !important;
}
.fi-pagination-item-btn[aria-current="page"] {
    background: var(--pac-yellow) !important;
    color: var(--pac-black) !important;
    font-weight: 700 !important;
    border-color: var(--pac-yellow) !important;
}
.fi-pagination-item-btn {
    transition: all 0.15s ease !important;
}
.fi-pagination-item-btn:hover {
    border-color: var(--pac-yellow) !important;
    color: var(--pac-yellow-d) !important;
}

/* ═══════════════════════════════════════════════════════
   Tabs
═══════════════════════════════════════════════════════ */
.fi-tabs-item-btn {
    font-family: 'Barlow Condensed', sans-serif !important;
    font-weight: 700 !important;
    letter-spacing: 0.06em !important;
    text-transform: uppercase !important;
    font-size: 0.82rem !important;
    transition: all 0.15s ease !important;
}
.fi-tabs-item-btn[aria-selected="true"],
.fi-tabs-item-btn.fi-active {
    color: var(--pac-yellow-d) !important;
    border-bottom-color: var(--pac-yellow) !important;
    border-bottom-width: 2px !important;
}

/* ═══════════════════════════════════════════════════════
   Modals
═══════════════════════════════════════════════════════ */
.fi-modal-window {
    border-radius: 12px !important;
    box-shadow: 0 20px 60px rgba(0,0,0,.2) !important;
}
.fi-modal-header {
    border-bottom: 1px solid #F0F0F0 !important;
    padding: 1.1rem 1.5rem !important;
}
.fi-modal-heading {
    font-family: 'Barlow Condensed', sans-serif !important;
    font-weight: 800 !important;
    font-size: 1.1rem !important;
    letter-spacing: 0.04em !important;
    text-transform: uppercase !important;
    color: var(--pac-black) !important;
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
    font-family: 'Barlow Condensed', sans-serif !important;
    font-weight: 700 !important;
    letter-spacing: 0.04em !important;
    text-transform: uppercase !important;
}

/* ═══════════════════════════════════════════════════════
   Dropdown Menu
═══════════════════════════════════════════════════════ */
.fi-dropdown-panel {
    border-radius: 10px !important;
    border: 1px solid #EBEBEB !important;
    box-shadow: 0 8px 24px rgba(0,0,0,.1) !important;
    overflow: hidden !important;
}
.fi-dropdown-header {
    font-family: 'Barlow Condensed', sans-serif !important;
    font-weight: 700 !important;
    font-size: 0.72rem !important;
    letter-spacing: 0.12em !important;
    text-transform: uppercase !important;
    color: #AAA !important;
    padding: 0.6rem 0.85rem !important;
}
.fi-dropdown-list-item-btn {
    font-size: 0.83rem !important;
    transition: all 0.12s ease !important;
}
.fi-dropdown-list-item-btn:hover {
    background: #FDFBF5 !important;
    color: var(--pac-yellow-d) !important;
}

/* ═══════════════════════════════════════════════════════
   Filters bar
═══════════════════════════════════════════════════════ */
.fi-ta-filters-dropdown-trigger {
    font-family: 'Barlow Condensed', sans-serif !important;
    font-weight: 700 !important;
    letter-spacing: 0.06em !important;
    text-transform: uppercase !important;
    font-size: 0.8rem !important;
}

/* ═══════════════════════════════════════════════════════
   Search input in table
═══════════════════════════════════════════════════════ */
.fi-ta-search-field input {
    border-radius: 7px !important;
    border: 1px solid #E0E0E0 !important;
    font-size: 0.83rem !important;
    transition: border-color 0.15s ease, box-shadow 0.15s ease !important;
}
.fi-ta-search-field input:focus {
    border-color: var(--pac-yellow) !important;
    box-shadow: 0 0 0 3px rgba(229,173,22,.1) !important;
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
    background: #3A2A2A;
    border-radius: 4px;
}
.fi-sidebar::-webkit-scrollbar-thumb:hover {
    background: var(--pac-yellow-d);
}
</style>
