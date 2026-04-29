<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('social_accounts', function (Blueprint $table) {
            $table->boolean('is_primary')->default(false)->after('is_verified');
        });

        $rows = DB::table('social_accounts')
            ->select('user_id', 'social_network_id', DB::raw('MIN(id) as first_id'))
            ->groupBy('user_id', 'social_network_id')
            ->get();

        foreach ($rows as $row) {
            DB::table('social_accounts')
                ->where('id', $row->first_id)
                ->update(['is_primary' => true]);
        }
    }

    public function down(): void
    {
        Schema::table('social_accounts', function (Blueprint $table) {
            $table->dropColumn('is_primary');
        });
    }
};
