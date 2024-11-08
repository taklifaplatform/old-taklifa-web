<?php

namespace Webkul\Marketplace\Http\Controllers\Shop\Seller\Account;

use Illuminate\Http\Response;
use Webkul\Category\Repositories\CategoryRepository;
use Webkul\Marketplace\Http\Controllers\Shop\Controller;
use Webkul\Marketplace\Repositories\SellerRepository;

class SellerInfoController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected SellerRepository $sellerRepository,
        protected CategoryRepository $categoryRepository,
    ) {}

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $allowedCategories = [];

        $seller = auth()->guard('seller')->user();

        if ($seller->categories?->categories) {
            $allowedCategories = $this->categoryRepository->findWhereIn('id', $seller->categories->categories);
        }

        $seller->allowed_product_types = $this->sellerRepository->getAllowedProducts($seller);

        return view('marketplace::shop.sellers.account.seller-info.index', ['seller' => $seller, 'allowedCategories' => $allowedCategories]);
    }
}
