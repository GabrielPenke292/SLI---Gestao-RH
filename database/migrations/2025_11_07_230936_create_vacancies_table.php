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
        Schema::create('vacancies', function (Blueprint $table) {
            $table->id('vacancy_id');
            $table->string('vacancy_title', 255);
            $table->text('vacancy_description');
            $table->enum('urgency_level', ['baixa', 'media', 'alta', 'critica'])->default('media');
            $table->decimal('salary', 10, 2)->nullable();
            $table->string('work_type', 50)->nullable()->comment('Presencial, Remoto, Híbrido');
            $table->string('work_schedule', 50)->nullable()->comment('Integral, Meio Período, etc');
            $table->text('requirements')->nullable();
            $table->text('benefits')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->enum('status', ['aberta', 'pausada', 'encerrada'])->default('aberta');
            $table->date('opening_date')->nullable();
            $table->date('closing_date')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->string('created_by', 45)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('updated_by', 45)->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->string('deleted_by', 45)->nullable();
            
            // Chave estrangeira para departamento
            $table->foreign('department_id')->references('department_id')->on('departments')->onDelete('set null');
            
            // Índices
            $table->index('urgency_level');
            $table->index('status');
            $table->index('opening_date');
            $table->index('department_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vacancies');
    }
};
