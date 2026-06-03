<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>조 편성 — {{ $event->name }} | PAC-RUN CREW 관리자</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Noto+Sans+KR:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
  --yellow: #E5AD16; --yellow2: #c99510; --pink: #E80043;
  --black: #1A1212;  --bg: #0D0D0D; --bg2: #111111;
  --bg3: #161616; --bg4: #1C1C1C;
  --border: rgba(255,255,255,0.07); --border2: rgba(255,255,255,0.12);
  --muted: rgba(255,255,255,0.40); --muted2: rgba(255,255,255,0.20);
  --white: #FFFFFF; --green: #16a34a;
}
html, body { height: 100%; overflow: hidden; }
body { font-family:'Noto Sans KR',system-ui,sans-serif; background:var(--bg2); color:var(--white); display:flex; flex-direction:column; }

/* ── HEADER ───────────────────────────────────────── */
.modal-head {
  display:flex; align-items:center; height:56px;
  border-bottom:1px solid var(--border2); flex-shrink:0;
}
.head-label {
  height:100%; padding:0 24px; display:flex; align-items:center; gap:10px;
  background:var(--yellow); flex-shrink:0;
}
.head-label-text { font-family:'Bebas Neue',sans-serif; font-size:20px; letter-spacing:3px; color:var(--black); }
.head-label-dot  { width:6px;height:6px;background:var(--black);border-radius:50%;opacity:.4; }
.head-meta { flex:1;display:flex;align-items:center;gap:20px;padding:0 24px; }
.head-event-name { font-family:'Bebas Neue',sans-serif;font-size:15px;letter-spacing:2px;color:rgba(255,255,255,.6); }
.head-status {
  font-family:'IBM Plex Mono',monospace;font-size:10px;font-weight:600;letter-spacing:2px;
  text-transform:uppercase;padding:3px 8px;
  background:rgba(22,163,74,.15);border:1px solid rgba(22,163,74,.4);color:#4ade80;
}
.head-sep { height:20px;width:1px;background:var(--border2); }
.head-gen { font-family:'IBM Plex Mono',monospace;font-size:11px;color:var(--muted);letter-spacing:1px; }
.head-back {
  height:100%;width:56px;display:flex;align-items:center;justify-content:center;
  cursor:pointer;border-left:1px solid var(--border);color:var(--muted);
  transition:all .15s;text-decoration:none;flex-shrink:0;
}
.head-back:hover { color:var(--white); background:rgba(255,255,255,.04); }

/* ── BRANCH TABS ──────────────────────────────────── */
.branch-tabs { display:flex;border-bottom:1px solid var(--border2);flex-shrink:0;background:var(--bg); }
.branch-tab {
  padding:0 28px;height:44px;display:flex;align-items:center;gap:8px;
  font-family:'IBM Plex Mono',monospace;font-size:11px;font-weight:600;letter-spacing:2px;
  text-transform:uppercase;color:var(--muted);cursor:pointer;
  border:none;background:transparent;border-bottom:2px solid transparent;
  transition:all .2s;position:relative;bottom:-1px;
}
.branch-tab:hover { color:rgba(255,255,255,.7); }
.branch-tab.active { color:var(--yellow);border-bottom-color:var(--yellow);background:rgba(229,173,22,.04); }
.branch-tab-count {
  font-family:'IBM Plex Mono',monospace;font-size:10px;padding:1px 6px;
  background:rgba(255,255,255,.07);border-radius:10px;min-width:22px;text-align:center;
}
.branch-tab.active .branch-tab-count { background:rgba(229,173,22,.15);color:var(--yellow); }

/* ── BODY ─────────────────────────────────────────── */
.modal-body { display:flex;flex:1;overflow:hidden; }

