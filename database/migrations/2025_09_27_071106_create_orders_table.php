<?php

use App\Enums\OrderStatusEnum;
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
        $orderStatus = OrderStatusEnum::list();

        Schema::create('orders', function (Blueprint $table) use ($orderStatus) {
            $table->id();
            $table->foreignId('package_id')->constrained('packages', 'id');
            $table->foreignId('user_id')->constrained('users', 'id');
            $table->integer('total_amount');
            $table->enum('status', $orderStatus);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
