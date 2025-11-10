<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Alterar o enum para incluir 'reprovado'
        DB::statement("ALTER TABLE selection_processes MODIFY COLUMN status ENUM('aguardando_aprovacao', 'em_andamento', 'encerrado', 'congelado', 'reprovado') DEFAULT 'aguardando_aprovacao'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverter para o enum original (remover 'reprovado')
        // Nota: Se houver registros com status 'reprovado', será necessário atualizá-los antes de reverter
        DB::statement("ALTER TABLE selection_processes MODIFY COLUMN status ENUM('aguardando_aprovacao', 'em_andamento', 'encerrado', 'congelado') DEFAULT 'aguardando_aprovacao'");
    }
};
