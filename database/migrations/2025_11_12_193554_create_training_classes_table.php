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
        Schema::create('training_classes', function (Blueprint $table) {
            $table->id('training_class_id');
            $table->string('title', 255)->comment('Título da turma');
            $table->text('description')->nullable()->comment('Descrição da turma');
            $table->date('start_date')->nullable()->comment('Data de início');
            $table->date('end_date')->nullable()->comment('Data de término');
            $table->enum('status', ['planejado', 'em_andamento', 'concluido', 'cancelado'])->default('planejado')->comment('Status da turma');
            $table->integer('max_participants')->nullable()->comment('Número máximo de participantes');
            $table->string('instructor', 255)->nullable()->comment('Instrutor responsável');
            $table->text('notes')->nullable()->comment('Observações');
            $table->timestamp('created_at')->nullable();
            $table->string('created_by', 45)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('updated_by', 45)->nullable();
            $table->timestamp('deleted_at')->nullable();
            
            // Índices
            $table->index('status');
            $table->index('start_date');
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_classes');
    }
};
