<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model representing the notebook content for a topic.
 * 
 * @property string $id Notebook UUID.
 * @property string $conteudo Annotation text.
 * @property string $assunto_id Related topic ID.
 */
class Caderno extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'cadernos';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'conteudo',
        'assunto_id',
    ];

    /**
     * Defines the topic this notebook belongs to.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function assunto(): BelongsTo
    {
        return $this->belongsTo(Assunto::class);
    }
}
