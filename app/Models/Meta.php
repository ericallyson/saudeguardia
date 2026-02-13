<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Meta extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'descricao',
        'tipo',
        'periodicidade_padrao',
        'user_id',
    ];

    public const TIPOS = [
        'boolean' => 'Sim/Não',
        'integer' => 'Número',
        'scale' => 'Escala de 1 a 5',
        'texto' => 'Texto',
        'blood_pressure' => 'Pressão arterial',
    ];

    public const PERIODICIDADES = [
        'diario' => 'Diário',
        'semanal' => 'Semanal',
        'quinzenal' => 'Quinzenal',
        'dia_sim_dia_nao' => 'Dia sim, dia não',
    ];

    public function pacientes(): BelongsToMany
    {
        return $this->belongsToMany(Paciente::class, 'meta_paciente')
            ->withPivot(['id', 'vencimento', 'horario', 'horarios', 'dias_semana'])
            ->withTimestamps();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(MetaMessage::class);
    }

    public function respostas(): HasMany
    {
        return $this->hasMany(MetaResposta::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
