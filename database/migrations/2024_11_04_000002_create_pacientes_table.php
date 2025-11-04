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
        Schema::create('pacientes', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('email')->nullable();
            $table->string('telefone')->nullable();
            $table->date('data_nascimento')->nullable();
            $table->string('plano')->nullable();
            $table->date('data_inicio')->nullable();
            $table->string('status')->default('ativo');
            $table->decimal('peso_inicial', 8, 2)->nullable();
            $table->unsignedSmallInteger('altura_cm')->nullable();
            $table->decimal('circunferencia_abdominal', 8, 2)->nullable();
            $table->text('condicoes_medicas')->nullable();
            $table->decimal('peso_meta', 8, 2)->nullable();
            $table->unsignedSmallInteger('prazo_meses')->nullable();
            $table->string('atividade_fisica')->nullable();
            $table->string('whatsapp_numero')->nullable();
            $table->string('whatsapp_frequencia')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pacientes');
    }
};
