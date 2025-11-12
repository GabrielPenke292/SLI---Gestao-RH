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
            // Adicionar campos de observação para aprovação e reprovação
            if (!Schema::hasColumn('selection_process_candidates', 'approval_observation')) {
                $table->text('approval_observation')->nullable()->after('notes')->comment('Observação ao aprovar o candidato');
            }
            if (!Schema::hasColumn('selection_process_candidates', 'rejection_observation')) {
                $table->text('rejection_observation')->nullable()->after('approval_observation')->comment('Observação ao reprovar o candidato');
            }
            if (!Schema::hasColumn('selection_process_candidates', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('rejection_observation')->comment('Data de aprovação');
            }
            if (!Schema::hasColumn('selection_process_candidates', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable()->after('approved_at')->comment('Data de reprovação');
            }
            if (!Schema::hasColumn('selection_process_candidates', 'approved_by')) {
                $table->string('approved_by', 45)->nullable()->after('rejected_at')->comment('Usuário que aprovou');
            }
            if (!Schema::hasColumn('selection_process_candidates', 'rejected_by')) {
                $table->string('rejected_by', 45)->nullable()->after('approved_by')->comment('Usuário que reprovou');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('selection_process_candidates', function (Blueprint $table) {
            if (Schema::hasColumn('selection_process_candidates', 'rejected_by')) {
                $table->dropColumn('rejected_by');
            }
            if (Schema::hasColumn('selection_process_candidates', 'approved_by')) {
                $table->dropColumn('approved_by');
            }
            if (Schema::hasColumn('selection_process_candidates', 'rejected_at')) {
                $table->dropColumn('rejected_at');
            }
            if (Schema::hasColumn('selection_process_candidates', 'approved_at')) {
                $table->dropColumn('approved_at');
            }
            if (Schema::hasColumn('selection_process_candidates', 'rejection_observation')) {
                $table->dropColumn('rejection_observation');
            }
            if (Schema::hasColumn('selection_process_candidates', 'approval_observation')) {
                $table->dropColumn('approval_observation');
            }
        });
    }
};
