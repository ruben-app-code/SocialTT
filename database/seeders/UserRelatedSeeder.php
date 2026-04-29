<?php

namespace Database\Seeders;

use App\Models\Comment;
use Database\Seeders\Concerns\SeedsWithProgress;
use App\Models\Follow;
use App\Models\FollowerSnapshot;
use App\Models\LiveAnnouncement;
use App\Models\Message;
use App\Models\Poll;
use App\Models\PollOption;
use App\Models\PollVote;
use App\Models\Schedule;
use App\Models\SocialAccount;
use App\Models\SocialAccountEvent;
use App\Models\SocialNetwork;
use App\Models\Subscription;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserRelatedSeeder extends Seeder
{
    use SeedsWithProgress;

    private const COUNTS = [
        'schedules_per_creator' => [1, 2],
        'polls_total' => 60,
        'snapshots' => 80,
        'live_announcements' => 40,
        'comments' => 70,
        'subscriptions' => 50,
        'messages' => 45,
        'follows' => 120,
    ];

    public function run(): void
    {
        $networks = SocialNetwork::all();

        if ($networks->isEmpty()) {
            $this->command->warn('Ejecuta BaseDataSeeder antes.');
            return;
        }

        $creators = User::where('role', 'creator')->get();
        $allUsers = User::all();
        $allUserIds = $allUsers->pluck('id')->toArray();
        $creatorIds = $creators->pluck('id')->toArray();

        if (empty($creatorIds)) {
            $this->command->warn('No hay usuarios con rol creator. Ejecuta UsersSeeder antes.');
            return;
        }

        $tiktok = $networks->firstWhere('slug', 'tiktok');
        $otrasRedes = $networks->filter(fn ($n) => $n->slug !== 'tiktok')->values();

        // Redes para TODOS los usuarios fake: cada uno con al menos TikTok
        $this->command->info('Creando cuentas de redes por usuario (cada uno con al menos TikTok)...');
        $socialAccountIds = [];
        foreach ($allUsers as $user) {
            $baseUsername = Str::slug($user->name, '');
            if (strlen($baseUsername) < 3) {
                $baseUsername = 'user' . $user->id;
            }
            $createdBase = $user->created_at ?? now()->subMonths(6);

            if ($tiktok) {
                $usernameTk = $baseUsername . '_tk';
                $accountCreated = $createdBase->copy()->addDays(fake()->numberBetween(0, 30));
                $account = SocialAccount::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'social_network_id' => $tiktok->id,
                    ],
                    [
                        'display_name' => 'TikTok · '.Str::limit($user->name, 120),
                        'username' => $usernameTk,
                        'url' => SocialNetwork::profileUrlForSlug('tiktok', $usernameTk),
                        'current_status' => 'active',
                        'is_verified' => fake()->boolean(20),
                        'is_primary' => true,
                        'last_checked_at' => now()->subDays(fake()->numberBetween(1, 14)),
                        'created_at' => $accountCreated,
                        'updated_at' => $accountCreated->copy()->addDays(fake()->numberBetween(0, 20)),
                    ]
                );
                $socialAccountIds[] = $account->id;
                $this->seedDot();
                if ($account->wasRecentlyCreated) {
                    SocialAccountEvent::create([
                        'social_account_id' => $account->id,
                        'type' => 'registered',
                        'meta' => null,
                        'created_at' => $account->created_at,
                    ]);
                    $this->seedDot();
                }
            }

            // Creadores: además de TikTok, de 0 a 3 redes más
            if (in_array($user->id, $creatorIds, true) && $otrasRedes->count() > 0) {
                $extra = fake()->numberBetween(0, min(3, $otrasRedes->count()));
                if ($extra > 0) {
                    $chosen = $otrasRedes->random($extra);
                    foreach ($chosen as $network) {
                        $username = $baseUsername . '_' . $network->slug;
                        $accountCreated = $createdBase->copy()->addDays(fake()->numberBetween(5, 60));
                        $account = SocialAccount::firstOrCreate(
                            [
                                'user_id' => $user->id,
                                'social_network_id' => $network->id,
                            ],
                            [
                                'display_name' => ($network->name ?? 'Red').' · '.Str::limit($user->name, 100),
                                'username' => $username,
                                'url' => SocialNetwork::profileUrlForSlug($network->slug, $username),
                                'current_status' => fake()->randomElement(['active', 'active', 'active', 'deleted']),
                                'is_verified' => fake()->boolean(25),
                                'is_primary' => true,
                                'last_checked_at' => now()->subDays(fake()->numberBetween(1, 14)),
                                'created_at' => $accountCreated,
                                'updated_at' => $accountCreated->copy()->addDays(fake()->numberBetween(0, 20)),
                            ]
                        );
                        $socialAccountIds[] = $account->id;
                        $this->seedDot();
                        if ($account->wasRecentlyCreated) {
                            SocialAccountEvent::create([
                                'social_account_id' => $account->id,
                                'type' => 'registered',
                                'meta' => null,
                                'created_at' => $account->created_at,
                            ]);
                            $this->seedDot();
                        }
                    }
                }
            }
        }

        // Horarios de publicación: al menos 1 por creador (y hasta 2)
        $this->command->info('Creando horarios (al menos 1 por creador)...');
        $daysOptions = [['mon', 'wed', 'fri'], ['tue', 'thu'], ['sat', 'sun'], ['mon', 'tue', 'wed', 'thu', 'fri'], ['sat']];
        foreach ($creators as $creator) {
            $num = fake()->numberBetween(1, 2);
            $baseCreated = $creator->created_at?->copy()->addDays(10) ?? now()->subMonths(2);
            for ($i = 0; $i < $num; $i++) {
                Schedule::create([
                    'user_id' => $creator->id,
                    'days' => fake()->randomElement($daysOptions),
                    'time' => fake()->randomElement(['09:00', '12:00', '18:00', '20:00', '21:00']),
                    'created_at' => $baseCreated->copy()->addDays($i * 7),
                ]);
                $this->seedDot();
            }
        }

        $this->seedPollsForCreators($creators, $creatorIds);

        // Snapshots de seguidores (histórico)
        $this->command->info('Creando snapshots de seguidores...');
        $accountIds = SocialAccount::pluck('id')->toArray();
        for ($n = 0; $n < self::COUNTS['snapshots']; $n++) {
            $accountId = fake()->randomElement($accountIds);
            $account = SocialAccount::find($accountId);
            if (!$account) {
                continue;
            }
            FollowerSnapshot::create([
                'user_id' => $account->user_id,
                'social_account_id' => $accountId,
                'followers_count' => fake()->numberBetween(500, 350000),
                'source' => fake()->randomElement(['manual', 'auto_prompt']),
                'recorded_at' => now()->subDays(fake()->numberBetween(0, 90)),
            ]);
            $this->seedDot();
        }

        // Temas: ya vienen de UsersSeeder/DemoUserSeeder; solo asegurar creadores sin ningún tema
        $topicPool = Topic::query()->whereNotNull('parent_id')->get();
        if ($topicPool->isEmpty()) {
            $topicPool = Topic::query()->whereNull('parent_id')->get();
        }
        if ($topicPool->isNotEmpty()) {
            foreach ($creators as $creator) {
                if ($creator->topics()->exists()) {
                    continue;
                }
                $topicPool->random()->users()->syncWithoutDetaching([$creator->id]);
                $this->seedDot();
            }
        }

        // Anuncios de directos: al menos 1 por creador + extras
        $this->command->info('Creando anuncios de directos (al menos 1 por creador)...');
        $titles = ['Directo sorpresa', 'Q&A en vivo', 'Colaboración especial', 'Nuevo proyecto', 'Tutorial en vivo'];
        foreach ($creators as $idx => $creator) {
            LiveAnnouncement::create([
                'user_id' => $creator->id,
                'title' => fake()->randomElement($titles),
                'scheduled_at' => now()->addDays(fake()->numberBetween(1, 60))->setTime(fake()->numberBetween(18, 22), 0),
                'description' => fake()->optional(0.7)->sentence(8),
                'created_at' => now()->subDays(fake()->numberBetween(0, 10)),
            ]);
            $this->seedDot();
        }
        $extrasLive = max(0, self::COUNTS['live_announcements'] - $creators->count());
        for ($n = 0; $n < $extrasLive; $n++) {
            LiveAnnouncement::create([
                'user_id' => fake()->randomElement($creatorIds),
                'title' => fake()->randomElement($titles) . ' – ' . now()->addDays($n)->format('d/m'),
                'scheduled_at' => now()->addDays(fake()->numberBetween(1, 60))->setTime(fake()->numberBetween(18, 22), 0),
                'description' => fake()->optional(0.7)->sentence(8),
                'created_at' => now()->subDays(fake()->numberBetween(0, 10)),
            ]);
            $this->seedDot();
        }

        // Comentarios (seguidores → creadores)
        $this->command->info('Creando comentarios...');
        $phrases = ['¡Muy bueno!', 'Cuándo el próximo?', 'Gracias por el contenido', 'Increíble directo', '¿Habrá parte 2?'];
        for ($n = 0; $n < self::COUNTS['comments']; $n++) {
            $commenter = fake()->randomElement($allUserIds);
            $creator = fake()->randomElement($creatorIds);
            if ($commenter === $creator) {
                $commenter = fake()->randomElement(array_diff($allUserIds, [$creator]));
            }
            if (!$commenter) {
                continue;
            }
            Comment::create([
                'user_id' => $commenter,
                'creator_id' => $creator,
                'content' => fake()->randomElement($phrases) . (fake()->boolean(30) ? ' ' . fake()->sentence() : ''),
                'created_at' => now()->subDays(fake()->numberBetween(0, 30)),
            ]);
            $this->seedDot();
        }

        // Suscripciones
        $this->command->info('Creando suscripciones...');
        for ($n = 0; $n < self::COUNTS['subscriptions']; $n++) {
            Subscription::create([
                'user_id' => fake()->randomElement($allUserIds),
                'type' => fake()->randomElement(['free', 'free', 'pro', 'lifetime']),
                'ads_enabled' => fake()->boolean(70),
                'expires_at' => fake()->boolean(50) ? now()->addMonths(fake()->numberBetween(1, 12)) : null,
                'created_at' => now()->subMonths(fake()->numberBetween(0, 6)),
            ]);
            $this->seedDot();
        }

        // Mensajes entre usuarios
        $this->command->info('Creando mensajes...');
        for ($n = 0; $n < self::COUNTS['messages']; $n++) {
            $from = fake()->randomElement($allUserIds);
            $to = fake()->randomElement(array_diff($allUserIds, [$from]));
            if (!$to) {
                continue;
            }
            Message::create([
                'from_user_id' => $from,
                'to_user_id' => $to,
                'content' => fake()->sentence(fake()->numberBetween(5, 20)),
                'channel' => 'whatsapp',
                'status' => fake()->randomElement(['pending', 'sent', 'sent', 'sent']),
                'created_at' => now()->subDays(fake()->numberBetween(0, 45)),
            ]);
            $this->seedDot();
        }

        // Seguidores / sigue a: cada usuario sigue al menos a 1 creador; cada creador tiene al menos 1 seguidor
        $this->command->info('Creando relaciones de seguimiento (sigue a / seguidores)...');
        $created = 0;

        // Cada usuario sigue al menos a un creador (que no sea él mismo si es creator)
        foreach ($allUsers as $user) {
            $candidatos = array_values(array_diff($creatorIds, [$user->id]));
            if (empty($candidatos)) {
                continue;
            }
            $creatorId = fake()->randomElement($candidatos);
            $follow = Follow::firstOrCreate(
                ['follower_id' => $user->id, 'creator_id' => $creatorId],
                ['created_at' => now()->subDays(fake()->numberBetween(0, 90))]
            );
            $this->seedDot();
            if ($follow->wasRecentlyCreated) {
                $created++;
            }
        }

        // Cada creador tiene al menos un seguidor
        foreach ($creators as $creator) {
            $candidatos = array_values(array_diff($allUserIds, [$creator->id]));
            if (empty($candidatos)) {
                continue;
            }
            $followerId = fake()->randomElement($candidatos);
            $follow = Follow::firstOrCreate(
                ['follower_id' => $followerId, 'creator_id' => $creator->id],
                ['created_at' => now()->subDays(fake()->numberBetween(0, 90))]
            );
            $this->seedDot();
            if ($follow->wasRecentlyCreated) {
                $created++;
            }
        }

        // Más relaciones aleatorias hasta completar el total
        for ($n = 0; $n < self::COUNTS['follows']; $n++) {
            $followerId = fake()->randomElement($allUserIds);
            $creatorId = fake()->randomElement($creatorIds);
            if ($followerId === $creatorId) {
                continue;
            }
            $follow = Follow::firstOrCreate(
                [
                    'follower_id' => $followerId,
                    'creator_id' => $creatorId,
                ],
                ['created_at' => now()->subDays(fake()->numberBetween(0, 90))]
            );
            $this->seedDot();
            if ($follow->wasRecentlyCreated) {
                $created++;
            }
        }
        $this->command->info("Creadas {$created} relaciones de seguimiento.");

        $this->command->info('Datos listos: cada usuario con al menos TikTok; cada creador con horarios, encuestas con votos de prueba y 1 live; sigue a / seguidores.');
    }

    /**
     * Encuestas con textos variados (sí/no y múltiple opción) + votos simulados (usuarios e invitados).
     */
    private function seedPollsForCreators($creators, array $creatorIds): void
    {
        $this->command->info('Creando encuestas (catálogo en español + votos de demostración)...');

        $catalog = [
            ['type' => 'yes_no', 'question' => '¿Te gustaría más contenido en vivo esta semana?', 'options' => null],
            ['type' => 'multiple', 'question' => '¿Qué horario te viene mejor para ver directos?', 'options' => ['Mañana (8–12 h)', 'Tarde (12–18 h)', 'Noche (18–24 h)', 'Fin de semana']],
            ['type' => 'yes_no', 'question' => '¿Has compartido algún video de este canal?', 'options' => null],
            ['type' => 'multiple', 'question' => '¿Qué tema te interesa para el próximo tutorial?', 'options' => ['Básicos', 'Intermedio', 'Avanzado', 'Solo tips rápidos']],
            ['type' => 'multiple', 'question' => '¿Qué formato prefieres?', 'options' => ['Videos cortos', 'Largos + profundidad', 'Lives Q&A', 'Series por capítulos']],
            ['type' => 'yes_no', 'question' => '¿Quieres que haya más colaboraciones con otros creadores?', 'options' => null],
            ['type' => 'multiple', 'question' => '¿Cuál es tu red favorita para seguirme?', 'options' => ['TikTok', 'Instagram', 'YouTube', 'Otra']],
            ['type' => 'multiple', 'question' => '¿Qué te motiva más a comentar?', 'options' => ['Preguntas al final del video', 'Encuestas', 'Sorteos', 'Debates']],
            ['type' => 'yes_no', 'question' => '¿Te interesaría un grupo exclusivo para la comunidad?', 'options' => null],
            ['type' => 'multiple', 'question' => '¿Con qué frecuencia ves el contenido?', 'options' => ['Todos los días', 'Varias veces por semana', 'Ocasional', 'Solo cuando me avisan']],
            ['type' => 'multiple', 'question' => '¿Qué mejora te gustaría ver primero?', 'options' => ['Mejor audio', 'Mejor iluminación', 'Más edición', 'Más espontaneidad']],
            ['type' => 'yes_no', 'question' => '¿El último directo cumplió tus expectativas?', 'options' => null],
        ];

        $userIds = User::query()->pluck('id')->all();

        foreach ($creators as $i => $creator) {
            $entry = $catalog[$i % count($catalog)];
            $expiresAt = fake()->boolean(75)
                ? now()->addDays(fake()->numberBetween(5, 45))
                : null;
            $isActive = $expiresAt !== null ? true : fake()->boolean(85);
            $poll = Poll::create([
                'user_id' => $creator->id,
                'question' => $entry['question'],
                'type' => $entry['type'],
                'is_active' => $isActive,
                'expires_at' => $expiresAt,
                'created_at' => now()->subDays(fake()->numberBetween(2, 60)),
            ]);
            $this->seedDot();
            $labels = $entry['type'] === 'yes_no'
                ? [__('Sí'), __('No')]
                : $entry['options'];
            $take = $entry['type'] === 'yes_no' ? 2 : min(count($labels), fake()->numberBetween(3, 4));
            foreach (array_slice($labels, 0, $take) as $text) {
                PollOption::create(['poll_id' => $poll->id, 'text' => $text]);
                $this->seedDot();
            }
        }

        $extras = max(0, self::COUNTS['polls_total'] - $creators->count());
        for ($n = 0; $n < $extras; $n++) {
            $entry = fake()->randomElement($catalog);
            $expiresAt = fake()->boolean(70)
                ? now()->addDays(fake()->numberBetween(3, 40))
                : null;
            $poll = Poll::create([
                'user_id' => fake()->randomElement($creatorIds),
                'question' => $entry['question'].' (#'.($n + 1).')',
                'type' => $entry['type'],
                'is_active' => $expiresAt !== null ? true : fake()->boolean(80),
                'expires_at' => $expiresAt,
                'created_at' => now()->subDays(fake()->numberBetween(1, 90)),
            ]);
            $this->seedDot();
            $labels = $entry['type'] === 'yes_no'
                ? [__('Sí'), __('No')]
                : $entry['options'];
            $take = $entry['type'] === 'yes_no' ? 2 : min(count($labels), fake()->numberBetween(2, 4));
            foreach (array_slice($labels, 0, $take) as $text) {
                PollOption::create(['poll_id' => $poll->id, 'text' => $text]);
                $this->seedDot();
            }
        }

        // Votos: usuarios registrados (voter_key user:id) + algunos invitados (guest:uuid)
        $allPolls = Poll::query()->with('pollOptions')->get();
        foreach ($allPolls as $poll) {
            if ($poll->pollOptions->isEmpty()) {
                continue;
            }
            $nUserVotes = min(count($userIds), fake()->numberBetween(8, 45));
            foreach (collect($userIds)->shuffle()->take($nUserVotes) as $uid) {
                PollVote::query()->updateOrCreate(
                    [
                        'poll_id' => $poll->id,
                        'voter_key' => 'user:'.$uid,
                    ],
                    [
                        'option_id' => $poll->pollOptions->random()->id,
                        'user_id' => $uid,
                    ]
                );
                $this->seedDot();
            }
            $nGuests = fake()->numberBetween(5, 35);
            for ($g = 0; $g < $nGuests; $g++) {
                PollVote::query()->create([
                    'poll_id' => $poll->id,
                    'voter_key' => 'guest:'.Str::uuid()->toString(),
                    'user_id' => null,
                    'option_id' => $poll->pollOptions->random()->id,
                ]);
                $this->seedDot();
            }
        }

        $this->command->info('Encuestas y votos de demostración listos.');
    }
}
