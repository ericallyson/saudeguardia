<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Paciente extends Model
{
    use HasFactory;
    use SoftDeletes;

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
        'ultima_observacao',
        'user_id',
    ];

    protected $casts = [
        'data_nascimento' => 'date',
        'data_inicio' => 'date',
        'peso_inicial' => 'decimal:2',
        'circunferencia_abdominal' => 'decimal:2',
        'peso_meta' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (Paciente $paciente): void {
            if (! $paciente->uuid) {
                $paciente->uuid = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function metas(): BelongsToMany
    {
        return $this->belongsToMany(Meta::class, 'meta_paciente')
            ->withPivot(['id', 'vencimento', 'horario', 'horarios', 'dias_semana'])
            ->withTimestamps();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function metaMessages(): HasMany
    {
        return $this->hasMany(MetaMessage::class);
    }

    public function metaRespostas(): HasMany
    {
        return $this->hasMany(MetaResposta::class);
    }
}
