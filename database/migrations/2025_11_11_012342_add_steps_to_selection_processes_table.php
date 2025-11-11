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
        Schema::table('selection_processes', function (Blueprint $table) {
            $table->json('steps')->nullable()->after('observations')->comment('Etapas do processo seletivo em formato JSON');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('selection_processes', function (Blueprint $table) {
            //
        });
    }
};