/* ── POOL ─────────────────────────────────────────── */
.pool-panel {
  width:220px;flex-shrink:0;background:var(--bg);border-right:1px solid var(--border2);
  display:flex;flex-direction:column;
}
.pool-head { padding:14px 16px 10px;border-bottom:1px solid var(--border);flex-shrink:0; }
.pool-head-label { font-family:'IBM Plex Mono',monospace;font-size:9px;font-weight:600;letter-spacing:3px;text-transform:uppercase;color:var(--muted);margin-bottom:6px; }
.pool-search {
  width:100%;background:var(--bg3);border:1px solid var(--border2);color:var(--white);
  padding:6px 10px;font-family:'Noto Sans KR',sans-serif;font-size:12px;outline:none;transition:border-color .15s;
}
.pool-search::placeholder { color:var(--muted2); }
.pool-search:focus { border-color:rgba(229,173,22,.5); }
.pool-list { flex:1;overflow-y:auto;padding:8px;display:flex;flex-direction:column;gap:4px; }
.pool-list::-webkit-scrollbar { width:3px; }
.pool-list::-webkit-scrollbar-thumb { background:rgba(255,255,255,.1);border-radius:2px; }

/* ── MEMBER CHIP ──────────────────────────────────── */
.member-chip {
  display:flex;align-items:center;gap:8px;padding:7px 10px;
  background:var(--bg3);border:1px solid var(--border);cursor:grab;
  transition:all .15s;user-select:none;
}
.member-chip:hover { border-color:rgba(229,173,22,.4);background:var(--bg4); }
.member-chip.sortable-ghost { opacity:.3;background:rgba(229,173,22,.08);border-color:rgba(229,173,22,.3); }
.member-chip.sortable-chosen {
  opacity:.9;box-shadow:0 8px 24px rgba(0,0,0,.6),0 0 0 1px rgba(229,173,22,.5);
  transform:rotate(-1deg) scale(1.02);z-index:999;
}
.member-id { font-family:'IBM Plex Mono',monospace;font-size:9px;font-weight:600;color:var(--muted2);min-width:22px; }
.member-name { font-size:12px;font-weight:600;color:var(--white);flex:1;white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }
.member-grade { font-family:'IBM Plex Mono',monospace;font-size:9px;font-weight:700;padding:1px 5px;border-radius:2px;flex-shrink:0; }
.grade-A { background:rgba(232,0,67,.15);color:#ff6b8a;border:1px solid rgba(232,0,67,.3); }
.grade-B { background:rgba(229,173,22,.12);color:var(--yellow);border:1px solid rgba(229,173,22,.3); }
.grade-C { background:rgba(255,255,255,.06);color:rgba(255,255,255,.5);border:1px solid rgba(255,255,255,.15); }
.drag-icon { display:flex;flex-direction:column;gap:2px;flex-shrink:0; }
.drag-icon span { width:12px;height:1px;background:var(--muted2);display:block; }
.member-chip:hover .drag-icon span { background:rgba(229,173,22,.5); }
.pool-empty { text-align:center;padding:32px 16px;font-size:11px;color:var(--muted2);line-height:1.7; }

/* ── GROUPS BOARD ─────────────────────────────────── */
.groups-board { flex:1;display:flex;flex-direction:column;overflow:hidden; }
.groups-toolbar {
  display:flex;align-items:center;justify-content:space-between;
  padding:10px 16px;border-bottom:1px solid var(--border);flex-shrink:0;gap:12px;
}
.toolbar-label { font-family:'IBM Plex Mono',monospace;font-size:9px;font-weight:600;letter-spacing:3px;text-transform:uppercase;color:var(--muted); }
.toolbar-stat {
  font-family:'IBM Plex Mono',monospace;font-size:10px;padding:2px 8px;
  background:rgba(229,173,22,.08);border:1px solid rgba(229,173,22,.2);color:rgba(229,173,22,.8);
}
.btn-add-group {
  display:flex;align-items:center;gap:6px;padding:6px 14px;border:1px solid rgba(229,173,22,.4);
  background:transparent;color:var(--yellow);font-family:'IBM Plex Mono',monospace;
  font-size:10px;font-weight:600;letter-spacing:2px;text-transform:uppercase;cursor:pointer;transition:all .15s;
}
.btn-add-group:hover { background:rgba(229,173,22,.1);border-color:var(--yellow); }
.groups-scroll {
  flex:1;overflow-x:auto;overflow-y:hidden;padding:16px;display:flex;gap:12px;align-items:flex-start;
}
.groups-scroll::-webkit-scrollbar { height:4px; }
.groups-scroll::-webkit-scrollbar-thumb { background:rgba(255,255,255,.1);border-radius:2px; }

/* ── GROUP COLUMN ─────────────────────────────────── */
.group-col {
  width:180px;flex-shrink:0;background:var(--bg3);border:1px solid var(--border);
  display:flex;flex-direction:column;transition:border-color .2s;
}
.group-col.drag-over { border-color:rgba(229,173,22,.6);background:rgba(229,173,22,.04); }
.group-col-head {
  padding:10px 12px 8px;border-bottom:1px solid var(--border);
  display:flex;align-items:center;gap:6px;flex-shrink:0;
}
.group-num { font-family:'Bebas Neue',sans-serif;font-size:16px;letter-spacing:1px;color:var(--yellow);line-height:1;flex:1; }
.group-count-badge {
  font-family:'IBM Plex Mono',monospace;font-size:9px;font-weight:700;
  padding:2px 6px;background:rgba(255,255,255,.06);border-radius:10px;color:var(--muted);
}
.group-col-actions { display:flex;gap:4px; }
.group-action-btn {
  width:22px;height:22px;display:flex;align-items:center;justify-content:center;
  background:transparent;border:1px solid var(--border);color:var(--muted);cursor:pointer;transition:all .15s;padding:0;
}
.group-action-btn:hover { color:var(--white);border-color:var(--border2); }
.group-action-btn.danger:hover { color:var(--pink);border-color:rgba(232,0,67,.4); }
.group-action-btn svg { width:10px;height:10px; }
.group-leader-tag {
  font-size:9px;color:var(--muted2);padding:2px 8px 4px 12px;border-bottom:1px solid var(--border);
  display:flex;align-items:center;gap:4px;flex-shrink:0;
  font-family:'IBM Plex Mono',monospace;letter-spacing:.5px;
}
.group-leader-name { color:rgba(229,173,22,.7);font-weight:600; }
.group-drop-zone {
  flex:1;min-height:120px;padding:6px;display:flex;flex-direction:column;gap:4px;transition:background .15s;
}
.group-drop-zone .member-chip { padding:5px 8px; }
.group-drop-zone .member-name { font-size:11px; }
.group-drop-zone .member-id  { font-size:8px;min-width:18px; }
.drop-hint-border {
  width:100%;height:100%;border:1px dashed rgba(255,255,255,.12);
  display:flex;flex-direction:column;align-items:center;justify-content:center;gap:6px;padding:16px 8px;
}
.drop-hint-text { font-family:'IBM Plex Mono',monospace;font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--muted2);text-align:center;pointer-events:none; }
.group-col.collapsed .group-drop-zone,.group-col.collapsed .group-leader-tag { display:none; }
.group-col.collapsed { width:56px;cursor:pointer; }
.group-col.collapsed .group-col-head { flex-direction:column;height:auto;padding:12px 8px;gap:8px; }
.group-col.collapsed .group-num { writing-mode:vertical-rl;font-size:14px; }
.group-col.collapsed .group-col-actions { flex-direction:column; }
.group-col.collapsed .group-action-btn.danger { display:none; }

