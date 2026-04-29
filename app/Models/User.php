<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, HasRoles, Notifiable;

    /** Tamaño fijo al que se normaliza cualquier avatar subido (ancho × alto, px). */
    private const PROFILE_PHOTO_SIZE = 128;

    /** Nombre del rol Spatie (`RoleSeeder`). */
    public const SUPERADMIN_ROLE = 'SuperAdmin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'timezone',
        'role',
        'level_id',
        'is_claimed',
        'profile_photo_path',
    ];

    /**
     * URL del avatar (imagen de perfil). Usa profile_photo_path o avatar por defecto del template.
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->profile_photo_path) {
            return asset(ltrim($this->profile_photo_path, '/'));
        }
        $num = 1 + ((int) $this->id % 8);
        return asset("template/assets/images/avatar/{$num}.jpg");
    }

    /**
     * Jetstream / barra de navegación usan profile_photo_url; misma URL que avatar_url.
     */
    public function getProfilePhotoUrlAttribute(): string
    {
        return $this->avatar_url;
    }

    /**
     * Actualiza la foto de perfil del usuario (usado por Fortify/Jetstream).
     * Cualquier imagen se recorta y escala a un cuadrado PROFILE_PHOTO_SIZE × PROFILE_PHOTO_SIZE (JPEG).
     */
    public function updateProfilePhoto(UploadedFile $photo): void
    {
        $disk = 'public';
        $directory = 'profile-photos';

        if ($this->profile_photo_path) {
            $oldPath = str_replace('storage/', '', $this->profile_photo_path);
            if (str_starts_with($oldPath, $directory.'/')) {
                Storage::disk($disk)->delete($oldPath);
            }
        }

        $sourcePath = $photo->getRealPath() ?: $photo->getPathname();

        $manager = extension_loaded('imagick')
            ? ImageManager::imagick()
            : ImageManager::gd();

        $image = $manager->read($sourcePath);
        $image->cover(self::PROFILE_PHOTO_SIZE, self::PROFILE_PHOTO_SIZE);

        $filename = $directory.'/'.Str::uuid()->toString().'.jpg';
        Storage::disk($disk)->put($filename, (string) $image->toJpeg(85));

        $this->forceFill([
            'profile_photo_path' => 'storage/'.$filename,
        ])->save();
    }

    /**
     * Elimina la foto de perfil en disco (Jetstream DeleteUser y formulario de perfil).
     */
    public function deleteProfilePhoto(): void
    {
        if (! $this->profile_photo_path) {
            return;
        }

        $disk = 'public';
        $directory = 'profile-photos';
        $oldPath = str_replace('storage/', '', $this->profile_photo_path);
        if (str_starts_with($oldPath, $directory.'/')) {
            Storage::disk($disk)->delete($oldPath);
        }

        $this->forceFill(['profile_photo_path' => null])->save();
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
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
            'level_id' => 'integer',
            'is_claimed' => 'boolean',
            'active' => 'boolean',
        ];
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(\App\Models\CreatorLevel::class);
    }

    public function socialAccounts(): HasMany
    {
        return $this->hasMany(SocialAccount::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function polls(): HasMany
    {
        return $this->hasMany(Poll::class);
    }

    public function liveAnnouncements(): HasMany
    {
        return $this->hasMany(LiveAnnouncement::class);
    }

    /** Enlaces personalizados (páginas web, tiendas, etc.) mostrados en el perfil público. */
    public function personalLinks(): HasMany
    {
        return $this->hasMany(PersonalLink::class)->orderBy('sort_order')->orderBy('id');
    }

    /** Temas a los que está afiliado el creador. */
    public function topics(): BelongsToMany
    {
        return $this->belongsToMany(Topic::class, 'topic_user')->withTimestamps();
    }

    /**
     * Cuenta de TikTok principal del creador (para URL pública y slug).
     * Usa la relación cargada si existe para evitar N+1.
     */
    public function primaryTiktokAccount(): ?SocialAccount
    {
        if ($this->relationLoaded('socialAccounts')) {
            return $this->socialAccounts
                ->filter(fn ($a) => $a->socialNetwork?->slug === 'tiktok')
                ->sortByDesc(fn ($a) => $a->is_primary)
                ->values()
                ->first(fn ($a) => $a->isUsableOnPublicProfile());
        }

        return $this->socialAccounts()
            ->whereHas('socialNetwork', fn ($q) => $q->where('slug', 'tiktok'))
            ->with('block')
            ->orderByDesc('is_primary')
            ->orderBy('id')
            ->get()
            ->first(fn ($a) => $a->isUsableOnPublicProfile());
    }

    /**
     * URL pública del perfil del creador: /@username si verificado, /u/username si no.
     * Si no tiene TikTok, devuelve la URL legacy por id.
     */
    public function getCreatorProfileUrlAttribute(): string
    {
        if ($this->role !== 'creator') {
            return url('/creadores/' . $this->id);
        }
        $tiktok = $this->primaryTiktokAccount();
        if (! $tiktok || ! $tiktok->username) {
            return url('/creadores/' . $this->id);
        }
        $segment = $tiktok->is_verified ? '@' : 'u/';

        return url($segment . $tiktok->username);
    }

    public function userSettings(): HasMany
    {
        return $this->hasMany(UserSetting::class);
    }

    /** Rol Spatie `SuperAdmin` (seed `RoleSeeder`). */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole(self::SUPERADMIN_ROLE);
    }

    /** Usuarios que siguen a este (sus seguidores). */
    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follows', 'creator_id', 'follower_id')
            ->using(Follow::class)
            ->withPivot(['personal_note', 'tags'])
            ->withTimestamps();
    }

    /** Creadores a los que sigue este usuario. */
    public function following(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'creator_id')
            ->using(Follow::class)
            ->withPivot(['personal_note', 'tags'])
            ->withTimestamps();
    }

    /** Cuentas que sigo (lista personal: perfiles del sitio o enlaces externos). */
    public function followingEntries(): HasMany
    {
        return $this->hasMany(FollowingEntry::class)->orderByDesc('id');
    }

    /**
     * Obtiene una preferencia del usuario (desde user_settings).
     */
    public function getPreference(string $key, mixed $default = null): mixed
    {
        $setting = $this->userSettings()->where('key', $key)->first();

        return $setting?->value ?? $default;
    }

    /**
     * Guarda una preferencia del usuario en user_settings.
     */
    public function setPreference(string $key, mixed $value): void
    {
        $this->userSettings()->updateOrCreate(
            ['key' => $key],
            ['value' => is_string($value) ? $value : json_encode($value)]
        );
    }

    /**
     * Sincroniza las preferencias (user_settings) a la sesión para uso en middleware/vistas.
     */
    public function syncPreferencesToSession(): void
    {
        $settings = $this->userSettings()->pluck('value', 'key')->toArray();
        session(['user_settings' => $settings]);
        session(['user_settings_loaded' => true]);
        if (isset($settings['theme'])) {
            session(['theme' => $settings['theme']]);
        }
    }
}
