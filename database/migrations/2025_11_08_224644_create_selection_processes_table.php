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
        Schema::create('selection_processes', function (Blueprint $table) {
            $table->id('selection_process_id');
            $table->string('process_number', 50)->unique()->comment('Número do processo seletivo');
            $table->unsignedBigInteger('vacancy_id');
            $table->enum('reason', ['substituicao', 'aumento_quadro'])->comment('Motivo do processo');
            $table->unsignedBigInteger('approver_id')->nullable()->comment('Usuário aprovador');
            $table->decimal('budget', 12, 2)->nullable()->comment('Verba para contratação');
            $table->enum('status', ['aguardando_aprovacao', 'em_andamento', 'encerrado', 'congelado'])->default('aguardando_aprovacao');
            $table->date('start_date')->nullable()->comment('Data de início do processo');
            $table->date('end_date')->nullable()->comment('Data de encerramento');
            $table->text('observations')->nullable()->comment('Observações gerais');
            $table->text('approval_notes')->nullable()->comment('Notas da aprovação');
            $table->date('approval_date')->nullable()->comment('Data de aprovação');
            $table->timestamp('created_at')->nullable();
            $table->string('created_by', 45)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('updated_by', 45)->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->string('deleted_by', 45)->nullable();
            
            // Chaves estrangeiras
            $table->foreign('vacancy_id')->references('vacancy_id')->on('vacancies')->onDelete('restrict');
            $table->foreign('approver_id')->references('users_id')->on('users')->onDelete('set null');
            
            // Índices
            $table->index('process_number');
            $table->index('vacancy_id');
            $table->index('status');
            $table->index('approver_id');
            $table->index('start_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('selection_processes');
    }
};
