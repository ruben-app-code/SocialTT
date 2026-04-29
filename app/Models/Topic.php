<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use InvalidArgumentException;

class Topic extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'parent_id',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'parent_id' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Topic $topic): void {
            if ($topic->parent_id === null) {
                return;
            }
            $parent = $topic->relationLoaded('parent')
                ? $topic->parent
                : Topic::query()->find($topic->parent_id);
            if ($parent && $parent->parent_id !== null) {
                throw new InvalidArgumentException('Los temas solo pueden tener dos niveles: principal y subtema.');
            }
        });
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Topic::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Topic::class, 'parent_id')->orderBy('name');
    }

    /** Temas principales (sin padre). */
    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    /** Subtemas (con padre). */
    public function scopeChildrenOnly($query)
    {
        return $query->whereNotNull('parent_id');
    }

    public function isRoot(): bool
    {
        return $this->parent_id === null;
    }

    /** Nombre para mostrar: incluye padre si es subtema. */
    public function getDisplayNameAttribute(): string
    {
        if ($this->parent_id === null) {
            return $this->name;
        }
        $parentName = $this->relationLoaded('parent')
            ? $this->parent?->name
            : $this->parent()->value('name');

        return $parentName ? $parentName.' · '.$this->name : $this->name;
    }

    /** Creadores afiliados a este tema. */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'topic_user')->withTimestamps();
    }

    /** Cuentas de redes asociadas a este tema (contenido de la cuenta en esa categoría). */
    public function socialAccounts(): BelongsToMany
    {
        return $this->belongsToMany(SocialAccount::class, 'social_account_topic')->withTimestamps();
    }
}
