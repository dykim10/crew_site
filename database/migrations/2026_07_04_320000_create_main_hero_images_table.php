<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('crew.main_hero_images')) {
            Schema::create('crew.main_hero_images', function (Blueprint $table) {
                $table->id();
                $table->string('image_path');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('crew.main_hero_images');
    }
};
