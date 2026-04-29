<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FollowingFollowerSnapshot extends Model
{
    protected $fillable = [
        'following_entry_id',
        'follower_count',
        'following_count',
        'heart_count',
        'recorded_at',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'following_entry_id' => 'integer',
            'follower_count' => 'integer',
            'following_count' => 'integer',
            'heart_count' => 'integer',
            'recorded_at' => 'datetime',
        ];
    }

    public function followingEntry(): BelongsTo
    {
        return $this->belongsTo(FollowingEntry::class);
    }
}
