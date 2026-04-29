<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;

use App\Models\SocialNetwork;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $usersQuery = User::select('users.*')
            ->leftJoin('model_has_roles', function ($join) {
                $join->on('users.id', '=', 'model_has_roles.model_id')
                    ->where('model_has_roles.model_type', User::class);
            })
            ->leftJoin('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->with('roles');

        $this->applyUsersTextFilter($usersQuery, $q);

        $users = $usersQuery
            ->orderBy('roles.name', 'asc')
            ->orderBy('users.name', 'desc')
            ->distinct()
            ->get();

        return view('administrator.users.index', compact('users', 'q'));
    }

    /**
     * Filtra usuarios por nombre o correo (LIKE, insensible a mayúsculas en collation típica UTF8).
     */
    private function applyUsersTextFilter(Builder $query, string $search): void
    {
        if ($search === '') {
            return;
        }

        $like = '%'.str_replace(['%', '_'], ['\\%', '\\_'], $search).'%';

        $query->where(function ($w) use ($like) {
            $w->where('users.name', 'like', $like)
                ->orWhere('users.email', 'like', $like);
        });
    }

    public function create(): View
    {
        $roles = Role::orderBy('name')->get();

        return view('administrator.users.create', compact('roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|unique:users,email',
            'password'              => 'required|string|min:8|confirmed',
            'active'                => 'nullable|boolean',
            'roles'                 => 'nullable|array',
            'roles.*'               => 'exists:roles,id',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'active'   => $request->boolean('active', true),
        ]);

        if ($request->has('roles')) {
            $user->syncRoles(Role::whereIn('id', $request->input('roles', []))->pluck('name'));
        }

        if ($user->isSuperAdmin()) {
            $user->active = true;
            $user->save();
        }

        return redirect()->route('users.index')->with('success', __('Usuario creado.'));
    }

    public function updateActive(Request $request, User $user): JsonResponse
    {
        $request->validate([
            'active' => 'required|boolean',
        ]);

        if (! $request->boolean('active') && $user->id === auth()->id()) {
            return response()->json([
                'message' => __('No puede desactivar su propia cuenta desde aquí.'),
            ], 422);
        }

        if (! $request->boolean('active') && $user->isSuperAdmin()) {
            return response()->json([
                'message' => __('No puede desactivar a un usuario con rol SuperAdmin.'),
            ], 422);
        }

        $user->active = $request->boolean('active');
        $user->save();

        return response()->json([
            'active' => $user->active,
            'message' => $user->active ? __('Usuario activado.') : __('Usuario desactivado.'),
        ]);
    }

    public function edit(User $user): View
    {
        $roles = Role::orderBy('name')->get();
        $socialNetworks = SocialNetwork::orderBy('name')->get();
        $user->load([
            'roles',
            'socialAccounts' => function ($q) {
                $q->with(['socialNetwork', 'block', 'topics'])->withCount('topics');
            },
        ]);
        $topicsForAccounts = Topic::query()->with('parent')->orderBy('name')->get();

        return view('administrator.users.edit', compact('user', 'roles', 'socialNetworks', 'topicsForAccounts'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'password'              => 'nullable|string|min:8',
            'password_confirmation' => 'nullable|required_with:password|same:password',
            'active'                => 'nullable|boolean',
            'roles'                 => 'nullable|array',
            'roles.*'               => 'exists:roles,id',
        ]);

        $user->fill($request->only(['name', 'email']));
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($request->has('roles')) {
            $user->syncRoles(Role::whereIn('id', $request->input('roles', []))->pluck('name'));
        } else {
            $user->syncRoles([]);
        }

        $user->active = $user->isSuperAdmin() ? true : $request->boolean('active');
        $user->save();

        return redirect()->route('users.index')->with('success', __('Usuario actualizado.'));
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', __('No puede eliminar su propia cuenta.'));
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', __('Usuario eliminado.'));
    }
}