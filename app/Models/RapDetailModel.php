<?php

namespace App\Models;

use CodeIgniter\Model;

class RapDetailModel extends Model
{
        protected $DBGroup = 'default';
        protected $table = 'rap_detail';
        protected $primaryKey = 'id_rap_detail';
        protected $useAutoIncrement = true;
        protected $returnType = 'array';
        protected $protectFields = true;

        protected $allowedFields = [
                'id_rap',
                'id_kategori',
                'pekerjaan',
                'volume',
                'satuan',
                'harga_bahan',
                'harga_upah',
                'harga_alat',
                'subtotal_bahan',
                'subtotal_upah',
                'subtotal_alat',
                'total_keseluruhan',
                'urutan',
                'id_parent',
                'keterangan',
                'sumber',
                'nomor_custom',
                'start_date',
                'finish_date',
        ];

        protected $useTimestamps = false;
}