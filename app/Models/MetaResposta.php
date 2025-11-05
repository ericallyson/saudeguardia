<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MetaResposta extends Model
{
    use HasFactory;

    protected $fillable = [
        'meta_message_id',
        'paciente_id',
        'meta_id',
        'valor',
        'respondido_em',
    ];

    protected $casts = [
        'respondido_em' => 'datetime',
    ];

    public function message(): BelongsTo
    {
        return $this->belongsTo(MetaMessage::class, 'meta_message_id');
    }

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }

    public function meta(): BelongsTo
    {
        return $this->belongsTo(Meta::class);
    }
}
