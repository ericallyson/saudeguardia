<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Meta extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'descricao',
        'tipo',
        'periodicidade_padrao',
    ];

    public const TIPOS = [
        'boolean' => 'Sim/Não',
        'integer' => 'Número inteiro',
        'scale' => 'Escala de 1 a 5',
        'texto' => 'Texto',
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
            ->withPivot(['periodicidade', 'vencimento', 'horario'])
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
}
