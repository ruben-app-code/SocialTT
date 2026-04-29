<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermisosController extends Controller
{
    public function index(): View
    {
        $permissions = Permission::orderBy('name')->get();
        $roles = Role::with('permissions')->orderBy('name')->get();

        return view('administrator.permisos.index', compact('permissions', 'roles'));
    }

    public function create(): View
    {
        $guardName = config('auth.defaults.guard');
        return view('administrator.permisos.form', ['permission' => null, 'guardName' => $guardName]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
            'guard_name' => 'nullable|string|max:255',
        ]);
        $guardName = $request->input('guard_name') ?: config('auth.defaults.guard');
        Permission::create(['name' => $request->input('name'), 'guard_name' => $guardName]);
        return redirect()->route('permisos.index')->with('success', __('Permiso creado.'));
    }

    public function edit(Permission $permission): View
    {
        return view('administrator.permisos.form', ['permission' => $permission, 'guardName' => $permission->guard_name]);
    }

    public function update(Request $request, Permission $permission): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
            'guard_name' => 'nullable|string|max:255',
        ]);
        $permission->update([
            'name' => $request->input('name'),
            'guard_name' => $request->input('guard_name') ?: config('auth.defaults.guard'),
        ]);
        return redirect()->route('permisos.index')->with('success', __('Permiso actualizado.'));
    }

    public function destroy(Permission $permission): RedirectResponse
    {
        $permission->delete();
        return redirect()->route('permisos.index')->with('success', __('Permiso eliminado.'));
    }

    public function editRole(Role $role): View
    {
        $permissions = Permission::orderBy('name')->get();
        $role->load('permissions');
        $rolePermissionIds = $role->permissions->pluck('id')->toArray();

        return view('administrator.permisos.edit-role', compact('role', 'permissions', 'rolePermissionIds'));
    }

    public function updateRole(Request $request, Role $role): RedirectResponse
    {
        $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $ids = $request->input('permissions', []);
        $role->syncPermissions(Permission::whereIn('id', $ids)->pluck('name'));

        return redirect()->route('permisos.index')->with('success', __('Permisos del rol actualizados.'));
    }
}