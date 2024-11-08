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
        if (! Schema::hasColumn('marketplace_sellers', 'address')) {
            Schema::table('marketplace_sellers', function (Blueprint $table) {
                $table->string('address')->nullable()->after('phone');
            });
        }

        if (Schema::hasColumn('marketplace_sellers', 'address1')
            && Schema::hasColumn('marketplace_sellers', 'address2')
        ) {
            DB::table('marketplace_sellers')->update([
                'address' => DB::raw('CONCAT(address1, CHAR(10), address2)'),
            ]);

            Schema::table('marketplace_sellers', function (Blueprint $table) {
                $table->dropColumn('address1');
            });

            Schema::table('marketplace_sellers', function (Blueprint $table) {
                $table->dropColumn('address2');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
