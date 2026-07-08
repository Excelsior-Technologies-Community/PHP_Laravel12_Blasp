<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    public const REPUTATION_START = 100;
    public const REPUTATION_PENALTY = 10;
    public const MUTE_THRESHOLD = 50;
    public const MUTE_DURATION_HOURS = 24;

    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'reputation_score',
        'muted_until',
        'profanity_hits',
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
            'muted_until' => 'datetime',
        ];
    }

    public function isMuted(): bool
    {
        return ! is_null($this->muted_until) && $this->muted_until->isFuture();
    }

    public function applyProfanityPenalty(int $hits = 1): void
    {
        $penalty = self::REPUTATION_PENALTY * max(1, $hits);
        $this->profanity_hits += max(1, $hits);
        $this->reputation_score = max(0, $this->reputation_score - $penalty);

        if ($this->reputation_score <= self::MUTE_THRESHOLD) {
            $this->muted_until = now()->addHours(self::MUTE_DURATION_HOURS);
        } elseif ($this->muted_until && $this->muted_until->isPast()) {
            $this->muted_until = null;
        }

        $this->save();
    }
}
