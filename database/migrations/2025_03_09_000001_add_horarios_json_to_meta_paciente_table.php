<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('meta_paciente', function (Blueprint $table) {
            if (! Schema::hasColumn('meta_paciente', 'horarios')) {
                $table->json('horarios')->nullable()->after('horario');
            }
        });

        $registros = DB::table('meta_paciente')
            ->select(['id', 'horario'])
            ->whereNotNull('horario')
            ->where('horario', '!=', '')
            ->get();

        foreach ($registros as $registro) {
            $horario = is_string($registro->horario) ? substr($registro->horario, 0, 5) : null;

            if (! $horario || preg_match('/^\d{2}:\d{2}$/', $horario) !== 1) {
                continue;
            }

            DB::table('meta_paciente')
                ->where('id', $registro->id)
                ->update(['horarios' => json_encode([$horario])]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meta_paciente', function (Blueprint $table) {
            if (Schema::hasColumn('meta_paciente', 'horarios')) {
                $table->dropColumn('horarios');
            }
        });
    }
};
