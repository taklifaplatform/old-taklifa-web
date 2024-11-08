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
            $table->decimal('sub_total_incl_tax', 12, 4)->default(0)->after('base_discount_amount');
            $table->decimal('base_sub_total_incl_tax', 12, 4)->default(0)->after('sub_total_incl_tax');
        });

        DB::table('marketplace_refunds')->update([
            'sub_total_incl_tax'      => DB::raw('sub_total + tax_amount'),
            'base_sub_total_incl_tax' => DB::raw('base_sub_total + base_tax_amount'),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('marketplace_refunds', function (Blueprint $table) {
            $table->dropColumn('sub_total_incl_tax');
            $table->dropColumn('base_sub_total_incl_tax');
        });
    }
};
