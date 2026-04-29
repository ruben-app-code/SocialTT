<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\File;

/**
 * Rutas públicas de avatar de plantilla (public/template/assets/images/avatar).
 * Los seeders y el API de seed usan este servicio para no duplicar reglas.
 */
class TemplateAvatarProfileService
{
    /** Ruta relativa a public/ (misma convención que User::avatar_url). */
    public const RELATIVE_BASE = 'template/assets/images/avatar';

    private const DEFAULT_SLOT_COUNT = 8;

    private const IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];

    public function absoluteBasePath(): string
    {
        return public_path(str_replace('/', DIRECTORY_SEPARATOR, self::RELATIVE_BASE));
    }

    /**
     * Archivos de imagen encontrados en la carpeta (solo nombre de archivo), ordenados.
     *
     * @return list<string>
     */
    public function discoverFilenames(): array
    {
        $dir = $this->absoluteBasePath();
        if (! is_dir($dir)) {
            return [];
        }

        $out = [];
        foreach (File::files($dir) as $file) {
            $ext = strtolower($file->getExtension());
            if (in_array($ext, self::IMAGE_EXTENSIONS, true)) {
                $out[] = $file->getFilename();
            }
        }

        sort($out);

        return array_values($out);
    }

    /**
     * Ruta lista para guardar en users.profile_photo_path (p. ej. template/.../foto.jpg).
     */
    public function relativePathForLoopIndex(int $index): string
    {
        $files = $this->discoverFilenames();
        if ($files !== []) {
            $n = count($files);

            return self::RELATIVE_BASE.'/'.$files[$index % $n];
        }

        $num = ($index % self::DEFAULT_SLOT_COUNT) + 1;

        return self::RELATIVE_BASE.'/'.$num.'.jpg';
    }

    /**
     * Misma lógica que el avatar por defecto del modelo: variación estable por id.
     */
    public function relativePathForUserId(int $userId): string
    {
        return $this->relativePathForLoopIndex(max(0, $userId - 1));
    }

    public function applyToUser(User $user, ?int $loopIndex = null): void
    {
        $idx = $loopIndex ?? max(0, (int) $user->id - 1);
        $user->forceFill([
            'profile_photo_path' => $this->relativePathForLoopIndex($idx),
        ])->save();
    }
}
