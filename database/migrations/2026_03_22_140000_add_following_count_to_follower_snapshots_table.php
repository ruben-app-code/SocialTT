<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('follower_snapshots', function (Blueprint $table) {
            $table->unsignedInteger('following_count')->nullable()->after('followers_count');
        });

        Schema::table('follower_snapshots', function (Blueprint $table) {
            $table->integer('followers_count')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('follower_snapshots', function (Blueprint $table) {
            $table->dropColumn('following_count');
        });

        Schema::table('follower_snapshots', function (Blueprint $table) {
            $table->integer('followers_count')->nullable(false)->change();
        });
    }
};
