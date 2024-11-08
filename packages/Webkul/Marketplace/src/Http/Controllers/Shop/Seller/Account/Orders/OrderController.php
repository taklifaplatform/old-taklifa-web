<?php

namespace Webkul\Marketplace\Http\Controllers\Shop\Seller\Account\Orders;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Webkul\Marketplace\DataGrids\Shop\OrderDataGrid;
use Webkul\Marketplace\Http\Controllers\Shop\Controller;
use Webkul\Marketplace\Repositories\OrderRepository;

class OrderController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(protected OrderRepository $orderRepository) {}

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        if (request()->ajax()) {
            return app(OrderDataGrid::class)->process();
        }

        return view('marketplace::shop.sellers.account.orders.index');
    }

    /**
     * Show the view for the specified resource.
     *
     * @return Response
     */
    public function view(int $id)
    {
        $sellerOrder = $this->orderRepository->with('order')
            ->findOneWhere([
                'order_id'              => $id,
                'marketplace_seller_id' => auth()->guard('seller')->user()->seller_id,
            ]);

        abort_if(! $sellerOrder, 404);

        return view('marketplace::shop.sellers.account.orders.view', compact('sellerOrder'));
    }

    /**
     * Cancel action for the specified resource.
     *
     * @return Response
     */
    public function cancel(int $id)
    {
        if (! core()->getConfigData('marketplace.settings.general.can_cancel_order')) {
            session()->flash('error', trans('marketplace::app.shop.sellers.account.orders.view.permission-error'));

            return back();
        }

        Event::dispatch('marketplace.seller.account.orders.cancel.before', $id);

        $result = $this->orderRepository->sellerCancelOrder($id, auth()->guard('seller')->user());

        Event::dispatch('marketplace.seller.account.orders.cancel.after', $id);

        if ($result) {
            session()->flash('success', trans('marketplace::app.shop.sellers.account.orders.view.cancel-success-msg'));
        } else {
            session()->flash('error', trans('marketplace::app.shop.sellers.account.orders.view.cancel-error-msg'));
        }

        return back();
    }
}
