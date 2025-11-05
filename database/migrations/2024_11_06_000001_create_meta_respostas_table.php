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
        Schema::create('meta_respostas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meta_message_id')->constrained('meta_messages')->cascadeOnDelete();
            $table->foreignId('paciente_id')->constrained()->cascadeOnDelete();
            $table->foreignId('meta_id')->constrained()->cascadeOnDelete();
            $table->text('valor');
            $table->timestamp('respondido_em');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meta_respostas');
    }
};
