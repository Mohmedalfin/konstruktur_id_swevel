<?php

namespace App\Models;

use CodeIgniter\Model;

class MProyek extends Model
{
    public function getDataEmpiris()
    {
        // Query tanpa filter id_pengguna
        $sql = "SELECT * FROM (
                    SELECT
                        ahs.id_proyek,
                        nama_pekerjaan AS uraian_pekerjaan,
                        satuan_pekerjaan,
                        nama_proyek AS keterangan,
                        id_pekerjaan 
                    FROM ahs
                    LEFT JOIN proyek ON proyek.id_proyek = ahs.id_proyek
                    WHERE sumber = '4' 
                        AND nama_pekerjaan <> ''
                        AND satuan_pekerjaan <> ''
                    GROUP BY ahs.id_proyek, nama_pekerjaan
                ) a LIMIT 1000";

        return $this->db->query($sql)->getResult();
    }
}