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
        Schema::table('selection_process_candidates', function (Blueprint $table) {
            // Remover a constraint unique que permite múltiplas etapas
            $table->dropUnique('unique_process_candidate_step');
            
            // Criar nova constraint unique que impede candidato em múltiplas etapas
            // Um candidato só pode estar em uma etapa por processo
            $table->unique(['selection_process_id', 'candidate_id'], 'unique_process_candidate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('selection_process_candidates', function (Blueprint $table) {
            // Remover a constraint única que impede múltiplas etapas
            $table->dropUnique('unique_process_candidate');
            
            // Restaurar a constraint que permite múltiplas etapas
            $table->unique(['selection_process_id', 'candidate_id', 'step'], 'unique_process_candidate_step');
        });
    }
};
