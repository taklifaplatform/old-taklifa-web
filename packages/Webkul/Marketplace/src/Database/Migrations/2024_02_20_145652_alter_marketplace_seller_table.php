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
        if (! Schema::hasColumn('marketplace_sellers', 'name')) {
            Schema::table('marketplace_sellers', function (Blueprint $table) {
                $table->string('name')->after('id');
            });
        }

        if (! Schema::hasColumn('marketplace_sellers', 'email')) {
            Schema::table('marketplace_sellers', function (Blueprint $table) {
                $table->string('email')->unique()->after('name');
            });
        }

        if (! Schema::hasColumn('marketplace_sellers', 'password')) {
            Schema::table('marketplace_sellers', function (Blueprint $table) {
                $table->string('password')->after('email');
            });
        }

        if (! Schema::hasColumn('marketplace_sellers', 'commission_enable')) {
            Schema::table('marketplace_sellers', function (Blueprint $table) {
                $table->boolean('commission_enable')->default(0)->after('privacy_policy');
            });
        }

        if (! Schema::hasColumn('marketplace_sellers', 'commission_percentage')) {
            Schema::table('marketplace_sellers', function (Blueprint $table) {
                $table->decimal('commission_percentage', 12, 4)->default(0)->nullable()->after('commission_enable');
            });
        }

        if (! Schema::hasColumn('marketplace_sellers', 'min_order_amount')) {
            Schema::table('marketplace_sellers', function (Blueprint $table) {
                $table->string('min_order_amount')->nullable()->after('commission_percentage');
            });
        }

        if (! Schema::hasColumn('marketplace_sellers', 'google_analytics_id')) {
            Schema::table('marketplace_sellers', function (Blueprint $table) {
                $table->string('google_analytics_id')->nullable()->after('min_order_amount');
            });
        }

        if (! Schema::hasColumn('marketplace_sellers', 'allowed_product_types')) {
            Schema::table('marketplace_sellers', function (Blueprint $table) {
                $table->json('allowed_product_types')->nullable()->after('google_analytics_id');
            });
        }

        if (Schema::hasColumn('marketplace_sellers', 'tax_vat')) {
            Schema::table('marketplace_sellers', function (Blueprint $table) {
                $table->dropColumn('tax_vat');
            });
        }

        if (Schema::hasColumn('marketplace_sellers', 'customer_id')) {
            Schema::table('marketplace_sellers', function (Blueprint $table) {
                $table->dropConstrainedForeignId('customer_id');
                $table->dropColumn('customer_id');
            });
        }

        if (Schema::hasColumn('marketplace_sellers', 'youtube')) {
            Schema::table('marketplace_sellers', function (Blueprint $table) {
                $table->dropColumn('youtube');
            });
        }

        if (Schema::hasColumn('marketplace_sellers', 'instagram')) {
            Schema::table('marketplace_sellers', function (Blueprint $table) {
                $table->dropColumn('instagram');
            });
        }

        if (Schema::hasColumn('marketplace_sellers', 'skype')) {
            Schema::table('marketplace_sellers', function (Blueprint $table) {
                $table->dropColumn('skype');
            });
        }

        if (Schema::hasColumn('marketplace_sellers', 'linked_in')) {
            Schema::table('marketplace_sellers', function (Blueprint $table) {
                $table->renameColumn('linked_in', 'linkedin');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
