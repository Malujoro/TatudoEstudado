<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model representing an individual study session.
 * 
 * @property string $id Session UUID.
 * @property \Illuminate\Support\Carbon $data Session date.
 * @property string $tipo Study type (e.g., Theory, Revision).
 * @property float $horas Duration in hours.
 * @property bool $finalizado Completion status.
 * @property string $assunto_id Studied topic ID.
 */
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

    /**
     * Defines the topic studied in this session.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function assunto(): BelongsTo
    {
        return $this->belongsTo(Assunto::class);
    }
}
