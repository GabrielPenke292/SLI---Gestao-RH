<?php

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
        Schema::create('training_contents', function (Blueprint $table) {
            $table->id('training_content_id');
            $table->string('title', 255)->comment('Título do conteúdo');
            $table->text('description')->nullable()->comment('Descrição do conteúdo');
            $table->enum('content_type', ['pdf', 'excel', 'powerpoint', 'video_file', 'youtube_link'])->comment('Tipo de conteúdo');
            $table->string('file_path', 500)->nullable()->comment('Caminho do arquivo (para PDF, Excel, PowerPoint, vídeo)');
            $table->string('file_name', 255)->nullable()->comment('Nome original do arquivo');
            $table->string('file_size', 50)->nullable()->comment('Tamanho do arquivo');
            $table->string('youtube_url', 500)->nullable()->comment('URL do YouTube (se tipo for youtube_link)');
            $table->string('youtube_video_id', 100)->nullable()->comment('ID do vídeo do YouTube');
            $table->string('category', 100)->nullable()->comment('Categoria do conteúdo');
            $table->integer('duration_minutes')->nullable()->comment('Duração em minutos (para vídeos)');
            $table->boolean('is_active')->default(true)->comment('Conteúdo está ativo');
            $table->integer('views_count')->default(0)->comment('Número de visualizações');
            $table->timestamp('created_at')->nullable();
            $table->string('created_by', 45)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('updated_by', 45)->nullable();
            $table->timestamp('deleted_at')->nullable();
            
            // Índices
            $table->index('content_type');
            $table->index('category');
            $table->index('is_active');
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_contents');
    }
};
