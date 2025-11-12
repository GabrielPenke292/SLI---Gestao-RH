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
        Schema::create('dismissal_exams', function (Blueprint $table) {
            $table->id('dismissal_exam_id');
            $table->unsignedBigInteger('worker_id');
            $table->unsignedBigInteger('clinic_id');
            $table->date('exam_date')->comment('Data do exame');
            $table->time('exam_time')->nullable()->comment('Horário do exame');
            $table->enum('status', ['agendado', 'cancelado', 'finalizado'])->default('agendado')->comment('Status do exame');
            $table->text('cancellation_reason')->nullable()->comment('Motivo do cancelamento');
            $table->text('exam_result')->nullable()->comment('Resultado do exame');
            $table->text('notes')->nullable()->comment('Observações');
            $table->timestamp('created_at')->nullable();
            $table->string('created_by', 45)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('updated_by', 45)->nullable();
            $table->timestamp('deleted_at')->nullable();
            
            // Chaves estrangeiras
            $table->foreign('worker_id')->references('worker_id')->on('workers')->onDelete('cascade');
            $table->foreign('clinic_id')->references('clinic_id')->on('clinics')->onDelete('cascade');
            
            // Índices
            $table->index('worker_id');
            $table->index('clinic_id');
            $table->index('exam_date');
            $table->index('status');
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dismissal_exams');
    }
};
