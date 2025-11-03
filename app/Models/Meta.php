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
    ];

    public const TIPOS = [
        'boolean' => 'Sim/Não',
        'integer' => 'Número inteiro',
        'scale' => 'Escala de 1 a 5',
    ];
}
