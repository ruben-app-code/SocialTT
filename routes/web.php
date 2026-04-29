<?php

use App\Http\Controllers\Administrator\CategoryController;
use App\Http\Controllers\Administrator\EstadoController;
use App\Http\Controllers\Administrator\LangController;
use App\Http\Controllers\Administrator\PermisosController;
use App\Http\Controllers\Administrator\RolesController;
use App\Http\Controllers\Administrator\TopicController;
use App\Http\Controllers\Administrator\UserController;
use App\Http\Controllers\Administrator\UserSocialAccountController;
use App\Http\Controllers\Creator\CommentController;
use App\Http\Controllers\Creator\LiveAnnouncementController;
use App\Http\Controllers\Creator\MessageController;
use App\Http\Controllers\Creator\PersonalLinkController;
use App\Http\Controllers\Creator\PollController;
use App\Http\Controllers\Creator\PollOptionController;
use App\Http\Controllers\Creator\ScheduleController;
use App\Http\Controllers\Creator\SocialAccountController;
use App\Http\Controllers\Creator\SubscriptionController;
use App\Http\Controllers\Creator\UserConfigurationController;
use App\Http\Controllers\Creator\UserTopicController;
use App\Http\Controllers\Creator\VerificadorRedSocialController;
use App\Http\Controllers\DashboardController as UserDashboardController;
use App\Http\Controllers\FollowingEntryController;
use App\Http\Controllers\Follower\DashboardController;
use App\Http\Controllers\AppearanceSessionController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\PublicPollVoteController;
use App\Http\Controllers\PublicSocialAccountTouchController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $featuredCreators = \App\Models\User::query()
        ->where('role', 'creator')
        ->with(['socialAccounts.socialNetwork'])
        ->withCount('socialAccounts')
        ->inRandomOrder()
        ->limit(8)
        ->get();

    return view('welcome', ['featuredCreators' => $featuredCreators]);
})->name('home');

Route::post('/appearance/session', [AppearanceSessionController::class, 'update'])
    ->name('appearance.session');


Route::get('/explore', [PublicController::class, 'explore'])->name('explore');

Route::get('/creadores', [PublicController::class, 'creadores'])->name('creadores');


Route::get('/explore/topics/search', [PublicController::class, 'searchTopics'])
    ->middleware('throttle:90,1')
    ->name('explore.topics.search');
Route::get('/temas', [PublicController::class, 'topics'])->name('public.topics');
// Perfil por usuario: /@Cristiano_Consciente (verificado) o /u/usuario (no verificado)
Route::get('@{username}', [PublicController::class, 'creatorProfileByUsername'])
    ->where('username', '[^/]+')
    ->name('public.creator.by_username');
Route::get('u/{username}', [PublicController::class, 'creatorProfileByUsername'])
    ->where('username', '[^/]+')
    ->name('public.creator.u');
// Legacy: redirige /creadores/29 a la URL canónica (@ o /u/)
Route::get('/creadores/{user}', [PublicController::class, 'creatorProfileRedirect'])->name('public.creator');

Route::post('public/social-accounts/{socialAccount}/touch-verify', [PublicSocialAccountTouchController::class, 'store'])
    ->middleware(['signed', 'throttle:40,1'])
    ->name('public.social-account.touch-verify');

Route::post('polls/{poll}/vote', [PublicPollVoteController::class, 'vote'])
    ->middleware('throttle:60,1')
    ->name('public.polls.vote');

