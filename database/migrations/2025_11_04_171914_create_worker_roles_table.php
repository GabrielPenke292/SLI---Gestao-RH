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
        Schema::create('worker_roles', function (Blueprint $table) {
            $table->id('worker_role_id');
            $table->unsignedBigInteger('worker_id');
            $table->unsignedBigInteger('role_id');
            $table->integer('worker_role_status')->default(1);
            $table->timestamp('created_at')->nullable();
            $table->string('created_by', 45)->nullable();
            
            // Foreign keys
            $table->foreign('worker_id')->references('worker_id')->on('workers')->onDelete('cascade');
            $table->foreign('role_id')->references('role_id')->on('roles')->onDelete('cascade');
            
            // Índices
            $table->index('worker_id');
            $table->index('role_id');
            $table->index('worker_role_status');
            
            // Evitar duplicatas
            $table->unique(['worker_id', 'role_id'], 'worker_role_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('worker_roles');
    }
};
