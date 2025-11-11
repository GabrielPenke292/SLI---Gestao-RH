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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id('log_id');
            $table->string('log_type', 50)->comment('Tipo de log: candidate_linked, candidate_unlinked, process_created, process_approved, etc.');
            $table->string('entity_type', 100)->comment('Tipo da entidade: Candidate, SelectionProcess, etc.');
            $table->unsignedBigInteger('entity_id')->comment('ID da entidade relacionada');
            $table->string('action', 100)->comment('Ação realizada');
            $table->text('description')->nullable()->comment('Descrição detalhada da ação');
            $table->json('metadata')->nullable()->comment('Dados adicionais em formato JSON');
            $table->unsignedBigInteger('user_id')->nullable()->comment('ID do usuário que realizou a ação');
            $table->string('user_name', 100)->nullable()->comment('Nome do usuário que realizou a ação');
            $table->string('ip_address', 45)->nullable()->comment('Endereço IP de origem');
            $table->timestamp('created_at')->nullable();
            
            // Índices
            $table->index('log_type');
            $table->index('entity_type');
            $table->index('entity_id');
            $table->index('created_at');
            $table->index(['entity_type', 'entity_id']);
            
            // Chave estrangeira opcional para usuário
            $table->foreign('user_id')->references('users_id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
