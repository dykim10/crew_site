<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::connection('pgsql')->hasTable('crew.boards')) {
            Schema::connection('pgsql')->create('crew.boards', function (Blueprint $table) {
                $table->id();
                $table->string('type', 20);                  // free / photo / qna
                $table->string('title', 200);
                $table->text('content')->nullable();
                $table->unsignedBigInteger('author_id');
                $table->string('thumbnail_url')->nullable(); // 포토게시판 대표 이미지
                $table->jsonb('image_urls')->nullable();     // 포토게시판 추가 이미지 배열
                $table->unsignedInteger('view_count')->default(0);
                $table->boolean('is_pinned')->default(false);
                $table->timestampsTz(6);

                $table->index('type');
                $table->index('author_id');
                $table->index(['type', 'created_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::connection('pgsql')->dropIfExists('crew.boards');
    }
};
