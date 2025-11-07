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
        Schema::create('calendar_events', function (Blueprint $table) {
            $table->id('event_id');
            $table->string('event_title', 255);
            $table->text('event_description')->nullable();
            $table->date('event_date');
            $table->time('event_start_time')->nullable();
            $table->time('event_end_time')->nullable();
            $table->string('event_type', 50)->default('custom'); // 'birthday', 'anniversary', 'custom'
            $table->unsignedBigInteger('worker_id')->nullable(); // Para aniversários e aniversários de empresa
            $table->string('event_color', 7)->default('#3788d8'); // Cor do evento no calendário
            $table->integer('event_status')->default(1); // 1 = ativo, 0 = inativo
            $table->timestamp('created_at')->nullable();
            $table->string('created_by', 45)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('updated_by', 45)->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->string('deleted_by', 45)->nullable();
            
            // Foreign keys
            $table->foreign('worker_id')->references('worker_id')->on('workers')->onDelete('cascade');
            
            // Índices
            $table->index('event_date');
            $table->index('event_type');
            $table->index('event_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendar_events');
    }
};
