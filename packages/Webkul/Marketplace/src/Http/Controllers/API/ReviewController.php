<?php

namespace Webkul\Marketplace\Http\Controllers\API;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Event;
use Webkul\Marketplace\Repositories\ProductRepository as MpProductRepository;
use Webkul\Marketplace\Repositories\ProductReviewRepository as MpProductReviewRepository;
use Webkul\Product\Repositories\ProductRepository;
use Webkul\Product\Repositories\ProductReviewAttachmentRepository;
use Webkul\Product\Repositories\ProductReviewRepository;
use Webkul\Shop\Http\Controllers\API\ReviewController as BaseReviewController;

class ReviewController extends BaseReviewController
{
    /**
     * Create a controller instance.
     */
    public function __construct(
        protected ProductRepository $productRepository,
        protected ProductReviewRepository $productReviewRepository,
        protected ProductReviewAttachmentRepository $productReviewAttachmentRepository,
        protected MpProductReviewRepository $mpProductReviewRepository,
        protected MpProductRepository $mpProductRepository
    ) {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(int $id): JsonResource
    {
        $this->validate(request(), [
            'title'         => 'required',
            'comment'       => 'required',
            'rating'        => 'required|numeric|min:1|max:5',
            'attachments'   => 'array',
            'attachments.*' => 'file|mimetypes:image/*,video/*',
        ]);

        $data = array_merge(request()->only([
            'title',
            'comment',
            'rating',
        ]), [
            'attachments' => request()->file('attachments') ?? [],
            'status'      => self::STATUS_PENDING,
            'product_id'  => $id,
        ]);

        $data['name'] = auth()->guard('customer')->user()?->name ?? request()->input('name');
        $data['customer_id'] = auth()->guard('customer')->id() ?? null;

        Event::dispatch('customer.product_review.create.before', $productId = $id);

        $review = $this->productReviewRepository->create($data);

        Event::dispatch('customer.product_review.create.after', $review);

        Event::dispatch('marketplace.product_review.create.before', $review);

        if ($sellerProduct = $this->mpProductRepository->where('is_owner', 1)->where('product_id', $id)->first()) {
            $mpReview = $this->mpProductReviewRepository->create([
                'product_id'            => $id,
                'product_review_id'     => $review->id,
                'marketplace_seller_id' => $sellerProduct->marketplace_seller_id,
                'customer_id'           => $review->customer_id,
            ]);
        }

        Event::dispatch('marketplace.product_review.create.after', $mpReview);

        $this->productReviewAttachmentRepository->upload($data['attachments'], $review);

        return new JsonResource([
            'message' => trans('shop::app.products.view.reviews.success'),
        ]);
    }
}
