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
        Schema::create('layoffs', function (Blueprint $table) {
            $table->id('layoff_id');
            $table->unsignedBigInteger('worker_id');
            $table->date('layoff_date')->comment('Data do desligamento');
            $table->enum('layoff_type', ['pedido_demissao', 'demitido', 'rescisao_indireta', 'justa_causa', 'outro'])->comment('Tipo de desligamento');
            $table->string('reason', 255)->nullable()->comment('Motivo do desligamento');
            $table->text('observations')->nullable()->comment('Observações');
            $table->boolean('has_notice_period')->default(false)->comment('Teve aviso prévio');
            $table->integer('notice_period_days')->nullable()->comment('Dias de aviso prévio');
            $table->decimal('severance_pay', 10, 2)->nullable()->comment('Valor da rescisão');
            $table->text('severance_details')->nullable()->comment('Detalhes da rescisão');
            $table->boolean('returned_equipment')->default(false)->comment('Equipamentos devolvidos');
            $table->text('equipment_details')->nullable()->comment('Detalhes dos equipamentos');
            $table->timestamp('created_at')->nullable();
            $table->string('created_by', 45)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('updated_by', 45)->nullable();
            $table->timestamp('deleted_at')->nullable();
            
            // Chaves estrangeiras
            $table->foreign('worker_id')->references('worker_id')->on('workers')->onDelete('cascade');
            
            // Índices
            $table->index('worker_id');
            $table->index('layoff_date');
            $table->index('layoff_type');
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('layoffs');
    }
};
