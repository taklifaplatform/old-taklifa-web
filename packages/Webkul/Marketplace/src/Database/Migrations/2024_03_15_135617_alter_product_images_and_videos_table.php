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
        Schema::table('marketplace_product_images', function (Blueprint $table) {
            if (Schema::hasColumn('marketplace_product_images', 'created_at')) {
                $table->dropColumn('created_at');
            }

            if (Schema::hasColumn('marketplace_product_images', 'updated_at')) {
                $table->dropColumn('updated_at');
            }

            if (! Schema::hasColumn('marketplace_product_images', 'position')) {
                $table->integer('position')->default(0);
            }
        });

        Schema::table('marketplace_product_videos', function (Blueprint $table) {
            if (Schema::hasColumn('marketplace_product_videos', 'created_at')) {
                $table->dropColumn('created_at');
            }

            if (Schema::hasColumn('marketplace_product_videos', 'updated_at')) {
                $table->dropColumn('updated_at');
            }

            if (! Schema::hasColumn('marketplace_product_videos', 'position')) {
                $table->integer('position')->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
