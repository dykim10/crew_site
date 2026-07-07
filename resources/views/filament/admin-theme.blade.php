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
   PAC-RUN ADMIN — Light Console
   Archivo + Pretendard · pac-yellow accent
═══════════════════════════════════════════════════════ */
:root {
    --adm-accent:     #E5AD16;
    --adm-accent-d:   #C99813;
    --adm-accent-ink: #1A1212;
    --adm-accent2:    #E80043;
    --adm-ink:        #1A1212;
    --adm-muted:      #6B7280;
    --adm-muted-2:    #9CA3AF;
    --adm-muted-3:    #D1D5DB;
    --adm-bg:         #F4F4F5;
    --adm-surface:    #FFFFFF;
    --adm-surface-2:  #F9FAFB;
    --adm-border:     #E5E7EB;
    --adm-border-2:   #F3F4F6;
    --adm-side:       #FFFFFF;
    --adm-side-2:     #F9FAFB;
    --adm-line:       #E5E7EB;
    --adm-shadow:     0 1px 3px rgba(0,0,0,0.06);
    --adm-shadow-lg:  0 8px 24px rgba(0,0,0,0.08);
}

.fi, .fi * {
    font-family: 'Pretendard', -apple-system, sans-serif;
    -webkit-font-smoothing: antialiased;
}

/* ── Page ── */
.fi-main {
    background: var(--adm-bg) !important;
    max-width: 100% !important;
}
.fi-main-ctn { width: 100% !important; }

/* ── Sidebar ── */
.fi-sidebar {
    background: var(--adm-side) !important;
    border-right: 1px solid var(--adm-line) !important;
    box-shadow: none !important;
}
.fi-sidebar-header,
.fi-sidebar-header-ctn {
    background: var(--adm-side) !important;
    border-bottom: 1px solid var(--adm-line) !important;
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
    color: var(--adm-ink) !important;
    line-height: 1 !important;
    display: block !important;
}
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

.fi-sidebar-nav {
    padding: 0 !important;
}
.fi-sidebar-nav-groups {
    display: flex !important;
    flex-direction: column !important;
    gap: 0.625rem !important;
    padding: 0.75rem 0.65rem 1.25rem !important;
}

/* 그룹 카드 — 라벨 있는 그룹만 박스 처리 */
.fi-sidebar-group {
    list-style: none !important;
    margin: 0 !important;
    padding: 0 !important;
    gap: 0 !important;
}
.fi-sidebar-group:has(.fi-sidebar-group-label) {
    border: 1px solid var(--adm-border) !important;
    border-radius: 10px !important;
    background: var(--adm-surface-2) !important;
    overflow: hidden !important;
}
.fi-sidebar-group:has(.fi-sidebar-group-label) .fi-sidebar-group-btn {
    display: flex !important;
    align-items: center !important;
    padding: 0.5rem 0.75rem 0.35rem !important;
    margin: 0 !important;
    border-bottom: 1px solid var(--adm-border-2) !important;
    background: rgba(255,255,255,0.65) !important;
    cursor: default !important;
}
.fi-sidebar-group-label {
    font-family: 'Archivo', sans-serif !important;
    font-size: 0.64rem !important;
    font-weight: 700 !important;
    letter-spacing: 0.11em !important;
    text-transform: uppercase !important;
    color: var(--adm-muted) !important;
    padding: 0 !important;
    line-height: 1.2 !important;
}
.fi-sidebar-group-label::before { content: none !important; }
.fi-sidebar-group-collapse-btn { display: none !important; }

.fi-sidebar-group:has(.fi-sidebar-group-label) .fi-sidebar-group-items {
    padding: 0.35rem 0.4rem 0.45rem !important;
    gap: 1px !important;
}

/* 라벨 없는 그룹(대시보드 등) — 상단 단독 */
.fi-sidebar-group:not(:has(.fi-sidebar-group-label)) {
    padding: 0 0.15rem 0.35rem !important;
    margin-bottom: 0.15rem !important;
    border-bottom: 1px solid var(--adm-border) !important;
}
.fi-sidebar-group:not(:has(.fi-sidebar-group-label)) .fi-sidebar-group-items {
    padding: 0 !important;
}

