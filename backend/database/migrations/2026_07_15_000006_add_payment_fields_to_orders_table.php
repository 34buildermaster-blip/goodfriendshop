<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_method', 40)->nullable()->after('currency');
            $table->string('payment_status', 40)->default('unpaid')->after('payment_method')->index();
            $table->string('payment_reference')->nullable()->after('payment_status');
            $table->text('payment_note')->nullable()->after('payment_reference');
            $table->timestamp('paid_at')->nullable()->after('payment_note');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'payment_method',
                'payment_status',
                'payment_reference',
                'payment_note',
                'paid_at',
            ]);
        });
    }
};
