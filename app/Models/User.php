<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

/**
 * User model.
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $role
 * @property string|null $photo_url
 * @property array|null $horario_semanal
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read int $sequencia_estudo Current consecutive study days streak.
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The default attribute values.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'horario_semanal' => '{"domingo":0,"segunda":0,"terca":0,"quarta":0,"quinta":0,"sexta":0,"sabado":0}',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'photo_url',
        'horario_semanal',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'horario_semanal' => 'array',
        ];
    }

    /**
     * Lists subjects created by this user.
     */
    public function materias(): HasMany
    {
        return $this->hasMany(Materia::class);
    }

    /**
     * Calculate the current streak of consecutive study days.
     * A day is counted if there is at least one completed study session.
     */
    public function obterSequenciaEstudo(): int
    {
        // Fetch all unique dates of completed study sessions for this user
        $datasEstudadas = SessaoEstudo::query()
            ->whereHas('assunto.materia', fn ($q) => $q->where('user_id', $this->id))
            ->where('finalizado', true)
            ->select('data')
            ->distinct()
            ->orderBy('data', 'desc')
            ->pluck('data')
            ->map(fn ($d) => $d instanceof Carbon ? $d->toDateString() : Carbon::parse($d)->toDateString())
            ->toArray();

        if (empty($datasEstudadas)) {
            return 0;
        }

        $hoje = Carbon::today();
        $hojeStr = $hoje->toDateString();
        $ontemStr = $hoje->copy()->subDay()->toDateString();

        $temHoje = in_array($hojeStr, $datasEstudadas);
        $temOntem = in_array($ontemStr, $datasEstudadas);

        // If the user did not study today nor yesterday, the streak is broken
        if (! $temHoje && ! $temOntem) {
            return 0;
        }

        // Start counting from today (if studied today) or yesterday
        $dataCorrente = $temHoje ? $hoje : $hoje->copy()->subDay();
        $sequencia = 0;

        // Decrement day by day as long as the date exists in the study records
        while (in_array($dataCorrente->toDateString(), $datasEstudadas)) {
            $sequencia++;
            $dataCorrente->subDay();
        }

        return $sequencia;
    }

    /**
     * Accessor to retrieve the user's current study streak.
     */
    public function getSequenciaEstudoAttribute(): int
    {
        return $this->obterSequenciaEstudo();
    }
}
