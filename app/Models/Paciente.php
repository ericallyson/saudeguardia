<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Paciente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'email',
        'telefone',
        'data_nascimento',
        'plano',
        'data_inicio',
        'status',
        'peso_inicial',
        'altura_cm',
        'circunferencia_abdominal',
        'condicoes_medicas',
        'peso_meta',
        'prazo_meses',
        'atividade_fisica',
        'whatsapp_numero',
        'whatsapp_frequencia',
    ];

    protected $casts = [
        'data_nascimento' => 'date',
        'data_inicio' => 'date',
        'peso_inicial' => 'decimal:2',
        'circunferencia_abdominal' => 'decimal:2',
        'peso_meta' => 'decimal:2',
    ];

    public function metas(): BelongsToMany
    {
        return $this->belongsToMany(Meta::class, 'meta_paciente')
            ->withPivot(['periodicidade', 'vencimento'])
            ->withTimestamps();
    }
}