/* ── FOOTER ───────────────────────────────────────── */
.modal-foot {
  display:flex;align-items:center;justify-content:space-between;
  padding:12px 20px;border-top:1px solid var(--border2);flex-shrink:0;background:var(--bg);gap:16px;
}
.foot-status { display:flex;align-items:center;gap:16px; }
.foot-stat-item { display:flex;align-items:center;gap:6px; }
.foot-stat-dot { width:6px;height:6px;border-radius:50%; }
.foot-stat-label { font-family:'IBM Plex Mono',monospace;font-size:10px;color:var(--muted);letter-spacing:1px; }
.foot-stat-num   { font-family:'IBM Plex Mono',monospace;font-size:10px;font-weight:700;color:var(--white); }
.foot-actions { display:flex;align-items:center;gap:8px; }
.btn-reset {
  padding:8px 18px;border:1px solid var(--border2);background:transparent;color:var(--muted);
  font-family:'IBM Plex Mono',monospace;font-size:10px;font-weight:600;letter-spacing:2px;
  text-transform:uppercase;cursor:pointer;transition:all .15s;
}
.btn-reset:hover { color:var(--white);border-color:var(--border2); }
.btn-save {
  padding:8px 28px;background:var(--yellow);color:var(--black);
  font-family:'Bebas Neue',sans-serif;font-size:17px;letter-spacing:2px;
  border:none;cursor:pointer;transition:opacity .15s;
  clip-path:polygon(6px 0,100% 0,calc(100% - 6px) 100%,0 100%);
  display:flex;align-items:center;gap:8px;
}
.btn-save:hover { opacity:.88; }
.btn-save:disabled { opacity:.4;cursor:not-allowed; }

