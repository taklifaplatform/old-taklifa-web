<?php

namespace Webkul\Marketplace\Http\Controllers\Shop;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Event;
use Webkul\Marketplace\Enum\Review;
use Webkul\Marketplace\Repositories\ReviewRepository;
use Webkul\Marketplace\Repositories\SellerRepository;

class ReviewController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected ReviewRepository $reviewRepository,
        protected SellerRepository $sellerRepository
    ) {}

    /**
     * Display the specified resource.
     *
     * @return mixed
     */
    public function index(string $url)
    {
        $seller = $this->sellerRepository->findByUrlOrFail($url);

        $reviews = $this->reviewRepository
            ->with('customer')
            ->where('marketplace_seller_id', $seller->seller_id)
            ->paginate(5);

        return view('marketplace::shop.sellers.reviews', ['seller' => $seller, 'reviews' => $reviews]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(): JsonResponse
    {
        $this->validate(request(), [
            'rating'  => 'required',
            'title'   => 'required',
            'comment' => 'required',
        ]);

        if (! auth()->guard('customer')->user()) {
            return new JsonResponse([
                'message' => trans('marketplace::app.shop.sellers.profile.login-first'),
            ]);
        }

        $seller = $this->sellerRepository->findByUrlOrFail(request()->input('seller_url'));

        Event::dispatch('marketplace.customer.review.create.before', $seller);

        $review = $this->reviewRepository->create(array_merge(request()->only([
            'rating',
            'title',
            'comment',
        ]), [
            'status'                => Review::STATUS_PENDING->value,
            'marketplace_seller_id' => $seller->seller_id,
            'customer_id'           => auth()->guard('customer')->user()->id,
        ]));

        Event::dispatch('marketplace.customer.review.create.after', $review);

        return new JsonResponse([
            'message' => trans('marketplace::app.shop.sellers.profile.review-success'),
        ]);
    }
}
