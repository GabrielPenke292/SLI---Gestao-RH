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
        Schema::create('workers', function (Blueprint $table) {
            $table->id('worker_id');
            $table->string('worker_name', 75);
            $table->string('worker_email', 45)->unique();
            $table->string('worker_document', 14)->nullable(); // CPF
            $table->string('worker_rg', 20)->nullable();
            $table->date('worker_birth_date')->nullable();
            $table->date('worker_start_date')->nullable();
            $table->integer('worker_status')->default(1);
            $table->decimal('worker_salary', 10, 2)->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->string('created_by', 45)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('updated_by', 45)->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->string('deleted_by', 45)->nullable();
            
            // Foreign keys
            $table->foreign('department_id')->references('department_id')->on('departments')->onDelete('set null');
            
            // Índices
            $table->index('worker_email');
            $table->index('worker_name');
            $table->index('worker_status');
            $table->index('department_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workers');
    }
};
