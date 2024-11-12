<?php

namespace Webkul\Shop\Http\Controllers;

use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Request;
use Webkul\Core\Models\Channel;
use Webkul\Checkout\Facades\Cart;
use Illuminate\Support\Facades\Event;
use Webkul\Sales\Models\Order;
use Webkul\Sales\Models\OrderAddress;

class QuoteController extends Controller
{
    /**
     * Cart page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('shop::checkout.quote.index');
    }

    public function generateQuotePDF(Request $request, Order $order, OrderAddress $orderAddress)
    {
        $channel = Channel::first();

        $html = view('shop::checkout.quote.pdf.export-pdf', [
            'channel' => $channel,
            'order' => $order,
            'orderAddress' => $orderAddress
        ])
            ->toArabicHTML();


        $pdf = app(PDF::class)
            ->loadHTML($html)
            ->setPaper('a4', 'portrait');

        return $pdf->stream();
    }


    public function address()
    {
        Event::dispatch('checkout.load.index');


        /**
         * If cart has errors then redirect back to the cart page
         */
        if (Cart::hasError()) {
            return redirect()->route('shop.checkout.cart.index');
        }

        $cart = Cart::getCart();


        /**
         * If cart minimum order amount is not satisfied then redirect back to the cart page
         */
        $minimumOrderAmount = (float) core()->getConfigData('sales.order_settings.minimum_order.minimum_order_amount') ?: 0;

        if (!$cart->checkMinimumOrder()) {
            session()->flash('warning', trans('shop::app.checkout.cart.minimum-order-message', [
                'amount' => core()->currency($minimumOrderAmount),
            ]));

            return redirect()->back();
        }

        return view('shop::checkout.onepage.quote-checkout', compact('cart'));
    }
}
