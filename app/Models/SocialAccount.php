<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SocialAccount extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'social_network_id',
        'display_name',
        'username',
        'url',
        'avatar_url',
        'current_status',
        'is_verified',
        'is_primary',
        'last_checked_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'user_id' => 'integer',
            'social_network_id' => 'integer',
            'is_verified' => 'boolean',
            'is_primary' => 'boolean',
            'last_checked_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function socialNetwork(): BelongsTo
    {
        return $this->belongsTo(SocialNetwork::class);
    }

    /** Temas de contenido vinculados a esta cuenta (puede haber varios). */
    public function topics(): BelongsToMany
    {
        return $this->belongsToMany(Topic::class, 'social_account_topic')->withTimestamps();
    }

    /** Datos del bloqueo temporal (1:1). Solo aplica cuando current_status es blocked. */
    public function block(): HasOne
    {
        return $this->hasOne(SocialAccountBlock::class);
    }

    public function isBlockedStatus(): bool
    {
        return $this->current_status === 'blocked';
    }

    /**
     * Dentro del periodo de bloqueo: estado blocked y aún no llega activates_at.
     */
    public function isInActiveBlockPeriod(): bool
    {
        if (! $this->isBlockedStatus()) {
            return false;
        }
        $block = $this->relationLoaded('block') ? $this->block : $this->block()->first();

        return $block !== null && now()->lt($block->activates_at);
    }

    /**
     * El bloqueo temporal ya venció (activates_at pasada); la cuenta puede mostrarse como operativa en público
     * aunque current_status siga siendo blocked hasta que el creador lo cambie.
     */
    public function hasBlockPeriodEnded(): bool
    {
        if (! $this->isBlockedStatus()) {
            return false;
        }
        $block = $this->relationLoaded('block') ? $this->block : $this->block()->first();

        return $block !== null && now()->greaterThanOrEqualTo($block->activates_at);
    }

    /**
     * Enlace público al perfil de la red: activa, o blocked pero ya pasó la fecha de reactivación.
     */
    public function isUsableOnPublicProfile(): bool
    {
        return $this->current_status === 'active'
            || ($this->isBlockedStatus() && $this->hasBlockPeriodEnded());
    }

    /** Deja en falso el resto de cuentas del mismo usuario en esa red (opcionalmente excepto una). */
    public static function demoteOthersPrimary(int $userId, int $socialNetworkId, ?int $exceptAccountId = null): void
    {
        $q = static::query()
            ->where('user_id', $userId)
            ->where('social_network_id', $socialNetworkId);
        if ($exceptAccountId !== null) {
            $q->where('id', '!=', $exceptAccountId);
        }
        $q->update(['is_primary' => false]);
    }

    /** Marca como principal la primera cuenta restante (menor id), excluyendo opcionalmente una fila. */
    public static function promoteFirstPrimaryCandidate(int $userId, int $socialNetworkId, ?int $excludeAccountId = null): void
    {
        $q = static::query()
            ->where('user_id', $userId)
            ->where('social_network_id', $socialNetworkId);
        if ($excludeAccountId !== null) {
            $q->where('id', '!=', $excludeAccountId);
        }
        $next = $q->orderBy('id')->first();
        if ($next !== null) {
            $next->update(['is_primary' => true]);
        }
    }
}
