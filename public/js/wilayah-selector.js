const WilayahSelector = (() => {
    const _base = (() => {
        const m = document.querySelector('meta[name="base-url"]');
        if (m) {
            const metaOrigin = new URL(m.getAttribute('content')).origin;
            if (metaOrigin === window.location.origin) return m.getAttribute('content').replace(/\/$/, '');
        }
        const path = window.location.pathname;
        const pubIdx = path.indexOf('/public');
        if (pubIdx !== -1) return window.location.origin + path.substring(0, pubIdx + 7);
        return window.location.origin;
    })();

    const API = {
        PROVINCES: `${_base}/api/wilayah/provinces`,
        CITIES: `${_base}/api/wilayah/cities`,
        TEMPLATES: `${_base}/api/wilayah/templates`
    };

    const elements = {
        prov: () => document.getElementById('sel_provinsi'),
        city: () => document.getElementById('sel_kota'),
        tmpl: () => document.getElementById('sel_template'),
        hiddenWilayah: () => document.getElementById('id_wilayah'),
        hiddenTemplate: () => document.getElementById('id_template'),
        hiddenLokasi: () => document.getElementById('lokasi_proyek'),
        info: () => document.getElementById('lokasi-info')
    };

    function resetSelect(el, text = 'Pilih...') {
        if (!el) return;
        el.innerHTML = `<option value="">-- ${text} --</option>`;
        el.disabled = true;
    }

    async function fetchData(url) {
        try {
            const res = await fetch(url);
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            const json = await res.json();
            return json.data || [];
        } catch (e) {
            console.error('[WilayahSelector] fetch error:', e);
            return [];
        }
    }

    async function loadProvinces(selectedId = null) {
        const el = elements.prov();
        if (!el) return;

        el.innerHTML = '<option value="">Memuat provinsi...</option>';
        el.disabled = true;

        const list = await fetchData(API.PROVINCES);
        el.innerHTML = '<option value="">-- Pilih Provinsi --</option>' +
            list.map(p => `<option value="${p.id}" ${p.id == selectedId ? 'selected' : ''}>${p.nama}</option>`).join('');
        el.disabled = false;

        if (selectedId) {
            await loadCities(selectedId);
        }
    }

    async function loadCities(idProv, selectedId = null) {
        const el = elements.city();
        if (!el) return;

        resetSelect(el, 'Memuat Kab/Kota...');
        resetSelect(elements.tmpl(), 'Pilih Kab/Kota dahulu');

        const list = await fetchData(`${API.CITIES}?id_prov=${idProv}`);
        el.innerHTML = '<option value="">-- Pilih Kabupaten/Kota --</option>' +
            list.map(c => `<option value="${c.id}" ${c.id == selectedId ? 'selected' : ''}>${c.nama}</option>`).join('');
        el.disabled = false;

        if (selectedId) {
            await loadTemplates(selectedId);
        }
    }

    async function loadTemplates(idWilayah, selectedId = null) {
        const el = elements.tmpl();
        if (!el) return;

        resetSelect(el, 'Memuat Tahun Harga...');

        const list = await fetchData(`${API.TEMPLATES}?id_wilayah=${idWilayah}`);
        if (list.length === 0) {
            el.innerHTML = '<option value="">Tidak ada referensi harga</option>';
            el.disabled = true;
            return;
        }

        el.innerHTML = '<option value="">-- Pilih Referensi Tahun --</option>' +
            list.map(t => `<option value="${t.id}" ${t.id == selectedId ? 'selected' : ''}>${t.nama}</option>`).join('');
        el.disabled = false;
    }

    function updateLabel() {
        const selCity = elements.city();
        const selTmpl = elements.tmpl();
        const info = elements.info();

        const cityName = selCity?.options[selCity.selectedIndex]?.text || '';
        const tmplName = selTmpl?.options[selTmpl.selectedIndex]?.text || '';

        if (elements.hiddenWilayah()) elements.hiddenWilayah().value = selCity?.value || '';
        if (elements.hiddenTemplate()) elements.hiddenTemplate().value = selTmpl?.value || '';
        if (elements.hiddenLokasi()) elements.hiddenLokasi().value = cityName;

        if (info) {
            if (selCity?.value && selTmpl?.value) {
                info.className = 'mt-1 text-xs text-green-600 font-medium';
                info.innerHTML = `<i class="fa-solid fa-circle-check mr-1"></i>Lokasi: <strong>${cityName}</strong> (${tmplName})`;
            } else {
                info.className = 'mt-1 text-xs text-slate-500';
                info.textContent = 'Pilih lokasi untuk menarik harga satuan resmi regional.';
            }
        }
    }

    async function init(opts = {}) {
        const { idProv, idWilayah, idTemplate } = opts;

        elements.prov()?.addEventListener('change', (e) => {
            const val = e.target.value;
            if (val) loadCities(val);
            else {
                resetSelect(elements.city(), 'Pilih Provinsi dahulu');
                resetSelect(elements.tmpl(), 'Pilih Kab/Kota dahulu');
            }
            updateLabel();
        });

        elements.city()?.addEventListener('change', (e) => {
            const val = e.target.value;
            if (val) loadTemplates(val);
            else resetSelect(elements.tmpl(), 'Pilih Kab/Kota dahulu');
            updateLabel();
        });

        elements.tmpl()?.addEventListener('change', updateLabel);

        await loadProvinces(idProv);
        if (idProv && idWilayah) {
            await loadCities(idProv, idWilayah);
            if (idTemplate) {
                await loadTemplates(idWilayah, idTemplate);
            }
        }
    }

    return { init };
})();
