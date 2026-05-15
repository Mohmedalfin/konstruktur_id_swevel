<?php

namespace App\Services;

use App\Models\ProyekModel;
use App\Models\KategoriPekerjaanModel;
use App\Models\RapDetailModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use InvalidArgumentException;

class ScheduleService
{
    protected $proyekModel;
    protected $kategoriModel;
    protected $rapDetailModel;

    public function __construct()
    {
        $this->proyekModel = new ProyekModel();
        $this->kategoriModel = new KategoriPekerjaanModel();
        $this->rapDetailModel = new RapDetailModel();
    }

    public function getSchedulePageData(?string $slug): array
    {
        $idProject = null;
        $categories = [];

        if ($slug) {
            $project = $this->proyekModel->where('slug', $slug)->first();
            if (!$project) {
                throw PageNotFoundException::forPageNotFound();
            }
            $idProject = $project['id_project'];

            $categories = $this->kategoriModel
                ->groupStart()
                    ->where('jenis_kategori', 'sistem')
                    ->orGroupStart()
                        ->where('jenis_kategori', 'custom')
                        ->where('id_project', $idProject)
                    ->groupEnd()
                ->groupEnd()
                ->orderBy('nama_kategori', 'ASC')
                ->findAll();
        }

        return [
            'slug' => $slug,
            'idProject' => $idProject,
            'categories' => $categories
        ];
    }

    public function getScheduleDataWithWeight(int $idProject): array
    {
        $project = $this->proyekModel->where('id_project', $idProject)->first();
        if (!$project) {
            throw PageNotFoundException::forPageNotFound();
        }

        $rapModel = new \App\Models\RapModel();
        $rapKategoriModel = new \App\Models\RapKategoriModel();
        
        $rap = $rapModel->where('id_project', $idProject)->first();
        if (!$rap) {
            return [];
        }

        $rapId = (int) $rap['id_rap'];
        $totalProyek = (float) $rap['total_keseluruhan'];
        
        if ($totalProyek <= 0) {
            $sum = $this->rapDetailModel->where('id_rap', $rapId)->selectSum('total_keseluruhan')->get()->getRow()->total_keseluruhan;
            $totalProyek = (float) $sum > 0 ? (float) $sum : 1;
        }

        $kategoriRows = $rapKategoriModel
            ->select('rap_kategori.id_kategori, kategori_pekerjaan.nama_kategori')
            ->join('kategori_pekerjaan', 'kategori_pekerjaan.id_kategori_pekerjaan = rap_kategori.id_kategori', 'left')
            ->where('rap_kategori.id_rap', $rapId)
            ->groupStart()
                ->where('kategori_pekerjaan.id_project', $idProject)
                ->orWhere('kategori_pekerjaan.id_project', null)
            ->groupEnd()
            ->orderBy('kategori_pekerjaan.nama_kategori', 'ASC')
            ->findAll();

        $detailRows = $this->rapDetailModel
            ->select('id_rap_detail, id_rap, id_kategori, id_parent, pekerjaan, urutan, start_date, finish_date, total_keseluruhan')
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

        $itemsByCategory = [];
        foreach ($detailRows as $row) {
            $catId = (string) ($row['id_kategori'] ?? '0');
            $itemsByCategory[$catId][] = $row;
        }

        foreach ($grouped as $catId => &$data) {
            $categoryItems = $itemsByCategory[$catId] ?? [];
            $data['items'] = $this->buildScheduleTree($categoryItems, null, $totalProyek);
        }

        return array_values($grouped);
    }

    private function buildScheduleTree(array $elements, $parentId, float $totalProyek): array
    {
        $branch = [];
        foreach ($elements as $element) {
            if ($element['id_parent'] == $parentId) {
                $children = $this->buildScheduleTree($elements, $element['id_rap_detail'], $totalProyek);
                
                $itemTotal = (float) ($element['total_keseluruhan'] ?? 0);
                $weight = $totalProyek > 0 ? ($itemTotal / $totalProyek) * 100 : 0;

                $item = [
                    'id_rap_detail' => (int) $element['id_rap_detail'],
                    'id_parent'     => $element['id_parent'] ? (int)$element['id_parent'] : null,
                    'uraian'        => $element['pekerjaan'],
                    'start_date'    => $element['start_date'] ?? null,
                    'finish_date'   => $element['finish_date'] ?? null,
                    'weight'        => round($weight, 4),
                    'children'      => $children
                ];
                $branch[] = $item;
            }
        }
        return $branch;
    }

    public function updateScheduleDates(array $payload): array
    {
        $idRapDetail = (int) ($payload['id_rap_detail'] ?? 0);
        $startDate = $payload['start_date'] ?? null;
        $finishDate = $payload['finish_date'] ?? null;

        if ($idRapDetail <= 0) {
            throw new InvalidArgumentException('ID Rap Detail wajib diisi', 400);
        }

        if ($startDate !== null && $startDate !== '' && $finishDate !== null && $finishDate !== '') {
            if (strtotime($startDate) > strtotime($finishDate)) {
                throw new InvalidArgumentException('Tanggal mulai tidak boleh lebih besar dari tanggal selesai', 400);
            }
        }

        $detail = $this->rapDetailModel->find($idRapDetail);
        if (!$detail) {
            throw new InvalidArgumentException('Item detail tidak ditemukan', 404);
        }

        $updateData = [];
        if ($startDate !== null) {
            $updateData['start_date'] = $startDate === '' ? null : $startDate;
        }
        if ($finishDate !== null) {
            $updateData['finish_date'] = $finishDate === '' ? null : $finishDate;
        }

        if (!empty($updateData)) {
            $this->rapDetailModel->update($idRapDetail, $updateData);
        }

        return [
            'status' => 'success',
            'message' => 'Tanggal berhasil diperbarui'
        ];
    }
}
