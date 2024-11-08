<?php

namespace Webkul\Marketplace\Http\Controllers\Shop;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Webkul\Category\Repositories\CategoryRepository;
use Webkul\Marketplace\Repositories\SellerRepository;

class MarketplaceController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected CategoryRepository $categoryRepository,
        protected SellerRepository $sellerRepository
    ) {}

    /**
     * Display the specified resource.
     *
     * @return View
     */
    public function index()
    {
        return view('marketplace::shop.default.seller-central.index');
    }

    /**
     * Returns the list of popular sellers.
     */
    public function getPopularSellers(): JsonResponse
    {
        $sellers = $this->sellerRepository->with('categories')->getPopularSellers();

        $sellers = $sellers->map(function ($seller) {
            $categoryIds = $seller?->categories?->categories ?? [];

            $seller->allowed_categories = $this->categoryRepository
                ->findWhereIn('id', $categoryIds)
                ->pluck('name');

            $seller->logo_url = Storage::url($seller->shop_url);

            return $seller;
        });

        return new JsonResponse($sellers);
    }
}
