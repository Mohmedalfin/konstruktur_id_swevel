<?php

namespace App\Services;

use App\Models\ProyekModel;
use App\Models\RapModel;
use App\Models\RapDetailModel;
use App\Models\RapKategoriModel;
use App\Services\ProjectInventoryService;
use CodeIgniter\Exceptions\PageNotFoundException;

class RealisasiService
{
    protected $proyekModel;
    protected $rapModel;
    protected $rapDetailModel;
    protected $rapKategoriModel;

    public function __construct()
    {
        $this->proyekModel           = new ProyekModel();
        $this->rapModel              = new RapModel();
        $this->rapDetailModel        = new RapDetailModel();
        $this->rapKategoriModel      = new RapKategoriModel();
        $this->realisasiSdmModel     = new \App\Models\RealisasiSdmModel();
        $this->realisasiSdmItemModel = new \App\Models\RealisasiSdmItemModel();
    }

    public function getPekerjaanProgressData(int $idProject): array
    {
        $project = $this->proyekModel->where('id_project', $idProject)->first();
        if (!$project) {
            throw PageNotFoundException::forPageNotFound();
        }

        $rap = $this->rapModel->where('id_project', $idProject)->first();
        if (!$rap) {
            return [];
        }

        $rapId = (int) $rap['id_rap'];

        $kategoriRows = $this->rapKategoriModel
            ->select('rap_kategori.id_kategori, kategori_pekerjaan.nama_kategori')
            ->join('kategori_pekerjaan', 'kategori_pekerjaan.id_kategori_pekerjaan = rap_kategori.id_kategori', 'left')
            ->where('rap_kategori.id_rap', $rapId)
            ->groupStart()
                ->where('kategori_pekerjaan.id_project', $idProject)
                ->orWhere('kategori_pekerjaan.id_project', null)
            ->groupEnd()
            ->orderBy('kategori_pekerjaan.nama_kategori', 'ASC')
            ->findAll();

        $db = \Config\Database::connect();
        $detailRows = $db->table('rap_detail')
            ->select('rap_detail.id_rap_detail, rap_detail.id_rap, rap_detail.id_kategori, rap_detail.id_parent, rap_detail.pekerjaan, rap_detail.volume, rap_detail.satuan, rap_detail.urutan')
            ->select('(SELECT COALESCE(SUM(rp.volume_tercapai), 0) FROM realisasi_pekerjaan rp WHERE rp.id_rap_detail = rap_detail.id_rap_detail) as total_tercapai')
            ->where('rap_detail.id_rap', $rapId)
            ->where('rap_detail.pekerjaan IS NOT NULL', null, false)
            ->where('rap_detail.pekerjaan !=', '')
            ->orderBy('rap_detail.id_kategori', 'ASC')
            ->orderBy('rap_detail.urutan', 'ASC')
            ->orderBy('rap_detail.id_rap_detail', 'ASC')
            ->get()
            ->getResultArray();

        $itemIds    = array_column($detailRows, 'id_rap_detail');
        $logsByItem = $this->fetchLogsByItemIds($db, $itemIds);

        $detailRows = array_map(function (array $row) use ($logsByItem): array {
            $row['history'] = $logsByItem[$row['id_rap_detail']] ?? [];
            return $row;
        }, $detailRows);

        $grouped = [];
        foreach ($kategoriRows as $cat) {
            $catId = (string) $cat['id_kategori'];
            $grouped[$catId] = [
                'id_kategori'   => $catId,
                'nama_kategori' => $cat['nama_kategori'] ?? 'Tanpa Kategori',
                'items'         => [],
            ];
        }

        $itemsByCategory = [];
        foreach ($detailRows as $row) {
            $catId = (string) ($row['id_kategori'] ?? '0');
            $itemsByCategory[$catId][] = $row;
        }

        foreach ($grouped as $catId => &$data) {
            $categoryItems = $itemsByCategory[$catId] ?? [];
            $data['items'] = $this->buildProgressTree($categoryItems, null);
        }
        unset($data);

        return array_values($grouped);
    }

