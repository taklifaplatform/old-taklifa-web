<?php

namespace Webkul\Marketplace\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Validator;
use Webkul\Admin\Http\Requests\MassUpdateRequest;
use Webkul\Marketplace\DataGrids\Admin\ProductReviewsDataGrid;
use Webkul\Product\Repositories\ProductReviewRepository;

class ProductReviewsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected ProductReviewRepository $productReviewRepository) {}

    /**
     * Products Reviews
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        if (request()->ajax()) {
            return app(ProductReviewsDataGrid::class)->process();
        }

        return view('marketplace::admin.product-reviews.index');
    }

    /**
     * Update product review.
     */
    public function update(int $id): JsonResponse
    {
        Validator::make(array_merge(request()->all(), [
            'id' => $id,
        ]), [
            'id'     => 'required|exists:product_reviews,id',
            'status' => 'required|in:approved,disapproved,pending',
        ])->validate();

        Event::dispatch('marketplace.product.review.update.before', $id);

        $review = $this->productReviewRepository->update([
            'status' => request()->input('status'),
        ], $id);

        Event::dispatch('marketplace.product.review.update.after', $review);

        return new JsonResponse([
            'message' => trans('marketplace::app.admin.product-reviews.update-success'),
        ]);
    }

    /**
     * Delete the review of the product.
     */
    public function destroy(int $id): JsonResponse
    {
        Validator::make(['id' => $id], [
            'id' => 'required|exists:product_reviews,id',
        ])->validate();

        Event::dispatch('marketplace.product.review.delete.before', $id);

        $review = $this->productReviewRepository->delete($id);

        Event::dispatch('marketplace.product.review.delete.after', $review);

        return new JsonResponse([
            'message' => trans('marketplace::app.admin.product-reviews.delete-success'),
        ]);
    }

    /**
     * Mass update the reviews.
     */
    public function massUpdate(MassUpdateRequest $request): JsonResponse
    {
        foreach ($request->input('indices') as $id) {
            Event::dispatch('marketplace.product.review.update.before', $id);

            $flag = $this->productReviewRepository->update([
                'status' => $request->input('value'),
            ], $id);

            Event::dispatch('marketplace.product.review.update.after', $flag);
        }

        return new JsonResponse([
            'message' => trans('marketplace::app.admin.product-reviews.update-success'),
        ], 200);
    }
}
