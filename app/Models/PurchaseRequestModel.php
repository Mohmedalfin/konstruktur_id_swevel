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
        'pr_number', 
        'request_date', 
        'status'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getPRsWithItemCount()
    {
        $db = \Config\Database::connect();
        return $db->table('purchase_requests')
            ->select('purchase_requests.*, COUNT(purchase_request_items.id) as total_items')
            ->join('purchase_request_items', 'purchase_request_items.pr_id = purchase_requests.id', 'left')
            ->groupBy('purchase_requests.id')
            ->orderBy('purchase_requests.created_at', 'DESC')
            ->get()
            ->getResultArray();
    }
}
