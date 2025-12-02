<?php

namespace Database\Seeders;

use App\Enums\OrderStatusEnum;
use App\Models\Order;
use App\Models\Package;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get students
        $students = User::whereHas('student')->get();
        
        if ($students->isEmpty()) {
            $this->command->warn('No students found. Run StudentSeeder first.');
            return;
        }

        $packages = Package::all();
        
        if ($packages->isEmpty()) {
            $this->command->warn('No packages found. Run PackageSeeder first.');
            return;
        }

        $orders = [];
        
        // Each student creates 1-3 orders
        foreach ($students as $student) {
            $numOrders = rand(1, 3);
            
            for ($i = 0; $i < $numOrders; $i++) {
                $package = $packages->random();
                
                // Status distribution: 60% paid, 30% pending, 10% cancelled
                $statusRoll = rand(1, 100);
                if ($statusRoll <= 60) {
                    $status = OrderStatusEnum::PAID;
                } elseif ($statusRoll <= 90) {
                    $status = OrderStatusEnum::PENDING;
                } else {
                    $status = OrderStatusEnum::CANCELLED;
                }
                
                $orders[] = Order::create([
                    'package_id' => $package->id,
                    'user_id' => $student->id,
                    'total_amount' => $package->price,
                    'status' => $status->value,
                    'created_at' => now()->subDays(rand(1, 60)),
                    'updated_at' => now()->subDays(rand(0, 30)),
                ]);
            }
        }

        $this->command->info('✅ Created ' . count($orders) . ' orders for ' . $students->count() . ' students');
    }
}
