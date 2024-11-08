<?php

namespace Webkul\Marketplace\Http\Controllers\Shop\Seller\Account;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Illuminate\View\View;
use Webkul\Core\Rules\Slug;
use Webkul\Marketplace\Http\Controllers\Shop\Controller;
use Webkul\Marketplace\Repositories\SellerRepository;

class RegistrationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected SellerRepository $sellerRepository) {}

    /**
     * Opens up the user's sign up form.
     *
     * @return View
     */
    public function index()
    {
        return view('marketplace::shop.default.sellers.account.sign-up');
    }

    /**
     * Method to store user's sign up form data to DB.
     *
     * @return Response
     */
    public function store()
    {
        $this->validate(request(), [
            'name'     => ['required'],
            'email'    => ['required', 'email', 'unique:marketplace_sellers,email'],
            'url'      => ['required', 'unique:marketplace_sellers,url', 'lowercase', new Slug],
            'password' => ['required', 'confirmed', 'min:6'],
        ]);

        Event::dispatch('marketplace.seller.account.create.before');

        $seller = $this->sellerRepository->create(array_merge(request()->only([
            'name',
            'email',
            'url',
            'password',
        ]), [
            'is_approved'           => ! core()->getConfigData('marketplace.settings.general.seller_approval_required'),
            'allowed_product_types' => [
                'simple',
                'configurable',
                'virtual',
                'downloadable',
            ],
        ]));

        Event::dispatch('marketplace.seller.account.create.after', $seller);

        session()->flash('success', trans('marketplace::app.shop.sellers.account.signup.success'));

        return redirect()->route('marketplace.seller.session.index');
    }
}
