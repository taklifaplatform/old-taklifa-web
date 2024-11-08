<?php

namespace Webkul\Marketplace\Http\Controllers\Shop\Seller\Account;

use Webkul\Marketplace\DataGrids\Shop\CustomerDataGrid;
use Webkul\Marketplace\Http\Controllers\Shop\Controller;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return mixed
     */
    public function index()
    {
        if (request()->ajax()) {
            return app(CustomerDataGrid::class)->process();
        }

        return view('marketplace::shop.sellers.account.customers.index');
    }
}
