<?php

namespace Webkul\Marketplace\Http\Controllers\Shop\Seller\Account;

use Webkul\Marketplace\DataGrids\Shop\SellerReviewDataGrid;
use Webkul\Marketplace\Http\Controllers\Shop\Controller;

class SellerReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return mixed
     */
    public function index()
    {
        if (request()->ajax()) {
            return app(SellerReviewDataGrid::class)->process();
        }

        return view('marketplace::shop.sellers.account.seller-reviews.index');
    }
}
