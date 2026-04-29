<?php

namespace Database\Seeders;

use App\Models\CreatorLevel;
use App\Models\Topic;
use App\Models\User;
use App\Services\TemplateAvatarProfileService;
use Database\Seeders\Concerns\SeedsWithProgress;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    use SeedsWithProgress;

    private const TOTAL_USERS = 30;

    private const USERS_PER_TOPIC = 3;

    private const TOPICS_COUNT = 10;

    private const RELIGIONES_SLUG = 'religiones';

    public function run(): void
    {
        $avatars = app(TemplateAvatarProfileService::class);
        $levels = CreatorLevel::pluck('id')->toArray();
        $timezones = ['America/Mexico_City', 'America/Bogota', 'Europe/Madrid', 'America/Argentina/Buenos_Aires', 'America/Lima', 'America/Santiago', 'UTC'];

        $religiones = Topic::query()->where('slug', self::RELIGIONES_SLUG)->first();
        $childTopics = $religiones
            ? Topic::query()->where('parent_id', $religiones->id)->inRandomOrder()->limit(self::TOPICS_COUNT)->get()
            : collect();

        if ($childTopics->count() < self::TOPICS_COUNT) {
            $this->command->warn(
                'Se esperaban al menos '.self::TOPICS_COUNT.' subtemas bajo «Religiones» (slug '.self::RELIGIONES_SLUG.'). Encontrados: '.$childTopics->count().'. Ejecuta BaseDataSeeder primero.'
            );
        }

        if ($childTopics->isEmpty()) {
            $this->seedUsersWithoutTopics($avatars, $levels, $timezones);

            return;
        }

        $userIndex = 0;
        foreach ($childTopics as $topic) {
            for ($j = 0; $j < self::USERS_PER_TOPIC; $j++) {
                $createdAt = now()->subMonths(14)->addDays(fake()->numberBetween(0, 420));

                $user = User::factory()->create([
                    'profile_photo_path' => $avatars->relativePathForLoopIndex($userIndex),
                    'level_id' => $levels ? fake()->randomElement($levels) : null,
                    'role' => 'creator',
                    'timezone' => fake()->randomElement($timezones),
                    'is_claimed' => fake()->boolean(35),
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt->copy()->addDays(fake()->numberBetween(0, 30)),
                ]);
                $user->topics()->sync([$topic->id]);
                $this->seedDot();
                $userIndex++;
            }
        }

        $this->command->info(
            'Creados '.$userIndex.' usuarios creadores: '.self::USERS_PER_TOPIC.' por cada uno de '.$childTopics->count().' subtemas de Religiones.'
        );
    }

    private function seedUsersWithoutTopics(
        TemplateAvatarProfileService $avatars,
        array $levels,
        array $timezones
    ): void {
        for ($i = 0; $i < self::TOTAL_USERS; $i++) {
            $createdAt = now()->subMonths(14)->addDays(fake()->numberBetween(0, 420));

            User::factory()->create([
                'profile_photo_path' => $avatars->relativePathForLoopIndex($i),
                'level_id' => $levels ? fake()->randomElement($levels) : null,
                'role' => 'creator',
                'timezone' => fake()->randomElement($timezones),
                'is_claimed' => fake()->boolean(35),
                'created_at' => $createdAt,
                'updated_at' => $createdAt->copy()->addDays(fake()->numberBetween(0, 30)),
            ]);
            $this->seedDot();
        }

        $this->command->info('Creados '.self::TOTAL_USERS.' usuarios (sin temas: falta categoría Religiones).');
    }
}
