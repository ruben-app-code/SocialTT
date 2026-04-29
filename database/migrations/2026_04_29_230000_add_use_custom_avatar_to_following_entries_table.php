<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('following_entries', function (Blueprint $table) {
            $table->boolean('use_custom_avatar')->default(false)->after('avatar_url');
        });
    }

    public function down(): void
    {
        Schema::table('following_entries', function (Blueprint $table) {
            $table->dropColumn('use_custom_avatar');
        });
    }
};
