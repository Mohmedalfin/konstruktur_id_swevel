export class ReconcileState {
    constructor() {
        this.items = [];
        this.activeProjects = [];
        this.currentProjectId = null;
        this.currentProjectName = '';
    }

    setItems(items) {
        // Prepare items with initial allocation values
        this.items = items.map(item => ({
            ...item,
            retur: 0,
            mutasi: 0,
            id_proyek_tujuan: '',
            waste: 0,
            stok_aktual: parseFloat(item.stok_aktual)
        }));
    }

    setActiveProjects(projects) {
        this.activeProjects = projects;
    }

    setCurrentProject(id, name) {
        this.currentProjectId = id;
        this.currentProjectName = name;
    }

    updateAllocation(idBarang, field, value) {
        const item = this.items.find(i => String(i.id_barang) === String(idBarang));
        if (item) {
            item[field] = value;
        }
    }

    isValid() {
        // Return true if all items have retur + mutasi + waste === stok_aktual
        // Also if mutasi > 0, id_proyek_tujuan must be selected
        return this.items.every(item => {
            const totalAlokasi = (item.retur + item.mutasi + item.waste);
            const isBalanced = Math.abs(totalAlokasi - item.stok_aktual) < 0.0001; // Avoid floating point precision issues
            
            if (!isBalanced) return false;
            
            if (item.mutasi > 0 && !item.id_proyek_tujuan) return false;
            
            return true;
        });
    }

    getPayload() {
        return {
            reconciliations: this.items.map(item => ({
                id_barang: item.id_barang,
                jumlah_retur: item.retur,
                jumlah_mutasi: item.mutasi,
                id_proyek_tujuan: item.id_proyek_tujuan,
                jumlah_waste: item.waste
            }))
        };
    }
}
