<?php

namespace Webkul\Marketplace\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Webkul\Admin\Http\Requests\MassDestroyRequest;
use Webkul\Admin\Http\Requests\MassUpdateRequest;
use Webkul\Marketplace\DataGrids\Admin\SellerDataGrid;
use Webkul\Marketplace\DataGrids\Admin\SellerFlagDataGrid;
use Webkul\Marketplace\Enum\Order;
use Webkul\Marketplace\Http\Requests\ProductFromRequest;
use Webkul\Marketplace\Http\Requests\SellerFormRequest;
use Webkul\Marketplace\Mail\SellerApprovalNotification;
use Webkul\Marketplace\Mail\SellerDeleteNotification;
use Webkul\Marketplace\Repositories\OrderRepository;
use Webkul\Marketplace\Repositories\ProductRepository;
use Webkul\Marketplace\Repositories\SellerRepository;
use Webkul\Product\Repositories\ProductRepository as BaseProductRepository;

class SellerController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected SellerRepository $sellerRepository,
        protected OrderRepository $orderRepository,
        protected ProductRepository $productRepository,
        protected BaseProductRepository $baseProductRepository
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            return app(SellerDataGrid::class)->process();
        }

        return view('marketplace::admin.sellers.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SellerFormRequest $request): JsonResponse
    {
        Event::dispatch('marketplace.seller.create.before');

        $seller = $this->sellerRepository->create(array_merge($request->validated(), [
            'password'              => rand(100000, 10000000),
            'is_approved'           => ! core()->getConfigData('marketplace.settings.general.seller_approval_required'),
            'allowed_product_types' => [
                'simple',
                'configurable',
                'virtual',
                'downloadable',
            ],
            'address' => implode(PHP_EOL, request('address')),
        ]));

        Event::dispatch('marketplace.seller.create.after', $seller);

        return new JsonResponse([
            'message' => trans('marketplace::app.admin.sellers.index.create.success'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View|JsonResponse
    {
        if (request()->ajax()) {
            return app(SellerFlagDataGrid::class)->process();
        }

        $seller = $this->sellerRepository->findOrFail($id);

        return view('marketplace::admin.sellers.edit')
            ->with('seller', $seller);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SellerFormRequest $request, int $id): JsonResponse
    {
        $this->sellerRepository->findOrFail($id);

        $data = array_merge($request->validated(), [
            'address'      => implode(PHP_EOL, request('address')),
            'is_suspended' => empty(request('is_suspended')) ? 0 : request('is_suspended'),
        ]);

        if (empty($data['commission_enable'])) {
            $data['commission_enable'] = 0;

            $data['commission_percentage'] = 0;
        }

        if (empty($data['allowed_product_types'])) {
            $data['allowed_product_types'] = null;
        }

        Event::dispatch('marketplace.seller.update.before', $id);

        $seller = $this->sellerRepository->update($data, $id);

        Event::dispatch('marketplace.seller.update.after', $seller);

        session()->flash('success', trans('marketplace::app.admin.sellers.edit.update-success'));

        return new JsonResponse([
            'redirect_url' => route('admin.marketplace.sellers.index'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        $orderCount = $this->orderRepository
            ->where('marketplace_seller_id', $id)
            ->whereIn('status', [
                Order::STATUS_PENDING->value,
                Order::STATUS_PROCESSING->value,
            ])
            ->count();

        if ($orderCount) {
            return new JsonResponse([
                'message' => trans('marketplace::app.admin.sellers.index.pending-orders'),
            ], 500);
        }

        try {
            $seller = $this->sellerRepository->find($id);

            Event::dispatch('marketplace.seller.delete.before', $id);

            try {
                Mail::queue(new SellerDeleteNotification($seller->toArray()));
            } catch (\Exception $e) {
            }

            $seller->delete();

            Event::dispatch('marketplace.seller.delete.after', $id);

            return new JsonResponse([
                'message' => trans('marketplace::app.admin.sellers.index.delete-success'),
            ], 200);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => trans('marketplace::app.admin.sellers.index.delete-failed'),
            ], 500);
        }
    }

    /**
     * Mass delete the sellers.
     */
    public function massDestroy(MassDestroyRequest $request): JsonResponse
    {
        $orderCount = $this->orderRepository
            ->WhereIn('marketplace_seller_id', $request->input('indices'))
            ->whereIn('status', [
                Order::STATUS_PENDING->value,
                Order::STATUS_PROCESSING->value,
            ])
            ->count();

        if ($orderCount) {
            return new JsonResponse([
                'message' => trans('marketplace::app.admin.sellers.index.pending-orders'),
            ], 500);
        }

        $sellerIds = $request->input('indices');

        try {
            foreach ($sellerIds as $sellerId) {
                $seller = $this->sellerRepository->find($sellerId);

                if (isset($seller)) {
                    Event::dispatch('marketplace.seller.delete.before', $sellerId);

                    $this->sellerRepository->delete($sellerId);

                    try {
                        Mail::queue(new SellerDeleteNotification($seller->toArray()));
                    } catch (\Exception $e) {
                    }

                    Event::dispatch('marketplace.seller.delete.after', $sellerId);
                }
            }

            return new JsonResponse([
                'message' => trans('marketplace::app.admin.sellers.index.delete-success'),
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mass update the reviews.
     */
    public function massUpdate(MassUpdateRequest $request): JsonResponse
    {
        $sellers = $this->sellerRepository
            ->whereIn('id', $request->input('indices'))
            ->get();

        foreach ($sellers as $seller) {
            Event::dispatch('marketplace.seller.update.before', $seller);

            $seller->is_approved = $request->input('value');

            $seller->save();

            try {
                Mail::to($seller->email)
                    ->send(new SellerApprovalNotification($seller));
            } catch (\Exception $e) {
            }

            Event::dispatch('marketplace.seller.update.after', $seller);
        }

        return new JsonResponse([
            'message' => trans('marketplace::app.admin.sellers.index.update-success'),
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function search($id): View|JsonResponse
    {
        $seller = $this->sellerRepository->findOrFail($id);

        $requiredFields = [
            'shop_title',
            'address',
            'phone',
            'postcode',
            'city',
            'state',
            'country',
        ];

        try {
            foreach ($requiredFields as $field) {
                if (empty($seller->{$field})) {
                    return response()->json([
                        'warning' => trans('marketplace::app.admin.sellers.index.shop-validation', ['name' => str_replace('_', ' ', $field)]),
                    ], 400); // Return a JSON response if used in an API context
                }
            }

            // Continue with your search logic
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }


        if (request()->input('query')) {
            $results = [];

            foreach ($this->productRepository->searchProducts(request()->input('query')) as $row) {
                $results[] = [
                    'id'              => $row->product_id,
                    'sku'             => $row->sku,
                    'name'            => $row->name,
                    'price'           => core()->convertPrice($row->price),
                    'formatted_price' => $row->getTypeInstance()->getPriceHtml(),
                    'base_image'      => $row->product->base_image_url,
                ];
            }

            return new JsonResponse($results);
        } else {
            return view('marketplace::admin.sellers.products.search');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function assignProduct(int $sellerId, int $productId): View|RedirectResponse
    {
        $seller = $this->sellerRepository->findOrFail($sellerId);

        $baseProduct = $this->baseProductRepository->findOrFail($productId);

        $product = $this->productRepository->findOneWhere([
            'product_id'            => $productId,
            'marketplace_seller_id' => $sellerId,
        ]);

        if ($product) {
            return back()
                ->with('error', trans('marketplace::app.admin.sellers.assign-product.already-selling'));
        }

        if (! $this->sellerRepository->getAllowedProducts($seller)->has($baseProduct->type)) {
            return back()
                ->with('error', trans('marketplace::app.admin.sellers.assign-product.product-not-allowed', [
                    'type' => trans(config('product_types')[$baseProduct->type]['name']),
                ]));
        }

        return view('marketplace::admin.sellers.products.assign')
            ->with('baseProduct', $baseProduct);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function saveAssignProduct(ProductFromRequest $request, int $sellerId, int $productId): RedirectResponse
    {
        $this->productRepository->createAssign(array_merge($request->all(), [
            'product_id'  => $productId,
            'is_owner'    => 0,
            'seller_id'   => $sellerId,
            'is_approved' => core()->getConfigData('marketplace.settings.general.product_approval_required') ? 1 : 0,
        ]));

        return to_route('admin.marketplace.sellers.index')
            ->with('success', trans('marketplace::app.admin.sellers.assign-product.assign-successfully'));
    }
}
