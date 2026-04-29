<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const FB_USERNAME = 'profile.php?id=61572832117907';

    private const FB_URL = 'https://www.facebook.com/profile.php?id=61572832117907';

    public function up(): void
    {
        $networkId = DB::table('social_networks')->where('slug', 'facebook')->value('id');
        if ($networkId === null) {
            return;
        }

        $row = DB::table('social_accounts')
            ->where('user_id', 1)
            ->where('social_network_id', $networkId)
            ->orderBy('id')
            ->first();

        if ($row === null) {
            DB::table('social_accounts')->insert([
                'user_id' => 1,
                'social_network_id' => $networkId,
                'display_name' => 'Ruben Ruíz',
                'username' => self::FB_USERNAME,
                'url' => self::FB_URL,
                'current_status' => 'active',
                'is_verified' => false,
                'is_primary' => true,
                'last_checked_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return;
        }

        DB::table('social_accounts')->where('id', $row->id)->update([
            'username' => self::FB_USERNAME,
            'url' => self::FB_URL,
            'updated_at' => now(),
        ]);
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
            ->where('username', self::FB_USERNAME)
            ->update([
                'username' => 'rubenrp81',
                'url' => 'https://www.facebook.com/rubenrp81',
                'updated_at' => now(),
            ]);
    }
};
