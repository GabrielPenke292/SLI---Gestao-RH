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
        Schema::create('selection_process_candidates', function (Blueprint $table) {
            $table->id('selection_process_candidate_id');
            $table->unsignedBigInteger('selection_process_id');
            $table->unsignedBigInteger('candidate_id');
            $table->enum('status', ['pendente', 'aprovado', 'reprovado', 'contratado'])->default('pendente')->comment('Status do candidato no processo');
            $table->text('notes')->nullable()->comment('Observações sobre o candidato no processo');
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
            $table->index('status');
            
            // Garantir que um candidato não seja vinculado duas vezes ao mesmo processo
            $table->unique(['selection_process_id', 'candidate_id'], 'unique_process_candidate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('selection_process_candidates');
    }
};
