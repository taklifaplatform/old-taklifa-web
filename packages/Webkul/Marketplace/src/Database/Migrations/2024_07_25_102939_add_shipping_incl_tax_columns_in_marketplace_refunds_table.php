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
        Schema::table('marketplace_refunds', function (Blueprint $table) {
            $table->decimal('shipping_amount_incl_tax', 12, 4)->default(0)->after('base_sub_total_incl_tax');
            $table->decimal('base_shipping_amount_incl_tax', 12, 4)->default(0)->after('shipping_amount_incl_tax');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('marketplace_refunds', function (Blueprint $table) {
            $table->dropColumn('shipping_amount_incl_tax');
            $table->dropColumn('base_shipping_amount_incl_tax');
        });
    }
};
