<?php

namespace Webkul\Marketplace\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Event;
use Webkul\Admin\Http\Requests\MassDestroyRequest;
use Webkul\Admin\Http\Requests\MassUpdateRequest;
use Webkul\Marketplace\DataGrids\Admin\SellerFlagReasonDataGrid;
use Webkul\Marketplace\Repositories\SellerFlagReasonRepository;

class SellerFlagReasonController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected SellerFlagReasonRepository $sellerFlagReasonRepository) {}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        if (request()->ajax()) {
            return app(SellerFlagReasonDataGrid::class)->process();
        }

        return view('marketplace::admin.seller-flag-reasons.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(): JsonResponse
    {
        $this->validate(request(), [
            'reason' => 'required',
        ]);

        Event::dispatch('marketplace.seller.flag_reason.create.before');

        $flag = $this->sellerFlagReasonRepository->create(request()->only([
            'reason',
            'status',
        ]));

        Event::dispatch('marketplace.seller.flag_reason.create.after', $flag);

        return new JsonResponse([
            'message' => trans('marketplace::app.admin.seller-flag-reasons.index.create.success'),
        ]);
    }

    /**
     * Seller Flag Reason Details
     */
    public function edit(int $id): JsonResponse
    {
        $flagReason = $this->sellerFlagReasonRepository->findOrFail($id);

        return new JsonResponse($flagReason);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(): JsonResponse
    {
        $this->validate(request(), [
            'reason' => 'required',
        ]);

        Event::dispatch('marketplace.seller.flag_reason.update.before', $id = request()->id);

        $flag = $this->sellerFlagReasonRepository->update(request()->only([
            'reason',
            'status',
        ]), $id);

        Event::dispatch('marketplace.seller.flag_reason.update.after', $flag);

        return new JsonResponse([
            'message' => trans('marketplace::app.admin.seller-flag-reasons.index.edit.success'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            Event::dispatch('marketplace.seller.flag_reason.delete.before', $id);

            $this->sellerFlagReasonRepository->delete($id);

            Event::dispatch('marketplace.seller.flag_reason.delete.after', $id);

            return new JsonResponse([
                'message' => trans('marketplace::app.admin.seller-flag-reasons.index.delete-success'),
            ], 200);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => trans('marketplace::app.admin.seller-flag-reasons.index.delete-failed'),
            ], 500);
        }
    }

    /**
     * Mass update the reviews.
     */
    public function massUpdate(MassUpdateRequest $request): JsonResponse
    {
        foreach ($request->input('indices') as $id) {
            Event::dispatch('marketplace.seller.flag_reason.update.before', $id);

            $flag = $this->sellerFlagReasonRepository->update([
                'status' => $request->input('value'),
            ], $id);

            Event::dispatch('marketplace.seller.flag_reason.update.after', $flag);
        }

        return new JsonResponse([
            'message' => trans('marketplace::app.admin.seller-flag-reasons.index.update-success'),
        ], 200);
    }

    /**
     * Mass delete the flag reasons.
     */
    public function massDestroy(MassDestroyRequest $request): JsonResponse
    {
        foreach ($request->input('indices') as $id) {
            Event::dispatch('marketplace.seller.flag_reason.delete.before', $id);

            $this->sellerFlagReasonRepository->delete($id);

            Event::dispatch('marketplace.seller.flag_reason.delete.after', $id);
        }

        return new JsonResponse([
            'message' => trans('marketplace::app.admin.seller-flag-reasons.index.delete-success'),
        ]);
    }
}
