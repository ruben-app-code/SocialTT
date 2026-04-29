<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesController extends Controller
{
    public function index(): View
    {
        $roles = Role::with('permissions')->orderBy('name')->get();
        return view('administrator.roles.index', compact('roles'));
    }

    public function create(): View
    {
        $guardName = config('auth.defaults.guard');
        $permissions = Permission::orderBy('name')->get();
        return view('administrator.roles.form', ['role' => null, 'guardName' => $guardName, 'permissions' => $permissions]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles')->where(fn ($q) => $q->where('guard_name', $request->input('guard_name'))),
            ],
            'guard_name' => ['required', 'string', Rule::in(['web', 'sanctum'])],
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create([
            'name' => $request->input('name'),
            'guard_name' => $request->input('guard_name'),
        ]);

        $ids = $request->input('permissions', []);
        if (!empty($ids)) {
            $role->syncPermissions(Permission::whereIn('id', $ids)->pluck('name'));
        }

        return redirect()->route('roles.index')->with('success', __('Rol creado.'));
    }

    public function edit(Role $role): View
    {
        $role->load('permissions');
        $permissions = Permission::orderBy('name')->get();
        $rolePermissionIds = $role->permissions->pluck('id')->toArray();
        return view('administrator.roles.form', [
            'role' => $role,
            'guardName' => $role->guard_name,
            'permissions' => $permissions,
            'rolePermissionIds' => $rolePermissionIds,
        ]);
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles')->ignore($role->id)->where(fn ($q) => $q->where('guard_name', $request->input('guard_name'))),
            ],
            'guard_name' => ['required', 'string', Rule::in(['web', 'sanctum'])],
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update([
            'name' => $request->input('name'),
            'guard_name' => $request->input('guard_name'),
        ]);

        $ids = $request->input('permissions', []);
        $role->syncPermissions(Permission::whereIn('id', $ids)->pluck('name'));

        return redirect()->route('roles.index')->with('success', __('Rol actualizado.'));
    }

    public function destroy(Role $role): RedirectResponse
    {
        $role->delete();
        return redirect()->route('roles.index')->with('success', __('Rol eliminado.'));
    }
}