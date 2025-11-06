<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [];

        for ($i=1; $i<=6; $i++){
            $packages[] = [
                'name' => 'package ' . $i,
                'session' => 4,
                'price' => 200000,
                'discount' => 0.05,
                'description' => 'lorem ipsum dolor sit amet',
                'benefit' => json_encode(['layanan bertanya 24 jam', 'pikir sendiri']),
                'subject_amount' => 2,
            ];
        }

        Package::insert($packages);
    }
}