    private function fetchLogsByItemIds(\CodeIgniter\Database\BaseConnection $db, array $itemIds): array
    {
        if (empty($itemIds)) {
            return [];
        }

        $logs = $db->table('realisasi_pekerjaan')
            ->whereIn('id_rap_detail', $itemIds)
            ->orderBy('tanggal', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();

        $grouped = [];
        foreach ($logs as $log) {
            $grouped[$log['id_rap_detail']][] = [
                'id_realisasi'    => (int)   $log['id_realisasi_pekerjaan'],
                'tanggal'         =>         $log['tanggal'],
                'volume_tercapai' => (float) $log['volume_tercapai'],
                'keterangan'      =>         $log['keterangan'],
                'foto'            => json_decode($log['foto'] ?? '[]', true) ?? [],
            ];
        }

        return $grouped;
    }

    private function buildProgressTree(array $elements, $parentId): array
    {
        $branch = [];
        foreach ($elements as $element) {
            if ($element['id_parent'] == $parentId) {
                $children    = $this->buildProgressTree($elements, $element['id_rap_detail']);

                $volTarget   = (float) ($element['volume'] ?? 0);
                $volTercapai = (float) ($element['total_tercapai'] ?? 0);
                $pct         = $volTarget > 0 ? round(($volTercapai / $volTarget) * 100, 2) : 0;

                $branch[] = [
                    'id_rap_detail'   => (int)    $element['id_rap_detail'],
                    'id_parent'       => $element['id_parent'] ? (int) $element['id_parent'] : null,
                    'uraian'          =>           $element['pekerjaan'],
                    'volume_target'   =>           $volTarget,
                    'volume_tercapai' =>           $volTercapai,
                    'satuan'          =>           $element['satuan'] ?? '',
                    'progress_pct'    =>           $pct,
                    'history'         =>           $element['history'] ?? [],
                    'children'        =>           $children,
                ];
            }
        }
        return $branch;
    }

    public function deleteLog(int $id): void
    {
        $db    = \Config\Database::connect();
        $model = new \App\Models\RealisasiPekerjaanModel();

        $log = $model->find($id);

        if (!$log) {
            throw new \InvalidArgumentException("Log progress dengan ID {$id} tidak ditemukan.");
        }

        if (!empty($log['foto'])) {
            $paths = json_decode($log['foto'], true) ?? [];
            foreach ($paths as $path) {
                $fullPath = FCPATH . $path;
                if (is_file($fullPath)) {
                    unlink($fullPath);
                }
            }
        }

        $model->delete($id);
    }

    public function deleteSdmItem(int $idItem): void
    {
        $db = \Config\Database::connect();
        
        $item = $this->realisasiSdmItemModel->find($idItem);
        if (!$item) {
            throw new \InvalidArgumentException("Item penggunaan dengan ID {$idItem} tidak ditemukan.");
        }

        $idHeader = $item['id_realisasi_sdm'];

        $db->transStart();
        
        $this->realisasiSdmItemModel->delete($idItem);

        $remainingItems = $this->realisasiSdmItemModel->where('id_realisasi_sdm', $idHeader)->countAllResults();
        
        if ($remainingItems === 0) {
            $header = $this->realisasiSdmModel->find($idHeader);
            if ($header) {
                if (!empty($header['dokumentasi'])) {
                    $paths = json_decode($header['dokumentasi'], true) ?? [];
                    foreach ($paths as $path) {
                        $fullPath = FCPATH . $path;
                        if (is_file($fullPath)) {
                            unlink($fullPath);
                        }
                    }
                }
                $this->realisasiSdmModel->delete($idHeader);
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            throw new \RuntimeException('Gagal menghapus item penggunaan.');
        }
    }

    public function saveProgressBatch(int $idProject, string $tanggal, array $items, array $uploadedFiles = []): void
    {
        $db               = \Config\Database::connect();
        $realisasiModel   = new \App\Models\RealisasiPekerjaanModel();

        $fotoPaths = $this->_handleFileUploads($uploadedFiles, $idProject);
        $fotoJson  = !empty($fotoPaths) ? json_encode($fotoPaths) : null;

        $db->transStart();

        foreach ($items as $item) {
            $idRapDetail    = (int) ($item['id_rap_detail'] ?? 0);
            $volTercapai    = (float) ($item['volume_tercapai'] ?? 0);
            $keterangan     = trim($item['keterangan'] ?? '');

            if ($idRapDetail <= 0 || $volTercapai <= 0) {
                continue;
            }

            $detail = $db->table('rap_detail')
                ->select('rap_detail.volume')
                ->select('COALESCE(SUM(rp.volume_tercapai), 0) AS total_tercapai', false)
                ->join('realisasi_pekerjaan rp', 'rp.id_rap_detail = rap_detail.id_rap_detail', 'left')
                ->where('rap_detail.id_rap_detail', $idRapDetail)
                ->groupBy('rap_detail.id_rap_detail')
                ->get()
                ->getRowArray();

            if (!$detail) {
                throw new \InvalidArgumentException("Item pekerjaan dengan ID {$idRapDetail} tidak ditemukan.");
            }

            $volTarget    = (float) $detail['volume'];
            $totalSoFar   = (float) $detail['total_tercapai'];

            if ($volTarget > 0 && ($totalSoFar + $volTercapai) > $volTarget) {
                throw new \InvalidArgumentException(
                    "Volume untuk ID pekerjaan {$idRapDetail} melebihi target ({$volTarget})."
                );
            }

            $realisasiModel->insert([
                'id_rap_detail'  => $idRapDetail,
                'tanggal'        => $tanggal,
                'volume_tercapai'=> $volTercapai,
                'keterangan'     => $keterangan ?: null,
                'foto'           => $fotoJson,
            ]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            throw new \RuntimeException('Gagal menyimpan progress. Transaksi dibatalkan.');
        }
    }

    private function _handleFileUploads(array $files, int $idProject): array
    {
        $paths     = [];
        $uploadDir = FCPATH . "uploads/realisasi/{$idProject}/";

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        foreach ($files as $file) {
            if (!$file instanceof \CodeIgniter\HTTP\Files\UploadedFile) {
                continue;
            }
            if (!$file->isValid() || $file->hasMoved()) {
                continue;
            }

            $newName = $file->getRandomName();
            $file->move($uploadDir, $newName);
            $paths[] = "uploads/realisasi/{$idProject}/{$newName}";
        }

        return $paths;
    }

    /**
     * Get Project-Wide SDM Resources (Pool) with Remaining Qty
     */
    public function getSdmResources(int $idProject): array
    {
        $db = \Config\Database::connect();

        $budgetItems = $db->table('rap_detail_item rdi')
            ->select('MIN(rdi.id_rap_detail_item) as id_rap_detail_item, rdi.nama_item, rdi.satuan, rdi.spesifikasi, rdi.merk, rdi.jenis_item as kategori')
            ->select('SUM(rd.volume * rdi.koefisien) as qty_budget', false)
            ->join('rap_detail rd', 'rd.id_rap_detail = rdi.id_rap_detail')
            ->join('rap r', 'r.id_rap = rd.id_rap')
            ->where('r.id_project', $idProject)
            ->groupBy('rdi.nama_item, rdi.satuan, rdi.spesifikasi, rdi.merk, rdi.jenis_item')
            ->get()
            ->getResultArray();

        $usageItems = $db->table('realisasi_sdm_item rsi')
            ->select('rsi.nama_item, rsi.satuan, rsi.spesifikasi, rsi.merk, rsi.kategori')
            ->select('SUM(rsi.qty) as total_usage', false)
            ->join('realisasi_sdm rs', 'rs.id_realisasi_sdm = rsi.id_realisasi_sdm')
            ->where('rs.id_project', $idProject)
            ->groupBy('rsi.nama_item, rsi.satuan, rsi.spesifikasi, rsi.merk, rsi.kategori')
            ->get()
            ->getResultArray();

        $usageMap = [];
        foreach ($usageItems as $u) {
            $key = strtolower(trim($u['nama_item'] . '|' . $u['satuan'] . '|' . $u['spesifikasi'] . '|' . $u['merk']));
            $usageMap[$key] = (float) $u['total_usage'];
        }

        foreach ($budgetItems as &$item) {
            $key = strtolower(trim($item['nama_item'] . '|' . $item['satuan'] . '|' . $item['spesifikasi'] . '|' . $item['merk']));
            $used = $usageMap[$key] ?? 0;
            
            $item['qty_budget'] = (float) $item['qty_budget'];
            $item['qty_used']   = $used;
            $item['qty_sisa']   = $item['qty_budget'] - $used;

            $item['stok_lapangan'] = 0.0;
            $kategoriInput = strtolower(trim($item['kategori'] ?? ''));
            $kategori = 'Bahan';
            if ($kategoriInput === 'tenaga kerja' || $kategoriInput === 'tenaga' || $kategoriInput === 'upah') {
                $kategori = 'Tenaga Kerja';
            } elseif ($kategoriInput === 'alat') {
                $kategori = 'Alat';
            }

            if (in_array($kategori, ['Bahan', 'Alat'])) {
                $merk = trim((string) ($item['merk'] ?? '')) ?: 'Tanpa Merk';
                $spesifikasi = trim((string) ($item['spesifikasi'] ?? '')) ?: '-';

                $stokProyek = $db->table('stok_proyek sp')
                    ->select('sp.stok_aktual')
                    ->join('master_barang mb', 'mb.id = sp.id_barang')
                    ->where('sp.id_project', $idProject)
                    ->where('mb.nama_barang', $item['nama_item'])
                    ->where('mb.merk', $merk)
                    ->where('mb.spesifikasi', $spesifikasi)
                    ->groupStart()
                        ->where('mb.jenis_item', $kategori)
                        ->orWhere('mb.jenis_item', $item['kategori'])
                    ->groupEnd()
                    ->get()->getRowArray();

                if ($stokProyek) {
                    $item['stok_lapangan'] = (float) $stokProyek['stok_aktual'];
                }
            }
        }

        return $budgetItems;
    }

    /**
     * Get SDM Realization History
     */
    public function getSdmHistory(int $idProject): array
    {
        $rows = $this->realisasiSdmModel
            ->where('id_project', $idProject)
            ->orderBy('tanggal', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->findAll();

        foreach ($rows as &$row) {
            $row['items'] = $this->realisasiSdmItemModel
                ->where('id_realisasi_sdm', $row['id_realisasi_sdm'])
                ->findAll();
            
            $row['dokumentasi'] = json_decode($row['dokumentasi'] ?? '[]', true) ?? [];
        }

        return $rows;
    }

    /**
     * Save SDM Progress
     */
    public function saveSdmProgress(int $idProject, string $tanggal, array $items, string $keterangan = null, array $uploadedFiles = []): void
    {
        $db = \Config\Database::connect();
        
        $fotoPaths = $this->_handleFileUploads($uploadedFiles, $idProject);
        $fotoJson  = !empty($fotoPaths) ? json_encode($fotoPaths) : null;

        $db->transStart();

        $header = $this->realisasiSdmModel
            ->where('id_project', $idProject)
            ->where('tanggal', $tanggal)
            ->first();

        if ($header) {
            $idHeader = $header['id_realisasi_sdm'];
            
            $existingFoto = json_decode($header['dokumentasi'] ?? '[]', true) ?? [];
            if (!is_array($existingFoto)) $existingFoto = [];
            $allFotos = array_merge($existingFoto, $fotoPaths);
            $fotoJson = !empty($allFotos) ? json_encode($allFotos) : null;

            $newKeterangan = $header['keterangan'];
            if (!empty($keterangan) && trim($keterangan) !== trim($header['keterangan'] ?? '')) {
                $newKeterangan = empty($header['keterangan']) ? $keterangan : $header['keterangan'] . "\n" . $keterangan;
            }

            $this->realisasiSdmModel->update($idHeader, [
                'keterangan'  => $newKeterangan,
                'dokumentasi' => $fotoJson,
            ]);
        } else {
            $fotoJson  = !empty($fotoPaths) ? json_encode($fotoPaths) : null;
            $idHeader = $this->realisasiSdmModel->insert([
                'id_project'  => $idProject,
                'tanggal'     => $tanggal,
                'keterangan'  => $keterangan,
                'dokumentasi' => $fotoJson,
            ]);

            if (!$idHeader) {
                throw new \RuntimeException('Gagal menyimpan header realisasi SDM: ' . json_encode($this->realisasiSdmModel->errors()));
            }
        }

        foreach ($items as $item) {
            $qtyInput = (float) ($item['qty'] ?? 0);
            if ($qtyInput <= 0) continue;

            $kategoriInput = strtolower(trim($item['kategori'] ?? ''));
            $kategori = 'Bahan';
            if ($kategoriInput === 'tenaga kerja' || $kategoriInput === 'tenaga' || $kategoriInput === 'upah') {
                $kategori = 'Tenaga Kerja';
            } elseif ($kategoriInput === 'alat') {
                $kategori = 'Alat';
            }

            // Ambil harga_satuan dari RAP jika tersedia
            $hargaSatuan = 0.0;
            $idRapDetailItem = (int) ($item['id_rap_detail_item'] ?? 0);
            if ($idRapDetailItem > 0) {
                $rapItem = $db->table('rap_detail_item')
                    ->select('harga_satuan')
                    ->where('id_rap_detail_item', $idRapDetailItem)
                    ->get()->getRowArray();
                if ($rapItem) {
                    $hargaSatuan = (float) ($rapItem['harga_satuan'] ?? 0);
                }
            }

            // Fallback: search by name, unit, spec, brand, and category if price is still 0
            if ($hargaSatuan <= 0) {
                $rapItem = $db->table('rap_detail_item rdi')
                    ->select('rdi.harga_satuan')
                    ->join('rap_detail rd', 'rd.id_rap_detail = rdi.id_rap_detail')
                    ->join('rap r', 'r.id_rap = rd.id_rap')
                    ->where('r.id_project', $idProject)
                    ->where('rdi.nama_item', $item['nama_item'])
                    ->where('rdi.satuan', $item['satuan'])
                    ->groupStart()
                        ->where('rdi.jenis_item', $kategori)
                        ->orWhere('rdi.jenis_item', $item['kategori'])
                    ->groupEnd()
                    ->orderBy('rdi.harga_satuan', 'DESC')
                    ->get()->getRowArray();
                if ($rapItem) {
                    $hargaSatuan = (float) ($rapItem['harga_satuan'] ?? 0);
                }
            }

            $success = $this->realisasiSdmItemModel->insert([
                'id_realisasi_sdm' => $idHeader,
                'kategori'         => $kategori,
                'nama_item'        => $item['nama_item'],
                'qty'              => $qtyInput,
                'harga_satuan'     => $hargaSatuan,
                'satuan'           => $item['satuan'],
                'spesifikasi'      => $item['spesifikasi'] ?? '-',
                'merk'             => $item['merk'] ?? '-',
                'keterangan'       => $item['keterangan'] ?? null,
            ]);

            if (!$success) {
                throw new \RuntimeException('Gagal menyimpan item SDM: ' . json_encode($this->realisasiSdmItemModel->errors()));
            }

            // --- Catat pemakaian di stok_proyek (Lapangan) ---
            // Hanya untuk kategori Bahan dan Alat (bukan Tenaga Kerja)
            if (in_array($kategori, ['Bahan', 'Alat'])) {
                // Cari id_barang dari master_barang berdasarkan nama, spesifikasi, merk, kategori (mengabaikan satuan)
                $merkRaw = trim((string) ($item['merk'] ?? ''));
                $merk = ($merkRaw === '' || $merkRaw === '-') ? 'Tanpa Merk' : $merkRaw;
                
                $spekRaw = trim((string) ($item['spesifikasi'] ?? ''));
                $spesifikasi = ($spekRaw === '') ? '-' : $spekRaw;

                // Cek prioritas pertama di stok_proyek proyek ini sendiri
                $masterBarang = $db->table('stok_proyek sp')
                    ->select('mb.id')
                    ->join('master_barang mb', 'mb.id = sp.id_barang')
                    ->where('sp.id_project', $idProject)
                    ->where('mb.nama_barang', $item['nama_item'])
                    ->where('mb.merk', $merk)
                    ->where('mb.spesifikasi', $spesifikasi)
                    ->where('mb.jenis_item', $kategori)
                    ->orderBy('sp.id', 'ASC')
                    ->limit(1)
                    ->get()->getRowArray();

                if (!$masterBarang) {
                    // Fallback jika belum ada di stok_proyek, cari di master_barang (dengan relasi company/project)
                    $masterBarang = $db->table('master_barang mb')
                        ->select('mb.id')
                        ->join('projects p', 'p.id_pengguna = mb.id_perusahaan', 'left')
                        ->where('mb.nama_barang', $item['nama_item'])
                        ->where('mb.merk', $merk)
                        ->where('mb.spesifikasi', $spesifikasi)
                        ->where('mb.jenis_item', $kategori)
                        ->groupStart()
                            ->where('p.id_project', $idProject)
                            ->orWhere('mb.id_perusahaan IS NULL', null, false)
                        ->groupEnd()
                        ->orderBy('mb.id', 'ASC')
                        ->limit(1)
                        ->get()->getRowArray();
                        
                    // Fallback pencarian terakhir tanpa filter id_perusahaan (untuk proyek yang id_pengguna-nya kosong)
                    if (!$masterBarang) {
                        $masterBarang = $db->table('master_barang mb')
                            ->select('mb.id')
                            ->where('mb.nama_barang', $item['nama_item'])
                            ->where('mb.merk', $merk)
                            ->where('mb.spesifikasi', $spesifikasi)
                            ->where('mb.jenis_item', $kategori)
                            ->orderBy('mb.id', 'ASC')
                            ->limit(1)
                            ->get()->getRowArray();
                    }
                }

                if ($masterBarang) {
                    $projectInventory = new ProjectInventoryService();
                    $projectInventory->catatPemakaian(
                        idProject:     $idProject,
                        idBarang:      (int)$masterBarang['id'],
                        jumlah:        $qtyInput,
                        idRealisasiSdm: (int)$idHeader,
                        namaBarang:    $item['nama_item']
                    );
                }
            }
        } // end foreach $items

        $db->transComplete();

        if ($db->transStatus() === false) {
            throw new \RuntimeException('Gagal menyimpan realisasi SDM. Transaksi dibatalkan.');
        }
    }
}
