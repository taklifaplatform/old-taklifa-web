<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTitleToMarketplaceSellerReviewsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('marketplace_seller_reviews', 'title')) {
            Schema::table('marketplace_seller_reviews', function (Blueprint $table) {
                $table->string('title')->after('rating');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('marketplace_seller_reviews', 'title')) {
            Schema::table('marketplace_seller_reviews', function (Blueprint $table) {
                $table->dropColumn('title');
            });
        }
    }
}