Route::get('/csrf-token', function () {
    $lifetimeSec = (int) config('session.lifetime', 120) * 60;
    $driver = config('session.driver');
    $sessionId = request()->session()->getId();
    $now = time();
    $lastActivity = null;

    if ($driver === 'database') {
        $connection = config('session.connection');
        $table = config('session.table', 'sessions');
        $row = DB::connection($connection)->table($table)->where('id', $sessionId)->first();
        if ($row && isset($row->last_activity)) {
            $lastActivity = (int) $row->last_activity;
        }
    } elseif ($driver === 'file') {
        $path = rtrim(config('session.files'), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$sessionId;
        if (is_file($path)) {
            $lastActivity = (int) filemtime($path);
        }
    }

    // Segundos reales hasta que la sesión caduca por inactividad (según última actividad ANTES de esta petición).
    if ($lastActivity !== null) {
        $expiresAtUnix = $lastActivity + $lifetimeSec;
        $expiresInSeconds = max(0, $expiresAtUnix - $now);
    } else {
        $expiresAtUnix = $now + $lifetimeSec;
        $expiresInSeconds = $lifetimeSec;
    }

    return response()->json([
        'csrf_token' => csrf_token(),
        'expires_in_seconds' => $expiresInSeconds,
        'expires_at_unix' => $expiresAtUnix,
        'session_lifetime_seconds' => $lifetimeSec,
        // Tras responder, Laravel guarda la sesión y renueva last_activity: el token queda válido otros N segundos.
        'valid_for_seconds_after_this_request' => $lifetimeSec,
        'explicacion' => 'expires_in_seconds = tiempo que quedaba con la sesión tal como estaba antes de esta llamada. Si era bajo o 0, el token de la página podía estar caducado. Esta petición renueva la sesión: el csrf_token devuelto vale valid_for_seconds_after_this_request segundos desde ahora.',
    ]);
})->name('csrf-token');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', UserDashboardController::class)->name('dashboard');

    Route::get('/siguiendo', [FollowingEntryController::class, 'index'])->name('following.index');
    Route::get('/siguiendo/agregar', [FollowingEntryController::class, 'create'])->name('following.create');
    Route::post('/siguiendo/externa', [FollowingEntryController::class, 'storeExternal'])->name('following.store-external');
    Route::post('/siguiendo/creador', [FollowingEntryController::class, 'storePlatform'])->name('following.store-platform');
    Route::get('/siguiendo/{followingEntry}/editar', [FollowingEntryController::class, 'edit'])->name('following.edit');
    Route::put('/siguiendo/{followingEntry}', [FollowingEntryController::class, 'update'])->name('following.update');
    // POST explícito: evita fallos cuando el servidor o el proxy no aplican bien el tunelado _method=DELETE.
    Route::post('/siguiendo/{followingEntry}/eliminar', [FollowingEntryController::class, 'destroy'])->name('following.destroy.post');
    Route::delete('/siguiendo/{followingEntry}', [FollowingEntryController::class, 'destroy'])->name('following.destroy');

    Route::get('/follower/dashboard', DashboardController::class)->name('follower.dashboard');

    Route::get('/profile', function () {
        return view('creator.profile');
    })->name('profile.show');

    Route::get('/user/profile', function () {
        return view('profile.show');
    })->name('profile.edit');

    Route::get('/template-demo', function () {
        return view('creator.home-template');
    })->name('template.demo');

    Route::get('/user/configuration', [UserConfigurationController::class, 'show'])->name('configuration.show');
    Route::get('/user/configuration/theme/{theme}', [UserConfigurationController::class, 'setTheme'])
        ->where('theme', 'light|dark')
        ->name('configuration.theme');
    Route::put('/user/configuration', [UserConfigurationController::class, 'update'])->name('configuration.update');

    // Permisos (Spatie)
    Route::get('/admin/permisos', [PermisosController::class, 'index'])->name('permisos.index');
    Route::get('/permisos/crear', [PermisosController::class, 'create'])->name('permisos.create');
    Route::post('/permisos', [PermisosController::class, 'store'])->name('permisos.store');
    Route::get('/permisos/{permission}/edit', [PermisosController::class, 'edit'])->name('permisos.edit');
    Route::put('/permisos/{permission}', [PermisosController::class, 'update'])->name('permisos.update');
    Route::delete('/permisos/{permission}', [PermisosController::class, 'destroy'])->name('permisos.destroy');
    Route::get('/permisos/role/{role}/edit', [PermisosController::class, 'editRole'])->name('permisos.role.edit');
    Route::put('/permisos/role/{role}', [PermisosController::class, 'updateRole'])->name('permisos.role.update');

    // Roles (Spatie)
    Route::get('/admin/roles', [RolesController::class, 'index'])->name('roles.index');
    Route::get('/roles/crear', [RolesController::class, 'create'])->name('roles.create');
    Route::post('/roles', [RolesController::class, 'store'])->name('roles.store');
    Route::get('/roles/{role}/edit', [RolesController::class, 'edit'])->name('roles.edit');
    Route::put('/roles/{role}', [RolesController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{role}', [RolesController::class, 'destroy'])->name('roles.destroy');

    // Traducciones (es.json / en.json)
    // Usuarios
    Route::get('/admin/usuarios', [UserController::class, 'index'])->name('users.index');
    Route::get('/admin/usuarios/crear', [UserController::class, 'create'])->name('users.create');
    Route::post('/admin/usuarios', [UserController::class, 'store'])->name('users.store');
    Route::patch('/admin/usuarios/{user}/active', [UserController::class, 'updateActive'])->name('users.update-active');
    Route::get('/admin/usuarios/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/admin/usuarios/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/admin/usuarios/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::put('/admin/usuarios/{user}/social-accounts/{socialAccount}', [UserSocialAccountController::class, 'update'])->name('admin.users.social-accounts.update');
    Route::delete('/admin/usuarios/{user}/social-accounts/{socialAccount}', [UserSocialAccountController::class, 'destroy'])->name('admin.users.social-accounts.destroy');

    // Categorías (icono + colores)
    Route::get('/admin/categorias', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/admin/categorias/crear', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/admin/categorias', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/admin/categorias/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/admin/categorias/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/admin/categorias/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Temas (jerarquía principal / subtema, búsqueda tipo redes sociales)
    Route::get('/admin/temas/buscar', [TopicController::class, 'search'])->name('admin.topics.search');
    Route::resource('admin/temas', TopicController::class)
        ->except(['show'])
        ->names([
            'index' => 'admin.topics.index',
            'create' => 'admin.topics.create',
            'store' => 'admin.topics.store',
            'edit' => 'admin.topics.edit',
            'update' => 'admin.topics.update',
            'destroy' => 'admin.topics.destroy',
        ]);

    // Estados (icono + colores)
    Route::get('/admin/estados', [EstadoController::class, 'index'])->name('estados.index');
    Route::get('/admin/estados/crear', [EstadoController::class, 'create'])->name('estados.create');
    Route::post('/admin/estados', [EstadoController::class, 'store'])->name('estados.store');
    Route::get('/admin/estados/{estado}/edit', [EstadoController::class, 'edit'])->name('estados.edit');
    Route::put('/admin/estados/{estado}', [EstadoController::class, 'update'])->name('estados.update');
    Route::delete('/admin/estados/{estado}', [EstadoController::class, 'destroy'])->name('estados.destroy');

    // Traducciones (es.json / en.json)
    Route::get('/admin/traducciones', [LangController::class, 'index'])->name('lang.index');
    Route::get('/admin/traducciones/{locale}/edit', [LangController::class, 'edit'])->name('lang.edit');
    Route::put('/admin/traducciones/{locale}', [LangController::class, 'update'])->name('lang.update');

    // Creador: redes, horarios, encuestas, temas, etc.
    Route::get('/mis-temas', [UserTopicController::class, 'index'])->name('user-topics.index');
    Route::put('/mis-temas', [UserTopicController::class, 'update'])->name('user-topics.update');
    Route::get('social-accounts/profile-url-preview', [SocialAccountController::class, 'profileUrlPreview'])
        ->name('social-accounts.profile-url-preview');
    Route::get('social-accounts/verificar', VerificadorRedSocialController::class)
        ->middleware('throttle:25,1')
        ->name('social-accounts.verificar');
    Route::get('social-accounts/topics/search', [SocialAccountController::class, 'searchTopics'])
        ->name('social-accounts.topics.search');
    Route::post('social-accounts/{socialAccount}/topics', [SocialAccountController::class, 'attachTopic'])
        ->name('social-accounts.topics.attach');
    Route::delete('social-accounts/{socialAccount}/topics/{topic}', [SocialAccountController::class, 'detachTopic'])
        ->name('social-accounts.topics.detach');
    Route::patch('social-accounts/{socialAccount}/verification', [SocialAccountController::class, 'updateVerification'])
        ->name('social-accounts.verification');
    Route::resource('social-accounts', SocialAccountController::class);
    Route::resource('personal-links', PersonalLinkController::class)->except(['show']);
    Route::resource('schedules', ScheduleController::class);
    Route::resource('polls', PollController::class);
    Route::resource('poll-options', PollOptionController::class);
    Route::resource('comments', CommentController::class);
    Route::resource('live-announcements', LiveAnnouncementController::class);
    Route::resource('subscriptions', SubscriptionController::class)->only('store');
    Route::resource('messages', MessageController::class)->only('store');
});
