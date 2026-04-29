<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const PROFILE_USERNAME = 'profile.php?id=61572832117907';

    private const VANITY_USERNAME = 'rubenrp81';

    public function up(): void
    {
        $networkId = DB::table('social_networks')->where('slug', 'facebook')->value('id');
        if ($networkId === null) {
            return;
        }

        $now = now();
        $vanityUrl = 'https://www.facebook.com/'.self::VANITY_USERNAME;

        $profileRow = DB::table('social_accounts')
            ->where('user_id', 1)
            ->where('social_network_id', $networkId)
            ->where('username', self::PROFILE_USERNAME)
            ->first();

        if ($profileRow !== null) {
            DB::table('social_accounts')->where('id', $profileRow->id)->update([
                'is_primary' => false,
                'updated_at' => $now,
            ]);
        }

        $vanity = DB::table('social_accounts')
            ->where('user_id', 1)
            ->where('social_network_id', $networkId)
            ->where('username', self::VANITY_USERNAME)
            ->first();

        if ($vanity === null) {
            DB::table('social_accounts')->insert([
                'user_id' => 1,
                'social_network_id' => $networkId,
                'display_name' => 'Rubén Ruíz',
                'username' => self::VANITY_USERNAME,
                'url' => $vanityUrl,
                'current_status' => 'active',
                'is_verified' => false,
                'is_primary' => true,
                'last_checked_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        } else {
            DB::table('social_accounts')->where('id', $vanity->id)->update([
                'display_name' => 'Rubén Ruíz',
                'url' => $vanityUrl,
                'is_primary' => true,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        $networkId = DB::table('social_networks')->where('slug', 'facebook')->value('id');
        if ($networkId === null) {
            return;
        }

        DB::table('social_accounts')
            ->where('user_id', 1)
            ->where('social_network_id', $networkId)
            ->where('username', self::VANITY_USERNAME)
            ->delete();

        DB::table('social_accounts')
            ->where('user_id', 1)
            ->where('social_network_id', $networkId)
            ->where('username', self::PROFILE_USERNAME)
            ->update([
                'is_primary' => true,
                'updated_at' => now(),
            ]);
    }
};
