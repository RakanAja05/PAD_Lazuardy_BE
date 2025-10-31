<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

echo "=== CREATE STUDENT DATA ===\n\n";

DB::beginTransaction();
try {
    // Update user data
    $user = User::find(1);
    $user->update([
        'name' => 'Muhammad Rakan Hibrizi',
        'gender' => 'pria',
        'date_of_birth' => '2005-01-15',
        'telephone_number' => '08123456789',
        'home_address' => [
            'province' => 'Jawa Barat',
            'regency' => 'Bandung',
            'district' => 'Coblong',
            'subdistrict' => 'Dago',
            'street' => 'Jl. Dago No. 123'
        ],
        'latitude' => '-6.870162',
        'longitude' => '107.612652',
    ]);
    
    // Create student record
    $student = Student::updateOrCreate(
        ['user_id' => 1],
        [
            'parent' => 'Bapak Rakan',
            'parent_telephone_number' => '08199999999',
            'class_id' => 1,
            'curriculum_id' => 1,
            'school' => 'SMA Negeri 1 Bandung'
        ]
    );
    
    DB::commit();
    
    echo "✅ Student data created successfully!\n\n";
    echo "Student ID: {$student->user_id}\n";
    echo "Name: {$user->name}\n";
    echo "School: {$student->school}\n";
    
} catch (Exception $e) {
    DB::rollBack();
    echo "❌ Error: " . $e->getMessage() . "\n";
}
