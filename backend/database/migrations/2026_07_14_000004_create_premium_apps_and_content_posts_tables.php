<?php

use App\Models\ContentPost;
use App\Models\PremiumApp;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('premium_apps', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('provider')->nullable();
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('cost', 10, 2)->nullable();
            $table->string('currency', 3)->default('THB');
            $table->unsignedInteger('duration_days')->nullable();
            $table->string('status', 24)->default(PremiumApp::STATUS_DRAFT)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('content_posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('type', 24)->default(ContentPost::TYPE_NEWS)->index();
            $table->string('excerpt')->nullable();
            $table->longText('content')->nullable();
            $table->string('cover_image_path')->nullable();
            $table->timestamp('published_at')->nullable()->index();
            $table->string('status', 24)->default(ContentPost::STATUS_DRAFT)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_posts');
        Schema::dropIfExists('premium_apps');
    }
};
