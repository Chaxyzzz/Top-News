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
        Schema::table('categories', function (Blueprint $table) {
            $table->boolean('show_on_homepage')->default(true)->after('sort_order');
            $table->unsignedInteger('homepage_order')->default(0)->after('show_on_homepage');
            $table->boolean('show_in_navigation')->default(true)->after('homepage_order');
            $table->unsignedInteger('navigation_order')->default(0)->after('show_in_navigation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn([
                'show_on_homepage',
                'homepage_order',
                'show_in_navigation',
                'navigation_order',
            ]);
        });
    }
};
