<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Todo:: Need to fix.
     */
    public function up(): void
    {
        Schema::table('product_inventory_indices', function (Blueprint $table) {
            $table->integer('vendor_id')->default(0)->after('product_id');

            $table->dropForeign(['product_id']);
            $table->dropForeign(['channel_id']);

            $table->dropUnique('product_inventory_indices_product_id_channel_id_unique');
            $table->unique(['product_id', 'channel_id', 'vendor_id'], 'product_channel_vendor_unique');

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('channel_id')->references('id')->on('channels')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
