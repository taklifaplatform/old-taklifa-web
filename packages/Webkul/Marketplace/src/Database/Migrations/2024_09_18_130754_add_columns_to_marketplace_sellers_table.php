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
        if (! Schema::hasColumn('marketplace_sellers', 'parent_id')) {
            Schema::table('marketplace_sellers', function (Blueprint $table) {
                $table->unsignedInteger('parent_id')
                    ->nullable()
                    ->after('is_approved');

                $table->foreign('parent_id')
                    ->references('id')
                    ->on('marketplace_sellers')
                    ->onDelete('cascade');
            });
        }

        if (! Schema::hasColumn('marketplace_sellers', 'marketplace_role_id')) {
            Schema::table('marketplace_sellers', function (Blueprint $table) {
                $table->foreignId('marketplace_role_id')
                    ->nullable()
                    ->after('is_approved')
                    ->constrained();
            });
        }

        if (Schema::hasColumn('marketplace_sellers', 'url')) {
            Schema::table('marketplace_sellers', function (Blueprint $table) {
                $table->string('url')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('marketplace_sellers', 'parent_id')) {
            Schema::table('marketplace_sellers', function (Blueprint $table) {
                $table->dropForeign(['parent_id']);
                $table->dropColumn('parent_id');
            });
        }

        if (Schema::hasColumn('marketplace_sellers', 'marketplace_role_id')) {
            Schema::table('marketplace_sellers', function (Blueprint $table) {
                $table->dropForeign(['marketplace_role_id']);
                $table->dropColumn('marketplace_role_id');
            });
        }

        if (Schema::hasColumn('marketplace_sellers', 'url')) {
            Schema::table('marketplace_sellers', function (Blueprint $table) {
                $table->string('url')->nullable(false)->change();
            });
        }
    }
};
