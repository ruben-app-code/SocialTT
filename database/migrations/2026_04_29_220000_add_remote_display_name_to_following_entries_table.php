<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('following_entries', function (Blueprint $table) {
            $table->string('remote_display_name', 255)->nullable()->after('username');
        });
    }

    public function down(): void
    {
        Schema::table('following_entries', function (Blueprint $table) {
            $table->dropColumn('remote_display_name');
        });
    }
};
