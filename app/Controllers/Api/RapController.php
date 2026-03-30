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

            foreach ($detailRows as $row) {
                $catId = (string) ($row['id_kategori'] ?? '0');

                if (!isset($grouped[$catId])) {
                    $kategori = $this->kategoriModel->find((int) $catId);

                    $grouped[$catId] = [
                        'id'    => $catId,
                        'name'  => $kategori['nama_kategori'] ?? 'Tanpa Kategori',
                        'items' => [],
                    ];
                }

                $grouped[$catId]['items'][] = [
                    'id_rap_detail'    => (int) $row['id_rap_detail'],
                    'no'               => count($grouped[$catId]['items']) + 1,
                    'uraian'           => $row['pekerjaan'],
                    'volume'           => (float) ($row['volume'] ?? 0),
                    'satuan'           => $row['satuan'] ?? '',
                    'hargaBahan'       => (float) ($row['harga_bahan'] ?? 0),
                    'hargaAlat'        => (float) ($row['harga_alat'] ?? 0),
                    'hargaUpah'        => (float) ($row['harga_upah'] ?? 0),
                    'hargaKeseluruhan' => (float) ($row['total_keseluruhan'] ?? 0),
                    'keterangan'       => $row['keterangan'] ?? null,
                ];
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
                    'id'   => (string) $row['id_kategori_pekerjaan'],
                    'nama' => $row['nama_kategori'],
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
}