.fi-sidebar-item-btn {
    border-radius: 7px !important;
    transition: all 0.15s ease !important;
    margin: 1px 0 !important;
    border-left: 2px solid transparent !important;
}
.fi-sidebar-item-label {
    font-size: 0.84rem !important;
    font-weight: 500 !important;
    color: #4B5563 !important;
}
.fi-sidebar-item-icon { color: #9CA3AF !important; }

.fi-sidebar-item-btn:hover { background: var(--adm-side-2) !important; }
.fi-sidebar-item-btn:hover .fi-sidebar-item-label { color: var(--adm-ink) !important; }
.fi-sidebar-item-btn:hover .fi-sidebar-item-icon { color: var(--adm-ink) !important; }
.fi-sidebar-item-btn::before { content: none !important; }

.fi-sidebar-item.fi-active .fi-sidebar-item-btn {
    background: rgba(229,173,22,0.1) !important;
    border-left-color: var(--adm-accent) !important;
}
.fi-sidebar-item.fi-active .fi-sidebar-item-label {
    color: var(--adm-accent-d) !important;
    font-weight: 600 !important;
}
.fi-sidebar-item.fi-active .fi-sidebar-item-icon { color: var(--adm-accent) !important; }

.fi-sidebar-footer {
    background: var(--adm-side) !important;
    border-top: 1px solid var(--adm-line) !important;
}
.fi-sidebar-footer, .fi-sidebar-footer * {
    color: var(--adm-muted) !important;
    font-size: 0.8rem !important;
}
.fi-sidebar-footer button:hover *, .fi-sidebar-footer a:hover * {
    color: var(--adm-accent-d) !important;
}
.fi-sidebar::-webkit-scrollbar { width: 4px; }
.fi-sidebar::-webkit-scrollbar-thumb {
    background: #D1D5DB;
    border-radius: 4px;
}
.fi-sidebar::-webkit-scrollbar-thumb:hover { background: var(--adm-accent); }

/* ── Topbar ── */
.fi-topbar {
    background: var(--adm-surface) !important;
    border-bottom: 1px solid var(--adm-line) !important;
    box-shadow: none !important;
}

/* ── Breadcrumbs & heading ── */
.fi-breadcrumbs ol li { font-size: 0.78rem !important; color: var(--adm-muted-2) !important; }
.fi-breadcrumbs ol li:last-child { color: var(--adm-ink) !important; font-weight: 600 !important; }
.fi-breadcrumbs ol li a:hover { color: var(--adm-accent-d) !important; }

.fi-header-heading {
    font-family: 'Archivo', sans-serif !important;
    font-weight: 700 !important;
    font-size: 1.4rem !important;
    letter-spacing: -0.01em !important;
    color: var(--adm-ink) !important;
}
.fi-header-subheading {
    font-size: 0.83rem !important;
    color: var(--adm-muted) !important;
    margin-top: 2px !important;
}

/* ── Sections ── */
html.fi .fi-section:not(.fi-section-not-contained):not(.fi-aside) {
    background: var(--adm-surface) !important;
    border: 1px solid var(--adm-border) !important;
    border-radius: 12px !important;
    box-shadow: var(--adm-shadow) !important;
    --tw-ring-shadow: 0 0 #0000 !important;
}
html.fi .fi-section > .fi-section-header {
    border-bottom: 1px solid var(--adm-border-2) !important;
    padding: 1rem 1.25rem 0.75rem !important;
}
html.fi .fi-section-header-heading,
.fi-section-header-heading {
    font-family: 'Archivo', sans-serif !important;
    font-weight: 700 !important;
    font-size: 0.92rem !important;
    color: var(--adm-ink) !important;
}
html.fi .fi-section-header-description,
.fi-section-header-description {
    font-size: 0.78rem !important;
    color: var(--adm-muted) !important;
}
html.fi .fi-section > .fi-section-content-ctn > .fi-section-content {
    padding: 1.25rem 1.5rem !important;
}
html.fi .fi-section > .fi-section-footer {
    border-top: 1px solid var(--adm-border) !important;
    background: var(--adm-surface-2) !important;
}
html.fi .fi-sc-section-label {
    font-family: 'Archivo', sans-serif !important;
    font-weight: 700 !important;
    font-size: 0.72rem !important;
    letter-spacing: 0.1em !important;
    text-transform: uppercase !important;
    color: var(--adm-muted-2) !important;
}

/* ── Tables ── */
.fi-ta {
    border: 1px solid var(--adm-border) !important;
    border-radius: 10px !important;
    overflow: hidden !important;
    box-shadow: var(--adm-shadow) !important;
    background: var(--adm-surface) !important;
}
.fi-ta-header-row { background: var(--adm-surface-2) !important; }
.fi-ta-header-cell {
    font-family: 'Archivo', sans-serif !important;
    font-size: 0.72rem !important;
    font-weight: 700 !important;
    letter-spacing: 0.04em !important;
    text-transform: uppercase !important;
    color: var(--adm-muted) !important;
    border-bottom: 1px solid var(--adm-border) !important;
    padding: 0.7rem 1.1rem !important;
}
.fi-ta-header-cell button { color: var(--adm-muted) !important; }
.fi-ta-header-cell button:hover { color: var(--adm-accent-d) !important; }
.fi-ta-row { border-bottom: 1px solid var(--adm-border-2) !important; }
.fi-ta-row:hover td { background: rgba(229,173,22,0.05) !important; }
.fi-ta-row:nth-child(even) td { background: #FAFAFA !important; }
.fi-ta-row:nth-child(even):hover td { background: rgba(229,173,22,0.05) !important; }
.fi-ta-cell {
    padding: 0.75rem 1.1rem !important;
    font-size: 0.84rem !important;
    color: var(--adm-ink) !important;
}
.fi-ta-empty-state-icon { color: var(--adm-muted-3) !important; }
.fi-ta-empty-state-heading {
    font-family: 'Archivo', sans-serif !important;
    font-weight: 700 !important;
    color: var(--adm-ink) !important;
}
.fi-ta-empty-state-description { color: var(--adm-muted) !important; }

/* ── Buttons ── */
.fi-btn-color-primary {
    background: var(--adm-accent) !important;
    color: var(--adm-accent-ink) !important;
    font-weight: 600 !important;
    border: none !important;
    box-shadow: none !important;
}
.fi-btn-color-primary:hover { background: var(--adm-accent-d) !important; }
.fi-btn-color-gray {
    font-weight: 500 !important;
    background: var(--adm-surface) !important;
    color: var(--adm-ink) !important;
    border: 1px solid var(--adm-border) !important;
}
.fi-btn-color-gray:hover {
    border-color: var(--adm-accent) !important;
    color: var(--adm-accent-d) !important;
    background: rgba(229,173,22,0.05) !important;
}

/* ── Form inputs ── */
html.fi .fi-input-wrp {
    background: var(--adm-surface) !important;
    border: 1px solid var(--adm-border) !important;
    border-radius: 8px !important;
    box-shadow: none !important;
    --tw-ring-shadow: 0 0 #0000 !important;
}
html.fi .fi-input-wrp:focus-within:not(.fi-disabled) {
    border-color: var(--adm-accent) !important;
    box-shadow: 0 0 0 3px rgba(229,173,22,0.15) !important;
}
html.fi .fi-input-wrp.fi-invalid:focus-within {
    border-color: #EF4444 !important;
    box-shadow: 0 0 0 3px rgba(239,68,68,0.12) !important;
}
html.fi .fi-input-wrp.fi-disabled {
    background: var(--adm-surface-2) !important;
    opacity: 0.7 !important;
}

html.fi .fi-input,
html.fi .fi-fo-field-wrp input,
html.fi .fi-fo-field-wrp textarea,
html.fi .fi-fo-textarea textarea,
html.fi .fi-select-input-btn,
html.fi .fi-fo-date-time-picker-display-text-input {
    background: transparent !important;
    color: var(--adm-ink) !important;
    font-size: 0.875rem !important;
}
html.fi .fi-fo-textarea textarea {
    min-height: 9rem;
    line-height: 1.65 !important;
    padding: 0.75rem 0.25rem !important;
}
html.fi input::placeholder,
html.fi textarea::placeholder,
html.fi .fi-select-input-placeholder {
    color: var(--adm-muted-3) !important;
}

html.fi .fi-fo-field-wrp-label label,
html.fi .fi-fo-field-label {
    font-weight: 600 !important;
    font-size: 0.82rem !important;
    color: #374151 !important;
}
html.fi .fi-fo-field-wrp-label .fi-fo-label-required-indicator {
    color: var(--adm-accent2) !important;
}
html.fi .fi-fo-helper-text {
    font-size: 0.76rem !important;
    color: var(--adm-muted) !important;
}

html.fi .fi-select-input-options-ctn,
html.fi .fi-dropdown-panel {
    background: var(--adm-surface) !important;
    border: 1px solid var(--adm-border) !important;
    box-shadow: var(--adm-shadow-lg) !important;
}
html.fi .fi-select-input-option:hover,
html.fi .fi-select-input-option.fi-selected {
    background: rgba(229,173,22,0.1) !important;
    color: var(--adm-accent-d) !important;
}

/* Checkbox list */
html.fi .adm-recipient-list .fi-fo-checkbox-list-options {
    max-height: 22rem;
    overflow-y: auto;
    padding: 0.75rem 1rem !important;
    border: 1px solid var(--adm-border) !important;
    border-radius: 10px !important;
    background: var(--adm-surface-2) !important;
    gap: 0.35rem 1.25rem !important;
}
html.fi .adm-recipient-list .fi-fo-checkbox-list-options::-webkit-scrollbar { width: 5px; }
html.fi .adm-recipient-list .fi-fo-checkbox-list-options::-webkit-scrollbar-thumb {
    background: #D1D5DB;
    border-radius: 4px;
}
html.fi .fi-fo-checkbox-list-option-label {
    color: var(--adm-ink) !important;
    font-size: 0.84rem !important;
}
html.fi .fi-checkbox-input:checked {
    background: var(--adm-accent) !important;
    border-color: var(--adm-accent) !important;
}

.adm-recipient-bar {
    display: flex;
    align-items: baseline;
    gap: 0.4rem;
    padding: 0.75rem 1rem;
    border-radius: 10px;
    border: 1px solid rgba(229,173,22,0.35);
    background: rgba(229,173,22,0.08);
}
.adm-recipient-bar__count {
    font-family: 'Archivo', sans-serif;
    font-size: 1.75rem;
    font-weight: 700;
    line-height: 1;
    color: var(--adm-accent-d);
}
.adm-recipient-bar__label { font-size: 0.84rem; color: var(--adm-muted); }

html.fi .fi-fo-date-time-picker-panel {
    background: var(--adm-surface) !important;
    border: 1px solid var(--adm-border) !important;
    box-shadow: var(--adm-shadow-lg) !important;
}
html.fi .fi-fo-date-time-picker-calendar-day.fi-selected {
    background: rgba(229,173,22,0.15) !important;
    color: var(--adm-accent-d) !important;
}

/* Form page layout */
html.fi .fi-resource-create-record-page .fi-page-content,
html.fi .fi-resource-edit-record-page .fi-page-content {
    max-width: 56rem;
}
html.fi .fi-resource-create-record-page .fi-ac,
html.fi .fi-resource-edit-record-page .fi-ac {
    margin-top: 0.5rem;
    padding: 1rem 1.25rem;
    border-radius: 12px;
    border: 1px solid var(--adm-border);
    background: var(--adm-surface);
    box-shadow: var(--adm-shadow);
}

/* ── Badges, stats, pagination ── */
.fi-badge {
    font-size: 0.72rem !important;
    font-weight: 600 !important;
    border-radius: 5px !important;
}
.fi-wi-stats-overview-stat {
    border: 1px solid var(--adm-border) !important;
    border-radius: 10px !important;
    background: var(--adm-surface) !important;
    box-shadow: var(--adm-shadow) !important;
    position: relative !important;
    overflow: hidden !important;
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
}
.fi-wi-stats-overview-stat-label {
    font-family: 'Archivo', sans-serif !important;
    font-size: 0.72rem !important;
    font-weight: 700 !important;
    letter-spacing: 0.1em !important;
    text-transform: uppercase !important;
    color: var(--adm-muted) !important;
}

.fi-pagination {
    border-top: 1px solid var(--adm-border) !important;
    background: var(--adm-surface) !important;
}
.fi-pagination-item-btn[aria-current="page"] {
    background: var(--adm-accent) !important;
    color: var(--adm-accent-ink) !important;
    border-color: var(--adm-accent) !important;
}
.fi-pagination-item-btn:hover {
    border-color: var(--adm-accent) !important;
    color: var(--adm-accent-d) !important;
}

.fi-tabs-item-btn[aria-selected="true"],
.fi-tabs-item-btn.fi-active {
    color: var(--adm-accent-d) !important;
    border-bottom-color: var(--adm-accent) !important;
}

/* ── Modals & dropdowns ── */
.fi-modal-window {
    border-radius: 12px !important;
    border: 1px solid var(--adm-border) !important;
    background: var(--adm-surface) !important;
    box-shadow: var(--adm-shadow-lg) !important;
}
.fi-modal-header, .fi-modal-footer {
    border-color: var(--adm-border) !important;
    background: var(--adm-surface) !important;
}
.fi-modal-heading {
    font-family: 'Archivo', sans-serif !important;
    font-weight: 700 !important;
    color: var(--adm-ink) !important;
}
.fi-no-notification {
    border: 1px solid var(--adm-border) !important;
    background: var(--adm-surface) !important;
    box-shadow: var(--adm-shadow-lg) !important;
}
.fi-dropdown-list-item-btn:hover {
    background: rgba(229,173,22,0.08) !important;
    color: var(--adm-accent-d) !important;
}

.fi-ta-search-field input {
    border: 1px solid var(--adm-border) !important;
    background: var(--adm-surface) !important;
    border-radius: 7px !important;
}
.fi-ta-search-field input:focus {
    border-color: var(--adm-accent) !important;
    box-shadow: 0 0 0 3px rgba(229,173,22,0.15) !important;
}

.fi-fo-rich-editor trix-editor {
    min-height: 320px;
    font-size: 0.9rem !important;
    line-height: 1.65 !important;
}
html.fi .fi-ta-image img {
    object-fit: contain;
}

/* ═══════════════════════════════════════════════════════
   Custom page utilities (adm-*)
═══════════════════════════════════════════════════════ */
.adm-stack { display: flex; flex-direction: column; gap: 1.25rem; }

.adm-alert {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
    padding: 0.875rem 1rem;
    border-radius: 10px;
    border: 1px solid var(--adm-border);
    background: var(--adm-surface);
    font-size: 0.84rem;
    line-height: 1.5;
}
.adm-alert strong { font-family: 'Archivo', sans-serif; font-weight: 700; color: var(--adm-ink); }
.adm-alert span { color: var(--adm-muted); }
.adm-alert--warning {
    border-color: rgba(229,173,22,0.45);
    background: rgba(229,173,22,0.08);
}
.adm-alert--warning strong { color: var(--adm-accent-d); }

.adm-card {
    border: 1px solid var(--adm-border);
    border-radius: 12px;
    background: var(--adm-surface);
    padding: 1.25rem 1.5rem;
    box-shadow: var(--adm-shadow);
}
.adm-card__title {
    font-family: 'Archivo', sans-serif;
    font-weight: 700;
    font-size: 1rem;
    color: var(--adm-ink);
    margin: 0 0 0.35rem;
}
.adm-card__desc { font-size: 0.82rem; color: var(--adm-muted); margin: 0 0 1rem; }

.adm-theme-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 1rem;
}
.adm-theme-tile {
    display: flex;
    flex-direction: column;
    overflow: hidden;
    border-radius: 12px;
    border: 1px solid var(--adm-border);
    background: var(--adm-surface);
    text-align: left;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
    cursor: pointer;
    box-shadow: var(--adm-shadow);
}
.adm-theme-tile:hover { border-color: rgba(229,173,22,0.45); }
.adm-theme-tile.is-active {
    border-color: var(--adm-accent);
    box-shadow: 0 0 0 1px var(--adm-accent);
}
.adm-theme-tile__preview { height: 10rem; position: relative; overflow: hidden; }
.adm-theme-tile__body {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    padding: 1rem 1.1rem;
    border-top: 1px solid var(--adm-border-2);
}
.adm-theme-tile__name {
    font-family: 'Archivo', sans-serif;
    font-weight: 700;
    font-size: 0.88rem;
    color: var(--adm-ink);
}
.adm-theme-tile__meta { font-size: 0.75rem; color: var(--adm-muted); margin-top: 0.2rem; }
.adm-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.2rem 0.55rem;
    border-radius: 999px;
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    background: rgba(229,173,22,0.15);
    color: var(--adm-accent-d);
}
.adm-hint {
    margin-top: 1rem;
    padding: 0.85rem 1rem;
    border-radius: 10px;
    border: 1px solid var(--adm-border);
    background: var(--adm-surface-2);
    font-size: 0.78rem;
    color: var(--adm-muted);
}
.adm-hint a { color: var(--adm-accent-d); text-decoration: underline; }

