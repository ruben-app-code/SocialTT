<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\SeedsWithProgress;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    use SeedsWithProgress;

    public function run(): void
    {
        $guardName = config('auth.defaults.guard');

        $permissions = [
            'usuarios.ver',
            'usuarios.crear',
            'usuarios.editar',
            'usuarios.eliminar',
            'categorias.administrar',
            'estados.administrar',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => $guardName],
                ['name' => $name, 'guard_name' => $guardName]
            );
            $this->seedDot();
        }
    }
}
