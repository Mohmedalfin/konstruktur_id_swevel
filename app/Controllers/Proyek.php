<?php

namespace App\Controllers;

use App\Models\MProyek;

class Proyek extends BaseController
{
    public function dataEmpiris()
    {
        $model = new MProyek();
        
        $data = [
            'ahs_data' => $model->getDataEmpiris(), 
            'title'    => 'Data AHS Umum'
        ];

        return view('v_ahs_empiris', $data);
    }
}