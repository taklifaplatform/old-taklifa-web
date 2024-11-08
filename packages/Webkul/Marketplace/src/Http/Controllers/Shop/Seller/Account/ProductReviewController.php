<?php

namespace Webkul\Marketplace\Http\Controllers\Shop\Seller\Account;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Webkul\Admin\Http\Requests\MassUpdateRequest;
use Webkul\Marketplace\DataGrids\Shop\ProductReviewsDataGrid;
use Webkul\Marketplace\Http\Controllers\Shop\Controller;
use Webkul\Product\Repositories\ProductReviewRepository;

class ProductReviewController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected ProductReviewRepository $productReviewRepository) {}

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        if (request()->ajax()) {
            return app(ProductReviewsDataGrid::class)->process();
        }

        return view('marketplace::shop.sellers.account.product-reviews.index');
    }

    /**
     * Mass update the reviews.
     */
    public function massUpdate(MassUpdateRequest $request): JsonResponse
    {

        foreach ($request->input('indices') as $id) {
            Event::dispatch('marketplace.seller.account.product.review.update.before', $id);

            $review = $this->productReviewRepository->update([
                'status' => $request->input('value'),
            ], $id);

            Event::dispatch('marketplace.seller.account.product.review.update.after', $review);
        }

        return new JsonResponse([
            'message' => trans('marketplace::app.shop.sellers.account.product-reviews.update-success'),
        ], 200);
    }
}
