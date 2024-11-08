<?php

namespace Webkul\Marketplace\Http\Controllers\Shop\Seller\Account;

use Illuminate\Http\Response;
use Webkul\Core\Traits\PDFHandler;
use Webkul\Marketplace\DataGrids\Shop\TransactionDataGrid;
use Webkul\Marketplace\Http\Controllers\Shop\Controller;
use Webkul\Marketplace\Repositories\TransactionRepository;

class TransactionController extends Controller
{
    use PDFHandler;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected TransactionRepository $transactionRepository) {}

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        if (request()->ajax()) {
            return app(TransactionDataGrid::class)->process();
        }

        return view('marketplace::shop.sellers.account.transactions.index')
            ->with([
                'statistics' => $this->transactionRepository->statistics(auth()->guard('seller')->user()),
            ]);
    }

    /**
     * Show the view for the specified resource.
     *
     * @return Response
     */
    public function view(int $id)
    {
        $transaction = $this->transactionRepository->with('order')->findOneWhere([
            'id'                    => $id,
            'marketplace_seller_id' => auth()->guard('seller')->user()->seller_id,
        ]);

        return view('marketplace::shop.sellers.account.transactions.view', compact('transaction'));
    }

    /**
     * Print and download the for the specified resource.
     *
     * @return Response
     */
    public function print(int $id)
    {
        $transaction = $this->transactionRepository->with('order')->findOneWhere([
            'id'                    => $id,
            'marketplace_seller_id' => auth()->guard('seller')->user()->seller_id,
        ]);

        return $this->downloadPDF(
            view('marketplace::shop.sellers.account.transactions.pdf', compact('transaction'))->render(),
            'transaction-'.$transaction->created_at->format('d-m-Y')
        );
    }
}
