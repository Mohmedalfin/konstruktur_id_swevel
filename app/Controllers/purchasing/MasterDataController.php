<?php

namespace App\Controllers\purchasing;

use App\Controllers\BaseController;
use App\Models\SupplierModel;
use App\Models\MasterBarangModel;
use App\Models\MaterialSupplierModel;

class MasterDataController extends BaseController
{
    protected $supplierModel;
    protected $masterBarangModel;
    protected $materialSupplierModel;

    public function __construct()
    {
        $this->supplierModel = new SupplierModel();
        $this->masterBarangModel = new MasterBarangModel();
        $this->materialSupplierModel = new MaterialSupplierModel();
    }

    public function index()
    {
        $data = [
            'title'     => 'Master Data Purchasing - Supplier',
            'suppliers' => $this->supplierModel->orderBy('id', 'DESC')->findAll(),
        ];

        $data['activeNav'] = 'master-data';
        return view('purchasing/master-data/index', $data);
    }

    public function material()
    {
        $data = [
            'title'     => 'Master Data Purchasing - Material',
            'materials' => $this->masterBarangModel->where('id_perusahaan', session()->get('id_perusahaan'))->orderBy('id', 'DESC')->findAll(),
        ];

        $data['activeNav'] = 'master-data';
        return view('purchasing/master-data/material', $data);
    }

    public function harga()
    {
        $group = $this->request->getGet('group') ?? 'none';
        $hargasFlat = $this->materialSupplierModel->getHargaWithDetails(session()->get('id_perusahaan'));
        
        $hargasGrouped = [];
        if ($group === 'supplier') {
            foreach ($hargasFlat as $item) {
                $hargasGrouped[$item['nama_supplier']][] = $item;
            }
            ksort($hargasGrouped); // Sort by supplier name
        } elseif ($group === 'material') {
            foreach ($hargasFlat as $item) {
                $hargasGrouped[$item['nama_material']][] = $item;
            }
            ksort($hargasGrouped); // Sort by material name
        } else {
            $hargasGrouped['Semua Data'] = $hargasFlat;
        }

        $data = [
            'title'     => 'Master Data Purchasing - Harga',
            'hargas'    => $hargasFlat,
            'hargasGrouped' => $hargasGrouped,
            'group'     => $group,
            'suppliers' => $this->supplierModel->orderBy('nama_supplier', 'ASC')->findAll(),
            'materials' => $this->masterBarangModel->where('id_perusahaan', session()->get('id_perusahaan'))->orderBy('nama_barang', 'ASC')->findAll(),
        ];

        $data['activeNav'] = 'master-data';
        return view('purchasing/master-data/harga', $data);
    }

    // --- SUPPLIER METHODS ---

    public function getSuppliers()
    {
        $suppliers = $this->supplierModel->orderBy('id', 'DESC')->findAll();
        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $suppliers
        ]);
    }

    public function storeSupplier()
    {
        $json = $this->request->getJSON(true); // get as array

        if (!$json) {
            $json = $this->request->getPost();
        }

        $rules = [
            'nama_supplier' => 'required',
        ];

        if (!$this->validateData($json, $rules)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Nama supplier wajib diisi',
                'errors'  => $this->validator->getErrors()
            ]);
        }

        $save = $this->supplierModel->save([
            'nama_supplier' => $json['nama_supplier'] ?? null,
            'telepon'       => $json['telepon'] ?? null,
            'email'         => $json['email'] ?? null,
            'alamat'        => $json['alamat'] ?? null,
            'npwp'          => $json['npwp'] ?? null,
            'rekening_bank' => $json['rekening_bank'] ?? null,
        ]);

        if ($save) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Supplier berhasil ditambahkan'
            ]);
        }

        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'Gagal menambahkan supplier'
        ]);
    }

    public function updateSupplier($id)
    {
        $json = $this->request->getJSON(true);

        if (!$json) {
            $json = $this->request->getRawInput();
        }

        $rules = [
            'nama_supplier' => 'required',
        ];

        if (!$this->validateData($json, $rules)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Nama supplier wajib diisi',
                'errors'  => $this->validator->getErrors()
            ]);
        }

        $update = $this->supplierModel->update($id, [
            'nama_supplier' => $json['nama_supplier'] ?? null,
            'telepon'       => $json['telepon'] ?? null,
            'email'         => $json['email'] ?? null,
            'alamat'        => $json['alamat'] ?? null,
            'npwp'          => $json['npwp'] ?? null,
            'rekening_bank' => $json['rekening_bank'] ?? null,
        ]);

        if ($update) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Supplier berhasil diperbarui'
            ]);
        }

        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'Gagal memperbarui supplier'
        ]);
    }

    public function deleteSupplier($id)
    {
        $delete = $this->supplierModel->delete($id);

        if ($delete) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Supplier berhasil dihapus'
            ]);
        }

        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'Gagal menghapus supplier'
        ]);
    }

    // --- MATERIAL METHODS ---

    public function getMaterials()
    {
        $materials = $this->masterBarangModel->where('id_perusahaan', session()->get('id_perusahaan'))->orderBy('id', 'DESC')->findAll();
        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $materials
        ]);
    }

    // --- HARGA METHODS ---

    public function storeHarga()
    {
        $json = $this->request->getJSON(true);

        if (!$json) {
            $json = $this->request->getPost();
        }

        $rules = [
            'supplier_id' => 'required',
            'material_id' => 'required',
            'harga'       => 'required|numeric',
        ];

        if (!$this->validateData($json, $rules)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Data harga belum lengkap atau tidak valid',
                'errors'  => $this->validator->getErrors()
            ]);
        }

        $save = $this->materialSupplierModel->save([
            'supplier_id' => $json['supplier_id'],
            'id_barang'   => $json['material_id'],
            'harga'       => $json['harga'],
        ]);

        if ($save) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Harga berhasil ditambahkan'
            ]);
        }

        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'Gagal menambahkan harga'
        ]);
    }

    public function updateHarga($id)
    {
        $json = $this->request->getJSON(true);

        if (!$json) {
            $json = $this->request->getRawInput();
        }

        $rules = [
            'supplier_id' => 'required',
            'material_id' => 'required',
            'harga'       => 'required|numeric',
        ];

        if (!$this->validateData($json, $rules)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Data harga belum lengkap atau tidak valid',
                'errors'  => $this->validator->getErrors()
            ]);
        }

        $update = $this->materialSupplierModel->update($id, [
            'supplier_id' => $json['supplier_id'],
            'id_barang'   => $json['material_id'],
            'harga'       => $json['harga'],
        ]);

        if ($update) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Harga berhasil diperbarui'
            ]);
        }

        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'Gagal memperbarui harga'
        ]);
    }

    public function deleteHarga($id)
    {
        $delete = $this->materialSupplierModel->delete($id);

        if ($delete) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Harga berhasil dihapus'
            ]);
        }

        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'Gagal menghapus harga'
        ]);
    }
}
