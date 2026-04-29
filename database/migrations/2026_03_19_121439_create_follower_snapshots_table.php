<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('follower_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('social_account_id')->nullable();
            $table->integer('followers_count');
            $table->enum('source', ["manual","auto_prompt"])->default('manual');
            $table->timestamp('recorded_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('follower_snapshots');
    }
};
