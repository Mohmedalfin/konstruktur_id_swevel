<?php

namespace App\Models;

use CodeIgniter\Model;

class PurchaseRequestModel extends Model
{
    protected $table            = 'purchase_requests';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_perusahaan',
        'pr_number', 
        'request_date', 
        'status',
        'keterangan',
        'created_by'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    public function getPRsWithItemCount($id_perusahaan = null)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('purchase_requests')
            ->select('purchase_requests.*, COUNT(purchase_request_items.id) as total_items')
            ->join('purchase_request_items', 'purchase_request_items.pr_id = purchase_requests.id', 'left');
            
        if ($id_perusahaan !== null) {
            $builder->where('purchase_requests.id_perusahaan', $id_perusahaan);
        }

        return $builder->groupBy('purchase_requests.id')
            ->orderBy('purchase_requests.created_at', 'DESC')
            ->get()
            ->getResultArray();
    }
}
