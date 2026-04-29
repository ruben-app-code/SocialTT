<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('following_follower_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('following_entry_id')->constrained('following_entries')->cascadeOnDelete();
            $table->unsignedBigInteger('follower_count')->nullable();
            $table->unsignedInteger('following_count')->nullable();
            $table->unsignedBigInteger('heart_count')->nullable();
            $table->timestamp('recorded_at');
            $table->timestamps();

            $table->index(['following_entry_id', 'recorded_at'], 'ff_snapshots_entry_recorded_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('following_follower_snapshots');
    }
};
