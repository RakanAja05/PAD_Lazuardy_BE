<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "=== TEST LOGIN ===\n\n";

$email = 'rakanhibrizi00@gmail.com';
$passwords = ['password123', 'password', '12345678', 'rakanhibrizi00'];

$user = User::where('email', $email)->first();

if (!$user) {
    echo "❌ User not found with email: $email\n";
    exit;
}

echo "✅ User found:\n";
echo "   ID: {$user->id}\n";
echo "   Email: {$user->email}\n";
echo "   Name: {$user->name}\n";
echo "   Role: {$user->role}\n\n";

echo "Testing passwords:\n";
foreach ($passwords as $password) {
    $match = Hash::check($password, $user->password);
    $status = $match ? "✅ MATCH" : "❌ NOT MATCH";
    echo "   '$password' => $status\n";
}

echo "\n=== Hash Info ===\n";
echo "Current hash: " . substr($user->password, 0, 30) . "...\n";
