<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use Throwable;
use App\Models\RapDetailItemModel;
use App\Models\RapDetailModel;
use App\Models\RapModel;

class AhsController extends BaseController
{
    public function getRincian($id_rap_detail): ResponseInterface
    {
        try {
            $model = new RapDetailItemModel();
            $data  = $model->where('id_rap_detail', $id_rap_detail)
                           ->orderBy('jenis_item', 'ASC')
                           ->orderBy('id_rap_detail_item', 'ASC')
                           ->findAll();
            
            // Format for frontend
            $formatted = array_map(function($row) {
                return [
                    'id'          => $row['id_rap_detail_item'],
                    'tipe'        => $row['jenis_item'], // mapped mapping
                    'uraian'      => $row['nama_item'],
                    'merk'        => $row['merk']        ?? '',
                    'spesifikasi' => $row['spesifikasi'] ?? '',
                    'koefisien'   => (float)$row['koefisien'],
                    'satuan'      => $row['satuan'],
                    'hargaSatuan' => (float)$row['harga_satuan'],
                    'sumber'      => $row['keterangan']  ?? '',
                ];
            }, $data);

            return $this->response->setJSON([
                'status' => 'success',
                'data'   => $formatted
            ]);
        } catch (Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function saveRincian(): ResponseInterface
    {
        try {
            $json   = $this->request->getJSON(true);
            $idDetail = $json['id_rap_detail'] ?? null;
            $items    = $json['items']          ?? [];

            if (!$idDetail) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'id_rap_detail wajib diisi'
                ]);
            }

            $model = new RapDetailItemModel();
            
            // ── Transaction ──────────────────────────────────────────────────
            $db = \Config\Database::connect();
            $db->transBegin();

            // 1. Delete existing
            $model->where('id_rap_detail', $idDetail)->delete();

            // 2. Insert new
            foreach ($items as $index => $item) {
                $inserted = $model->insert([
                    'id_rap_detail' => $idDetail,
                    'jenis_item'    => $item['tipe']   ?? 'bahan',
                    'nama_item'     => $item['uraian'] ?? '',
                    'merk'          => $item['merk']   ?? '',
                    'spesifikasi'   => $item['spesifikasi'] ?? '',
                    'koefisien'     => $item['koefisien']   ?? 0,
                    'satuan'        => $item['satuan']      ?? '',
                    'harga_dasar'   => $item['hargaSatuan'] ?? 0,
                    'harga_satuan'  => $item['hargaSatuan'] ?? 0,
                    'keterangan'    => $item['sumber']      ?? '',
                    'urutan'        => $index + 1,
                ]);
                if (!$inserted) {
                    $db->transRollback();
                    $errors = implode(', ', $model->errors() ?: ['Unknown detail']);
                    throw new \Exception('Insert failed: ' . $errors);
                }
            }

            if ($db->transStatus() === false) {
                $db->transRollback();
                throw new \Exception('Gagal menyimpan rincian ke database');
            }

            $db->transCommit();

            $rapDetailModel = new RapDetailModel();
            $rapDetail = $rapDetailModel->find($idDetail);

            if ($rapDetail) {
                $totals = ['bahan' => 0.0, 'alat' => 0.0, 'upah' => 0.0];

                $savedItems = $model
                    ->where('id_rap_detail', $idDetail)
                    ->findAll();

                foreach ($savedItems as $si) {
                    $jenis  = strtolower($si['jenis_item'] ?? 'bahan');
                    $jumlah = (float)($si['koefisien'] ?? 0) * (float)($si['harga_satuan'] ?? 0);
                    if (isset($totals[$jenis])) {
                        $totals[$jenis] += $jumlah;
                    }
                }

                $volume           = (float)($rapDetail['volume'] ?? 1);
                $hargaBahan       = $totals['bahan'];
                $hargaAlat        = $totals['alat'];
                $hargaUpah        = $totals['upah'];
                $subtotalBahan    = $volume * $hargaBahan;
                $subtotalAlat     = $volume * $hargaAlat;
                $subtotalUpah     = $volume * $hargaUpah;
                $totalKeseluruhan = $subtotalBahan + $subtotalAlat + $subtotalUpah;

                $rapDetailModel->update($idDetail, [
                    'harga_bahan'       => $hargaBahan,
                    'harga_alat'        => $hargaAlat,
                    'harga_upah'        => $hargaUpah,
                    'subtotal_bahan'    => $subtotalBahan,
                    'subtotal_alat'     => $subtotalAlat,
                    'subtotal_upah'     => $subtotalUpah,
                    'total_keseluruhan' => $totalKeseluruhan,
                ]);

                $rapModel  = new RapModel();
                $idRap     = (int)($rapDetail['id_rap'] ?? 0);
                if ($idRap > 0) {
                    $allDetails = $rapDetailModel->where('id_rap', $idRap)->findAll();
                    $grandTotal = array_reduce($allDetails, function ($carry, $d) {
                        return $carry + (float)($d['total_keseluruhan'] ?? 0);
                    }, 0.0);
                    $rapModel->update($idRap, ['total_keseluruhan' => $grandTotal]);
                }
            }

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Rincian AHS berhasil disimpan'
            ]);

        } catch (Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function deleteItem($id_rap_detail_item): ResponseInterface
    {
        try {
            $model = new RapDetailItemModel();
            if ($model->delete($id_rap_detail_item)) {
                return $this->response->setJSON([
                    'status'  => 'success',
                    'message' => 'Item berhasil dihapus'
                ]);
            } else {
                throw new \Exception('Gagal menghapus item dari database');
            }
        } catch (Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function index(): ResponseInterface
    {
        try {
            $db     = \Config\Database::connect('estimator');
            $search = $this->request->getGet('q');
            $tipe   = $this->request->getGet('tipe');
            $page   = max(1, (int) $this->request->getGet('page'));
            $limit  = 20;
            $offset = ($page - 1) * $limit;

            $sql = "
                SELECT * FROM (
                    SELECT
                        id_bahan      AS id,
                        nama_bahan    AS uraian,
                        satuan,
                        keterangan    AS sumber,
                        spesifikasi,
                        merk,
                        'bahan'       AS tipe,
                        harga_dasar   AS hargaSatuan
                    FROM bahan_utama

                    UNION ALL

                    SELECT
                        id_upah       AS id,
                        nama_upah     AS uraian,
                        satuan,
                        keterangan    AS sumber,
                        spesifikasi,
                        merk,
                        'upah'        AS tipe,
                        harga_dasar   AS hargaSatuan
                    FROM upah_utama

                    UNION ALL

                    SELECT
                        id_alat       AS id,
                        nama_alat     AS uraian,
                        satuan,
                        keterangan    AS sumber,
                        spesifikasi,
                        merk,
                        'alat'        AS tipe,
                        harga_dasar   AS hargaSatuan
                    FROM alat_utama
                ) AS master_bua
                WHERE 1=1
            ";

            $params = [];

            if (!empty($search)) {
                $sql .= ' AND (master_bua.uraian LIKE ? OR master_bua.merk LIKE ? OR master_bua.spesifikasi LIKE ?)';
                $term      = "%{$search}%";
                $params[]  = $term;
                $params[]  = $term;
                $params[]  = $term;
            }

            if (!empty($tipe) && $tipe !== 'all') {
                $sql     .= ' AND master_bua.tipe = ?';
                $params[] = $tipe;
            }

            $sql .= " ORDER BY master_bua.uraian ASC, master_bua.id ASC
                      LIMIT {$limit} OFFSET {$offset}";

            $data = $db->query($sql, $params)->getResultArray();

            foreach ($data as &$row) {
                $row['hargaSatuan'] = (float) $row['hargaSatuan'];
            }
            unset($row);

            return $this->response
                ->setStatusCode(ResponseInterface::HTTP_OK)
                ->setJSON([
                    'status' => 'success',
                    'page'   => $page,
                    'limit'  => $limit,
                    'data'   => $data,
                ]);

        } catch (\Throwable $e) {
            log_message('error', '[AhsController::index] ' . $e->getMessage());

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'status'  => 'error',
                    'message' => 'Gagal memuat data AHS. Silakan coba lagi.',
                ]);
        }
    }
}