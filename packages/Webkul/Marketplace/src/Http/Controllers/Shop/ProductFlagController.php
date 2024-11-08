<?php

namespace Webkul\Marketplace\Http\Controllers\Shop;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Event;
use Webkul\Marketplace\Repositories\ProductFlagRepository;
use Webkul\Marketplace\Repositories\ProductRepository;

class ProductFlagController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected ProductFlagRepository $productFlagRepository,
        protected ProductRepository $productRepository,
    ) {}

    /**
     * Create product flag record.
     */
    public function store(): JsonResponse
    {
        $this->validate(request(), [
            'name'       => 'required',
            'reason'     => 'required',
            'product_id' => 'required',
            'email'      => 'required|email',
        ]);

        $marketplaceProduct = $this->productRepository->where('product_id', request('product_id'));

        if (! empty(request('seller_id'))) {
            $marketplaceProduct
                ->where('is_owner', 0)
                ->where('marketplace_seller_id', request('seller_id'));
        } else {
            $marketplaceProduct->where('is_owner', 1);
        }

        $marketplaceProduct = $marketplaceProduct->first();

        $seller = $marketplaceProduct->seller;

        $productFlag = $this->productFlagRepository->where('email', request('email'))
            ->where('is_owner', $marketplaceProduct->is_owner ?? 1)
            ->where('product_id', $marketplaceProduct->id)
            ->orderBy('id', 'desc')
            ->first();

        if (! empty($productFlag)
            && (
                ($productFlag->seller_id == $seller->seller_id // check seller own duplicate product entry.
                    && $productFlag->is_owner == 1)
                || ($productFlag->seller_id == request('seller_id') // check seller assign duplicate product entry.
                    && $productFlag->is_owner == 0)
            )
        ) {
            return new JsonResponse([
                'message' => trans('marketplace::app.shop.products.already-product-reported'),
            ], 400);
        }

        $additionalFields = [
            'is_owner'   => $marketplaceProduct->is_owner,
            'seller_id'  => $seller->seller_id,
            'product_id' => $marketplaceProduct->id,
        ];

        Event::dispatch('marketplace.customer.product_flag.create.before', $additionalFields);

        $flag = $this->productFlagRepository->create(array_merge(request()->only([
            'name',
            'email',
            'reason',
        ]), $additionalFields ?? []));

        Event::dispatch('marketplace.customer.product_flag.create.after', $flag);

        return new JsonResponse([
            'message' => trans('marketplace::app.shop.products.report-success'),
        ]);
    }
}
