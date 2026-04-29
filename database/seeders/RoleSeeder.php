<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\SeedsWithProgress;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    use SeedsWithProgress;

    public function run(): void
    {
        $guard = config('auth.defaults.guard');

        foreach (['SuperAdmin', 'Administrador', 'Usuario'] as $name) {
            Role::firstOrCreate([
                'name'       => $name,
                'guard_name' => $guard,
            ]);
            $this->seedDot();
        }

        Role::findByName('SuperAdmin', $guard)
            ?->syncPermissions(Permission::pluck('name'));
        $this->seedDot();

        Role::findByName('Administrador', $guard)
            ?->syncPermissions([
                'usuarios.ver',
                'usuarios.crear',
                'usuarios.editar',
                'usuarios.eliminar',
                'categorias.administrar',
                'estados.administrar',
            ]);
        $this->seedDot();
    }
}
