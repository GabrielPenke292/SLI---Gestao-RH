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
        Schema::create('proposals', function (Blueprint $table) {
            $table->id('proposal_id');
            $table->unsignedBigInteger('selection_process_id');
            $table->unsignedBigInteger('candidate_id');
            $table->integer('version')->default(1)->comment('Versão da proposta (para auditoria)');
            $table->unsignedBigInteger('parent_proposal_id')->nullable()->comment('ID da proposta original (se for contraproposta)');
            $table->decimal('salary', 10, 2)->nullable()->comment('Salário proposto');
            $table->string('contract_model', 100)->nullable()->comment('Modelo de contratação (CLT, PJ, etc)');
            $table->string('workload', 50)->nullable()->comment('Carga horária (ex: 40h, 44h)');
            $table->text('benefits')->nullable()->comment('Benefícios oferecidos');
            $table->date('start_date')->nullable()->comment('Data de início');
            $table->text('additional_info')->nullable()->comment('Informações adicionais');
            $table->string('proposal_file_path')->nullable()->comment('Caminho do arquivo PDF da proposta');
            $table->string('proposal_file_name')->nullable()->comment('Nome original do arquivo');
            $table->enum('status', ['pendente', 'aceita', 'recusada', 'contraproposta'])->default('pendente')->comment('Status da proposta');
            $table->text('rejection_observation')->nullable()->comment('Observação ao recusar');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->string('accepted_by', 45)->nullable();
            $table->string('rejected_by', 45)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->string('created_by', 45)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('updated_by', 45)->nullable();
            
            // Chaves estrangeiras
            $table->foreign('selection_process_id')->references('selection_process_id')->on('selection_processes')->onDelete('cascade');
            $table->foreign('candidate_id')->references('candidate_id')->on('candidates')->onDelete('cascade');
            $table->foreign('parent_proposal_id')->references('proposal_id')->on('proposals')->onDelete('set null');
            
            // Índices
            $table->index('selection_process_id');
            $table->index('candidate_id');
            $table->index('parent_proposal_id');
            $table->index('status');
            $table->index('version');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposals');
    }
};
