<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'from_user_id',
        'to_user_id',
        'content',
        'channel',
        'status',
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
            'from_user_id' => 'integer',
            'to_user_id' => 'integer',
        ];
    }

    public function fromUser(): BelongsTo
    {
        return $this->belongsTo(FromUser::class);
    }

    public function toUser(): BelongsTo
    {
        return $this->belongsTo(ToUser::class);
    }
}
