<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('poll_votes', function (Blueprint $table) {
            $table->string('voter_key', 64)->nullable()->after('user_id');
        });

        DB::table('poll_votes')->orderBy('id')->chunk(200, function ($rows): void {
            foreach ($rows as $row) {
                if (! empty($row->user_id)) {
                    DB::table('poll_votes')->where('id', $row->id)->update([
                        'voter_key' => 'user:'.$row->user_id,
                    ]);
                }
            }
        });

        Schema::table('poll_votes', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->change();
            $table->string('voter_key', 64)->nullable(false)->change();
            $table->unique(['poll_id', 'voter_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('poll_votes', function (Blueprint $table) {
            $table->dropUnique(['poll_id', 'voter_key']);
        });

        Schema::table('poll_votes', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
            $table->dropColumn('voter_key');
        });
    }
};
