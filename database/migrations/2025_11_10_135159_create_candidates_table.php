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
        Schema::create('candidates', function (Blueprint $table) {
            $table->id('candidate_id');
            $table->string('candidate_name', 100);
            $table->string('candidate_email', 100)->nullable();
            $table->string('candidate_phone', 20)->nullable();
            $table->string('candidate_document', 14)->nullable()->comment('CPF');
            $table->string('candidate_rg', 20)->nullable();
            $table->date('candidate_birth_date')->nullable();
            $table->string('candidate_address', 255)->nullable();
            $table->string('candidate_city', 100)->nullable();
            $table->string('candidate_state', 2)->nullable();
            $table->string('candidate_zipcode', 10)->nullable();
            $table->text('candidate_experience')->nullable()->comment('Experiência profissional');
            $table->text('candidate_education')->nullable()->comment('Formação acadêmica');
            $table->text('candidate_skills')->nullable()->comment('Habilidades e competências');
            $table->text('candidate_resume_text')->nullable()->comment('Texto completo do currículo colado');
            $table->string('candidate_resume_pdf', 255)->nullable()->comment('Caminho do arquivo PDF do currículo');
            $table->text('candidate_notes')->nullable()->comment('Observações adicionais');
            $table->timestamp('created_at')->nullable();
            $table->string('created_by', 45)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('updated_by', 45)->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->string('deleted_by', 45)->nullable();
            
            // Índices
            $table->index('candidate_name');
            $table->index('candidate_email');
            $table->index('candidate_document');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};
