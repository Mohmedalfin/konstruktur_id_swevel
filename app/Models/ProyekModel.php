<?php

namespace App\Models;

use CodeIgniter\Model;

class ProyekModel extends Model
{
    protected $table = 'projects';
    protected $primaryKey = 'id_project';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'kode_proyek',
        'nama_proyek',
        'slug',
        'lokasi_proyek',
        'id_template',      // ID template harga resmi dari DB estimator
        'tanggal_mulai',
        'estimasi_selesai',
        'nama_owner',
        'nama_perusahaan',
        'nomor_kontrak',
        'keterangan',
        'foto_proyek',
        'harga_deal',
        'id_wilayah',
        'jenis_proyek',
        'sumber_data',
        'id_ref_sumber',
        'status_proyek',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    public function generateUniqueSlug(string $namaProyek, ?int $ignoreId = null): string
    {
        helper(['text', 'url']);

        $baseSlug = url_title(convert_accented_characters($namaProyek), '-', true);

        if (empty($baseSlug)) {
            $baseSlug = 'proyek';
        }

        $slug = $baseSlug;
        $counter = 1;

        while ($this->slugExists($slug, $ignoreId)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    protected function slugExists(string $slug, ?int $ignoreId = null): bool
    {
        $builder = $this->where('slug', $slug);

        if ($ignoreId !== null) {
            $builder->where('id_project !=', $ignoreId);
        }

        return $builder->first() !== null;
    }
}

