<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameDiscountInvoicedColumnsInMarketplaceOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('marketplace_orders', function (Blueprint $table) {
            $table->renameColumn('discount_invoiced', 'discount_amount_invoiced');
            $table->renameColumn('base_discount_invoiced', 'base_discount_amount_invoiced');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('marketplace_orders', function (Blueprint $table) {
            //
        });
    }
}
