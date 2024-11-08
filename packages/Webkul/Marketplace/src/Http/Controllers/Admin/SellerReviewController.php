<?php

namespace Webkul\Marketplace\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Event;
use Webkul\Admin\Http\Requests\MassUpdateRequest;
use Webkul\Marketplace\DataGrids\Admin\SellerReviewDataGrid;
use Webkul\Marketplace\Repositories\ProductRepository;
use Webkul\Marketplace\Repositories\ReviewRepository;
use Webkul\Product\Repositories\ProductFlatRepository;

class SellerReviewController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected ReviewRepository $reviewRepository,
        protected ProductRepository $productRepository,
        protected ProductFlatRepository $productFlatRepository,
    ) {}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        if (request()->ajax()) {
            return app(SellerReviewDataGrid::class)->process();
        }

        return view('marketplace::admin.seller-reviews.index');
    }

    /**
     * Mass update the reviews.
     */
    public function massUpdate(MassUpdateRequest $request): JsonResponse
    {
        foreach ($request->input('indices') as $id) {
            Event::dispatch('marketplace.seller.review.update.before', $id);

            $flag = $this->reviewRepository->update([
                'status' => $request->input('value'),
            ], $id);

            Event::dispatch('marketplace.seller.review.update.after', $flag);
        }

        return new JsonResponse([
            'message' => trans('marketplace::app.admin.seller-reviews.index.update-success'),
        ], 200);
    }
}
