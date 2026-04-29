<?php

namespace Database\Seeders;

use App\Models\CreatorLevel;
use App\Services\TemplateAvatarProfileService;
use Database\Seeders\Concerns\SeedsWithProgress;
use App\Models\Schedule;
use App\Models\SocialAccount;
use App\Models\SocialAccountEvent;
use App\Models\SocialNetwork;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Crea un usuario fijo para probar la app como creador.
 * Email: creador@social-tiktok.test
 * Contraseña: password
 */
class DemoUserSeeder extends Seeder
{
    use SeedsWithProgress;

    private const EMAIL = 'digitalizacion@rosarito.gob.mx';
    private const PASSWORD = 'Panasonic';

    public function run(): void
    {
        $avatarPath = app(TemplateAvatarProfileService::class)->relativePathForLoopIndex(0);

        $user = User::firstOrCreate(
            ['email' => self::EMAIL],
            [
                'name' => 'Creador Demo',
                'password' => Hash::make(self::PASSWORD),
                'timezone' => 'America/Mexico_City',
                'role' => 'creator',
                'level_id' => CreatorLevel::where('badge', 'growth')->value('id') ?? CreatorLevel::first()?->id,
                'is_claimed' => true,
                'profile_photo_path' => $avatarPath,
                'created_at' => now()->subMonths(5),
            ]
        );
        $this->seedDot();

        $networks = SocialNetwork::whereIn('slug', ['tiktok', 'instagram', 'youtube'])->get();
        if ($networks->isEmpty()) {
            $this->command->warn('Ejecuta BaseDataSeeder antes para tener redes sociales.');
            return;
        }

        // Primero: cuenta de TikTok del creador (tu cuenta como creador)
        $tiktok = $networks->firstWhere('slug', 'tiktok');
        if ($tiktok) {
            $tiktokUsername = 'Cristiano_Consciente';
            $account = SocialAccount::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'social_network_id' => $tiktok->id,
                ],
                [
                    'display_name' => 'TikTok principal',
                    'username' => $tiktokUsername,
                    'url' => SocialNetwork::profileUrlForSlug('tiktok', $tiktokUsername),
                    'current_status' => 'active',
                    'is_verified' => true,
                    'is_primary' => true,
                    'last_checked_at' => now()->subDays(2),
                    'created_at' => now()->subMonths(4),
                ]
            );
            $this->seedDot();
            if ($account->wasRecentlyCreated) {
                SocialAccountEvent::create([
                    'social_account_id' => $account->id,
                    'type' => 'registered',
                    'meta' => json_encode(['registered_at' => $account->created_at->toIso8601String()]),
                    'created_at' => $account->created_at,
                ]);
                $this->seedDot();
            }
        }

        // Resto de redes (instagram, youtube)
        $usernames = ['creador.demo', 'CreadorDemo'];
        foreach ($networks->whereIn('slug', ['instagram', 'youtube'])->take(2) as $i => $network) {
            $username = $usernames[$i] ?? 'creador' . $i;
            $account = SocialAccount::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'social_network_id' => $network->id,
                ],
                [
                    'display_name' => ($network->name ?? 'Red').' — Creador demo',
                    'username' => $username,
                    'url' => SocialNetwork::profileUrlForSlug($network->slug, $username),
                    'current_status' => 'active',
                    'is_verified' => false,
                    'is_primary' => true,
                    'last_checked_at' => now()->subDays(2),
                    'created_at' => now()->subMonths(4)->addDays(($i + 1) * 5),
                ]
            );
            $this->seedDot();
            if ($account->wasRecentlyCreated) {
                SocialAccountEvent::create([
                    'social_account_id' => $account->id,
                    'type' => 'registered',
                    'meta' => json_encode(['registered_at' => $account->created_at->toIso8601String()]),
                    'created_at' => $account->created_at,
                ]);
                $this->seedDot();
            }
        }

        Schedule::firstOrCreate(
            ['user_id' => $user->id],
            [
                'days' => ['wed', 'sat'],
                'time' => '20:00',
                'created_at' => now()->subMonths(3),
            ]
        );
        $this->seedDot();

        $topicIds = Topic::query()->inRandomOrder()->limit(5)->pluck('id')->toArray();
        if ($topicIds !== []) {
            $user->topics()->sync($topicIds);
            $this->seedDot();
        }

        $this->command->info('Usuario demo listo: ' . self::EMAIL);
    }
}
