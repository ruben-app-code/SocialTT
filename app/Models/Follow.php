<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Follow extends Pivot
{
    public $incrementing = true;

    protected $table = 'follows';

    protected $fillable = [
        'follower_id',
        'creator_id',
        'personal_note',
        'tags',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'follower_id' => 'integer',
            'creator_id' => 'integer',
            'tags' => 'array',
        ];
    }

    public function follower(): BelongsTo
    {
        return $this->belongsTo(User::class, 'follower_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
}
