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
        Schema::create('user_permissions', function (Blueprint $table) {
            $table->id('user_permission_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('permission_id');
            $table->timestamps();
            
            // Chaves estrangeiras
            $table->foreign('user_id')->references('users_id')->on('users')->onDelete('cascade');
            $table->foreign('permission_id')->references('permissio_id')->on('permissions')->onDelete('cascade');
            
            // Índices
            $table->index(['user_id', 'permission_id']);
            $table->unique(['user_id', 'permission_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_permissions');
    }
};
