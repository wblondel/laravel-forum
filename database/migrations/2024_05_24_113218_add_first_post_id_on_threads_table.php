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
        Schema::table('threads', static function (Blueprint $table) {
            $table->foreignId('first_post_id')->index()->nullable()->constrained('posts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('threads', static function (Blueprint $table) {
            $table->dropConstrainedForeignId('first_post_id');
        });

    }
};
