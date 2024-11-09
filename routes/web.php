<?php

use Webkul\Tax\Helpers\Tax;
use Webkul\Sales\Models\Order;
use Webkul\Checkout\Models\Cart;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/test', function () {
    $cart = Cart::find(397);


    $totalAmount = 0;

    foreach ($cart->items as $item) {
        $totalAmount += $item->total;
    }

    $taxPercent = 15; // Set this to your desired tax percentage or retrieve it from configuration

    $taxTotal = Tax::calculateTax($totalAmount, $taxPercent);

    $cart->tax_total = $taxTotal;

    foreach ($cart->items as $item) {
        $item->tax_amount = Tax::calculateTax($item->total, $taxPercent);
    }

    $cart->save();

    return $cart;


});



Route::get('/test-12', function () {
    $order = Order::find(226);


    return $order;
});

