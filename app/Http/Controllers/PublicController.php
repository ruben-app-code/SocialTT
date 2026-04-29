<?php

namespace App\Http\Controllers;

use App\Models\SocialAccount;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicController extends Controller
{

    public function creadores(Request $request): View
    {
        $query = User::query()
            ->where('role', 'creator')
            ->with([
                'socialAccounts.socialNetwork',
                'topics' => fn ($q) => $q->with('parent:id,name,slug')->orderBy('name'),
            ])
            ->withCount('socialAccounts');

        $q = $request->filled('q') ? trim($request->get('q')) : null;
        if ($q !== null && $q !== '') {
            $query->where(function ($qb) use ($q) {
                $qb->where('name', 'like', '%' . $q . '%')
                    ->orWhereHas('socialAccounts', function ($sq) use ($q) {
                        $sq->where('username', 'like', '%' . $q . '%');
                    });
            });
        }

        $topicSlug = $request->filled('topic') ? trim($request->get('topic')) : null;
        if ($topicSlug !== null && $topicSlug !== '') {
            $topic = Topic::where('slug', $topicSlug)->first();
            if ($topic) {
                $ids = collect([$topic->id]);
                if ($topic->isRoot()) {
                    $ids = $ids->merge($topic->children()->pluck('id'));
                }
                $query->whereHas('topics', fn ($t) => $t->whereIn('topics.id', $ids->all()));
            }
        }

        $creators = $query->orderBy('name')->paginate(32)->withQueryString();

        $selectedTopic = null;
        if ($topicSlug !== null && $topicSlug !== '') {
            $selectedTopic = Topic::query()
                ->with(['parent:id,name,slug'])
                ->where('slug', $topicSlug)
                ->first();
        }

        $topics = Topic::query()
            ->with([
                'children' => fn ($q) => $q->orderBy('name')->withCount('users'),
            ])
            ->withCount('users')
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();



        return view('front.creators', [
            'creators' => $creators,
            'topics' => $topics,
            'selectedTopic' => $selectedTopic,
            'currentTopic' => $topicSlug,
            'searchQuery' => $q,
        ]);
    }


    /**
     * Listado público de creadores (explore) con buscador y filtro por tema.
     */
    public function explore(Request $request): View
    {
        $query = User::query()
            ->where('role', 'creator')
            ->with([
                'socialAccounts.socialNetwork',
                'topics' => fn ($q) => $q->with('parent:id,name,slug')->orderBy('name'),
            ])
            ->withCount('socialAccounts');

        $q = $request->filled('q') ? trim($request->get('q')) : null;
        if ($q !== null && $q !== '') {
            $query->where(function ($qb) use ($q) {
                $qb->where('name', 'like', '%' . $q . '%')
                    ->orWhereHas('socialAccounts', function ($sq) use ($q) {
                        $sq->where('username', 'like', '%' . $q . '%');
                    });
            });
        }

        $topicSlug = $request->filled('topic') ? trim($request->get('topic')) : null;
        if ($topicSlug !== null && $topicSlug !== '') {
            $topic = Topic::where('slug', $topicSlug)->first();
            if ($topic) {
                $ids = collect([$topic->id]);
                if ($topic->isRoot()) {
                    $ids = $ids->merge($topic->children()->pluck('id'));
                }
                $query->whereHas('topics', fn ($t) => $t->whereIn('topics.id', $ids->all()));
            }
        }

        $creators = $query->orderBy('name')->paginate(32)->withQueryString();

        $selectedTopic = null;
        if ($topicSlug !== null && $topicSlug !== '') {
            $selectedTopic = Topic::query()
                ->with(['parent:id,name,slug'])
                ->where('slug', $topicSlug)
                ->first();
        }

        return view('public.explore', [
            'creators' => $creators,
            'selectedTopic' => $selectedTopic,
            'currentTopic' => $topicSlug,
            'searchQuery' => $q,
        ]);
    }

    /**
     * Autocompletado de temas para la página pública /explore (sin autenticación).
     */
    public function searchTopics(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));
        if (mb_strlen($q) < 3) {
            return response()->json(['topics' => []]);
        }

        $like = '%'.str_replace(['%', '_'], ['\\%', '\\_'], $q).'%';
        $topics = Topic::query()
            ->with('parent:id,name,slug')
            ->where('name', 'like', $like)
            ->orderBy('name')
            ->limit(30)
            ->get()
            ->map(fn (Topic $t) => [
                'id' => $t->id,
                'slug' => $t->slug,
                'name' => $t->display_name,
            ]);

        return response()->json(['topics' => $topics]);
    }

    /**
     * Listado público de temas (para explorar por categoría).
     */
    public function topics(Request $request): View
    {
        $topics = Topic::query()
            ->with([
                'children' => fn ($q) => $q->orderBy('name')->withCount('users'),
            ])
            ->withCount('users')
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        return view('public.topics', ['topics' => $topics]);
    }

    /**
     * Perfil público por username (TikTok): /@username o /u/username.
     */
    public function creatorProfileByUsername(Request $request, string $username): View|RedirectResponse
    {
        $account = SocialAccount::query()
            ->whereHas('socialNetwork', fn ($q) => $q->where('slug', 'tiktok'))
            ->where('username', $username)
            ->with(['user.socialAccounts.socialNetwork', 'user.level'])
            ->firstOrFail();

        $user = $account->user;
        if ($user->role !== 'creator') {
            abort(404);
        }

        $this->loadCreatorProfileRelations($user);
        $upcomingLives = $user->liveAnnouncements->where('scheduled_at', '>=', now())->values();
        $pastLives = $user->liveAnnouncements->where('scheduled_at', '<', now())->take(10)->values();

        return view('public.creator-profile', [
            'creator' => $user,
            'upcomingLives' => $upcomingLives,
            'pastLives' => $pastLives,
        ]);
    }

    /**
     * Redirige /creadores/{id} a la URL canónica (@ o /u/) si tiene TikTok; si no, muestra el perfil.
     */
    public function creatorProfileRedirect(Request $request, User $user): View|RedirectResponse
    {
        if ($user->role !== 'creator') {
            abort(404);
        }

        $this->loadCreatorProfileRelations($user);
        $canonical = $user->creator_profile_url;
        if (str_contains($canonical, '/creadores/')) {
            $upcomingLives = $user->liveAnnouncements->where('scheduled_at', '>=', now())->values();
            $pastLives = $user->liveAnnouncements->where('scheduled_at', '<', now())->take(10)->values();
            return view('public.creator-profile', [
                'creator' => $user,
                'upcomingLives' => $upcomingLives,
                'pastLives' => $pastLives,
            ]);
        }

        return redirect()->to($canonical, 301);
    }

    /**
     * Relaciones del perfil público. Las encuestas no usan limit() en eager load:
     * Laravel envolvería la consulta con row_number(), que MySQL rechaza (sql_mode ONLY_FULL_GROUP_BY).
     */
    private function loadCreatorProfileRelations(User $user): void
    {
        $user->load([
            'socialAccounts' => fn ($q) => $q->with(['socialNetwork', 'block']),
            'level',
            'schedules',
            'topics.parent',
            'personalLinks',
            'liveAnnouncements' => fn ($q) => $q->orderBy('scheduled_at', 'desc'),
            'followingEntries' => fn ($q) => $q->with(['platformUser', 'socialNetwork', 'latestFollowerSnapshot']),
        ]);

        $polls = $user->polls()
            ->with(['pollOptions' => fn ($q) => $q->orderBy('id')->withCount('votes')])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();
        $user->setRelation('polls', $polls);

        $user->loadCount(['followers', 'following']);
    }
}
