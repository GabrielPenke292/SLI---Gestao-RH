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
        Schema::create('movements', function (Blueprint $table) {
            $table->id('movement_id');
            $table->unsignedBigInteger('worker_id');
            $table->unsignedBigInteger('old_department_id')->nullable();
            $table->unsignedBigInteger('new_department_id')->nullable();
            $table->unsignedBigInteger('old_role_id')->nullable();
            $table->unsignedBigInteger('new_role_id')->nullable();
            $table->enum('status', ['pendente', 'aprovado', 'rejeitado'])->default('pendente');
            $table->text('observation')->nullable()->comment('Observação da movimentação');
            $table->text('rejection_reason')->nullable()->comment('Motivo da rejeição');
            $table->unsignedBigInteger('requested_by')->nullable()->comment('Usuário que solicitou');
            $table->unsignedBigInteger('approved_by')->nullable()->comment('Usuário que aprovou');
            $table->unsignedBigInteger('rejected_by')->nullable()->comment('Usuário que rejeitou');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->string('created_by', 45)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('updated_by', 45)->nullable();
            $table->timestamp('deleted_at')->nullable();
            
            // Chaves estrangeiras
            $table->foreign('worker_id')->references('worker_id')->on('workers')->onDelete('cascade');
            $table->foreign('old_department_id')->references('department_id')->on('departments')->onDelete('set null');
            $table->foreign('new_department_id')->references('department_id')->on('departments')->onDelete('set null');
            $table->foreign('old_role_id')->references('role_id')->on('roles')->onDelete('set null');
            $table->foreign('new_role_id')->references('role_id')->on('roles')->onDelete('set null');
            $table->foreign('requested_by')->references('users_id')->on('users')->onDelete('set null');
            $table->foreign('approved_by')->references('users_id')->on('users')->onDelete('set null');
            $table->foreign('rejected_by')->references('users_id')->on('users')->onDelete('set null');
            
            // Índices
            $table->index('worker_id');
            $table->index('status');
            $table->index('created_at');
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movements');
    }
};
