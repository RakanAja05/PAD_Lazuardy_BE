<?php

namespace Database\Seeders;

use App\Enums\OrderStatusEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all orders
        $orders = Order::all();

        if ($orders->isEmpty()) {
            $this->command->warn('No orders found. Run OrderSeeder first.');
            return;
        }

        $paymentMethods = PaymentMethodEnum::cases();
        $count = 0;

        foreach ($orders as $order) {
            // Determine payment status based on order status
            if ($order->status === OrderStatusEnum::PAID->value) {
                $paymentStatus = PaymentStatusEnum::VALIDATED;
                $proofUrl = 'https://example.com/payments/proof_' . $order->id . '.jpg';
                $paidAt = $order->updated_at ?? now();
            } elseif ($order->status === OrderStatusEnum::PENDING->value) {
                // 70% uploaded awaiting validation, 30% still pending upload
                if (rand(1, 100) <= 70) {
                    $paymentStatus = PaymentStatusEnum::UPLOADED;
                    $proofUrl = 'https://example.com/payments/proof_' . $order->id . '.jpg';
                    $paidAt = null;
                } else {
                    $paymentStatus = PaymentStatusEnum::PENDING;
                    $proofUrl = null;
                    $paidAt = null;
                }
            } else { // CANCELLED
                $paymentStatus = PaymentStatusEnum::REJECTED;
                $proofUrl = null;
                $paidAt = null;
            }

            Payment::create([
                'order_id' => $order->id,
                'amount' => $order->total_amount,
                'proof_image_url' => $proofUrl,
                'paid_at' => $paidAt,
                'payment_method' => fake()->randomElement($paymentMethods)->value,
                'status' => $paymentStatus->value,
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at,
            ]);
            $count++;
        }

        $this->command->info("✅ Created {$count} payment records");
    }
}
