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
        Schema::create('training_topic_contents', function (Blueprint $table) {
            $table->id('training_topic_content_id');
            $table->unsignedBigInteger('training_topic_id')->comment('ID do tópico');
            $table->unsignedBigInteger('training_content_id')->comment('ID do conteúdo');
            $table->integer('order')->default(0)->comment('Ordem de apresentação do conteúdo no tópico');
            $table->timestamp('created_at')->nullable();
            $table->string('created_by', 45)->nullable();
            
            // Chaves estrangeiras
            $table->foreign('training_topic_id')->references('training_topic_id')->on('training_topics')->onDelete('cascade');
            $table->foreign('training_content_id')->references('training_content_id')->on('training_contents')->onDelete('cascade');
            
            // Índices
            $table->index('training_topic_id');
            $table->index('training_content_id');
            $table->index('order');
            
            // Evitar duplicatas
            $table->unique(['training_topic_id', 'training_content_id'], 'unique_topic_content');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_topic_contents');
    }
};
