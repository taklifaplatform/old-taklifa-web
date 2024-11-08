<?php

namespace Webkul\Marketplace\Http\Controllers\Shop\Seller\Account;

use Illuminate\Http\JsonResponse;
use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\Marketplace\Http\Controllers\Shop\Controller;
use Webkul\Marketplace\Repositories\OrderRepository;
use Webkul\Marketplace\Repositories\ProductRepository;

class MegaSearchController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected CustomerRepository $customerRepository,
        protected OrderRepository $orderRepository,
        protected ProductRepository $productRepository
    ) {}

    /**
     * Search Products.
     *
     * @return JsonResponse
     */
    public function products()
    {
        $results = [];

        request()->query->add([
            'status'               => null,
            'visible_individually' => null,
            'name'                 => request('query'),
            'sort'                 => 'created_at',
            'order'                => 'desc',
        ]);

        $products = $this->productRepository->searchFromDatabase(auth()->guard('seller')->user());

        foreach ($products as $product) {
            $results[] = [
                'id'              => $product->id,
                'sku'             => $product->sku,
                'name'            => $product->name,
                'price'           => $product->price,
                'formatted_price' => core()->formatBasePrice($product->price),
                'images'          => $product->images,
                'inventories'     => $product->inventories,
            ];
        }

        $products->setCollection(collect($results));

        return response()->json($products);
    }

    /**
     * Search Orders.
     *
     * @return JsonResponse
     */
    public function orders()
    {
        $orders = $this->orderRepository->searchOrders(auth()->guard('seller')->user());

        foreach ($orders as $key => $order) {
            $orders[$key]['formatted_created_at'] = core()->formatDate($order->created_at, 'd M Y');

            $orders[$key]['status_label'] = $order->status_label;

            $orders[$key]['customer_full_name'] = $order->customer_first_name.' '.$order->customer_last_name;
        }

        return response()->json($orders);
    }

    /**
     * Search customers.
     *
     * @return JsonResponse
     */
    public function customers()
    {
        $customers = $this->orderRepository->searchCustomers(auth()->guard('seller')->user());

        return response()->json($customers);
    }
}
