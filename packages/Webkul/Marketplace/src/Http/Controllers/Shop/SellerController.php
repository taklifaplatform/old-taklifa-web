<?php

namespace Webkul\Marketplace\Http\Controllers\Shop;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Webkul\Marketplace\Mail\ContactSellerNotification;
use Webkul\Marketplace\Repositories\SellerRepository;

class SellerController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param  Webkul\Marketplace\Repositories\SellerRepository  $seller
     * @return void
     */
    public function __construct(protected SellerRepository $sellerRepository) {}

    /**
     * Display the specified resource.
     *
     * @return mixed
     */
    public function show(string $url)
    {
        $seller = $this->sellerRepository->findByUrlOrFail($url);

        if (! $seller->is_approved) {
            session()->flash('warning', trans('marketplace::app.shop.sellers.profile.not-approved'));

            return Back();
        }

        return view('marketplace::shop.sellers.profile', compact('seller'));
    }

    /**
     * Send query email to seller
     */
    public function contact(): JsonResponse
    {
        $this->validate(request(), [
            'name'    => 'required',
            'email'   => 'required',
            'subject' => 'required',
            'query'   => 'required',
        ]);

        if (! auth()->guard('customer')->user()) {
            return new JsonResponse([
                'message' => trans('marketplace::app.shop.sellers.profile.login-first'),
            ]);
        }

        $seller = $this->sellerRepository->findByUrlOrFail(request()->input('seller_url'));

        try {
            Mail::queue(new ContactSellerNotification($seller, request()->input()));
        } catch (\Exception $e) {
        }

        return new JsonResponse([
            'message' => trans('marketplace::app.shop.sellers.profile.message-success'),
        ]);
    }
}
