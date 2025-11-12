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
        // Para MySQL/MariaDB, precisamos alterar o enum
        DB::statement("ALTER TABLE `training_contents` MODIFY COLUMN `content_type` ENUM('pdf', 'excel', 'powerpoint', 'video_file', 'youtube_link') NOT NULL COMMENT 'Tipo de conteúdo'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverter para o enum original
        DB::statement("ALTER TABLE `training_contents` MODIFY COLUMN `content_type` ENUM('pdf', 'excel', 'video_file', 'youtube_link') NOT NULL COMMENT 'Tipo de conteúdo'");
    }
};
