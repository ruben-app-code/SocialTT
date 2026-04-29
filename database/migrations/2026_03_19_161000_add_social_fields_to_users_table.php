<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'timezone')) {
                $table->string('timezone')->nullable()->after('password');
            }
            if (! Schema::hasColumn('users', 'role')) {
                $table->string('role', 20)->default('creator')->after('timezone');
            }
            if (! Schema::hasColumn('users', 'level_id')) {
                $table->unsignedBigInteger('level_id')->nullable()->after('role');
            }
            if (! Schema::hasColumn('users', 'is_claimed')) {
                $table->boolean('is_claimed')->default(false)->after('level_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            foreach (['timezone', 'role', 'level_id', 'is_claimed'] as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
