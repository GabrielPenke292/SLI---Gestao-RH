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
        Schema::create('training_topics', function (Blueprint $table) {
            $table->id('training_topic_id');
            $table->unsignedBigInteger('training_class_id')->comment('ID da turma');
            $table->string('title', 255)->comment('Título do tópico (disciplina)');
            $table->text('description')->nullable()->comment('Descrição do tópico');
            $table->integer('order')->default(0)->comment('Ordem de apresentação');
            $table->integer('duration_minutes')->nullable()->comment('Duração estimada em minutos');
            $table->timestamp('created_at')->nullable();
            $table->string('created_by', 45)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('updated_by', 45)->nullable();
            $table->timestamp('deleted_at')->nullable();
            
            // Chave estrangeira
            $table->foreign('training_class_id')->references('training_class_id')->on('training_classes')->onDelete('cascade');
            
            // Índices
            $table->index('training_class_id');
            $table->index('order');
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_topics');
    }
};
