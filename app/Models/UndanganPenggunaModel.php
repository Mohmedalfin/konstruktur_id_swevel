<?php

namespace App\Models;

use CodeIgniter\Model;

class UndanganPenggunaModel extends Model
{
    protected $table            = 'undangan_pengguna';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    
    protected $allowedFields    = [
        'email', 'kategori_akun', 'parent_id', 'token', 'status', 'expires_at', 'created_at', 'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'email'         => 'required|valid_email',
        'kategori_akun' => 'required',
        'parent_id'     => 'required|integer',
        'token'         => 'required|exact_length[64]',
        'status'        => 'required|in_list[pending,accepted,expired]',
        'expires_at'    => 'required|valid_date[Y-m-d H:i:s]'
    ];

    protected $skipValidation = false;

    /**
     * Cari undangan berdasarkan token yang masih berlaku.
     * 
     * @param string $token
     * @return object|null
     */
    public function getActiveInvitation(string $token)
    {
        return $this->where('token', $token)
                    ->where('status', 'pending')
                    ->where('expires_at >=', date('Y-m-d H:i:s'))
                    ->first();
    }
}
