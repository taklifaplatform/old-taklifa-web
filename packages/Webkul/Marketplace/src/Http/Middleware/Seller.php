<?php

namespace Webkul\Marketplace\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Route;

class Seller
{
    /**
     * Check for restricted routes.
     */
    protected const RESTRICTED_ROUTES = [
        'shop.marketplace.seller.account.profile.index',
        'shop.marketplace.seller.account.profile.update',
        'shop.marketplace.seller.account.roles.index',
        'shop.marketplace.seller.account.roles.create',
        'shop.marketplace.seller.account.roles.store',
        'shop.marketplace.seller.account.roles.edit',
        'shop.marketplace.seller.account.roles.update',
        'shop.marketplace.seller.account.roles.delete',
        'shop.marketplace.seller.account.users.index',
        'shop.marketplace.seller.account.users.store',
        'shop.marketplace.seller.account.users.edit',
        'shop.marketplace.seller.account.users.update',
        'shop.marketplace.seller.account.users.delete',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = 'seller')
    {
        if (! auth()->guard($guard)->check()) {
            return redirect()->route('marketplace.seller.session.index');
        }

        if (! (bool) auth()->guard($guard)->user()->is_approved) {
            auth()->guard($guard)->logout();

            return to_route('marketplace.seller.session.index')
                ->with('warning', trans('marketplace::app.shop.sellers.account.login.not-approved'));
        }

        abort_if($this->isPermissionsEmpty($guard), 401);

        return $next($request);
    }

    /**
     * Check for seller, if they have empty permissions or not except seller.
     */
    public function isPermissionsEmpty($guard): bool
    {
        $user = auth()->guard($guard)->user();

        if (
            (
                ! is_null($user->parent_id)
                && ! $user->role
            )
            || $this->restrictedRoutes($user)
        ) {
            abort(401, 'This action is unauthorized.');
        }

        if (
            ! is_null($user->parent_id)
            && ! $role = $user->role
        ) {
            abort(401, 'This action is unauthorized.');
        }

        if ($this->restrictedRoutes($user)) {
            abort(401, 'This action is unauthorized.');
        }

        if (
            is_null($user->parent_id)
            || $role->permission_type === 'all'
        ) {
            return false;
        }

        if (
            $role->permission_type !== 'all'
            && empty($role->permissions)
        ) {
            return true;
        }

        $this->checkIfAuthorized();

        return false;
    }

    /**
     * Check authorization.
     *
     * @return null
     */
    public function checkIfAuthorized()
    {
        $roles = marketplace_acl()->getRoles();

        if (isset($roles[Route::currentRouteName()])) {
            seller()->allow($roles[Route::currentRouteName()]);
        }
    }

    /**
     * Check for restricted routes.
     */
    public function restrictedRoutes($user): bool
    {
        return in_array(Route::currentRouteName(), self::RESTRICTED_ROUTES) && $user->parent_id;
    }
}
