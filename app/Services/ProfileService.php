<?php

namespace App\Services;

use App\Models\PenggunaModel;

class ProfileService
{
    protected $penggunaModel;
    protected const SUBACCOUNT_PARENT_PREFIX = 'PARENT:';
    protected const ALLOWED_SUBACCOUNT_ROLES = ['Gudang', 'Purchasing'];

    public function __construct()
    {
        $this->penggunaModel = new PenggunaModel();
    }

    public function getDetailProfile(int $id_pengguna)
    {
        return $this->penggunaModel->getProfileData($id_pengguna);
    }

    /**
     * Memperbarui data profil pengguna, termasuk menangani upload foto baru.
     * 
     * @param int $id_pengguna
     * @param array $data Data teks dari form
     * @param mixed $foto File upload object (bisa null)
     * @return array Status sukses atau pesan error
     */
    public function updateProfile(int $id_pengguna, array $data, $foto = null)
    {
        $user = $this->penggunaModel->find($id_pengguna);
        
        if (!$user) {
            return ['success' => false, 'message' => 'Pengguna tidak ditemukan'];
        }

        $data['id_pengguna'] = $id_pengguna;

        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $newName = $foto->getRandomName();
            $uploadPath = FCPATH . 'uploads/profile/';
            
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $foto->move($uploadPath, $newName);
            
            $data['foto'] = 'uploads/profile/' . $newName;

            if (!empty($user->foto) && file_exists(FCPATH . $user->foto) && strpos($user->foto, 'default-avatar') === false) {
                @unlink(FCPATH . $user->foto);
            }
        }

