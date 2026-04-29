<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Asegura la red social X (slug x) en instalaciones que ya tenían la BD sin re-sembrar.
     */
    public function up(): void
    {
        if (DB::table('social_networks')->where('slug', 'x')->exists()) {
            return;
        }

        $now = now();

        DB::table('social_networks')->insert([
            'name' => 'X',
            'slug' => 'x',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('social_networks')->where('slug', 'x')->delete();
    }
};
