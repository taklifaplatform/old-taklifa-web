<?php

namespace Webkul\Marketplace\Http\Controllers\Shop\Seller\Account;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Illuminate\View\View;
use Webkul\Marketplace\Http\Controllers\Shop\Controller;

class SessionController extends Controller
{
    /**
     * Display the resource.
     *
     * @return RedirectResponse|View
     */
    public function show()
    {
        return auth()->guard('seller')->check()
            ? redirect()->route('shop.marketplace.seller.account.dashboard.index')
            : view('marketplace::shop.default.sellers.account.sign-in');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        if (! auth()->guard('seller')->attempt(request()->only(['email', 'password']))) {
            session()->flash('error', trans('marketplace::app.shop.sellers.account.login.invalid-credentials'));

            return redirect()->back();
        }

        if (! auth()->guard('seller')->user()->is_approved) {
            session()->flash('info', trans('marketplace::app.shop.sellers.account.login.not-approved'));

            auth()->guard('seller')->logout();

            return redirect()->back();
        }

        return redirect()->route('shop.marketplace.seller.account.dashboard.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return Response
     */
    public function destroy()
    {
        Event::dispatch('marketplace.seller.account.logout.before');

        auth()->guard('seller')->logout();

        Event::dispatch('marketplace.seller.account.logout.after');

        return redirect()->route('marketplace.seller.session.index');
    }
}
