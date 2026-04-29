<?php

namespace Database\Seeders;

use App\Models\SocialAccount;
use App\Models\SocialNetwork;
use App\Models\User;
use Database\Seeders\Concerns\SeedsWithProgress;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    use SeedsWithProgress;

    private const ADMIN_PROFILE_PHOTO = 'template/assets/images/avatar/ruben.jpg';

    public function run(): void
    {
        $guard = config('auth.defaults.guard');
        $superAdmin = Role::findByName('SuperAdmin', $guard);
        if (! $superAdmin) {
            return;
        }

        if (! User::query()->whereKey(1)->exists()) {
            DB::table('users')->insert([
                'id'                 => 1,
                'name'               => 'Rubén Ruíz',
                'email'              => 'dev.ruben.ruiz@gmail.com',
                'email_verified_at'  => now(),
                'password'           => Hash::make('Panasonic'),
                'remember_token'     => null,
                'current_team_id'    => null,
                'profile_photo_path' => self::ADMIN_PROFILE_PHOTO,
                'active'             => true,
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);
            $this->seedDot();
        } else {
            $u = User::query()->find(1);
            if ($u) {
                $u->name = 'Rubén Ruíz';
                $u->profile_photo_path = self::ADMIN_PROFILE_PHOTO;
                if (! User::query()
                    ->where('email', 'dev.ruben.ruiz@gmail.com')
                    ->where('id', '!=', 1)
                    ->exists())
                {
                    $u->email = 'dev.ruben.ruiz@gmail.com';
                }
                $u->save();
                $this->seedDot();
            }
        }

        User::find(1)?->syncRoles([$superAdmin]);
        $this->seedDot();

        $this->seedSocialAccountsForUser1();
    }

    /**
     * Redes del usuario 1 (requiere BaseDataSeeder antes para social_networks).
     *
     * TikTok y Facebook: varias cuentas → la clave incluye username. Resto: una cuenta por red →
     * clave user_id + social_network_id para que al cambiar username no queden filas duplicadas.
     */
    private function seedSocialAccountsForUser1(): void
    {
        $userId = 1;
        $multiAccountSlugs = ['tiktok', 'facebook'];
        $definitions = [
            ['display_name' => 'Cristiano Consciente', 'username' => 'Cristiano_Consciente', 'slug' => 'tiktok', 'is_primary' => true, 'is_verified' => true],
            ['display_name' => 'Ruben Web Developer', 'username' => 'rubenrp81', 'slug' => 'tiktok', 'is_primary' => false, 'is_verified' => false],
            ['display_name' => 'Ruben Ruíz', 'username' => 'profile.php?id=61572832117907', 'slug' => 'facebook', 'is_primary' => false, 'is_verified' => false],
            ['display_name' => 'Rubén Ruíz', 'username' => 'rubenrp81', 'slug' => 'facebook', 'is_primary' => true, 'is_verified' => false],
            ['display_name' => 'Rubén Ruíz', 'username' => 'rubenrp81', 'slug' => 'instagram', 'is_primary' => true, 'is_verified' => false],
            ['display_name' => 'Rubén Ruíz', 'username' => 'rubenrp81', 'slug' => 'x', 'is_primary' => true, 'is_verified' => false],
        ];

        foreach ($definitions as $row) {
            $network = SocialNetwork::query()->where('slug', $row['slug'])->first();
            if (! $network) {
                $this->command?->warn('Red social «'.$row['slug'].'» no encontrada; ejecuta BaseDataSeeder antes de AdminUserSeeder.');

                continue;
            }

            $match = in_array($row['slug'], $multiAccountSlugs, true)
                ? [
                    'user_id' => $userId,
                    'social_network_id' => $network->id,
                    'username' => $row['username'],
                ]
                : [
                    'user_id' => $userId,
                    'social_network_id' => $network->id,
                ];

            $existing = SocialAccount::query()->where($match)->first();
            $isVerified = $existing?->is_verified ?? $row['is_verified'];

            SocialAccount::query()->updateOrCreate(
                $match,
                [
                    'display_name' => $row['display_name'],
                    'username' => $row['username'],
                    'url' => SocialNetwork::profileUrlForSlug($row['slug'], $row['username']),
                    'current_status' => 'active',
                    'is_verified' => $isVerified,
                    'is_primary' => $row['is_primary'],
                    'last_checked_at' => now(),
                ]
            );
            $this->seedDot();
        }

        $xNetwork = SocialNetwork::query()->where('slug', 'x')->first();
        $twitterNetwork = SocialNetwork::query()->where('slug', 'twitter')->first();
        if ($xNetwork && $twitterNetwork
            && SocialAccount::query()->where('user_id', $userId)->where('social_network_id', $xNetwork->id)->exists()) {
            SocialAccount::query()
                ->where('user_id', $userId)
                ->where('social_network_id', $twitterNetwork->id)
                ->delete();
            $this->seedDot();
        }
    }
}
