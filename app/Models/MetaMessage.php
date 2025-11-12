<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MetaMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'paciente_id',
        'meta_id',
        'paciente_nome',
        'telefone',
        'token',
        'data_envio',
        'status',
        'enviado_em',
        'respondido_em',
    ];

    protected $casts = [
        'data_envio' => 'datetime',
        'enviado_em' => 'datetime',
        'respondido_em' => 'datetime',
    ];

    protected $appends = ['link'];

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }

    public function meta(): BelongsTo
    {
        return $this->belongsTo(Meta::class);
    }

    public function resposta(): HasOne
    {
        return $this->hasOne(MetaResposta::class, 'meta_message_id');
    }

    public function getLinkAttribute(): string
    {
        return url('/metas/responder/' . $this->token);
    }
}