/* ── LOADING ──────────────────────────────────────── */
.loading-mask {
  position:fixed;inset:0;background:rgba(0,0,0,.6);
  display:flex;align-items:center;justify-content:center;z-index:500;
}
.spinner { width:32px;height:32px;border:2px solid rgba(229,173,22,.3);border-top-color:var(--yellow);border-radius:50%;animation:spin .7s linear infinite; }
@keyframes spin { to { transform:rotate(360deg); } }

/* ── TOAST ────────────────────────────────────────── */
.toast {
  position:fixed;bottom:24px;right:24px;background:var(--bg3);
  border:1px solid rgba(229,173,22,.4);padding:10px 18px;
  font-size:12px;font-weight:600;color:var(--yellow);
  font-family:'IBM Plex Mono',monospace;letter-spacing:1px;z-index:9999;
  transform:translateY(60px);opacity:0;transition:all .3s cubic-bezier(.34,1.56,.64,1);
}
.toast.show { transform:translateY(0);opacity:1; }
.toast.error { border-color:rgba(232,0,67,.5);color:#ff6b8a; }
</style>
</head>
<body>
<!-- LOADING -->
<div class="loading-mask" id="loadingMask">
  <div class="spinner"></div>
</div>

<!-- HEAD -->
<div class="modal-head">
  <div class="head-label">
    <div class="head-label-dot"></div>
    <span class="head-label-text">조 편성</span>
    <div class="head-label-dot"></div>
  </div>
  <div class="head-meta">
    <span class="head-event-name">{{ $event->name }}</span>
    <div class="head-sep"></div>
    <span class="head-status">
      {{ $event->status === 'active' ? 'ACTIVE' : ($event->status === 'upcoming' ? 'UPCOMING' : 'ENDED') }}
    </span>
    <div class="head-sep"></div>
    <span class="head-gen">{{ $event->generation }}기 · {{ $event->start_date->format('Y.m.d') }} ~ {{ $event->end_date->format('Y.m.d') }}</span>
  </div>
  <a href="{{ url()->previous(route('filament.admin.resources.events.index')) }}" class="head-back">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M18 6 6 18M6 6l12 12"/>
    </svg>
  </a>
</div>

<!-- BRANCH TABS -->
<div class="branch-tabs" id="branchTabs">
  @foreach ($branches as $idx => $branch)
    <button class="branch-tab{{ $idx === 0 ? ' active' : '' }}"
            data-branch-id="{{ $branch->id }}"
            onclick="switchBranch({{ $branch->id }}, this)">
      {{ $branch->name }}
      <span class="branch-tab-count" id="tab-count-{{ $branch->id }}">0/0</span>
    </button>
  @endforeach
  @if($branches->isEmpty())
    <div style="padding:0 24px;display:flex;align-items:center;font-size:12px;color:var(--muted);">
      참여 기수가 설정되지 않았거나 지부 소속 구성원이 없습니다.
    </div>
  @endif
</div>

<!-- BODY -->
<div class="modal-body">
  <!-- LEFT: POOL -->
  <div class="pool-panel">
    <div class="pool-head">
      <div class="pool-head-label">미배정 인원</div>
      <input class="pool-search" type="text" placeholder="이름 검색" id="poolSearch" oninput="filterPool()">
    </div>
    <div class="pool-list" id="poolList"></div>
  </div>

  <!-- RIGHT: GROUPS -->
  <div class="groups-board">
    <div class="groups-toolbar">
      <div style="display:flex;align-items:center;gap:8px;">
        <span class="toolbar-label">조 편성</span>
        <span class="toolbar-stat" id="toolbarStat">0조 편성 중</span>
      </div>
      <button class="btn-add-group" onclick="addGroup()">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
        조 추가
      </button>
    </div>
    <div class="groups-scroll" id="groupsScroll"></div>
  </div>
</div>

<!-- FOOTER -->
<div class="modal-foot">
  <div class="foot-status" id="footStatus"></div>
  <div class="foot-actions">
    <button class="btn-reset" onclick="resetBranch()">초기화</button>
    <button class="btn-save" id="saveBtn" onclick="saveGroups()">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
        <polyline points="17 21 17 13 7 13 7 21"/>
        <polyline points="7 3 7 8 15 8"/>
      </svg>
      편성 저장
    </button>
  </div>
</div>

<div class="toast" id="toast"></div>

<script>
const EVENT_ID  = {{ $event->id }};
const SAVE_URL  = '{{ route('admin.events.groups.save', $event) }}';
const DATA_URL  = '{{ route('admin.events.groups.data', $event) }}';
const CSRF      = document.querySelector('meta[name="csrf-token"]').content;

// 지부별 상태
let currentBranchId = {{ $branches->isNotEmpty() ? $branches->first()->id : 'null' }};
let branchState     = {}; // { branchId: { allMembers, pool:[userId], groups:[{id,name,members:[]}] } }
let groupCounter    = {};
let poolSortable    = null;
let groupSortables  = {};

/* ── INIT ──────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
  if (currentBranchId) loadBranch(currentBranchId);
  else hideLoading();
});

/* ── LOAD BRANCH DATA ──────────────────────────────── */
async function loadBranch(branchId) {
  showLoading();
  try {
    const res  = await fetch(`${DATA_URL}?branch_id=${branchId}`, {
      headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
    });
    const data = await res.json();

    branchState[branchId] = {
      allMembers: data.members,   // [{id, name, grade}]
      pool: [],
      groups: data.groups.map(g => ({
        id:      g.id,
        name:    g.group_name,
        members: g.members,       // [userId]
      })),
    };

    // pool = allMembers 중 어느 그룹에도 없는 사용자
    const assigned = new Set(data.groups.flatMap(g => g.members));
    branchState[branchId].pool = data.members
      .filter(m => !assigned.has(m.id))
      .map(m => m.id);

    if (!groupCounter[branchId]) groupCounter[branchId] = data.groups.length;

    renderPool();
    renderGroups();
    updateFooter();
    updateTabCount(branchId);
  } catch (e) {
    showToast('데이터 로드 실패: ' + e.message, true);
  } finally {
    hideLoading();
  }
}

/* ── BRANCH SWITCH ─────────────────────────────────── */
function switchBranch(branchId, el) {
  document.querySelectorAll('.branch-tab').forEach(t => t.classList.remove('active'));
  el.classList.add('active');
  currentBranchId = branchId;

  if (branchState[branchId]) {
    renderPool(); renderGroups(); updateFooter();
  } else {
    loadBranch(branchId);
  }
}

/* ── MEMBER HELPER ─────────────────────────────────── */
function getMem(branchId, userId) {
  const s = branchState[branchId];
  return s ? s.allMembers.find(m => m.id === userId) : null;
}

/* ── POOL ──────────────────────────────────────────── */
function renderPool() {
  const s = branchState[currentBranchId];
  if (!s) return;
  const query = document.getElementById('poolSearch').value.trim().toLowerCase();
  const list  = document.getElementById('poolList');
  list.innerHTML = '';

  const filtered = s.pool.filter(id => {
    const m = getMem(currentBranchId, id);
    return !query || (m && m.name.includes(query));
  });

  if (filtered.length === 0) {
    const em = document.createElement('div');
    em.className = 'pool-empty';
    em.textContent = s.pool.length === 0 ? '전원 배정 완료' : '검색 결과 없음';
    list.appendChild(em);
    return;
  }

  filtered.forEach(id => {
    const m = getMem(currentBranchId, id);
    if (m) list.appendChild(createChip(m));
  });

  initPoolSortable();
}

function filterPool() { renderPool(); }

function createChip(m) {
  const el = document.createElement('div');
  el.className = 'member-chip';
  el.dataset.memberId = m.id;
  const idStr = String(m.id).slice(-2);
  const grade = m.grade || '';
  el.innerHTML = `
    <div class="drag-icon"><span></span><span></span><span></span></div>
    <span class="member-id">#${idStr}</span>
    <span class="member-name">${m.name}</span>
    ${grade ? `<span class="member-grade grade-${grade}">${grade}</span>` : ''}
  `;
  return el;
}

/* ── GROUPS ────────────────────────────────────────── */
function renderGroups() {
  const s      = branchState[currentBranchId];
  const scroll = document.getElementById('groupsScroll');
  scroll.innerHTML = '';
  if (!s) return;

  s.groups.forEach((group, gIdx) => scroll.appendChild(createGroupCol(s, group, gIdx)));
  updateToolbar();
}

function createGroupCol(s, group, gIdx) {
  const col = document.createElement('div');
  col.className = 'group-col';
  col.dataset.groupIdx = gIdx;

  const cnt      = group.members.length;
  const leaderId = group.members[0];
  const leader   = leaderId ? getMem(currentBranchId, leaderId) : null;

  col.innerHTML = `
    <div class="group-col-head">
      <span class="group-num">${group.name}</span>
      <span class="group-count-badge">${cnt}명</span>
      <div class="group-col-actions">
        <button class="group-action-btn" title="접기/펴기" onclick="toggleCollapse(this)">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 15l7-7 7 7"/></svg>
        </button>
        <button class="group-action-btn danger" title="조 삭제" onclick="removeGroup(${gIdx})">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
        </button>
      </div>
    </div>
    ${leader ? `<div class="group-leader-tag">
      <svg width="8" height="8" viewBox="0 0 24 24" fill="var(--yellow)"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87L18.18 21 12 17.77 5.82 21 7 14.14 2 9.27l6.91-1.01z"/></svg>
      <span>대장</span><span class="group-leader-name">${leader.name}</span>
    </div>` : ''}
    <div class="group-drop-zone${cnt === 0 ? '' : ''}" id="zone-${currentBranchId}-${gIdx}">
      ${cnt === 0 ? `<div class="drop-hint-border"><span class="drop-hint-text">드래그하여<br>배정</span></div>` : ''}
    </div>
  `;

  const zone = col.querySelector(`#zone-${currentBranchId}-${gIdx}`);
  group.members.forEach(uid => {
    const m = getMem(currentBranchId, uid);
    if (m) zone.appendChild(createChip(m));
  });

  return col;
}

function toggleCollapse(btn) {
  const col  = btn.closest('.group-col');
  const path = btn.querySelector('path');
  col.classList.toggle('collapsed');
  path.setAttribute('d', col.classList.contains('collapsed') ? 'M19 9l-7 7-7-7' : 'M5 15l7-7 7 7');
}

/* ── ADD / REMOVE GROUP ────────────────────────────── */
function addGroup() {
  const s = branchState[currentBranchId];
  if (!s) return;
  groupCounter[currentBranchId] = (groupCounter[currentBranchId] || 0) + 1;
  const no = groupCounter[currentBranchId];
  s.groups.push({ id: null, name: `${no}조`, members: [] });
  renderGroups();
  setTimeout(() => {
    const scroll = document.getElementById('groupsScroll');
    scroll.scrollLeft = scroll.scrollWidth;
  }, 50);
}

function removeGroup(gIdx) {
  if (!confirm('조를 삭제하면 구성원이 미배정으로 돌아갑니다. 삭제하시겠습니까?')) return;
  const s = branchState[currentBranchId];
  if (!s) return;
  const group = s.groups[gIdx];
  group.members.forEach(uid => { if (!s.pool.includes(uid)) s.pool.push(uid); });
  s.groups.splice(gIdx, 1);
  renderPool(); renderGroups(); updateFooter(); updateTabCount(currentBranchId);
}

function resetBranch() {
  if (!confirm('이 지부의 조 편성을 초기화하시겠습니까?')) return;
  const s = branchState[currentBranchId];
  if (!s) return;
  s.pool = s.allMembers.map(m => m.id);
  s.groups = [];
  groupCounter[currentBranchId] = 0;
  renderPool(); renderGroups(); updateFooter(); updateTabCount(currentBranchId);
  showToast('초기화 완료');
}

/* ── SORTABLE ──────────────────────────────────────── */
function initPoolSortable() {
  const poolEl = document.getElementById('poolList');
  if (poolSortable) { poolSortable.destroy(); poolSortable = null; }
  poolSortable = Sortable.create(poolEl, {
    group: { name: 'members', pull: true, put: true },
    animation: 150,
    ghostClass: 'sortable-ghost',
    chosenClass: 'sortable-chosen',
    onAdd(evt) {
      const uid = parseInt(evt.item.dataset.memberId);
      const s   = branchState[currentBranchId];
      s.groups.forEach(g => { const i = g.members.indexOf(uid); if (i !== -1) g.members.splice(i, 1); });
      if (!s.pool.includes(uid)) s.pool.push(uid);
      renderPool(); renderGroups(); updateFooter(); updateTabCount(currentBranchId);
    },
    onRemove(evt) {
      const uid = parseInt(evt.item.dataset.memberId);
      const s   = branchState[currentBranchId];
      const i   = s.pool.indexOf(uid);
      if (i !== -1) s.pool.splice(i, 1);
      updateFooter(); updateTabCount(currentBranchId);
    },
  });
}

function initGroupSortables() {
  const s = branchState[currentBranchId];
  if (!s) return;
  Object.values(groupSortables).forEach(so => so.destroy());
  groupSortables = {};

  s.groups.forEach((group, gIdx) => {
    const el = document.getElementById(`zone-${currentBranchId}-${gIdx}`);
    if (!el) return;
    groupSortables[gIdx] = Sortable.create(el, {
      group: { name: 'members', pull: true, put: true },
      animation: 150,
      ghostClass: 'sortable-ghost',
      chosenClass: 'sortable-chosen',
      filter: '.drop-hint-border',
      onAdd(evt) {
        const uid = parseInt(evt.item.dataset.memberId);
        // 1인 1조: 다른 그룹에서 제거
        s.groups.forEach((g, i) => {
          if (i !== gIdx) { const pos = g.members.indexOf(uid); if (pos !== -1) g.members.splice(pos, 1); }
        });
        if (!group.members.includes(uid)) {
          const chips = Array.from(el.querySelectorAll('.member-chip'));
          const pos   = chips.findIndex(c => parseInt(c.dataset.memberId) === uid);
          group.members.splice(pos !== -1 ? pos : group.members.length, 0, uid);
        }
        renderPool(); renderGroups(); updateFooter(); updateTabCount(currentBranchId);
      },
      onUpdate() {
        const ids = Array.from(el.querySelectorAll('.member-chip')).map(c => parseInt(c.dataset.memberId));
        group.members = ids;
        renderGroups();
      },
      onRemove(evt) {
        const uid = parseInt(evt.item.dataset.memberId);
        const i   = group.members.indexOf(uid);
        if (i !== -1) group.members.splice(i, 1);
        updateFooter(); updateTabCount(currentBranchId);
      },
    });
  });
}

const _origRenderGroups = renderGroups;
window.renderGroups = function() { _origRenderGroups(); setTimeout(initGroupSortables, 0); };

/* ── UI HELPERS ────────────────────────────────────── */
function updateFooter() {
  const s = branchState[currentBranchId];
  if (!s) { document.getElementById('footStatus').innerHTML = ''; return; }
  const total = s.allMembers.length;
  const unassigned = s.pool.length;
  const assigned   = total - unassigned;
  document.getElementById('footStatus').innerHTML = `
    <div class="foot-stat-item"><div class="foot-stat-dot" style="background:var(--green)"></div><span class="foot-stat-label">배정</span><span class="foot-stat-num">${assigned}</span></div>
    <div class="foot-stat-item"><div class="foot-stat-dot" style="background:rgba(255,255,255,.2)"></div><span class="foot-stat-label">미배정</span><span class="foot-stat-num">${unassigned}</span></div>
    <div class="foot-stat-item"><div class="foot-stat-dot" style="background:var(--yellow)"></div><span class="foot-stat-label">전체</span><span class="foot-stat-num">${total}</span></div>
  `;
}

function updateTabCount(branchId) {
  const s = branchState[branchId];
  if (!s) return;
  const el = document.getElementById(`tab-count-${branchId}`);
  if (el) el.textContent = `${s.allMembers.length - s.pool.length}/${s.allMembers.length}`;
}

function updateToolbar() {
  const s = branchState[currentBranchId];
  document.getElementById('toolbarStat').textContent = s ? `${s.groups.length}조 편성 중` : '0조 편성 중';
}

/* ── SAVE ──────────────────────────────────────────── */
async function saveGroups() {
  const s = branchState[currentBranchId];
  if (!s || s.groups.length === 0) { showToast('편성된 조가 없습니다', true); return; }

  const btn = document.getElementById('saveBtn');
  btn.disabled = true;
  showLoading();

  const payload = {
    branch_id: currentBranchId,
    groups: s.groups.map(g => ({ group_name: g.name, members: g.members })),
  };

  try {
    const res = await fetch(SAVE_URL, {
      method: 'POST',
      headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
      body: JSON.stringify(payload),
    });
    const data = await res.json();
    if (!res.ok) throw new Error(data.message || '저장 실패');
    showToast(data.message || '저장 완료');
    // 저장 후 최신 데이터 재로드
    delete branchState[currentBranchId];
    await loadBranch(currentBranchId);
  } catch (e) {
    showToast(e.message, true);
  } finally {
    btn.disabled = false;
    hideLoading();
  }
}

/* ── LOADING / TOAST ───────────────────────────────── */
function showLoading() { document.getElementById('loadingMask').style.display = 'flex'; }
function hideLoading() { document.getElementById('loadingMask').style.display = 'none'; }

function showToast(msg, isErr = false) {
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.className   = 'toast' + (isErr ? ' error' : '') + ' show';
  setTimeout(() => t.className = 'toast' + (isErr ? ' error' : ''), 2800);
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') history.back(); });
</script>
</body>
</html>
