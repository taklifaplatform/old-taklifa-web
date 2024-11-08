<?php

namespace Webkul\Marketplace\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Event;
use Webkul\Admin\Http\Requests\MassDestroyRequest;
use Webkul\Admin\Http\Requests\MassUpdateRequest;
use Webkul\Marketplace\DataGrids\Admin\ProductFlagReasonDataGrid;
use Webkul\Marketplace\Repositories\ProductFlagReasonRepository;

class ProductFlagReasonController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected ProductFlagReasonRepository $productFlagReasonRepository) {}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        if (request()->ajax()) {
            return app(ProductFlagReasonDataGrid::class)->process();
        }

        return view('marketplace::admin.product-flag-reasons.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(): JsonResponse
    {
        $this->validate(request(), [
            'reason' => 'required',
        ]);

        Event::dispatch('marketplace.product_flag_reason.create.before');

        $flag = $this->productFlagReasonRepository->create(request()->only([
            'reason',
            'status',
        ]));

        Event::dispatch('marketplace.product_flag_reason.create.after', $flag);

        return new JsonResponse([
            'message' => trans('marketplace::app.admin.product-flag-reasons.index.create.success'),
        ]);
    }

    /**
     * Product Flag Reason Details
     *
     * @param  int  $id
     */
    public function edit($id): JsonResponse
    {
        $flagReason = $this->productFlagReasonRepository->findOrFail($id);

        return new JsonResponse($flagReason);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     */
    public function update(): JsonResponse
    {
        $this->validate(request(), [
            'reason' => 'required',
        ]);

        Event::dispatch('marketplace.product_flag_reason.update.before', $id = request()->id);

        $flag = $this->productFlagReasonRepository->update(request()->only([
            'reason',
            'status',
        ]), $id);

        Event::dispatch('marketplace.product_flag_reason.update.after', $flag);

        return new JsonResponse([
            'message' => trans('marketplace::app.admin.product-flag-reasons.index.edit.success'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     */
    public function destroy($id): JsonResponse
    {
        try {
            Event::dispatch('marketplace.product_flag_reason.delete.before', $id);

            $this->productFlagReasonRepository->delete($id);

            Event::dispatch('marketplace.product_flag_reason.delete.after', $id);

            return new JsonResponse([
                'message' => trans('marketplace::app.admin.product-flag-reasons.index.delete-success'),
            ], 200);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => trans('marketplace::app.admin.product-flag-reasons.index.delete-failed'),
            ], 500);
        }
    }

    /**
     * Mass update the reviews.
     */
    public function massUpdate(MassUpdateRequest $request): JsonResponse
    {
        foreach ($request->input('indices') as $id) {
            Event::dispatch('marketplace.product_flag_reason.update.before', $id);

            $flag = $this->productFlagReasonRepository->update([
                'status' => $request->input('value'),
            ], $id);

            Event::dispatch('marketplace.product_flag_reason.update.after', $flag);
        }

        return new JsonResponse([
            'message' => trans('marketplace::app.admin.product-flag-reasons.index.update-success'),
        ], 200);
    }

    /**
     * Mass delete the flag reasons.
     */
    public function massDestroy(MassDestroyRequest $request): JsonResponse
    {
        foreach ($request->input('indices') as $id) {
            Event::dispatch('marketplace.product_flag_reason.delete.before', $id);

            $this->productFlagReasonRepository->delete($id);

            Event::dispatch('marketplace.product_flag_reason.delete.after', $id);
        }

        return new JsonResponse([
            'message' => trans('marketplace::app.admin.product-flag-reasons.index.delete-success'),
        ]);
    }
}
