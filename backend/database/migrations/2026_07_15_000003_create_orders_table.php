<?php

use App\Models\Order;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 32)->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('game_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('game_package_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_phone', 30)->nullable();
            $table->string('player_identifier');
            $table->string('server_identifier')->nullable();
            $table->json('extra_fields')->nullable();
            $table->string('game_name');
            $table->string('package_name');
            $table->decimal('price', 10, 2);
            $table->string('currency', 3)->default('THB');
            $table->string('status', 24)->default(Order::STATUS_PENDING)->index();
            $table->text('customer_note')->nullable();
            $table->text('admin_note')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