        if ($this->penggunaModel->update($id_pengguna, $data)) {
            return ['success' => true, 'message' => 'Profil berhasil diperbarui'];
        } else {
            return [
                'success' => false, 
                'message' => 'Gagal memperbarui profil. Pastikan data valid.', 
                'errors' => $this->penggunaModel->errors()
            ];
        }
    }

    /**
     * Mengambil daftar sub-account milik akun utama.
     */
    public function getSubAccounts(int $id_pengguna): array
    {
        $items = $this->penggunaModel
            ->where('parent_id', $id_pengguna)
            ->orderBy('id_pengguna', 'DESC')
            ->findAll();

        $safe = [];
        foreach ($items as $item) {
            if (is_object($item) && property_exists($item, 'password')) {
                unset($item->password);
            }
            $safe[] = $item;
        }

        return $safe;
    }

    /**
     * Mengambil daftar undangan tertunda (pending) milik akun utama.
     */
    public function getPendingInvitations(int $parentId): array
    {
        $undanganModel = new \App\Models\UndanganPenggunaModel();
        
        // Bersihkan secara otomatis undangan yang sudah lewat batas waktu (expires_at)
        $undanganModel->where('status', 'pending')
                      ->where('expires_at <', date('Y-m-d H:i:s'))
                      ->set(['status' => 'expired'])
                      ->update();

        return $undanganModel
            ->where('parent_id', $parentId)
            ->where('status', 'pending')
            ->orderBy('id', 'DESC')
            ->findAll();
    }

    /**
     * Membuat undangan sub-account baru untuk Gudang/Purchasing.
     */
    public function createSubAccount(int $parentId, array $payload): array
    {
        $email = trim((string) ($payload['email'] ?? ''));
        $role = trim((string) ($payload['kategori_akun'] ?? ''));

        if ($email === '' || $role === '') {
            return ['success' => false, 'message' => 'Email dan role wajib diisi'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Format email tidak valid'];
        }

        if (!in_array($role, self::ALLOWED_SUBACCOUNT_ROLES, true)) {
            return ['success' => false, 'message' => 'Role tidak valid. Harus Gudang atau Purchasing'];
        }

        $parent = $this->penggunaModel->find($parentId);
        if (!$parent) {
            return ['success' => false, 'message' => 'Akun utama tidak ditemukan'];
        }

        // 1. Cek apakah email sudah terdaftar di tabel pengguna
        $existingUser = $this->penggunaModel->where('email', $email)->first();
        if ($existingUser) {
            return ['success' => false, 'message' => 'Email ini sudah terdaftar sebagai pengguna aktif'];
        }

        $undanganModel = new \App\Models\UndanganPenggunaModel();

        // 2. Cek apakah ada undangan pending yang masih aktif untuk email ini
        $existingInvite = $undanganModel->where('email', $email)
                                        ->where('status', 'pending')
                                        ->where('expires_at >=', date('Y-m-d H:i:s'))
                                        ->first();
        if ($existingInvite) {
            return [
                'success' => false, 
                'message' => 'Undangan untuk email ini sudah pernah dikirim dan masih berlaku.',
                'invite_link' => base_url('accept-invite?token=' . $existingInvite->token)
            ];
        }

        // Hapus undangan lama (expired/pending yang kedaluwarsa) agar rapi
        $undanganModel->where('email', $email)->delete();

        // 3. Buat secure token acak (64 karakter)
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+48 hours'));

        $data = [
            'email'         => $email,
            'kategori_akun' => $role,
            'parent_id'     => $parentId,
            'token'         => $token,
            'status'        => 'pending',
            'expires_at'    => $expiresAt,
        ];

        try {
            $insertId = $undanganModel->insert($data, true);

            if (!$insertId) {
                return [
                    'success' => false,
                    'message' => 'Gagal mengirim undangan',
                    'errors'  => $undanganModel->errors(),
                ];
            }

            $inviteLink = base_url('accept-invite?token=' . $token);

            // Kirim email undangan nyata menggunakan Brevo / Email Service
            $emailSent = $this->sendInvitationEmail($email, $inviteLink, $role, $parent->perusahaan ?? 'Admin');

            return [
                'success'     => true, 
                'message'     => $emailSent 
                                    ? 'Undangan berhasil dikirim ke ' . $email 
                                    : 'Undangan dibuat, namun gagal mengirimkan email. Silakan salin tautan di bawah.', 
                'invite_link' => $inviteLink,
                'id_undangan' => $insertId
            ];
        } catch (\Throwable $e) {
            log_message('error', '[ProfileService::createSubAccount] ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan internal pada server'];
        }
    }

    /**
     * Mengirim email undangan menggunakan Brevo SMTP / CI4 Email Service
     */
    private function sendInvitationEmail(string $recipientEmail, string $inviteLink, string $role, string $companyName): bool
    {
        $email = \Config\Services::email();

        // Template HTML premium (Bebas scroll, warna dasar biru pekat, font Plus Jakarta Sans)
        $message = "
        <div style='font-family: Arial, sans-serif; background-color: #f4f5f7; padding: 40px 20px; text-align: center;'>
            <div style='max-width: 500px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(15,23,69,0.08); border: 1px solid #e2e8f0; text-align: left;'>
                
                <!-- Header (Biru Pekat #162345) -->
                <div style='background-color: #162345; padding: 30px; text-align: center;'>
                    <h2 style='color: #ffffff; margin: 0; font-size: 20px; font-weight: bold; letter-spacing: 1px;'>KONTRAKTOR.ID</h2>
                </div>
                
                <!-- Body -->
                <div style='padding: 30px; color: #334155; line-height: 1.6;'>
                    <h3 style='color: #1e293b; margin-top: 0; font-size: 16px; font-weight: bold;'>Halo!</h3>
                    <p style='font-size: 13px; color: #475569;'>
                        Anda telah diundang oleh <strong>{$companyName}</strong> untuk bergabung sebagai staff <strong>" . esc(ucfirst($role)) . "</strong> di platform manajemen konstruksi modern <strong>Kontraktor.id</strong>.
                    </p>
                    <p style='font-size: 13px; color: #475569;'>
                        Dengan akun ini, Anda dapat mengelola kebutuhan proyek serta berkolaborasi dengan tim secara terpadu. Silakan klik tombol di bawah untuk mengaktifkan akun Anda:
                    </p>
                    
                    <!-- Action Button (Gradasi Kuning/Amber Emas) -->
                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='{$inviteLink}' style='background: linear-gradient(90deg, #FBBF24 0%, #D97706 100%); color: #ffffff; text-decoration: none; padding: 12px 28px; border-radius: 8px; font-weight: bold; font-size: 13px; display: inline-block; box-shadow: 0 4px 12px rgba(217,119,6,0.25);'>
                            Aktifkan Akun Staff
                        </a>
                    </div>
                    
                    <p style='font-size: 11px; color: #94a3b8; margin-bottom: 0;'>
                        * Tautan aktivasi di atas hanya berlaku selama <strong>48 jam</strong>. Jika Anda merasa tidak mengenali undangan ini, silakan abaikan email ini.
                    </p>
                </div>
                
                <!-- Footer -->
                <div style='background-color: #f8fafc; padding: 20px; border-top: 1px solid #e2e8f0; text-align: center; font-size: 10px; color: #94a3b8;'>
                    Manajemen Konstruksi Modern © " . date('Y') . " Kontraktor.id
                </div>
            </div>
        </div>
        ";

        $email->setTo($recipientEmail);
        $email->setSubject("Undangan Bergabung Tim Staf di Kontraktor.id");
        $email->setMessage($message);

        // Kirim email
        if ($email->send()) {
            return true;
        } else {
            // Log error debugger jika pengiriman SMTP gagal
            log_message('error', '[Email Service Error] ' . $email->printDebugger(['headers', 'subject', 'body']));
            return false;
        }
    }

    /**
     * Membatalkan / menghapus undangan yang masih pending.
     */
    public function deleteInvitation(int $parentId, int $invitationId): array
    {
        $undanganModel = new \App\Models\UndanganPenggunaModel();
        $invite = $undanganModel->find($invitationId);

        if (!$invite || (int)$invite->parent_id !== $parentId) {
            return ['success' => false, 'message' => 'Undangan tidak ditemukan atau Anda tidak memiliki akses'];
        }

        if ($undanganModel->delete($invitationId)) {
            return ['success' => true, 'message' => 'Undangan berhasil dibatalkan'];
        }

        return ['success' => false, 'message' => 'Gagal membatalkan undangan'];
    }

    /**
     * Menghapus sub-account milik akun utama.
     */
    public function deleteSubAccount(int $parentId, int $subAccountId): array
    {
        $sub = $this->penggunaModel->find($subAccountId);

        if (!$sub || (int)$sub->parent_id !== $parentId) {
            return ['success' => false, 'message' => 'Akun tim tidak ditemukan atau Anda tidak memiliki akses'];
        }

        if ($this->penggunaModel->delete($subAccountId)) {
            return ['success' => true, 'message' => 'Akun tim berhasil dihapus'];
        }

        return ['success' => false, 'message' => 'Gagal menghapus akun tim'];
    }
}
