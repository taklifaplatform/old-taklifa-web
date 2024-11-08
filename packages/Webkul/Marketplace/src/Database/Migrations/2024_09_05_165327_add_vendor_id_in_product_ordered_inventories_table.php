<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('product_ordered_inventories', 'vendor_id')) {
            return;
        }

        Schema::table('product_ordered_inventories', function (Blueprint $table) {
            $table->integer('vendor_id')->default(0)->after('product_id');

            $table->dropForeign(['product_id']);
            $table->dropForeign(['channel_id']);

            $table->dropUnique('product_ordered_inventories_product_id_channel_id_unique');
            $table->unique(['product_id', 'channel_id', 'vendor_id'], 'product_channel_vendor_unique');

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('channel_id')->references('id')->on('channels')->onDelete('cascade');
        });

        foreach (DB::table('product_ordered_inventories')->get() as $orderInventory) {
            $orders = app('Webkul\Sales\Repositories\OrderItemRepository')
                ->select('orders.id')
                ->join('orders', function ($join) {
                    $join->on('orders.id', 'order_items.order_id');
                })
                ->where('order_items.product_id', $orderInventory->product_id)
                ->where('orders.status', 'pending')
                ->where('orders.channel_id', $orderInventory->channel_id)
                ->distinct()
                ->get();

            if (empty($orders)) {
                continue;
            }

            $sellerOrderTotalQty = 0;

            foreach ($orders as $order) {
                foreach (app('Webkul\Marketplace\Repositories\OrderRepository')->where('order_id', $order->id)->where('status', 'pending')->get() as $mpOrder) {
                    $sellerOrderTotalQty += $mpOrder->total_qty_ordered;

                    DB::table('product_ordered_inventories')->updateOrInsert([
                        'product_id' => $orderInventory->product_id,
                        'channel_id' => $orderInventory->channel_id,
                        'vendor_id'  => $mpOrder->marketplace_seller_id,
                    ], [
                        'qty' => DB::raw('qty + '.$mpOrder->total_qty_ordered),
                    ]);
                }
            }

            DB::table('product_ordered_inventories')
                ->where('product_id', $orderInventory->product_id)
                ->where('channel_id', $orderInventory->channel_id)
                ->where('vendor_id', 0)
                ->update([
                    'qty' => $orderInventory->qty - $sellerOrderTotalQty,
                ]);
        }

        DB::table('product_ordered_inventories')
            ->where('qty', 0)
            ->delete();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {}
};
