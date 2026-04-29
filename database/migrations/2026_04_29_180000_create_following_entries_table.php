<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('following_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('entry_type', 32);
            $table->foreignId('platform_user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('social_network_id')->nullable()->constrained('social_networks')->nullOnDelete();
            $table->string('username', 255)->nullable();
            $table->string('url', 2048);
            $table->string('label', 255)->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'entry_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('following_entries');
    }
};
