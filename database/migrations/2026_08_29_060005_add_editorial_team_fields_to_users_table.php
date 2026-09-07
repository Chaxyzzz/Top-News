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
        Schema::table('users', function (Blueprint $table) {
            $table->string('public_title', 100)->nullable()->after('phone');
            $table->text('bio')->nullable()->after('public_title');
            $table->boolean('show_on_editorial_team')->default(false)->index()->after('bio');
            $table->integer('editorial_team_order')->default(0)->index()->after('show_on_editorial_team');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'public_title',
                'bio',
                'show_on_editorial_team',
                'editorial_team_order',
            ]);
        });
    }
};
