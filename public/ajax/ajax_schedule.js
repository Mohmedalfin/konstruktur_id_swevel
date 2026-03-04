(function () {
  'use strict';

  /* ============================================================
     LAYOUT CONSTANTS (REVISI BIAR GA KEPOTONG)
  ============================================================ */
  const LEFT_W = 680;
  const NO_W = 60;
  const DATE_W = 120;           // 150 -> 120
  const BOBOT_W = 80;           // 90  -> 80
  const URAIAN_MIN_W = 180;     // 220 -> 180

  const state = {
    mode: null,
    currentId: null,
    collapsed: {},
    ganttHidden: false
  };

  const wrapper = document.getElementById('schedule-table-wrapper');
  const header = document.getElementById('schedule-header');
  const body = document.getElementById('schedule-body');
  const btnToggleGantt = document.getElementById('btnToggleGantt');

  if (!wrapper || !header || !body) return;

  /* ============================================================
     DUMMY DB (timeline dibuat DINAMIS dari range tanggal)
  ============================================================ */
  const dummyDatabase = {
    1: {
      timeline: null, // <-- DINAMIS
      categories: [
        {
          id: 'persiapan',
          name: 'Pekerjaan Persiapan',
          color: 'bg-blue-600',
          items: [
            { no: '1.1', uraian: 'Mobilisasi Alat & Tenaga', start: '02/02/2026', finish: '03/02/2026', durasi: '3 Hari', bobot: '0.80%', from: 1, to: 3 },
            { no: '1.2', uraian: 'Pembuatan Direksi Keet', start: '02/02/2026', finish: '06/02/2026', durasi: '5 Hari', bobot: '1.20%', from: 1, to: 2 },
            { no: '1.3', uraian: 'Pemasangan Papan Nama', start: '03/02/2026', finish: '03/02/2026', durasi: '1 Hari', bobot: '0.20%', from: 1, to: 1 }
          ]
        },
        {
          id: 'struktur',
          name: 'Pekerjaan Struktural',
          color: 'bg-emerald-600',
          items: [
            { no: '2.1', uraian: 'Pekerjaan Pondasi', start: '15/02/2026', finish: '05/03/2026', durasi: '19 Hari', bobot: '10%', from: 3, to: 6 },
            { no: '2.2', uraian: 'Pekerjaan Sloof', start: '06/03/2026', finish: '15/03/2026', durasi: '10 Hari', bobot: '7%', from: 6, to: 7 },
            { no: '2.3', uraian: 'Pekerjaan Kolom', start: '10/03/2026', finish: '10/04/2026', durasi: '32 Hari', bobot: '9%', from: 6, to: 10 }
          ]
        }
      ]
    }
  };

  function fetchScheduleData(id) {
    return new Promise(resolve => setTimeout(() => resolve(dummyDatabase[id] || null), 120));
  }

  /* ============================================================
     DATE + TIMELINE HELPERS
  ============================================================ */
  function pad2(n) { return String(n).padStart(2, '0'); }

  function parseDDMMYYYY(s) {
    if (!s || typeof s !== 'string') return null;
    const parts = s.split('/');
    if (parts.length !== 3) return null;
    const [dd, mm, yyyy] = parts.map(x => parseInt(x, 10));
    if (!dd || !mm || !yyyy) return null;
    const d = new Date(Date.UTC(yyyy, mm - 1, dd));
    if (d.getUTCFullYear() !== yyyy || d.getUTCMonth() !== (mm - 1) || d.getUTCDate() !== dd) return null;
    return d;
  }

  function formatDDMMYYYY(date) {
    if (!(date instanceof Date)) return '';
    return `${pad2(date.getUTCDate())}/${pad2(date.getUTCMonth() + 1)}/${date.getUTCFullYear()}`;
  }

  function toISODate(date) {
    if (!(date instanceof Date)) return '';
    return `${date.getUTCFullYear()}-${pad2(date.getUTCMonth() + 1)}-${pad2(date.getUTCDate())}`;
  }

  function isoToDate(iso) {
    if (!iso) return null;
    const [y, m, d] = iso.split('-').map(x => parseInt(x, 10));
    if (!y || !m || !d) return null;
    return new Date(Date.UTC(y, m - 1, d));
  }

  function diffDaysInclusive(startDate, endDate) {
    const ms = endDate.getTime() - startDate.getTime();
    const days = Math.floor(ms / 86400000) + 1;
    return Math.max(1, days);
  }

  function formatDateRange(start, end) {
    const opt = { day: '2-digit', month: 'short' };
    const s = start.toLocaleDateString('id-ID', opt);
    const e = end.toLocaleDateString('id-ID', opt);
    return `${s} – ${e}`;
  }

  function addDaysUTC(d, days) {
    const x = new Date(d);
    x.setUTCDate(x.getUTCDate() + days);
    return x;
  }

  // Senin sebagai start minggu
  function startOfWeekUTC(d) {
    const x = new Date(Date.UTC(d.getUTCFullYear(), d.getUTCMonth(), d.getUTCDate()));
    const day = x.getUTCDay(); // 0=minggu, 1=senin, ...
    const diffToMon = (day === 0 ? -6 : 1 - day);
    x.setUTCDate(x.getUTCDate() + diffToMon);
    return x;
  }

  function endOfWeekUTC(d) {
    return addDaysUTC(startOfWeekUTC(d), 6);
  }

  function getProjectDateRange(project) {
    let min = null;
    let max = null;

    (project.categories || []).forEach(cat => {
      (cat.items || []).forEach(it => {
        const s = parseDDMMYYYY(it.start);
        const e = parseDDMMYYYY(it.finish);
        if (s) min = (min === null || s < min) ? s : min;
        if (e) max = (max === null || e > max) ? e : max;
      });
    });

    if (!min || !max) return null;
    return { min, max };
  }

  // TIMELINE DINAMIS ikut range tanggal + bufferWeeks
  function buildTimelineAutoFromProject(project, bufferWeeks = 2) {
    const range = getProjectDateRange(project);
    if (!range) {
      // fallback kalau kosong
      return buildTimeline('2026-02-01', 10);
    }

    const bufferDays = bufferWeeks * 7;

    let start = startOfWeekUTC(addDaysUTC(range.min, -bufferDays));
    let end = endOfWeekUTC(addDaysUTC(range.max, bufferDays));

    const weeks = [];
    while (start <= end) {
      const wStart = new Date(start);
      const wEnd = addDaysUTC(wStart, 6);

      const monthNum = pad2(wStart.getUTCMonth() + 1);
      const year = String(wStart.getUTCFullYear());
      const monthLabel = wStart.toLocaleString('id-ID', { month: 'long' }).toUpperCase();

      weeks.push({
        start: toISODate(wStart),
        end: toISODate(wEnd),
        monthKey: `${year}-${monthNum}`,
        monthLabel,
        tooltip: formatDateRange(wStart, wEnd)
      });

      start = addDaysUTC(start, 7);
    }

    const monthGroups = [];
    let current = null;
    weeks.forEach(w => {
      if (!current || current.key !== w.monthKey) {
        current = { key: w.monthKey, label: w.monthLabel, count: 0 };
        monthGroups.push(current);
      }
      current.count++;
    });

    return { weeks, monthGroups };
  }

  // Fallback builder (kalau data kosong)
  function buildTimeline(startDate, weeksCount) {
    const start = new Date(startDate + 'T00:00:00Z');
    const weeks = [];

    for (let i = 0; i < weeksCount; i++) {
      const wStart = new Date(start);
      wStart.setUTCDate(start.getUTCDate() + (i * 7));
      const wEnd = new Date(wStart);
      wEnd.setUTCDate(wStart.getUTCDate() + 6);

      const monthNum = pad2(wStart.getUTCMonth() + 1);
      const year = String(wStart.getUTCFullYear());
      const monthLabel = wStart.toLocaleString('id-ID', { month: 'long' }).toUpperCase();

      weeks.push({
        start: wStart.toISOString().slice(0, 10),
        end: wEnd.toISOString().slice(0, 10),
        monthKey: `${year}-${monthNum}`,
        monthLabel,
        tooltip: formatDateRange(wStart, wEnd)
      });
    }

    const monthGroups = [];
    let current = null;
    weeks.forEach(w => {
      if (!current || current.key !== w.monthKey) {
        current = { key: w.monthKey, label: w.monthLabel, count: 0 };
        monthGroups.push(current);
      }
      current.count++;
    });

    return { weeks, monthGroups };
  }

  function weekIndexOfDate(timeline, dateUTC) {
    if (!(dateUTC instanceof Date)) return 1;
    const iso = toISODate(dateUTC);
    const idx = timeline.weeks.findIndex(w => iso >= w.start && iso <= w.end);
    if (idx >= 0) return idx + 1;
    if (iso < timeline.weeks[0].start) return 1;
    return timeline.weeks.length;
  }

  function recomputeAll(project) {
    if (!project || !project.timeline || !project.categories) return;
    const tl = project.timeline;

    project.categories.forEach(cat => {
      (cat.items || []).forEach(it => {
        const s = parseDDMMYYYY(it.start);
        const e = parseDDMMYYYY(it.finish);
        if (!s || !e) return;

        if (e.getTime() < s.getTime()) it.finish = it.start;

        const s2 = parseDDMMYYYY(it.start);
        const e2 = parseDDMMYYYY(it.finish);

        it.durasi = `${diffDaysInclusive(s2, e2)} Hari`;
        it.from = weekIndexOfDate(tl, s2);
        it.to = weekIndexOfDate(tl, e2);
      });
    });
  }

  function toNum(v) {
    const n = Number(v);
    return Number.isFinite(n) ? n : null;
  }

  function getCategoryColor(cat) {
    return cat?.color || 'bg-blue-600';
  }

  function getCategoryRange(items) {
    if (!Array.isArray(items) || items.length === 0) return null;
    let minFrom = null, maxTo = null;
    for (const it of items) {
      const f = toNum(it?.from);
      const t = toNum(it?.to);
      if (f === null || t === null) continue;
      if (minFrom === null || f < minFrom) minFrom = f;
      if (maxTo === null || t > maxTo) maxTo = t;
    }
    if (minFrom === null || maxTo === null) return null;
    return { from: minFrom, to: maxTo };
  }

  /* ============================================================
     LOADING
  ============================================================ */
  function renderLoading() {
    header.innerHTML = '';
    body.innerHTML = `
      <div class="p-10 text-center text-slate-500 text-sm">
        <svg class="animate-spin w-5 h-5 mx-auto mb-2 text-slate-400" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
        </svg>
        Memuat schedule…
      </div>
    `;
  }

  /* ============================================================
     HEADER
  ============================================================ */
  function renderHeader(timeline) {
    const weeks = timeline.weeks;
    const monthGroups = timeline.monthGroups;
    const weekCols = weeks.length;

    header.innerHTML = `
      <div class="grid" style="grid-template-columns: ${LEFT_W}px 1fr;">
        <div class="bg-slate-900 text-white px-4 py-3 text-xs md:text-sm font-bold rounded-tl-xl">
          <div class="grid items-center gap-3"
               style="grid-template-columns: ${NO_W}px minmax(${URAIAN_MIN_W}px, 1fr) ${DATE_W}px ${DATE_W}px ${BOBOT_W}px;">
            <div>No</div>
            <div>Uraian Pekerjaan</div>
            <div>Start</div>
            <div>Finish</div>
            <div class="text-right">Bobot (%)</div>
          </div>
        </div>

        <div class="bg-slate-900 text-white px-2 py-3 text-xs md:text-sm font-bold rounded-tr-xl">
          <div class="grid" style="grid-template-columns: repeat(${weekCols}, minmax(44px, 1fr));">
            ${monthGroups.map(mg => `
              <div class="text-center border-l border-white/20" style="grid-column: span ${mg.count};">
                ${mg.label}
              </div>
            `).join('')}
          </div>

          <div class="mt-2 grid text-[10px] md:text-xs opacity-90"
               style="grid-template-columns: repeat(${weekCols}, minmax(44px, 1fr));">
            ${weeks.map((w, idx) => `
              <div class="text-center border-l border-white/20 cursor-help" title="${w.tooltip}">
                W${(idx % 5) + 1}
              </div>
            `).join('')}
          </div>
        </div>
      </div>
    `;
  }

  /* ============================================================
     GANTT
  ============================================================ */
  function renderGanttGridOnly(weekCols) {
    return `
    <div class="relative px-2 py-3">
      <div 
        class="h-8"
        style="
          background-image: repeating-linear-gradient(
            to right,
            transparent,
            transparent calc(100% / ${weekCols} - 1px),
            #e2e8f0 calc(100% / ${weekCols} - 1px),
            #e2e8f0 calc(100% / ${weekCols})
          );
        ">
      </div>
    </div>
  `;
  }

  function renderGanttBar(weekCols, from, to, barClass, extraClass = '') {
    const f = Number(from);
    const t = Number(to);
    if (!f || !t) return renderGanttGridOnly(weekCols);

    return `
    <div class="relative px-2 py-3">
      <div 
        class="h-8"
        style="
          background-image: repeating-linear-gradient(
            to right,
            transparent,
            transparent calc(100% / ${weekCols} - 1px),
            #e2e8f0 calc(100% / ${weekCols} - 1px),
            #e2e8f0 calc(100% / ${weekCols})
          );
        ">
      </div>

      <div class="absolute inset-y-3 left-2 right-2 grid items-center"
           style="grid-template-columns: repeat(${weekCols}, 1fr);">
        <div class="h-4 ${barClass} ${extraClass}"
             style="grid-column: ${f} / ${t + 1};">
        </div>
      </div>
    </div>
  `;
  }
  /* ============================================================
     BODY
  ============================================================ */
  function renderBody(timeline, categories) {
    const weekCols = timeline.weeks.length;

    if (!categories || categories.length === 0) {
      body.innerHTML = `<div class="p-10 text-center text-slate-500 text-sm">Tidak ada data schedule.</div>`;
      return;
    }

    let html = '';
    let noKat = 1;

    categories.forEach(cat => {
      const isClosed = !!state.collapsed[cat.id];
      const subHidden = isClosed ? 'hidden' : '';
      const arrowRot = isClosed ? 'rotate-180' : '';
      const catColor = getCategoryColor(cat);

      const range = getCategoryRange(cat.items);
      const summaryBar = (!state.ganttHidden && range)
        ? renderGanttBar(weekCols, range.from, range.to, catColor, 'opacity-80')
        : renderGanttGridOnly(weekCols);

      html += `
        <!-- CATEGORY ROW -->
        <div class="grid border-t border-slate-200" style="grid-template-columns: ${LEFT_W}px 1fr;">
          <div class="bg-[#2E3E57] hover:bg-[#334764] transition-colors text-white px-4 py-3 overflow-hidden">
            <button class="flex w-full items-center justify-between cursor-pointer" data-toggle="${cat.id}">
              <div class="flex items-center gap-3 min-w-0">
                <div class="w-[60px] flex items-center justify-center gap-2 shrink-0">
                  <svg class="w-4 h-4 opacity-85 transition-transform duration-200 ${arrowRot}"
                       data-ico="${cat.id}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                  </svg>
                  <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-white/20 text-xs font-extrabold">
                    ${noKat}
                  </span>
                </div>

                <div class="font-extrabold tracking-widest text-sm uppercase truncate">
                  ${cat.name}
                </div>
              </div>
              <span class="w-4 h-4"></span>
            </button>
          </div>

          <div class="bg-white border-l border-slate-300">
            ${state.ganttHidden
          ? `<div class="px-2 py-3 text-slate-400 text-xs italic">Gantt disembunyikan</div>`
          : summaryBar}
          </div>
        </div>

        <!-- ITEMS -->
        <div data-body="${cat.id}" class="${subHidden}">
          ${(cat.items || []).map(it => renderItemRow(it, weekCols, catColor, cat.id)).join('')}
        </div>
      `;

      noKat++;
    });

    body.innerHTML = html;
    bindCategoryToggle();
    bindInlineDateEdit();
  }

  function renderItemRow(it, weekCols, catColor, catId) {
    const itemColor = catColor || 'bg-blue-600';

    const startDate = parseDDMMYYYY(it.start);
    const endDate = parseDDMMYYYY(it.finish);
    const startISO = startDate ? toISODate(startDate) : '';
    const endISO = endDate ? toISODate(endDate) : '';

    const ganttInner = state.ganttHidden
      ? `<div class="px-2 py-3 text-slate-400 text-xs italic">Gantt disembunyikan</div>`
      : renderGanttBar(weekCols, it.from, it.to, itemColor);

    return `
      <div class="grid border-t border-slate-200" style="grid-template-columns: ${LEFT_W}px 1fr;">
        <!-- LEFT -->
        <div class="px-4 py-3 text-sm overflow-hidden">
          <div class="grid items-center gap-3"
               style="grid-template-columns: ${NO_W}px minmax(${URAIAN_MIN_W}px, 1fr) ${DATE_W}px ${DATE_W}px ${BOBOT_W}px;">
            <div class="text-slate-500 whitespace-nowrap">${it.no || ''}</div>

            <div class="font-medium text-slate-800 min-w-0">
              <div class="truncate" title="${it.uraian || ''}">${it.uraian || ''}</div>
            </div>

            <div>
              <input
                type="date"
                class="w-full rounded-md border border-slate-200 px-2 py-1 text-[11px]
                       focus:outline-none focus:ring-2 focus:ring-slate-900/20"
                value="${startISO}"
                data-date="start"
                data-cat="${catId}"
                data-no="${it.no}"
              />
            </div>

            <div>
              <input
                type="date"
                class="w-full rounded-md border border-slate-200 px-2 py-1 text-[11px]
                       focus:outline-none focus:ring-2 focus:ring-slate-900/20"
                value="${endISO}"
                data-date="finish"
                data-cat="${catId}"
                data-no="${it.no}"
              />
            </div>

            <div class="text-right font-semibold text-slate-700 whitespace-nowrap">
              ${it.bobot || ''}
            </div>
          </div>
        </div>

        <!-- RIGHT (GANTT) -->
        <div class="bg-white border-l border-slate-300">
          ${ganttInner}
        </div>
      </div>
    `;
  }

  /* ============================================================
     EVENTS: ACCORDION
  ============================================================ */
  function bindCategoryToggle() {
    document.querySelectorAll('[data-toggle]').forEach(btn => {
      btn.addEventListener('click', () => {
        const id = btn.getAttribute('data-toggle');
        const bodyEl = document.querySelector(`[data-body="${id}"]`);
        const ico = document.querySelector(`[data-ico="${id}"]`);
        if (!bodyEl) return;

        const wasHidden = bodyEl.classList.contains('hidden');
        bodyEl.classList.toggle('hidden', !wasHidden);

        const nowHidden = bodyEl.classList.contains('hidden');
        if (ico) ico.classList.toggle('rotate-180', nowHidden);

        state.collapsed[id] = nowHidden;
      });
    });
  }

  /* ============================================================
     EVENTS: INLINE DATE EDIT
     - Update timeline otomatis setiap user ubah tanggal
  ============================================================ */
  function bindInlineDateEdit() {
    body.querySelectorAll('input[type="date"][data-date]').forEach(inp => {
      inp.addEventListener('change', (e) => {
        const el = e.currentTarget;
        const kind = el.dataset.date; // start / finish
        const catId = el.dataset.cat;
        const no = el.dataset.no;
        const iso = el.value;

        if (!state.currentId || !catId || !no) return;

        const project = dummyDatabase[state.currentId];
        if (!project) return;

        const cat = (project.categories || []).find(c => c.id === catId);
        if (!cat) return;

        const item = (cat.items || []).find(i => i.no === no);
        if (!item) return;

        const d = isoToDate(iso);
        if (!d) return;

        const ddmmyy = formatDDMMYYYY(d);

        if (kind === 'start') item.start = ddmmyy;
        if (kind === 'finish') item.finish = ddmmyy;

        project.timeline = buildTimelineAutoFromProject(project, 2); // <-- DINAMIS
        recomputeAll(project);
        renderHeader(project.timeline);
        renderBody(project.timeline, project.categories);
      });
    });
  }

  /* ============================================================
     TOGGLE GANTT
  ============================================================ */
  if (btnToggleGantt) {
    btnToggleGantt.addEventListener('click', async () => {
      state.ganttHidden = !state.ganttHidden;

      if (!state.currentId) return;
      const data = await fetchScheduleData(state.currentId);
      if (!data) return;

      data.timeline = buildTimelineAutoFromProject(data, 0); // <-- pastiin ada
      recomputeAll(data);
      renderHeader(data.timeline);
      renderBody(data.timeline, data.categories);
    });
  }

  /* ============================================================
     INIT
  ============================================================ */
  document.addEventListener('DOMContentLoaded', async function () {
    const init = window.SCHEDULE_INIT;
    if (!init || !init.mode) return;

    state.mode = init.mode;
    state.currentId = init.id;

    if (init.mode === 'readonly' && init.id) {
      renderLoading();
      const data = await fetchScheduleData(init.id);

      if (!data) {
        header.innerHTML = '';
        body.innerHTML = `<div class="p-10 text-center text-slate-500 text-sm">Data schedule tidak ditemukan.</div>`;
        return;
      }

      // TIMELINE AUTO
      data.timeline = buildTimelineAutoFromProject(data, 0);

      recomputeAll(data);
      renderHeader(data.timeline);
      renderBody(data.timeline, data.categories);
    }
  });

})();