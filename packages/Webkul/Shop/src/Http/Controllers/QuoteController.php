<?php

namespace Webkul\Shop\Http\Controllers;

use Barryvdh\DomPDF\PDF;
use Webkul\Checkout\Facades\Cart;
use Webkul\Marketplace\Models\Order;
use Illuminate\Support\Facades\Event;
use Webkul\Marketplace\Models\Seller;
use Webkul\Marketplace\Repositories\OrderRepository;
use Webkul\Marketplace\Repositories\InvoiceRepository;
use Webkul\Marketplace\Http\Controllers\Shop\Controller;
use Webkul\Sales\Repositories\InvoiceRepository as BaseInvoiceRepository;

class QuoteController extends Controller
{
    public function __construct(
        protected BaseInvoiceRepository $baseInvoiceRepository,
        protected InvoiceRepository $invoiceRepository,
        protected OrderRepository $orderRepository
    ) {}
    /**
     * Cart page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('shop::checkout.quote.index');
    }

    public function generateQuotePDF(Order $order)
    {
        $seller = Seller::findOrFail($order->marketplace_seller_id);

        $html = view('shop::checkout.quote.pdf.pdf-seller', [
            'seller' => $seller,
            'marketplaceOrder'  => $order,
        ])->toArabicHTML();

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
