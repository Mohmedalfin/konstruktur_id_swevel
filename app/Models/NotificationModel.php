<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table            = 'notifications';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['user_id', 'role_target', 'title', 'message', 'link', 'icon', 'color', 'source_module', 'is_read', 'created_at', 'updated_at'];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Ambil notifikasi aktif berdasarkan user_id dan/atau role user
     */
    public function getForUser(int $userId, string $role, int $limit = 50)
    {
        return $this->groupStart()
                        ->where('user_id', $userId)
                        ->orWhere('role_target', strtolower($role))
                    ->groupEnd()
                    ->orderBy('created_at', 'DESC')
                    ->findAll($limit);
    }

    /**
     * Hitung notifikasi belum dibaca berdasarkan user_id dan/atau role user
     */
    public function getUnreadCount(int $userId, string $role)
    {
        return $this->groupStart()
                        ->where('user_id', $userId)
                        ->orWhere('role_target', strtolower($role))
                    ->groupEnd()
                    ->where('is_read', 0)
                    ->countAllResults();
    }
}
