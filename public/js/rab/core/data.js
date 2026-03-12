/**
 * core/data.js
 * Data layer for RAB. Replace fetchRabData and fetchKategoriMaster
 * with real API calls when the database is ready — no other files need to change.
 */

const dummyDatabase = {
    1: {
        categories: [
            {
                id: 'persiapan',
                name: 'Pekerjaan Persiapan',
                items: [
                    { no: 1, uraian: 'Pembuatan gudang semen dan peralatan', volume: 1,    satuan: 'm²', hargaBahan: 18000.00,  hargaAlat: 5000.00,  hargaUpah: 9621.60,  hargaKeseluruhan: 32621.60  },
                    { no: 2, uraian: 'Buangan tanah galian',                 volume: 12.5, satuan: 'm³', hargaBahan: 0,         hargaAlat: 25000.00, hargaUpah: 20000.00, hargaKeseluruhan: 562500.00 }
                ]
            },
            {
                id: 'struktur',
                name: 'Pekerjaan Struktur',
                items: [
                    { no: 1, uraian: 'Pengecoran pondasi beton',    volume: 5,   satuan: 'm³', hargaBahan: 600000.00, hargaAlat: 150000.00, hargaUpah: 200000.00, hargaKeseluruhan: 4750000.00  },
                    { no: 2, uraian: 'Pemasangan besi tulangan D16', volume: 200, satuan: 'kg', hargaBahan: 10000.00,  hargaAlat: 1500.00,   hargaUpah: 3000.00,   hargaKeseluruhan: 2900000.00  },
                    { no: 3, uraian: 'Bekisting kolom',             volume: 30,  satuan: 'm²', hargaBahan: 70000.00,  hargaAlat: 20000.00,  hargaUpah: 35000.00,  hargaKeseluruhan: 3750000.00  }
                ]
            },
            {
                id: 'arsitektur',
                name: 'Pekerjaan Arsitektur',
                items: [
                    { no: 1, uraian: 'Pasangan dinding bata merah 1:4', volume: 80,  satuan: 'm²', hargaBahan: 110000.00, hargaAlat: 15000.00, hargaUpah: 60000.00, hargaKeseluruhan: 14800000.00 },
                    { no: 2, uraian: 'Plesteran & acian dinding',       volume: 160, satuan: 'm²', hargaBahan: 40000.00,  hargaAlat: 8000.00,  hargaUpah: 24000.00, hargaKeseluruhan: 11520000.00 }
                ]
            }
        ]
    },
    2: {
        categories: [
            {
                id: 'persiapan',
                name: 'Pekerjaan Persiapan',
                items: [
                    { no: 1, uraian: 'Pembongkaran atap lama', volume: 1, satuan: 'ls', hargaBahan: 500000.00, hargaAlat: 800000.00, hargaUpah: 1200000.00, hargaKeseluruhan: 2500000.00 }
                ]
            },
            {
                id: 'struktur',
                name: 'Pekerjaan Struktur',
                items: [
                    { no: 1, uraian: 'Perkuatan balok eksisting', volume: 8, satuan: 'm',  hargaBahan: 250000.00, hargaAlat: 80000.00, hargaUpah: 120000.00, hargaKeseluruhan: 3600000.00 },
                    { no: 2, uraian: 'Cor plat lantai t=12cm',    volume: 6, satuan: 'm²', hargaBahan: 450000.00, hargaAlat: 130000.00, hargaUpah: 200000.00, hargaKeseluruhan: 4680000.00 }
                ]
            }
        ]
    }
};

export const dummyKategoriMaster = [
    { id: 'persiapan',  nama: 'Pekerjaan Persiapan'  },
    { id: 'struktur',   nama: 'Pekerjaan Struktur'    },
    { id: 'arsitektur', nama: 'Pekerjaan Arsitektur'  },
    { id: 'mep',        nama: 'Pekerjaan MEP'         },
    { id: 'finishing',  nama: 'Pekerjaan Finishing'   }
];

export function fetchKategoriMaster() {
    return Promise.resolve(dummyKategoriMaster);
}

export function fetchRabData(id) {
    return new Promise(resolve => {
        setTimeout(() => resolve(dummyDatabase[id] || { categories: [] }), 350);
    });
}
