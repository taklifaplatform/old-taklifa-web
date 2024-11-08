<?php

namespace Webkul\Marketplace\Http\Controllers\Shop\Seller\Account;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Webkul\Marketplace\Http\Controllers\Shop\Controller;
use Webkul\Marketplace\Http\Requests\SellerFormRequest;
use Webkul\Marketplace\Repositories\SellerRepository;

class ProfileController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected SellerRepository $sellerRepository) {}

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return view('marketplace::shop.sellers.account.profile.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @return Response
     */
    public function update(SellerFormRequest $request, int $id)
    {
        Event::dispatch('marketplace.seller.update.before', $id);

        $seller = $this->sellerRepository->update(array_merge($request->validated(), [
            'address' => implode(PHP_EOL, request('address')),
        ]), $id);

        Event::dispatch('marketplace.seller.update.after', $seller);

        session()->flash('success', trans('marketplace::app.shop.sellers.account.manage-profile.update-success'));

        return back();
    }
}
