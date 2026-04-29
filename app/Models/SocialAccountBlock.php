<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialAccountBlock extends Model
{
    protected $fillable = [
        'social_account_id',
        'blocked_at',
        'activates_at',
    ];

    protected function casts(): array
    {
        return [
            'social_account_id' => 'integer',
            'blocked_at' => 'datetime',
            'activates_at' => 'datetime',
        ];
    }

    public function socialAccount(): BelongsTo
    {
        return $this->belongsTo(SocialAccount::class);
    }
}
