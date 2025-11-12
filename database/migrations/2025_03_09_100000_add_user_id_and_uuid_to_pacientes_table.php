<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pacientes', function (Blueprint $table): void {
            $table->uuid('uuid')->nullable()->after('id')->unique();
            $table->foreignId('user_id')->nullable()->after('uuid')->constrained()->nullOnDelete();
        });

        DB::table('pacientes')
            ->whereNull('uuid')
            ->chunkById(100, function ($pacientes): void {
                foreach ($pacientes as $paciente) {
                    DB::table('pacientes')
                        ->where('id', $paciente->id)
                        ->update(['uuid' => Str::uuid()]);
                }
            });

        $firstUserId = DB::table('users')->orderBy('id')->value('id');

        if ($firstUserId) {
            DB::table('pacientes')
                ->whereNull('user_id')
                ->update(['user_id' => $firstUserId]);
        }
    }

    public function down(): void
    {
        Schema::table('pacientes', function (Blueprint $table): void {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['uuid', 'user_id']);
        });
    }
};
