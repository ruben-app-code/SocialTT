<?php

namespace Database\Seeders;

use App\Models\CreatorLevel;
use App\Models\Topic;
use App\Models\User;
use App\Services\TemplateAvatarProfileService;
use Database\Seeders\Concerns\SeedsWithProgress;
use Illuminate\Database\Seeder;

/**
 * Añade 30 usuarios creadores y los asocia a un tema por ID (variable de entorno).
 *
 * Uso (PowerShell):  $env:SEED_TOPIC_ID=51; php artisan db:seed --class=UsersForTopicSeeder
 * Uso (bash):        SEED_TOPIC_ID=51 php artisan db:seed --class=UsersForTopicSeeder
 */
class UsersForTopicSeeder extends Seeder
{
    use SeedsWithProgress;

    private const COUNT = 30;

    public function run(): void
    {
        $topicId = (int) env('SEED_TOPIC_ID', 0);
        if ($topicId < 1) {
            $this->command->error('Define SEED_TOPIC_ID (id del tema en topics). Ejemplo: SEED_TOPIC_ID=51 php artisan db:seed --class=UsersForTopicSeeder');

            return;
        }

        $topic = Topic::query()->find($topicId);
        if (! $topic) {
            $this->command->error("No existe un tema con id {$topicId}.");

            return;
        }

        $avatars = app(TemplateAvatarProfileService::class);
        $levels = CreatorLevel::pluck('id')->toArray();
        $timezones = ['America/Mexico_City', 'America/Bogota', 'Europe/Madrid', 'America/Argentina/Buenos_Aires', 'America/Lima', 'America/Santiago', 'UTC'];

        for ($i = 0; $i < self::COUNT; $i++) {
            $createdAt = now()->subMonths(14)->addDays(fake()->numberBetween(0, 420));

            $user = User::factory()->create([
                'profile_photo_path' => $avatars->relativePathForLoopIndex($i),
                'level_id' => $levels ? fake()->randomElement($levels) : null,
                'role' => 'creator',
                'timezone' => fake()->randomElement($timezones),
                'is_claimed' => fake()->boolean(35),
                'created_at' => $createdAt,
                'updated_at' => $createdAt->copy()->addDays(fake()->numberBetween(0, 30)),
            ]);
            $user->topics()->sync([$topic->id]);
            $this->seedDot();
        }

        $this->command->info('Añadidos '.self::COUNT." creadores al tema «{$topic->name}» (id {$topic->id}).");
    }
}
