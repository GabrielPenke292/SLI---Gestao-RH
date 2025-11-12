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
        Schema::create('clinics', function (Blueprint $table) {
            $table->id('clinic_id');
            $table->string('corporate_name', 255)->comment('Razão Social');
            $table->string('trade_name', 255)->nullable()->comment('Nome Fantasia');
            $table->string('cnpj', 18)->unique()->comment('CNPJ');
            $table->string('email', 255)->nullable()->comment('E-mail');
            $table->string('phone', 20)->nullable()->comment('Telefone');
            $table->string('address', 255)->nullable()->comment('Endereço');
            $table->string('address_number', 20)->nullable()->comment('Número');
            $table->string('address_complement', 100)->nullable()->comment('Complemento');
            $table->string('neighborhood', 100)->nullable()->comment('Bairro');
            $table->string('city', 100)->nullable()->comment('Cidade');
            $table->string('state', 2)->nullable()->comment('Estado (UF)');
            $table->string('zip_code', 10)->nullable()->comment('CEP');
            $table->text('notes')->nullable()->comment('Observações');
            $table->boolean('is_active')->default(true)->comment('Clínica ativa');
            $table->timestamp('created_at')->nullable();
            $table->string('created_by', 45)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('updated_by', 45)->nullable();
            $table->timestamp('deleted_at')->nullable();
            
            // Índices
            $table->index('cnpj');
            $table->index('is_active');
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinics');
    }
};
