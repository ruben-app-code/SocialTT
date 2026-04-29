<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class FollowingEntry extends Model
{
    public const TYPE_PLATFORM = 'platform';

    public const TYPE_EXTERNAL = 'external';

    protected $fillable = [
        'user_id',
        'entry_type',
        'platform_user_id',
        'social_network_id',
        'username',
        'remote_display_name',
        'url',
        'avatar_url',
        'use_custom_avatar',
        'label',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'user_id' => 'integer',
            'platform_user_id' => 'integer',
            'social_network_id' => 'integer',
            'use_custom_avatar' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function platformUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'platform_user_id');
    }

    public function socialNetwork(): BelongsTo
    {
        return $this->belongsTo(SocialNetwork::class);
    }

    /** Histórico de seguidores (y métricas) cada vez que se consulta el perfil en TikTok. */
    public function followerSnapshots(): HasMany
    {
        return $this->hasMany(FollowingFollowerSnapshot::class);
    }

    public function latestFollowerSnapshot(): HasOne
    {
        return $this->hasOne(FollowingFollowerSnapshot::class)->latestOfMany('recorded_at');
    }

    public function isPlatform(): bool
    {
        return $this->entry_type === self::TYPE_PLATFORM;
    }

    public function isExternal(): bool
    {
        return $this->entry_type === self::TYPE_EXTERNAL;
    }

    public function displayTitle(): string
    {
        if ($this->label) {
            return $this->label;
        }
        if ($this->isPlatform() && $this->relationLoaded('platformUser') && $this->platformUser) {
            return $this->platformUser->name;
        }
        if ($this->isExternal()) {
            $at = '@'.ltrim((string) ($this->username ?? ''), '@');
            if (filled($this->remote_display_name)) {
                return $this->remote_display_name.' · '.$at;
            }
            if ($this->relationLoaded('socialNetwork') && $this->socialNetwork) {
                return $this->socialNetwork->name.' · '.$at;
            }

            return $at !== '@' ? $at : __('Cuenta');
        }

        return $this->username ? '@'.ltrim((string) $this->username, '@') : __('Cuenta');
    }
}
