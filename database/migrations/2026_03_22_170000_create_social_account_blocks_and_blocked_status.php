<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('social_account_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('social_account_id')->unique()->constrained()->cascadeOnDelete();
            $table->dateTime('blocked_at');
            $table->dateTime('activates_at');
            $table->timestamps();
        });

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE social_accounts MODIFY COLUMN current_status ENUM('active','deleted','stolen','blocked') NOT NULL DEFAULT 'active'");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('social_account_blocks');

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("UPDATE social_accounts SET current_status = 'active' WHERE current_status = 'blocked'");
            DB::statement("ALTER TABLE social_accounts MODIFY COLUMN current_status ENUM('active','deleted','stolen') NOT NULL DEFAULT 'active'");
        }
    }
};
