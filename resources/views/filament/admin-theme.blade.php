<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700;800;900&family=Noto+Sans+KR:wght@400;500;700&display=swap" rel="stylesheet">

<style>
/* ── 전체 폰트 ─────────────────────────────────── */
.fi, .fi * {
    font-family: 'Noto Sans KR', sans-serif;
}

/* ── 사이드바 배경 ─────────────────────────────── */
.fi-sidebar {
    background-color: #1A1212 !important;
    border-right: 1px solid #2D2020 !important;
}
.fi-sidebar-header,
.fi-sidebar-header-ctn {
    background-color: #1A1212 !important;
    border-bottom: 1px solid #2D2020 !important;
}

/* 브랜드명 */
.fi-sidebar-header a,
.fi-sidebar-header span,
.fi-sidebar-header-logo-ctn a,
.fi-sidebar-header-logo-ctn span {
    font-family: 'Barlow Condensed', sans-serif !important;
    font-weight: 800 !important;
    font-size: 1.2rem !important;
    letter-spacing: 0.06em !important;
    color: #E5AD16 !important;
    text-transform: uppercase !important;
}

/* ── 네비게이션 그룹 레이블 ───────────────────── */
.fi-sidebar-group-label {
    font-family: 'Barlow Condensed', sans-serif !important;
    font-size: 0.68rem !important;
    font-weight: 700 !important;
    letter-spacing: 0.12em !important;
    text-transform: uppercase !important;
    color: #A47C0F !important;
}

/* ── 네비게이션 아이템 — 기본 ─────────────────── */
.fi-sidebar-item-btn {
    color: #A0A0A0 !important;
}
.fi-sidebar-item-label {
    color: #A0A0A0 !important;
}
.fi-sidebar-item-icon {
    color: #707070 !important;
}

/* ── 네비게이션 아이템 — hover ─────────────────── */
.fi-sidebar-item-btn:hover {
    background-color: #231818 !important;
}
.fi-sidebar-item-btn:hover .fi-sidebar-item-label,
.fi-sidebar-item-btn:hover .fi-sidebar-item-icon {
    color: #E5AD16 !important;
}

/* ── 네비게이션 아이템 — 활성 ─────────────────── */
.fi-sidebar-item.fi-active .fi-sidebar-item-btn {
    background-color: #E5AD16 !important;
}
.fi-sidebar-item.fi-active .fi-sidebar-item-label,
.fi-sidebar-item.fi-active .fi-sidebar-item-icon {
    color: #1A1212 !important;
    font-weight: 600 !important;
}

/* ── 사이드바 푸터 ─────────────────────────────── */
.fi-sidebar-footer {
    background-color: #1A1212 !important;
    border-top: 1px solid #2D2020 !important;
}
.fi-sidebar-footer,
.fi-sidebar-footer * {
    color: #707070 !important;
}
.fi-sidebar-footer button:hover *,
.fi-sidebar-footer a:hover * {
    color: #E5AD16 !important;
}

/* ── 페이지 헤딩 ───────────────────────────────── */
.fi-header-heading {
    font-family: 'Barlow Condensed', sans-serif !important;
    font-weight: 700 !important;
    font-size: 1.75rem !important;
    letter-spacing: 0.02em !important;
}

/* ── 테이블 헤더 ───────────────────────────────── */
.fi-ta-header-cell {
    font-family: 'Barlow Condensed', sans-serif !important;
    font-size: 0.75rem !important;
    font-weight: 700 !important;
    letter-spacing: 0.08em !important;
    text-transform: uppercase !important;
}

/* ── 섹션 헤딩 ─────────────────────────────────── */
.fi-section-header-heading {
    font-family: 'Barlow Condensed', sans-serif !important;
    font-weight: 700 !important;
    letter-spacing: 0.04em !important;
    text-transform: uppercase !important;
}

/* ── 글쓰기 에디터 height 조정 ─────────────────────────────────── */
.fi-fo-rich-editor trix-editor {
    min-height: 320px;
}
</style>
