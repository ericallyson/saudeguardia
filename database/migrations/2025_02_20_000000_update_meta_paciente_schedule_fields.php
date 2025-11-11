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
        Schema::table('meta_paciente', function (Blueprint $table) {
            try {
                $table->dropUnique(['paciente_id', 'meta_id']);
            } catch (\Throwable $exception) {
                // O índice pode já ter sido removido em outra migração.
            }

            if (Schema::hasColumn('meta_paciente', 'periodicidade')) {
                $table->dropColumn('periodicidade');
            }

            $table->json('dias_semana')->nullable()->after('horario');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meta_paciente', function (Blueprint $table) {
            $table->dropColumn('dias_semana');

            $table->string('periodicidade')->nullable()->after('meta_id');

            $table->unique(['paciente_id', 'meta_id']);
        });
    }
};
