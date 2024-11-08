<?php

namespace Webkul\Marketplace\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Webkul\Admin\Http\Requests\MassDestroyRequest;
use Webkul\Admin\Http\Requests\MassUpdateRequest;
use Webkul\Marketplace\DataGrids\Admin\ProductDataGrid;
use Webkul\Marketplace\Mail\ProductApprovalNotification;
use Webkul\Marketplace\Repositories\ProductRepository;
use Webkul\Product\Repositories\ProductRepository as BaseProductRepository;
use Webkul\Marketplace\DataGrids\Admin\ProductFlagDataGrid;

class ProductController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected BaseProductRepository $baseProductRepository,
        protected ProductRepository $productRepository
    ) {}

    /**
     * Display a listing of the resource.
     *
     * @return mixed
     */
    public function index()
    {
        if (request()->ajax()) {
            return app(ProductDataGrid::class)->process();
        }

        return view('marketplace::admin.products.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $product = $this->productRepository->findOrFail($id);

        Event::dispatch('marketplace.product.delete.before', $id);

        if ($product->is_owner) {
            Event::dispatch('catalog.product.delete.before', $product->product_id);

            $this->baseProductRepository->delete($product->product_id);

            Event::dispatch('catalog.product.delete.after', $product->product_id);
        } else {
            $this->productRepository->delete($id);
        }

        Event::dispatch('marketplace.product.delete.after', $id);

        return new JsonResponse([
            'message' => trans('marketplace::app.admin.products.index.delete-success'),
        ], 200);
    }

    /**
     * Mass delete the product.
     */
    public function massDestroy(MassDestroyRequest $request): JsonResponse
    {
        try {
            foreach ($request->input('indices') as $id) {
                $product = $this->productRepository->find($id);

                if (! $product) {
                    continue;
                }

                Event::dispatch('marketplace.product.delete.before', $id);

                if ($product->is_owner) {
                    Event::dispatch('catalog.product.delete.before', $product->product_id);

                    $this->baseProductRepository->delete($product->product_id);

                    Event::dispatch('catalog.product.delete.after', $product->product_id);
                } else {
                    $this->productRepository->delete($id);
                }

                Event::dispatch('marketplace.product.delete.after', $id);
            }

            return new JsonResponse([
                'message' => trans('marketplace::app.admin.products.index.delete-success'),
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mass update the reviews.
     */
    public function massUpdate(MassUpdateRequest $request): JsonResponse
    {
        $products = $this->productRepository
            ->with('seller')
            ->whereIn('id', $request->input('indices'))
            ->get();

        foreach ($products as $product) {
            if (! $product->seller->is_approved) {
                continue;
            }

            Event::dispatch('marketplace.product.update.before', $product);

            $product->is_approved = $request->input('value');

            $product->save();

            try {
                Mail::queue(new ProductApprovalNotification($product));
            } catch (\Exception $e) {
            }

            Event::dispatch('marketplace.product.update.after', $product);
        }

        return new JsonResponse([
            'message' => trans('marketplace::app.admin.products.index.update-success'),
        ], 200);
    }

    /**
     * Display the product flags on product edit page(admin).
     */
    public function flags(int $id): JsonResponse
    {
        request()->merge(['product_id' => $id]);

        if (request()->ajax()) {
            return datagrid(ProductFlagDataGrid::class)->process();
        }
    }
}
