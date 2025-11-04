<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
