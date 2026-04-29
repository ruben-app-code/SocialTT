<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialNetwork extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
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
        ];
    }

    /**
     * URL pública del perfil según el slug de la red y el nombre de usuario.
     */
    public static function profileUrlForSlug(string $slug, string $username): string
    {
        $username = trim($username);
        if ($username === '') {
            return '';
        }

        return match ($slug) {
            'tiktok' => 'https://www.tiktok.com/@' . ltrim($username, '@'),
            'instagram' => 'https://www.instagram.com/' . $username,
            'youtube' => 'https://www.youtube.com/@' . ltrim($username, '@'),
            'twitter' => 'https://twitter.com/' . $username,
            'x' => 'https://x.com/' . ltrim($username, '@'),
            'facebook' => 'https://www.facebook.com/' . $username,
            'twitch' => 'https://www.twitch.tv/' . $username,
            'linkedin' => 'https://www.linkedin.com/in/' . $username,
            'threads' => 'https://www.threads.net/@' . ltrim($username, '@'),
            default => 'https://' . $slug . '.com/' . $username,
        };
    }
}
