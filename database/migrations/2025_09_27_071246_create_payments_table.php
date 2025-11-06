<?php

use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $payment_methods = PaymentMethodEnum::list();
        $payment_status = PaymentStatusEnum::list();

        Schema::create('payments', function (Blueprint $table) use ($payment_methods, $payment_status) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders');
            $table->integer('amount')->nullable();
            $table->string('proof_image_url')->nullable();
            $table->date('paid_at')->nullable();
            $table->enum('payment_method', $payment_methods)->nullable();
            $table->enum('status', $payment_status)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
