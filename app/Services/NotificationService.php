<?php

namespace App\Services;

use App\Models\NotificationModel;

class NotificationService
{
    protected $model;

    public function __construct()
    {
        $this->model = new NotificationModel();
    }

    /**
     * Kirim notifikasi ke user tertentu
     */
    public function sendToUser(int $userId, string $title, string $message, string $link = null, string $icon = 'fa-solid fa-bell', string $color = 'blue', string $sourceModule = null)
    {
        return $this->model->insert([
            'user_id'       => $userId,
            'role_target'   => null,
            'title'         => $title,
            'message'       => $message,
            'link'          => $link,
            'icon'          => $icon,
            'color'         => $color,
            'source_module' => $sourceModule,
            'is_read'       => 0
        ]);
    }

    /**
     * Kirim notifikasi ke seluruh role/divisi tertentu (misal: 'gudang' atau 'purchasing')
     */
    public function sendToRole(string $role, string $title, string $message, string $link = null, string $icon = 'fa-solid fa-bell', string $color = 'blue', string $sourceModule = null)
    {
        return $this->model->insert([
            'user_id'       => null,
            'role_target'   => strtolower($role),
            'title'         => $title,
            'message'       => $message,
            'link'          => $link,
            'icon'          => $icon,
            'color'         => $color,
            'source_module' => $sourceModule,
            'is_read'       => 0
        ]);
    }
}