.adm-detail { display: flex; flex-direction: column; gap: 1rem; font-size: 0.84rem; color: var(--adm-ink); }
.adm-detail-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0.875rem 1.25rem; }
.adm-detail-label {
    font-family: 'Archivo', sans-serif;
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--adm-muted);
    margin-bottom: 0.25rem;
}
.adm-detail-value { font-weight: 500; color: var(--adm-ink); word-break: break-word; }
.adm-detail-divider { border: none; border-top: 1px solid var(--adm-border); margin: 0; }
.adm-detail-body { color: #374151; line-height: 1.6; white-space: pre-wrap; }
.adm-status {
    display: inline-flex;
    align-items: center;
    padding: 0.2rem 0.6rem;
    border-radius: 999px;
    font-size: 0.72rem;
    font-weight: 700;
}
.adm-status--approved  { background: #DCFCE7; color: #15803D; }
.adm-status--rejected  { background: #FEE2E2; color: #B91C1C; }
.adm-status--waitlisted { background: rgba(229,173,22,0.15); color: var(--adm-accent-d); }
.adm-status--default   { background: #F3F4F6; color: var(--adm-muted); }

.adm-table-wrap { overflow-x: auto; border-radius: 10px; border: 1px solid var(--adm-border); }
.adm-table { width: 100%; border-collapse: collapse; font-size: 0.82rem; background: var(--adm-surface); }
.adm-table thead { background: var(--adm-surface-2); }
.adm-table th {
    padding: 0.65rem 1rem;
    text-align: left;
    font-family: 'Archivo', sans-serif;
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: var(--adm-muted);
    border-bottom: 1px solid var(--adm-border);
}
.adm-table td {
    padding: 0.65rem 1rem;
    color: #374151;
    border-bottom: 1px solid var(--adm-border-2);
}
.adm-table tbody tr:hover td { background: rgba(229,173,22,0.04); }
.adm-table tbody tr:last-child td { border-bottom: none; }

.adm-error-box {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 0.875rem 1rem;
    border-radius: 10px;
    border: 1px solid #FECACA;
    background: #FEF2F2;
}
.adm-error-box strong { color: #B91C1C; font-weight: 600; }
.adm-error-box p { color: #DC2626; font-size: 0.82rem; margin: 0.2rem 0 0; }
.adm-empty { padding: 2.5rem 1rem; text-align: center; color: var(--adm-muted); font-size: 0.84rem; }

/* SMS detail modal */
.sd {
    font-family: 'Pretendard', sans-serif;
    color: var(--adm-ink);
    border: 1px solid var(--adm-border);
    border-top: 3px solid var(--adm-accent);
    border-radius: 10px;
    overflow: hidden;
    background: var(--adm-surface);
    box-shadow: var(--adm-shadow);
}
.sd-mono { font-family: ui-monospace, 'Cascadia Code', monospace; }
.sd-label { color: var(--adm-muted) !important; }
.sd-divider, .sd-stat, .sd-cell { border-color: var(--adm-border) !important; }
.sd-stat-num { color: var(--adm-ink) !important; }
.sd-stat-num.ok   { color: #15803D !important; }
.sd-stat-num.fail { color: #DC2626 !important; }
.sd-cell-val, .sd-msg { color: #374151 !important; }
.sd-msg-block { background: var(--adm-surface-2); border-bottom: 1px solid var(--adm-border); }
.sd-section-head {
    padding: 0.5rem 0.875rem;
    font-family: 'Archivo', sans-serif;
    font-size: 0.62rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--adm-muted);
    background: var(--adm-surface-2);
    border-bottom: 1px solid var(--adm-border);
}
.sd-table th {
    background: var(--adm-surface-2) !important;
    color: var(--adm-muted) !important;
    border-color: var(--adm-border) !important;
}
.sd-table td { border-color: var(--adm-border-2) !important; color: var(--adm-ink) !important; }
.sd-empty { padding: 1.5rem; text-align: center; font-size: 0.78rem; color: var(--adm-muted); }
.sd-status--delivered { background: #DCFCE7; color: #15803D; }
.sd-status--partial   { background: #FFEDD5; color: #C2410C; }
.sd-status--failed    { background: #FEE2E2; color: #B91C1C; }
.sd-status--default   { background: #F3F4F6; color: var(--adm-muted); }
.sd-ok   { color: #15803D; font-weight: 500; }
.sd-fail { color: #DC2626; }
.sd-muted { color: var(--adm-muted-2); }
</style>
