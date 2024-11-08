<?php

namespace Webkul\Marketplace\Http\Controllers\Shop\Seller\Account;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Event;
use Illuminate\View\View;
use Webkul\Marketplace\DataGrids\Shop\RoleDataGrid;
use Webkul\Marketplace\Http\Controllers\Shop\Controller;
use Webkul\Marketplace\Repositories\RoleRepository;
use Webkul\Marketplace\Repositories\SellerRepository;

class RoleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected RoleRepository $roleRepository,
        protected SellerRepository $sellerRepository
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            return datagrid(RoleDataGrid::class)->process();
        }

        return view('marketplace::shop.sellers.account.roles.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('marketplace::shop.sellers.account.roles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(): RedirectResponse
    {
        $this->validate(request(), [
            'name'            => 'required',
            'permission_type' => 'required',
            'description'     => 'required',
            'permissions'     => 'required_if:permission_type,custom|array|min:1',
        ]);

        Event::dispatch('marketplace.seller.account.role.create.before');

        $role = $this->roleRepository->create(array_merge(request()->only([
            'name',
            'description',
            'permission_type',
            'permissions',
        ]), [
            'marketplace_seller_id' => auth()->guard('seller')->user()->seller_id,
        ]));

        Event::dispatch('marketplace.seller.account.role.create.after', $role);

        return to_route('shop.marketplace.seller.account.roles.index')
            ->with('success', trans('marketplace::app.shop.sellers.account.roles.create-success'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $role = $this->roleRepository->findOrFail($id);

        return view('marketplace::shop.sellers.account.roles.edit')
            ->with('role', $role);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(int $id): RedirectResponse
    {
        $this->validate(request(), [
            'name'            => 'required',
            'permission_type' => 'required|in:all,custom',
            'description'     => 'required',
            'permissions'     => 'required_if:permission_type,custom|array|min:1',
        ]);

        /**
         * Check for other sellers if the role has been changed from all to custom.
         */
        $isChangedFromAll = request('permission_type') == 'custom' && $this->roleRepository->find($id)->permission_type == 'all';

        if (
            $isChangedFromAll
            && $this->sellerRepository->countSellersWithAllAccess(auth()->guard('seller')->user()) === 1
        ) {
            return to_route('shop.marketplace.seller.account.roles.index')
                ->with('error', trans('marketplace::app.shop.sellers.account.roles.being-used'));
        }

        Event::dispatch('marketplace.seller.account.role.update.before', $id);

        $role = $this->roleRepository->update(request()->only([
            'name',
            'description',
            'permission_type',
            'permissions',
        ]), $id);

        Event::dispatch('marketplace.seller.account.role.update.after', $role);

        return to_route('shop.marketplace.seller.account.roles.index')
            ->with('success', trans('marketplace::app.shop.sellers.account.roles.update-success'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $seller = auth()->guard('seller')->user();

        $role = $this->roleRepository->findOneWhere([
            'id'                    => $id,
            'marketplace_seller_id' => $seller->seller_id,
        ]);

        if ($role?->sellers?->count() >= 1) {
            return new JsonResponse([
                'message' => trans('marketplace::app.shop.sellers.account.roles.being-used'),
            ], 400);
        }

        if ($this->roleRepository->where('marketplace_seller_id', $seller->seller_id)->count() == 1) {
            return new JsonResponse([
                'message' => trans('marketplace::app.shop.sellers.account.roles.last-delete-error'),
            ], 400);
        }

        try {
            Event::dispatch('marketplace.seller.account.role.delete.before', $id);

            $role->delete();

            Event::dispatch('marketplace.seller.account.role.delete.after', $id);

            return new JsonResponse([
                'message' => trans('marketplace::app.shop.sellers.account.roles.delete-success'),
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => trans('marketplace::app.shop.sellers.account.roles.delete-failed'),
            ], 500);
        }
    }
}
