<?php

namespace App\Models;

use CodeIgniter\Model;

class RapDetailModel extends Model
{
    protected $table         = 'rap_detail';
    protected $primaryKey    = 'id_detail_rap';
    protected $returnType    = 'array';
    protected $useSoftDeletes = false;
    protected $useTimestamps  = false;

    protected $allowedFields = [
        'id_rap', 'id_ahs', 'koefesien', 'harga_satuan',
    ];
}
