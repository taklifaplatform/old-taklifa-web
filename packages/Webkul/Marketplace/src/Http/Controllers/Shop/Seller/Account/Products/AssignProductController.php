<?php

namespace Webkul\Marketplace\Http\Controllers\Shop\Seller\Account\Products;

use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Webkul\Marketplace\Http\Controllers\Shop\Controller;
use Webkul\Marketplace\Http\Requests\ProductFromRequest;
use Webkul\Marketplace\Repositories\ProductDownloadableLinkRepository;
use Webkul\Marketplace\Repositories\ProductDownloadableSampleRepository;
use Webkul\Marketplace\Repositories\ProductRepository;
use Webkul\Marketplace\Repositories\SellerRepository;
use Webkul\Product\Repositories\ProductRepository as BaseProductRepository;

class AssignProductController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected BaseProductRepository $baseProductRepository,
        protected ProductDownloadableLinkRepository $productDownloadableLinkRepository,
        protected ProductDownloadableSampleRepository $productDownloadableSampleRepository,
        protected ProductRepository $productRepository,
        protected SellerRepository $sellerRepository
    ) {}

    /**
     * Show the form for creating a new resource.
     */
    public function create(int $id): View|RedirectResponse
    {
        if (empty(core()->getConfigData('marketplace.settings.general.seller_can_assign_product'))) {
            return back();
        }

        $product = $this->productRepository->findOneWhere([
            'product_id'            => $id,
            'marketplace_seller_id' => auth()->guard('seller')->user()->seller_id,
        ]);

        if ($product) {
            return back()
                ->with('error', trans('marketplace::app.shop.sellers.account.products.assign.already-selling'));
        }

        $baseProduct = $this->baseProductRepository->findOrFail($id);

        if (! $this->sellerRepository->getAllowedProducts()->has($baseProduct->type)) {
            return back()
                ->with('error', trans('marketplace::app.shop.sellers.account.products.assign.product-not-allowed', [
                    'type' => trans(config('product_types')[$baseProduct->type]['name']),
                ]));
        }

        return view('marketplace::shop.sellers.account.products.assign.create')
            ->with('baseProduct', $baseProduct);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductFromRequest $request, int $id): RedirectResponse
    {
        Event::dispatch('marketplace.seller.account.products.assign.before', $id);

        $sellerProduct = $this->productRepository->createAssign(array_merge($request->all(), [
            'product_id'  => $id,
            'is_owner'    => 0,
            'is_approved' => core()->getConfigData('marketplace.settings.general.product_approval_required') ? 1 : 0,
        ]));

        Event::dispatch('marketplace.seller.account.products.assign.after', $sellerProduct);

        Event::dispatch('catalog.product.update.after', $sellerProduct->product);

        return to_route('shop.marketplace.seller.account.products.index')
            ->with('success', trans('marketplace::app.shop.sellers.account.products.assign.create-success'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function edit(int $id): View
    {
        $product = $this->productRepository->findOrFail($id);

        return view('marketplace::shop.sellers.account.products.assign.edit')
            ->with('product', $product);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return Response
     */
    public function update(ProductFromRequest $request, int $id)
    {
        $sellerProduct = $this->productRepository->updateAssign($request->all(), $id);

        Event::dispatch('catalog.product.update.after', $sellerProduct->product);

        return to_route('shop.marketplace.seller.account.products.index')
            ->with('success', trans('marketplace::app.shop.sellers.account.products.assign.update-success'));
    }

    /**
     * Uploads downloadable file
     */
    public function uploadLink(int $id): JsonResponse
    {
        return new JsonResponse(
            $this->productDownloadableLinkRepository->upload(request()->all(), $id)
        );
    }

    /**
     * Uploads downloadable sample file
     */
    public function uploadSample(int $id): JsonResponse
    {
        return new JsonResponse(
            $this->productDownloadableSampleRepository->upload(request()->all(), $id)
        );
    }
}
