<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SessaoEstudo extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'sessoes_estudo';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'data',
        'tipo',
        'horas',
        'finalizado',
        'assunto_id',
    ];

    protected $casts = [
        'data' => 'date',
        'finalizado' => 'boolean',
        'horas' => 'float',
    ];

    public function assunto(): BelongsTo
    {
        return $this->belongsTo(Assunto::class);
    }
}
