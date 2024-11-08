<?php

namespace Webkul\Marketplace\Http\Controllers\Admin;

use Webkul\Marketplace\DataGrids\Admin\TransactionDataGrid;
use Webkul\Marketplace\Repositories\TransactionRepository;

class TransactionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected TransactionRepository $transactionRepository) {}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            return app(TransactionDataGrid::class)->process();
        }

        return view('marketplace::admin.transactions.index');
    }

    /**
     * Show the view for the specified resource.
     *
     * @return \Illuminate\View\View
     */
    public function view(int $id)
    {
        $transaction = $this->transactionRepository->with('order')->find($id);

        return view('marketplace::admin.transactions.view', compact('transaction'));
    }
}
