<?php

namespace App\Services;

use App\Models\Package;

class PackageService
{
    public function getData(Package $query)
    {
        $data = [
            'package_id' => $query->id,
            'package_name' => $query->name,
            'package_session' => $query->session,
            ''
        ];
    }
}
