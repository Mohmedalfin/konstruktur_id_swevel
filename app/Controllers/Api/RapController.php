<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\RapModel;
use App\Models\RapDetailModel;
use App\Models\KategoriPekerjaanModel;
use App\Models\RapKategoriModel;
use App\Models\RapDetailItemModel;
use App\Models\AhsModel;
use App\Models\ProyekModel;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\HTTP\ResponseInterface;
use Throwable;

class RapController extends BaseController
{
    protected RapModel $rapModel;
    protected RapDetailModel $rapDetailModel;
    protected KategoriPekerjaanModel $kategoriModel;
    protected RapKategoriModel $rapKategoriModel;
    protected RapDetailItemModel $rapDetailItemModel;
    protected AhsModel $ahsModel;
    protected ProyekModel $proyekModel;

    public function __construct()
    {
        $this->rapModel           = new RapModel();
        $this->rapDetailModel     = new RapDetailModel();
        $this->kategoriModel      = new KategoriPekerjaanModel();
        $this->rapKategoriModel   = new RapKategoriModel();
        $this->rapDetailItemModel = new RapDetailItemModel();
        $this->ahsModel           = new AhsModel();
        $this->proyekModel        = new ProyekModel();
    }

    public function index()
    {
        try {
            $idProject = (int) ($this->request->getGet('id_project') ?? 0);

            if ($idProject <= 0) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status'  => 'error',
                    'message' => 'id_project wajib diisi',
                ]);
            }

            $project = $this->proyekModel
                ->where('id_project', $idProject)
                ->first();

            if (!$project) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status'  => 'error',
                    'message' => 'Project tidak ditemukan',
                ]);
            }

            $sumberData = $project['sumber_data'] ?? 'manual';

            $rap = $this->rapModel
                ->where('id_project', $idProject)
                ->first();

            if (!$rap) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'data'   => [
                        'id_project'  => $idProject,
                        'id_rap'      => null,
                        'sumber_data' => $sumberData,
                        'categories'  => [],
                    ],
                ]);
            }

            $rapId = (int) $rap['id_rap'];

            $kategoriRows = $this->rapKategoriModel
                ->select('rap_kategori.id_kategori, kategori_pekerjaan.nama_kategori, kategori_pekerjaan.id_project')
                ->join(
                    'kategori_pekerjaan',
                    'kategori_pekerjaan.id_kategori_pekerjaan = rap_kategori.id_kategori',
                    'left'
                )
                ->where('rap_kategori.id_rap', $rapId)
                ->groupStart()
                    ->where('kategori_pekerjaan.id_project', $idProject)
                    ->orWhere('kategori_pekerjaan.id_project', null)
                ->groupEnd()
                ->orderBy('kategori_pekerjaan.nama_kategori', 'ASC')
                ->findAll();

            $detailRows = $this->rapDetailModel
                ->where('id_rap', $rapId)
                ->where('pekerjaan IS NOT NULL', null, false)
                ->where('pekerjaan !=', '')
                ->orderBy('id_kategori', 'ASC')
                ->orderBy('urutan', 'ASC')
                ->orderBy('id_rap_detail', 'ASC')
                ->findAll();

            $grouped = [];

            foreach ($kategoriRows as $cat) {
                $catId = (string) $cat['id_kategori'];

                $grouped[$catId] = [
                    'id'    => $catId,
                    'name'  => $cat['nama_kategori'] ?? 'Tanpa Kategori',
                    'items' => [],
                ];
            }

            // Organisasi item ke kategori dulu
            $itemsByCategory = [];
            foreach ($detailRows as $row) {
                $catId = (string) ($row['id_kategori'] ?? '0');
                $itemsByCategory[$catId][] = $row;
            }

            foreach ($grouped as $catId => &$data) {
                $categoryItems = $itemsByCategory[$catId] ?? [];
                $data['items'] = $this->buildTree($categoryItems);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'data'   => [
                    'id_project'  => $idProject,
                    'id_rap'      => $rapId,
                    'sumber_data' => $sumberData,
                    'categories'  => array_values($grouped),
                ],
            ]);
        } catch (Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function kategoriMaster()
    {
        try {
            $idProject = (int) ($this->request->getGet('id_project') ?? 0);

            if ($idProject <= 0) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status'  => 'error',
                    'message' => 'id_project wajib diisi',
                ]);
            }

            $idUser = session()->get('id_user'); // Assuming user ID is stored in session

            $rows = $this->kategoriModel
                ->groupStart()
                    ->where('jenis_kategori', 'sistem')
                ->groupEnd()
                ->orGroupStart()
                    ->where('jenis_kategori', 'custom')
                    ->where('id_project', $idProject)
                    ->where('id_user', $idUser)
                ->groupEnd()
                ->orderBy('nama_kategori', 'ASC')
                ->findAll();

            error_log('SQL KategoriMaster: ' . $this->kategoriModel->builder()->getCompiledSelect());

            $data = array_map(function ($row) {
                return [
                    'id'    => (string) $row['id_kategori_pekerjaan'],
                    'nama'  => $row['nama_kategori'],
                    'jenis' => $row['jenis_kategori'] ?? 'sistem',
                ];
            }, $rows);

            return $this->response->setJSON([
                'status' => 'success',
                'sql'    => $this->kategoriModel->builder()->getCompiledSelect(),
                'data'   => $data,
            ]);
        } catch (Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function tambahKategori()
    {
        $db = db_connect();

        try {
            $payload = $this->request->getJSON(true);

            $idProject    = (int) ($payload['id_project'] ?? 0);
            $kategoriList = $payload['kategori'] ?? [];
            $isMasterOnly = filter_var($payload['is_master_only'] ?? false, FILTER_VALIDATE_BOOLEAN);

            if ($idProject <= 0) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status'  => 'error',
                    'message' => 'id_project wajib diisi',
                ]);
            }

            $project = $this->proyekModel
                ->where('id_project', $idProject)
                ->first();

            if (!$project) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status'  => 'error',
                    'message' => 'Project tidak ditemukan',
                ]);
            }

            if (($project['sumber_data'] ?? 'manual') !== 'manual') {
                return $this->response->setStatusCode(403)->setJSON([
                    'status'  => 'error',
                    'message' => 'Kategori proyek estimator tidak dapat diubah',
                ]);
            }

            if (!is_array($kategoriList) || empty($kategoriList)) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status'  => 'error',
                    'message' => 'kategori wajib berupa array',
                ]);
            }

            $db->transStart();

            $rap = $this->rapModel
                ->where('id_project', $idProject)
                ->first();

            if (!$rap) {
                $this->rapModel->insert([
                    'id_project'        => $idProject,
                    'nama_rap'          => 'RAP Proyek ' . $idProject,
                    'subtotal_bahan'    => 0,
                    'subtotal_upah'     => 0,
                    'subtotal_alat'     => 0,
                    'total_keseluruhan' => 0,
                    'status_rap'        => 'draft',
                    'keterangan'        => null,
                ]);

                $rapId = (int) $this->rapModel->getInsertID();
            } else {
                $rapId = (int) $rap['id_rap'];
            }

            $saved = [];

            foreach ($kategoriList as $item) {
                $namaKategori = is_array($item)
                    ? trim((string) ($item['nama'] ?? ''))
                    : trim((string) $item);

                if ($namaKategori === '') {
                    continue;
                }

                $idUser = session()->get('id_user');

                $existingKategori = $this->kategoriModel
                    ->where('nama_kategori', $namaKategori)
                    ->groupStart()
                        ->where('jenis_kategori', 'sistem')
                        ->orGroupStart()
                            ->where('jenis_kategori', 'custom')
                            ->where('id_project', $idProject)
                            ->where('id_user', $idUser)
                        ->groupEnd()
                    ->groupEnd()
                    ->orderBy('id_project', 'ASC')
                    ->first();

                if (!$existingKategori) {
                    $this->kategoriModel->insert([
                        'nama_kategori'  => $namaKategori,
                        'id_project'     => $idProject,
                        'id_user'        => $idUser,
                        'jenis_kategori' => 'custom',
                    ]);

                    $kategoriId = (int) $this->kategoriModel->getInsertID();
                } else {
                    $kategoriId = (int) $existingKategori['id_kategori_pekerjaan'];
                }

                if (!$isMasterOnly) {
                    $existingRapKategori = $this->rapKategoriModel
                        ->where('id_rap', $rapId)
                        ->where('id_kategori', $kategoriId)
                        ->first();
    
                    if (!$existingRapKategori) {
                        $this->rapKategoriModel->insert([
                            'id_rap'      => $rapId,
                            'id_kategori' => $kategoriId,
                        ]);
                    }
                }

                $saved[] = [
                    'id'   => $kategoriId,
                    'nama' => $namaKategori,
                ];
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new DatabaseException('Gagal menyimpan kategori');
            }

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Kategori berhasil disimpan',
                'data'    => [
                    'id_rap'   => $rapId,
                    'kategori' => $saved,
                ],
            ]);
        } catch (Throwable $e) {
            if ($db->transStatus()) {
                $db->transRollback();
            }

            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function updateKategoriMaster($idKategori = null)
    {
        try {
            $idKategori = (int) $idKategori;
            $payload    = $this->request->getJSON(true);
            $nama       = trim($payload['nama'] ?? '');

            if ($idKategori <= 0 || $nama === '') {
                return $this->response->setStatusCode(400)->setJSON([
                    'status'  => 'error',
                    'message' => 'ID kategori dan nama wajib diisi'
                ]);
            }

            $kategori = $this->kategoriModel->find($idKategori);

            if (!$kategori) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status'  => 'error',
                    'message' => 'Kategori tidak ditemukan'
                ]);
            }

            if (($kategori['jenis_kategori'] ?? '') === 'sistem') {
                return $this->response->setStatusCode(403)->setJSON([
                    'status'  => 'error',
                    'message' => 'Kategori sistem tidak dapat diedit'
                ]);
            }

            // Optional: verify ownership
            $idUser = session()->get('id_user');
            if ($kategori['id_user'] != $idUser && $idUser !== null) {
                return $this->response->setStatusCode(403)->setJSON([
                    'status'  => 'error',
                    'message' => 'Anda tidak memiliki hak untuk mengedit kategori ini'
                ]);
            }

            $this->kategoriModel->update($idKategori, [
                'nama_kategori' => $nama
            ]);

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Kategori berhasil diupdate'
            ]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function deleteKategoriMaster($idKategori = null)
    {
        try {
            $idKategori = (int) $idKategori;

            if ($idKategori <= 0) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status'  => 'error',
                    'message' => 'ID kategori wajib diisi'
                ]);
            }

            $kategori = $this->kategoriModel->find($idKategori);

            if (!$kategori) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status'  => 'error',
                    'message' => 'Kategori tidak ditemukan'
                ]);
            }

            if (($kategori['jenis_kategori'] ?? '') === 'sistem') {
                return $this->response->setStatusCode(403)->setJSON([
                    'status'  => 'error',
                    'message' => 'Kategori sistem tidak dapat dihapus'
                ]);
            }

            // Verify ownership
            $idUser = session()->get('id_user');
            if ($kategori['id_user'] != $idUser && $idUser !== null) {
                return $this->response->setStatusCode(403)->setJSON([
                    'status'  => 'error',
                    'message' => 'Anda tidak memiliki hak untuk menghapus kategori ini'
                ]);
            }

            // Check if it's used in RAP
            $used = $this->rapKategoriModel->where('id_kategori', $idKategori)->first();
            if ($used) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status'  => 'error',
                    'message' => 'Kategori sedang digunakan di RAP. Hapus dari RAP terlebih dahulu.'
                ]);
            }

            $this->kategoriModel->delete($idKategori);

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Kategori berhasil dihapus'
            ]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function deleteKategori($idKategori = null)
    {
        $db = db_connect();

        try {
            $idKategori = (int) $idKategori;
            $idProject  = (int) ($this->request->getGet('id_project') ?? 0);

            if ($idProject <= 0 || $idKategori <= 0) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status'  => 'error',
                    'message' => 'id_project dan id_kategori wajib diisi',
                ]);
            }

            $project = $this->proyekModel
                ->where('id_project', $idProject)
                ->first();

            if (!$project) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status'  => 'error',
                    'message' => 'Project tidak ditemukan',
                ]);
            }

            if (($project['sumber_data'] ?? 'manual') !== 'manual') {
                return $this->response->setStatusCode(403)->setJSON([
                    'status'  => 'error',
                    'message' => 'Kategori proyek estimator tidak dapat dihapus',
                ]);
            }

            $rap = $this->rapModel
                ->where('id_project', $idProject)
                ->first();

            if (!$rap) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status'  => 'error',
                    'message' => 'RAP tidak ditemukan',
                ]);
            }

            $kategori = $this->kategoriModel->find($idKategori);

            if (!$kategori) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status'  => 'error',
                    'message' => 'Kategori tidak ditemukan',
                ]);
            }

            $rapId = (int) $rap['id_rap'];

            $existingRapKategori = $this->rapKategoriModel
                ->where('id_rap', $rapId)
                ->where('id_kategori', $idKategori)
                ->first();

            if (!$existingRapKategori) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status'  => 'error',
                    'message' => 'Kategori tidak ditemukan pada RAP ini',
                ]);
            }

            $db->transStart();

            $detailRows = $this->rapDetailModel
                ->where('id_rap', $rapId)
                ->where('id_kategori', $idKategori)
                ->findAll();

            foreach ($detailRows as $detail) {
                $this->rapDetailItemModel
                    ->where('id_rap_detail', $detail['id_rap_detail'])
                    ->delete();
            }

            $this->rapDetailModel
                ->where('id_rap', $rapId)
                ->where('id_kategori', $idKategori)
                ->delete();

            $this->rapKategoriModel
                ->where('id_rap', $rapId)
                ->where('id_kategori', $idKategori)
                ->delete();

            $this->recalculateRapTotal($rapId);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new DatabaseException('Gagal menghapus kategori');
            }

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Kategori berhasil dihapus',
            ]);
        } catch (Throwable $e) {
            if ($db->transStatus()) {
                $db->transRollback();
            }

            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function tambahPekerjaan()
    {
        $db = db_connect();

        try {
            $payload = $this->request->getJSON(true);

            $idProject  = (int) ($payload['id_project'] ?? 0);
            $idKategori = (int) ($payload['id_kategori'] ?? 0);
            $idParent   = isset($payload['id_parent']) ? (int) $payload['id_parent'] : null;
            $items      = $payload['pekerjaan'] ?? [];

            if ($idProject <= 0 || $idKategori <= 0) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status'  => 'error',
                    'message' => 'id_project dan id_kategori wajib diisi',
                ]);
            }

            $project = $this->proyekModel
                ->where('id_project', $idProject)
                ->first();

            if (!$project) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status'  => 'error',
                    'message' => 'Project tidak ditemukan',
                ]);
            }

            if (($project['sumber_data'] ?? 'manual') !== 'manual') {
                return $this->response->setStatusCode(403)->setJSON([
                    'status'  => 'error',
                    'message' => 'Pekerjaan proyek estimator tidak dapat diubah',
                ]);
            }

            if (!is_array($items) || empty($items)) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status'  => 'error',
                    'message' => 'pekerjaan wajib berupa array',
                ]);
            }

            $kategori = $this->kategoriModel->find($idKategori);
            if (!$kategori) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status'  => 'error',
                    'message' => 'Kategori tidak ditemukan',
                ]);
            }

            if ($kategori['id_project'] !== null && (int) $kategori['id_project'] !== $idProject) {
                return $this->response->setStatusCode(403)->setJSON([
                    'status'  => 'error',
                    'message' => 'Kategori ini bukan milik project aktif',
                ]);
            }

            $db->transStart();

            $rap = $this->rapModel
                ->where('id_project', $idProject)
                ->first();

            if (!$rap) {
                $this->rapModel->insert([
                    'id_project'        => $idProject,
                    'nama_rap'          => 'RAP Proyek ' . $idProject,
                    'subtotal_bahan'    => 0,
                    'subtotal_upah'     => 0,
                    'subtotal_alat'     => 0,
                    'total_keseluruhan' => 0,
                    'status_rap'        => 'draft',
                    'keterangan'        => null,
                ]);

                $rapId = (int) $this->rapModel->getInsertID();
            } else {
                $rapId = (int) $rap['id_rap'];
            }

            $existingRapKategori = $this->rapKategoriModel
                ->where('id_rap', $rapId)
                ->where('id_kategori', $idKategori)
                ->first();

            if (!$existingRapKategori) {
                $this->rapKategoriModel->insert([
                    'id_rap'      => $rapId,
                    'id_kategori' => $idKategori,
                ]);
            }

            $lastUrutan = $this->rapDetailModel
                ->selectMax('urutan')
                ->where('id_rap', $rapId)
                ->where('id_kategori', $idKategori)
                ->where('id_parent', $idParent)
                ->first();

            $urutan = (int) ($lastUrutan['urutan'] ?? 0);

            foreach ($items as $item) {
                $urutan++;

                $nama       = trim((string) ($item['nama'] ?? ''));
                $volume     = (float) ($item['volume'] ?? 1);
                $satuan     = trim((string) ($item['satuan'] ?? ''));
                $hargaBahan = (float) ($item['harga_bahan'] ?? 0);
                $hargaAlat  = (float) ($item['harga_alat'] ?? 0);
                $hargaUpah  = (float) ($item['harga_upah'] ?? 0);
                $keterangan = $item['keterangan'] ?? null;

                if ($nama === '') {
                    continue;
                }

                if ($volume <= 0) {
                    $volume = 1;
                }

                $subtotalBahan = $volume * $hargaBahan;
                $subtotalAlat  = $volume * $hargaAlat;
                $subtotalUpah  = $volume * $hargaUpah;
                $total         = $subtotalBahan + $subtotalAlat + $subtotalUpah;

                $this->rapDetailModel->insert([
                    'id_rap'            => $rapId,
                    'id_kategori'       => $idKategori,
                    'id_parent'         => $idParent,
                    'pekerjaan'         => $nama,
                    'volume'            => $volume,
                    'satuan'            => $satuan,
                    'harga_bahan'       => $hargaBahan,
                    'harga_upah'        => $hargaUpah,
                    'harga_alat'        => $hargaAlat,
                    'subtotal_bahan'    => $subtotalBahan,
                    'subtotal_upah'     => $subtotalUpah,
                    'subtotal_alat'     => $subtotalAlat,
                    'total_keseluruhan' => $total,
                    'urutan'            => $urutan,
                    'keterangan'        => $keterangan,
                ]);
            }

            $this->recalculateRapTotal($rapId);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new DatabaseException('Gagal menyimpan pekerjaan');
            }

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Pekerjaan berhasil ditambahkan',
            ]);
        } catch (Throwable $e) {
            if ($db->transStatus()) {
                $db->transRollback();
            }

            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function deletePekerjaan($idRapDetail = null)
    {
        try {
            $idRapDetail = (int) $idRapDetail;

            if ($idRapDetail <= 0) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status'  => 'error',
                    'message' => 'id_rap_detail tidak valid',
                ]);
            }

            $detail = $this->rapDetailModel->find($idRapDetail);

            if (!$detail) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status'  => 'error',
                    'message' => 'Data pekerjaan tidak ditemukan',
                ]);
            }

            $rap = $this->rapModel->find($detail['id_rap']);

            if (!$rap) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status'  => 'error',
                    'message' => 'RAP tidak ditemukan',
                ]);
            }

            $project = $this->proyekModel
                ->where('id_project', $rap['id_project'])
                ->first();

            if (!$project) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status'  => 'error',
                    'message' => 'Project tidak ditemukan',
                ]);
            }

            if (($project['sumber_data'] ?? 'manual') !== 'manual') {
                return $this->response->setStatusCode(403)->setJSON([
                    'status'  => 'error',
                    'message' => 'Pekerjaan proyek estimator tidak dapat dihapus',
                ]);
            }

            $this->rapDetailItemModel
                ->where('id_rap_detail', $idRapDetail)
                ->delete();

            $this->rapDetailModel->delete($idRapDetail);

            $this->recalculateRapTotal((int) $detail['id_rap']);

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Pekerjaan berhasil dihapus',
            ]);
        } catch (Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function copyAhsEstimator()
    {
        $db = db_connect();

        try {
            $payload = $this->request->getJSON(true);

            $idRapDetail = (int) ($payload['id_rap_detail'] ?? 0);
            $idPekerjaan = trim((string) ($payload['id_pekerjaan'] ?? ''));

            if ($idRapDetail <= 0 || $idPekerjaan === '') {
                return $this->response->setStatusCode(400)->setJSON([
                    'status'  => 'error',
                    'message' => 'id_rap_detail dan id_pekerjaan wajib diisi',
                ]);
            }

            $ahsRows = $this->ahsModel
                ->where('id_pekerjaan', $idPekerjaan)
                ->orderBy('kategori', 'ASC')
                ->orderBy('id_ahs', 'ASC')
                ->findAll();

            if (empty($ahsRows)) {
                return $this->response->setJSON([
                    'status'  => 'success',
                    'message' => 'Tidak ada rincian AHS pada estimator',
                ]);
            }

            $db->transStart();

            $this->rapDetailItemModel
                ->where('id_rap_detail', $idRapDetail)
                ->delete();

            $urutan = 0;

            foreach ($ahsRows as $row) {
                $urutan++;

                $jenis = strtolower(trim((string) ($row['kategori'] ?? '')));
                if (!in_array($jenis, ['bahan', 'alat', 'upah'], true)) {
                    $jenis = 'bahan';
                }

                $koefisien   = (float) ($row['koefisien'] ?? 0);
                $hargaDasar  = (float) ($row['harga_dasar'] ?? 0);
                $hargaSatuan = $koefisien * $hargaDasar;

                $this->rapDetailItemModel->insert([
                    'id_rap_detail' => $idRapDetail,
                    'jenis_item'    => $jenis,
                    'nama_item'     => $row['nama_kategori'] ?? '-',
                    'koefisien'     => $koefisien,
                    'satuan'        => $row['satuan_kategori'] ?? '',
                    'harga_dasar'   => $hargaDasar,
                    'harga_satuan'  => $hargaSatuan,
                    'merk'          => $row['merk'] ?? null,
                    'spesifikasi'   => $row['spesifikasi'] ?? null,
                    'urutan'        => $urutan,
                    'keterangan'    => $row['keterangan'] ?? null,
                ]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new DatabaseException('Gagal copy AHS estimator');
            }

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Rincian AHS berhasil dicopy',
            ]);
        } catch (Throwable $e) {
            if ($db->transStatus()) {
                $db->transRollback();
            }

            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    protected function recalculateRapTotal(int $rapId): void
    {
        $details = $this->rapDetailModel
            ->where('id_rap', $rapId)
            ->findAll();

        $subtotalBahan = 0;
        $subtotalUpah  = 0;
        $subtotalAlat  = 0;
        $total         = 0;

        foreach ($details as $row) {
            $subtotalBahan += (float) ($row['subtotal_bahan'] ?? 0);
            $subtotalUpah  += (float) ($row['subtotal_upah'] ?? 0);
            $subtotalAlat  += (float) ($row['subtotal_alat'] ?? 0);
            $total         += (float) ($row['total_keseluruhan'] ?? 0);
        }

        $this->rapModel->update($rapId, [
            'subtotal_bahan'    => $subtotalBahan,
            'subtotal_upah'     => $subtotalUpah,
            'subtotal_alat'     => $subtotalAlat,
            'total_keseluruhan' => $total,
        ]);
    }

    /**
     * POST /api/rap/recalculate
     * Recalculates every rap_detail row's harga from its AHS items,
     * then updates the RAP-level totals.
     */
    public function recalculateFromAhs(): ResponseInterface
    {
        try {
            $payload   = $this->request->getJSON(true);
            $idProject = (int) ($payload['id_project'] ?? 0);

            if ($idProject <= 0) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status'  => 'error',
                    'message' => 'id_project wajib diisi',
                ]);
            }

            $rap = $this->rapModel->where('id_project', $idProject)->first();
            if (!$rap) {
                return $this->response->setJSON([
                    'status'  => 'success',
                    'message' => 'Tidak ada RAP untuk project ini',
                ]);
            }

            $rapId       = (int) $rap['id_rap'];
            $allDetails  = $this->rapDetailModel->where('id_rap', $rapId)->findAll();
            $itemModel   = $this->rapDetailItemModel;
            $updated     = 0;

            foreach ($allDetails as $detail) {
                $idDetail = (int) $detail['id_rap_detail'];
                $volume   = (float) ($detail['volume'] ?? 1);

                // Aggregate AHS items for this detail
                $ahsItems = $itemModel->where('id_rap_detail', $idDetail)->findAll();

                $totals = ['bahan' => 0.0, 'alat' => 0.0, 'upah' => 0.0];
                foreach ($ahsItems as $ai) {
                    $jenis  = strtolower($ai['jenis_item'] ?? 'bahan');
                    $jumlah = (float)($ai['koefisien'] ?? 0) * (float)($ai['harga_satuan'] ?? 0);
                    if (isset($totals[$jenis])) {
                        $totals[$jenis] += $jumlah;
                    }
                }

                $hargaBahan       = $totals['bahan'];
                $hargaAlat        = $totals['alat'];
                $hargaUpah        = $totals['upah'];
                $subtotalBahan    = $volume * $hargaBahan;
                $subtotalAlat     = $volume * $hargaAlat;
                $subtotalUpah     = $volume * $hargaUpah;
                $totalKeseluruhan = $subtotalBahan + $subtotalAlat + $subtotalUpah;

                $this->rapDetailModel->update($idDetail, [
                    'harga_bahan'       => $hargaBahan,
                    'harga_alat'        => $hargaAlat,
                    'harga_upah'        => $hargaUpah,
                    'subtotal_bahan'    => $subtotalBahan,
                    'subtotal_alat'     => $subtotalAlat,
                    'subtotal_upah'     => $subtotalUpah,
                    'total_keseluruhan' => $totalKeseluruhan,
                ]);

                $updated++;
            }

            // Rebuild RAP-level grand totals
            $this->recalculateRapTotal($rapId);

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => "{$updated} pekerjaan berhasil direkalikulasi dari AHS",
                'updated' => $updated,
            ]);
        } catch (Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function reorderPekerjaan()
    {
        try {
            $payload = $this->request->getJSON(true);
            $items   = $payload['items'] ?? [];

            if (!is_array($items) || empty($items)) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status'  => 'error',
                    'message' => 'Data items wajib diisi berupa array'
                ]);
            }

            $batchData = [];
            foreach ($items as $item) {
                if (isset($item['id_rap_detail'], $item['urutan'])) {
                    $row = [
                        'id_rap_detail' => (int) $item['id_rap_detail'],
                        'urutan'        => (int) $item['urutan'],
                    ];
                    if (array_key_exists('id_parent', $item)) {
                        $row['id_parent'] = ($item['id_parent'] === '' || $item['id_parent'] === null) ? null : (int) $item['id_parent'];
                    }
                    $batchData[] = $row;
                }
            }

            $db = db_connect();
            $db->transStart();

            foreach ($batchData as $data) {
                $id = $data['id_rap_detail'];
                unset($data['id_rap_detail']);
                $this->rapDetailModel->update($id, $data);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Gagal memperbarui urutan');
            }

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Urutan dan hierarki berhasil disimpan'
            ]);
        } catch (Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
    public function importBoq()
    {
        $db = db_connect();

        try {
            $payload = $this->request->getJSON(true);
            $idProject = (int) ($payload['id_project'] ?? 0);
            $items = $payload['items'] ?? [];

            file_put_contents(WRITEPATH . 'logs/import_dump.json', json_encode($items, JSON_PRETTY_PRINT));

            if ($idProject <= 0 || empty($items)) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status'  => 'error',
                    'message' => 'id_project dan items wajib diisi',
                ]);
            }

            $idUser = session()->get('id_user');
            $db->transStart();

            $rap = $this->rapModel->where('id_project', $idProject)->first();
            if (!$rap) {
                $this->rapModel->insert([
                    'id_project'        => $idProject,
                    'nama_rap'          => 'RAP Proyek ' . $idProject,
                    'subtotal_bahan'    => 0, 'subtotal_upah' => 0, 'subtotal_alat' => 0,
                    'total_keseluruhan' => 0, 'status_rap' => 'draft'
                ]);
                $rapId = (int) $this->rapModel->getInsertID();
            } else {
                $rapId = (int) $rap['id_rap'];
            }

            // Fungsi rekursif untuk simpan tree
            $this->saveImportTree($items, $rapId, $idProject, $idUser);

            $this->recalculateRapTotal($rapId);
            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new DatabaseException('Gagal import BOQ');
            }

            return $this->response->setJSON(['status' => 'success', 'message' => 'BOQ berhasil diimport']);
        } catch (Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    private function saveImportTree($items, $rapId, $idProject, $idUser, $idParent = null, $idKategori = null)
    {
        $currentKategori = $idKategori;

        foreach ($items as $idx => $item) {
            $currentParent = $idParent;

            if ($item['type'] === 'kategori') {
                if (!empty($item['id_kategori_master'])) {
                    $catId = (int) $item['id_kategori_master'];
                } else {
                    $nama = trim($item['nama']);
                    $existing = $this->kategoriModel->where('nama_kategori', $nama)
                        ->groupStart()->where('id_project', $idProject)->orWhere('id_project', null)->groupEnd()
                        ->first();

                    if ($existing) {
                        $catId = (int) $existing['id_kategori_pekerjaan'];
                    } else {
                        $this->kategoriModel->insert([
                            'nama_kategori' => $nama, 'id_project' => $idProject, 'id_user' => $idUser, 'jenis_kategori' => 'custom'
                        ]);
                        $catId = (int) $this->kategoriModel->getInsertID();
                    }
                }

                $existsInRap = $this->rapKategoriModel->where('id_rap', $rapId)->where('id_kategori', $catId)->first();
                if (!$existsInRap) {
                    $this->rapKategoriModel->insert(['id_rap' => $rapId, 'id_kategori' => $catId]);
                }
                $currentKategori = $catId;
                $currentParent = null; 
            } else {
                if ($currentKategori === null) {
                    // Fallback to error if frontend sends an item without a category above it
                    // This throws an exception which rolls back the transaction safely
                    throw new \Exception("Pekerjaan wajib diletakkan di bawah salah satu Kategori Pekerjaan");
                }

                $vol = (float)($item['volume'] ?? 1);
                $bh = (float)($item['harga_bahan'] ?? 0);
                $al = (float)($item['harga_alat'] ?? 0);
                $up = (float)($item['harga_upah'] ?? 0);
                
                $data = [
                    'id_rap' => $rapId,
                    'id_kategori' => $currentKategori,
                    'id_parent' => $currentParent,
                    'pekerjaan' => $item['nama'],
                    'volume' => $vol,
                    'satuan' => $item['satuan'] ?? '-',
                    'harga_bahan' => $bh,
                    'harga_upah' => $up,
                    'harga_alat' => $al,
                    'subtotal_bahan' => $vol * $bh,
                    'subtotal_upah' => $vol * $up,
                    'subtotal_alat' => $vol * $al,
                    'total_keseluruhan' => $vol * ($bh + $al + $up),
                    'urutan' => $idx + 1
                ];
                $this->rapDetailModel->insert($data);
                $currentParent = (int) $this->rapDetailModel->getInsertID();
            }

            if (!empty($item['children'])) {
                $this->saveImportTree($item['children'], $rapId, $idProject, $idUser, $currentParent, $currentKategori);
            }
        }
    }

    public function moveItem()
    {
        try {
            $payload = $this->request->getJSON(true);
            $id = (int)($payload['id'] ?? 0);
            $newParentId = isset($payload['new_parent_id']) ? (int)$payload['new_parent_id'] : null;

            if ($id <= 0) {
                return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'id wajib diisi']);
            }

            $this->rapDetailModel->update($id, ['id_parent' => $newParentId]);

            return $this->response->setJSON(['status' => 'success', 'message' => 'Item berhasil dipindahkan']);
        } catch (Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    private function buildTree(array $elements, $parentId = null)
    {
        $branch = [];
        foreach ($elements as $element) {
            if ($element['id_parent'] == $parentId) {
                $children = $this->buildTree($elements, $element['id_rap_detail']);
                $item = [
                    'id_rap_detail'    => (int) $element['id_rap_detail'],
                    'id_parent'        => $element['id_parent'] ? (int)$element['id_parent'] : null,
                    'uraian'           => $element['pekerjaan'],
                    'volume'           => (float) ($element['volume'] ?? 0),
                    'satuan'           => $element['satuan'] ?? '',
                    'hargaBahan'       => (float) ($element['harga_bahan'] ?? 0),
                    'hargaAlat'        => (float) ($element['harga_alat'] ?? 0),
                    'hargaUpah'        => (float) ($element['harga_upah'] ?? 0),
                    'hargaKeseluruhan' => (float) ($element['total_keseluruhan'] ?? 0),
                    'keterangan'       => $element['keterangan'] ?? null,
                    'children'         => $children
                ];
                $branch[] = $item;
            }
        }
        return $branch;
    }
}