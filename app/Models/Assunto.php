<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Model representing a study topic within a subject.
 *
 * @property string $id Topic UUID.
 * @property string $nome Topic name.
 * @property string $materia_id Parent subject ID.
 * @property bool $teoria_finalizada Indicates if the theory part was completed.
 * @property string|null $tipo Indicates if the topic is only teoria/exercicio/revisao.
 */
class Assunto extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'assuntos';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'nome',
        'materia_id',
        'teoria_finalizada',
        'tipo',
    ];

    protected $casts = [
        'teoria_finalizada' => 'boolean',
    ];

    /**
     * Defines the subject this topic belongs to.
     */
    public function materia(): BelongsTo
    {
        return $this->belongsTo(Materia::class);
    }

    /**
     * Lists all study sessions performed for this topic.
     */
    public function sessoesEstudo(): HasMany
    {
        return $this->hasMany(SessaoEstudo::class);
    }

    /**
     * Gets the notebook linked to this topic.
     */
    public function caderno(): HasOne
    {
        return $this->hasOne(Caderno::class);
    }

    /**
     * Gets the performance metrics linked to this topic.
     */
    public function metrica(): HasOne
    {
        return $this->hasOne(Metrica::class);
    }
}
