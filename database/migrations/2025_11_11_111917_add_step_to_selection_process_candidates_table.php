<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Verificar se a coluna step já existe
        $columns = DB::select("SHOW COLUMNS FROM selection_process_candidates LIKE 'step'");
        $stepColumnExists = !empty($columns);
        
        // Verificar e remover a constraint unique antiga se existir
        $indexExists = DB::select("SHOW INDEX FROM selection_process_candidates WHERE Key_name = 'unique_process_candidate'");
        if (!empty($indexExists)) {
            Schema::table('selection_process_candidates', function (Blueprint $table) {
                $table->dropUnique('unique_process_candidate');
            });
        }
        
        Schema::table('selection_process_candidates', function (Blueprint $table) use ($stepColumnExists) {
            // Adicionar coluna step apenas se não existir
            if (!$stepColumnExists) {
                // Adicionar coluna step (não nullable pois a etapa é obrigatória)
                // Usando 100 caracteres para evitar exceder o limite de índice do MySQL (1000 bytes)
                $table->string('step', 100)->after('candidate_id')->comment('Etapa do processo em que o candidato está vinculado');
            } else {
                // Se a coluna já existe, modificar para garantir o tamanho correto
                $table->string('step', 100)->change();
            }
        });
        
        // Verificar se o índice único já existe antes de criar
        $uniqueIndexExists = DB::select("SHOW INDEX FROM selection_process_candidates WHERE Key_name = 'unique_process_candidate_step'");
        if (empty($uniqueIndexExists)) {
            Schema::table('selection_process_candidates', function (Blueprint $table) {
                // Criar nova constraint unique incluindo a etapa
                // Permite que o mesmo candidato esteja em etapas diferentes do mesmo processo
                $table->unique(['selection_process_id', 'candidate_id', 'step'], 'unique_process_candidate_step');
            });
        }
        
        // Verificar se o índice de step já existe
        $stepIndexExists = DB::select("SHOW INDEX FROM selection_process_candidates WHERE Key_name = 'selection_process_candidates_step_index'");
        if (empty($stepIndexExists)) {
            Schema::table('selection_process_candidates', function (Blueprint $table) {
                // Adicionar índice para step
                $table->index('step');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('selection_process_candidates', function (Blueprint $table) {
            // Remover a constraint unique com step
            $table->dropUnique('unique_process_candidate_step');
            
            // Remover índice de step
            $table->dropIndex(['step']);
            
            // Remover coluna step
            $table->dropColumn('step');
            
            // Restaurar a constraint unique antiga
            $table->unique(['selection_process_id', 'candidate_id'], 'unique_process_candidate');
        });
    }
};
