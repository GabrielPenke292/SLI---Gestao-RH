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
        Schema::table('admissional_exams', function (Blueprint $table) {
            if (!Schema::hasColumn('admissional_exams', 'exam_performed')) {
                $table->boolean('exam_performed')->default(false)->comment('Exame foi realizado');
            }
            if (!Schema::hasColumn('admissional_exams', 'exam_file_path')) {
                $table->string('exam_file_path', 500)->nullable()->comment('Caminho do arquivo do exame');
            }
            if (!Schema::hasColumn('admissional_exams', 'exam_file_name')) {
                $table->string('exam_file_name', 255)->nullable()->comment('Nome do arquivo do exame');
            }
            if (!Schema::hasColumn('admissional_exams', 'performed_at')) {
                $table->timestamp('performed_at')->nullable()->comment('Data/hora que foi marcado como realizado');
            }
            if (!Schema::hasColumn('admissional_exams', 'performed_by')) {
                $table->string('performed_by', 45)->nullable()->comment('Usuário que marcou como realizado');
            }
            if (!Schema::hasColumn('admissional_exams', 'performed_observations')) {
                $table->text('performed_observations')->nullable()->comment('Observações sobre a realização do exame');
            }
            
            // Índices
            if (!Schema::hasColumn('admissional_exams', 'exam_performed')) {
                $table->index('exam_performed');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admissional_exams', function (Blueprint $table) {
            if (Schema::hasColumn('admissional_exams', 'exam_performed')) {
                $table->dropIndex(['exam_performed']);
                $table->dropColumn('exam_performed');
            }
            if (Schema::hasColumn('admissional_exams', 'exam_file_path')) {
                $table->dropColumn('exam_file_path');
            }
            if (Schema::hasColumn('admissional_exams', 'exam_file_name')) {
                $table->dropColumn('exam_file_name');
            }
            if (Schema::hasColumn('admissional_exams', 'performed_at')) {
                $table->dropColumn('performed_at');
            }
            if (Schema::hasColumn('admissional_exams', 'performed_by')) {
                $table->dropColumn('performed_by');
            }
            if (Schema::hasColumn('admissional_exams', 'performed_observations')) {
                $table->dropColumn('performed_observations');
            }
        });
    }
};
