export async function fetchRealisasiData() {
    try {
        const rawData = window.REALISASI_INIT.progressData || [];

        return rawData.map((cat, index) => {
            return {
                id: 'cat-' + cat.id_kategori,
                uraian: cat.nama_kategori,
                expanded: true,
                children: mapItems(cat.items, '')
            };
        });
    } catch (error) {
        console.error("Failed to parse realisasi data:", error);
        return [];
    }
}

function mapItems(items, prefix) {
    if (!items) return [];

    return items.map((item, index) => {
        const no        = prefix ? `${prefix}.${index + 1}` : `${index + 1}`;
        const satuan    = item.satuan || '';
        const volTarget = parseFloat(item.volume_target) || 0;
        const volDone   = parseFloat(item.volume_tercapai) || 0;
        const pct       = parseFloat(item.progress_pct) || 0;

        return {
            id             : item.id_rap_detail,
            no             : no,
            uraian         : item.uraian,
            volume         : volTarget,
            volumeTercapai : volDone,
            progress       : `${pct}%`,
            volTarget      : volTarget,
            volTercapai    : volDone,
            satuan         : satuan,
            expandedItem   : false,
            logs           : mapLogs(item.history || [], volTarget, satuan),
            children       : mapItems(item.children, no),
        };
    });
}
    
function mapLogs(history, volTarget, satuan) {
    return history.map((entry, index) => {
        const volAchieved   = parseFloat(entry.volume_tercapai) || 0;
        const contributionPct = volTarget > 0
            ? ((volAchieved / volTarget) * 100).toFixed(2)
            : 0;

        return {
            no             : index + 1,
            id_realisasi   : entry.id_realisasi,
            tanggal        : entry.tanggal,
            volRaw         : volAchieved,
            volumeTercapai : volAchieved,
            progress       : `${contributionPct}%`,
            keterangan     : entry.keterangan || '-',
            foto           : entry.foto || [],
        };
    });
}

export async function fetchSDMData() {
    try {
        const response = await fetch(`/api/realisasi/sdm-data?id_project=${window.REALISASI_INIT.idProject}`);
        if (!response.ok) throw new Error('Network error');
        const res = await response.json();
        
        if (res.status === 'success' && res.data) {
            return res.data.map(row => {
                const mappedItems = (row.items || []).map(i => ({
                    ...i,
                    nama: i.nama_item
                }));
                
                return {
                    ...row,
                    id: row.id_realisasi_sdm,
                    bahan: mappedItems.filter(i => (i.kategori || '').toLowerCase() === 'bahan'),
                    alat: mappedItems.filter(i => (i.kategori || '').toLowerCase() === 'alat'),
                    tenaga: mappedItems.filter(i => {
                        const k = (i.kategori || '').toLowerCase();
                        return k === 'upah' || k === 'tenaga' || k === 'tenaga kerja';
                    }),
                    dokumentasi: row.dokumentasi || []
                };
            }).filter(row => row.bahan.length > 0 || row.alat.length > 0 || row.tenaga.length > 0);
        }
        return [];
    } catch (error) {
        console.error("Failed to fetch SDM data:", error);
        return [];
    }
}

export async function fetchSDMResources() {
    try {
        const response = await fetch(`/api/realisasi/sdm-resources?id_project=${window.REALISASI_INIT.idProject}`);
        if (!response.ok) throw new Error('Network error');
        const res = await response.json();
        
        if (res.status === 'success' && res.data) {
            return res.data;
        }
        return [];
    } catch (error) {
        console.error("Failed to fetch SDM resources:", error);
        return [];
    }
}
