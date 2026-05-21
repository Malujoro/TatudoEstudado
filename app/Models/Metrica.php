<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model storing performance (correct/wrong) for a topic.
 *
 * @property string $id Metric UUID.
 * @property int $acertos Number of correct answers.
 * @property int $erros Number of wrong answers.
 * @property string $assunto_id Related topic ID.
 */
class Metrica extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'metricas';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'acertos',
        'erros',
        'assunto_id',
    ];

    /**
     * Defines the topic this metric refers to.
     */
    public function assunto(): BelongsTo
    {
        return $this->belongsTo(Assunto::class);
    }
}
