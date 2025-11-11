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
        Schema::create('step_interactions', function (Blueprint $table) {
            $table->id('step_interaction_id');
            $table->unsignedBigInteger('selection_process_id');
            $table->unsignedBigInteger('candidate_id');
            $table->string('step', 100)->comment('Etapa do processo em que a interação ocorreu');
            $table->enum('interaction_type', ['pergunta', 'observacao'])->comment('Tipo de interação');
            $table->text('question')->nullable()->comment('Pergunta (se tipo for pergunta)');
            $table->text('answer')->nullable()->comment('Resposta (se tipo for pergunta)');
            $table->text('observation')->nullable()->comment('Observação (se tipo for observacao)');
            $table->timestamp('created_at')->nullable();
            $table->string('created_by', 45)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('updated_by', 45)->nullable();
            
            // Chaves estrangeiras
            $table->foreign('selection_process_id')->references('selection_process_id')->on('selection_processes')->onDelete('cascade');
            $table->foreign('candidate_id')->references('candidate_id')->on('candidates')->onDelete('cascade');
            
            // Índices
            $table->index('selection_process_id');
            $table->index('candidate_id');
            $table->index('step');
            $table->index('interaction_type');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('step_interactions');
    }
};
