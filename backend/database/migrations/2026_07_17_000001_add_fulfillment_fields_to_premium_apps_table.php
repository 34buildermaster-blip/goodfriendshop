<?php

use App\Models\PremiumApp;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('premium_apps', function (Blueprint $table) {
            $table->string('delivery_type', 40)->default(PremiumApp::DELIVERY_MANUAL_SERVICE)->after('duration_days');
            $table->json('customer_required_fields')->nullable()->after('delivery_type');
            $table->unsignedInteger('warranty_days')->nullable()->after('customer_required_fields');
            $table->string('stock_status', 40)->default(PremiumApp::STOCK_IN_STOCK)->after('warranty_days');
            $table->string('supplier_name')->nullable()->after('stock_status');
            $table->string('supplier_contact')->nullable()->after('supplier_name');
            $table->text('fulfillment_note')->nullable()->after('supplier_contact');
            $table->text('terms')->nullable()->after('fulfillment_note');
        });
    }

    public function down(): void
    {
        Schema::table('premium_apps', function (Blueprint $table) {
            $table->dropColumn([
                'delivery_type',
                'customer_required_fields',
                'warranty_days',
                'stock_status',
                'supplier_name',
                'supplier_contact',
                'fulfillment_note',
                'terms',
            ]);
        });
    }
};
