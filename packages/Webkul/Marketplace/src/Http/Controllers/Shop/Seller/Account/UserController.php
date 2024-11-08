<?php

namespace Webkul\Marketplace\Http\Controllers\Shop\Seller\Account;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Webkul\Marketplace\DataGrids\Shop\UserDataGrid;
use Webkul\Marketplace\Http\Controllers\Shop\Controller;
use Webkul\Marketplace\Http\Requests\UserFormRequest;
use Webkul\Marketplace\Repositories\RoleRepository;
use Webkul\Marketplace\Repositories\SellerRepository;

class UserController extends Controller
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
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        if (request()->ajax()) {
            return app(UserDataGrid::class)->process();
        }

        $roles = $this->roleRepository
            ->where('marketplace_seller_id', auth()->guard('seller')->user()->seller_id)
            ->get(['id', 'name']);

        return view('marketplace::shop.sellers.account.users.index')
            ->with('roles', $roles);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserFormRequest $request): JsonResponse
    {
        Event::dispatch('marketplace.seller.user.create.before');

        $parent = auth()->guard('seller')->user();

        $seller = $this->sellerRepository->create(array_merge($request->validated(), [
            'is_approved'           => ! core()->getConfigData('marketplace.settings.general.seller_approval_required'),
            'parent_id'             => $parent->id,
            'allowed_product_types' => $parent->allowed_product_types,
        ]));

        Event::dispatch('marketplace.seller.user.create.after', $seller);

        return new JsonResponse([
            'message' => trans('marketplace::app.shop.sellers.account.users.index.create-success'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $seller = $this->sellerRepository->findOrFail($id);

        return new JsonResponse($seller);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserFormRequest $request): JsonResponse
    {
        $parent = auth()->guard('seller')->user();

        Event::dispatch('marketplace.seller.user.update.before', request()->id);

        $data = $request->validated();

        if (empty($data['password'])) {
            $data = Arr::except($data, 'password');
        }

        $seller = $this->sellerRepository->update(array_merge($data, [
            'allowed_product_types' => $parent->allowed_product_types,
        ]), request()->id);

        Event::dispatch('marketplace.seller.user.update.after', $seller);

        return new JsonResponse([
            'message' => trans('marketplace::app.shop.sellers.account.users.index.update-success'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            Event::dispatch('marketplace.seller.user.delete.before', $id);

            $this->sellerRepository->deleteWhere([
                'id'        => $id,
                'parent_id' => auth()->guard('seller')->user()->seller_id,
            ]);

            Event::dispatch('marketplace.seller.user.delete.after', $id);

            return new JsonResponse([
                'message' => trans('marketplace::app.shop.sellers.account.users.index.delete-success'),
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => trans('marketplace::app.shop.sellers.account.users.index.delete-failed'),
            ], 500);
        }
    }
}
