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
 * @property array<int, string>|null $tipo Indicates which types are allowed (teoria/exercicio/revisao).
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

    /**
     * Attribute casting.
     *
     * - teoria_finalizada: bool
     * - tipo: array<string>|null (stored as JSON/JSONB)
     *
     * @var array<string, string>
     */
    protected $casts = [
        'teoria_finalizada' => 'boolean',
        'tipo' => 'array',
    ];

    /**
     * Defines the subject this topic belongs to.
     *
     * @return BelongsTo<Materia, Assunto>
     */
    public function materia(): BelongsTo
    {
        return $this->belongsTo(Materia::class);
    }

    /**
     * Lists all study sessions performed for this topic.
     *
     * @return HasMany<SessaoEstudo, Assunto>
     */
    public function sessoesEstudo(): HasMany
    {
        return $this->hasMany(SessaoEstudo::class);
    }

    /**
     * Gets the notebook linked to this topic.
     *
     * @return HasOne<Caderno, Assunto>
     */
    public function caderno(): HasOne
    {
        return $this->hasOne(Caderno::class);
    }

    /**
     * Gets the performance metrics linked to this topic.
     *
     * @return HasOne<Metrica, Assunto>
     */
    public function metrica(): HasOne
    {
        return $this->hasOne(Metrica::class);
    }
}
