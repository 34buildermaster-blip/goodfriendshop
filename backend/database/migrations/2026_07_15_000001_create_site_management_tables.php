<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('label');
            $table->text('value')->nullable();
            $table->string('type', 24)->default('text');
            $table->string('group', 64)->default('general')->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();
        });

        Schema::create('hero_slides', function (Blueprint $table) {
            $table->id();
            $table->string('eyebrow', 80)->nullable();
            $table->string('title');
            $table->string('highlight')->nullable();
            $table->text('quote')->nullable();
            $table->string('image_path')->nullable();
            $table->string('cta_label', 80)->nullable();
            $table->string('cta_url')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();
        });

        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->text('message');
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamp('starts_at')->nullable()->index();
            $table->timestamp('ends_at')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('hero_slides');
        Schema::dropIfExists('site_settings');
    }
};
