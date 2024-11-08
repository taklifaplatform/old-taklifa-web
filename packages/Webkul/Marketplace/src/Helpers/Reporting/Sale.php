<?php

namespace Webkul\Marketplace\Helpers\Reporting;

use Illuminate\Support\Facades\DB;
use Webkul\Admin\Helpers\Reporting\AbstractReporting;
use Webkul\Marketplace\Repositories\OrderRepository;
use Webkul\Marketplace\Repositories\TransactionRepository;

class Sale extends AbstractReporting
{
    /**
     * Create a helper instance.
     *
     * @return void
     */
    public function __construct(
        protected OrderRepository $orderRepository,
        protected TransactionRepository $transactionRepository,
    ) {
        parent::__construct();
    }

    /**
     * Retrieves total orders and their progress.
     *
     * @param  object  $seller
     * @return array
     */
    public function getTotalOrdersProgress($seller)
    {
        return [
            'previous' => $previous = $this->getTotalOrders($seller, $this->lastStartDate, $this->lastEndDate),
            'current'  => $current = $this->getTotalOrders($seller, $this->startDate, $this->endDate),
            'progress' => $this->getPercentageChange($previous, $current),
        ];
    }

    /**
     * Retrieves total orders
     *
     * @param  object  $seller
     * @param  \Carbon\Carbon  $startDate
     * @param  \Carbon\Carbon  $endDate
     */
    public function getTotalOrders($seller, $startDate, $endDate): int
    {
        return $this->orderRepository
            ->where('marketplace_seller_id', $seller->seller_id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
    }

    /**
     * Retrieves total sales and their progress.
     *
     * @param  object  $seller
     */
    public function getTotalSalesProgress($seller): array
    {
        return [
            'previous'        => $previous = $this->getTotalSales($seller, $this->lastStartDate, $this->lastEndDate),
            'current'         => $current = $this->getTotalSales($seller, $this->startDate, $this->endDate),
            'formatted_total' => core()->formatBasePrice($current),
            'progress'        => $this->getPercentageChange($previous, $current),
        ];
    }

    /**
     * Retrieves total sales
     *
     * @param  object  $seller
     * @param  \Carbon\Carbon  $startDate
     * @param  \Carbon\Carbon  $endDate
     */
    public function getTotalSales($seller, $startDate, $endDate): float
    {
        return $this->orderRepository
            ->where('marketplace_seller_id', $seller->seller_id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum(DB::raw('base_sub_total_invoiced - base_sub_total_refunded + base_tax_amount_invoiced - base_tax_amount_refunded'));
    }

    /**
     * Returns current sales over time
     *
     * @param  object  $seller
     * @param  string  $period
     * @param  bool  $includeEmpty
     */
    public function getCurrentTotalSalesOverTime($seller, $period = 'auto', $includeEmpty = true): array
    {
        return $this->getTotalSalesOverTime($seller, $this->startDate, $this->endDate, $period, $includeEmpty);
    }

    /**
     * Returns sales over time
     *
     * @param  object  $seller
     * @param  \Carbon\Carbon  $startDate
     * @param  \Carbon\Carbon  $endDate
     * @param  string  $period
     * @param  bool  $includeEmpty
     */
    public function getTotalSalesOverTime($seller, $startDate, $endDate, $period, $includeEmpty): array
    {
        return $this->getOverTimeStats(
            $seller,
            $startDate,
            $endDate,
            'SUM(base_sub_total_invoiced - base_sub_total_refunded)',
            $period
        );
    }

    /**
     * Retrieves average sales and their progress.
     *
     * @param  object  $seller
     */
    public function getAverageSalesProgress($seller): array
    {
        return [
            'previous'        => $previous = $this->getAverageSales($seller, $this->lastStartDate, $this->lastEndDate),
            'current'         => $current = $this->getAverageSales($seller, $this->startDate, $this->endDate),
            'formatted_total' => core()->formatBasePrice($current),
            'progress'        => $this->getPercentageChange($previous, $current),
        ];
    }

    /**
     * Retrieves average sales
     *
     * @param  object  $seller
     * @param  \Carbon\Carbon  $startDate
     * @param  \Carbon\Carbon  $endDate
     * @return array
     */
    public function getAverageSales($seller, $startDate, $endDate): ?float
    {
        return $this->orderRepository
            ->where('marketplace_seller_id', $seller->seller_id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->avg(DB::raw('base_sub_total_invoiced - base_sub_total_refunded + base_tax_amount_invoiced - base_tax_amount_refunded'));
    }

    /**
     * Retrieves seller total payout progress.
     *
     * @param  object  $seller
     */
    public function getTotalPayoutProgress($seller): array
    {
        return [
            'previous'        => $this->getTotalPayout($seller, $this->lastStartDate, $this->lastEndDate),
            'current'         => $current = $this->getTotalPayout($seller, $this->startDate, $this->endDate),
            'formatted_total' => core()->formatBasePrice($current),
            'percent'         => $this->getAverageTotalPayout($seller, $this->startDate, $this->endDate),
        ];
    }

    /**
     * Retrieves seller remaining payout progress.
     *
     * @param  object  $seller
     */
    public function getRemainingPayoutProgress($seller): array
    {
        return [
            'previous'        => $this->getRemainingPayout($seller, $this->lastStartDate, $this->lastEndDate),
            'current'         => $current = $this->getRemainingPayout($seller, $this->startDate, $this->endDate),
            'formatted_total' => core()->formatBasePrice($current),
            'percent'         => $this->getAverageRemainingPayout($seller, $this->startDate, $this->endDate),
        ];
    }

    /**
     * Retrieves seller remaining payout.
     *
     * @param  object  $seller
     * @param  \Carbon\Carbon  $startDate
     * @param  \Carbon\Carbon  $endDate
     */
    public function getTotalPayout($seller, $startDate, $endDate): ?float
    {
        return $this->transactionRepository
            ->where('marketplace_transactions.marketplace_seller_id', $seller->seller_id)
            ->whereBetween('marketplace_transactions.created_at', [$startDate, $endDate])
            ->sum(DB::raw('base_total'));
    }

    /**
     * Retrieves seller remaining payout.
     *
     * @param  object  $seller
     * @param  \Carbon\Carbon  $startDate
     * @param  \Carbon\Carbon  $endDate
     */
    public function getRemainingPayout($seller, $startDate, $endDate): ?float
    {
        return $this->orderRepository
            ->where('marketplace_seller_id', $seller->seller_id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('seller_payout_status', ['pending', 'requested'])
            ->where('status', 'completed')
            ->sum(DB::raw('base_sub_total_invoiced - base_sub_total_refunded + base_tax_amount_invoiced - base_tax_amount_refunded'));
    }

    /**
     * Retrieves average total payout.
     *
     * @param  object  $seller
     * @param  \Carbon\Carbon  $startDate
     * @param  \Carbon\Carbon  $endDate
     */
    public function getAverageTotalPayout($seller, $startDate, $endDate): ?float
    {
        if (! $this->getRemainingPayout($seller, $startDate, $endDate)
            || ! $this->getTotalPayout($seller, $startDate, $endDate)
        ) {
            return 0;
        }

        return $this->getTotalPayout($seller, $startDate, $endDate) * 100 /
        ($this->getTotalPayout($seller, $startDate, $endDate) + $this->getRemainingPayout($seller, $startDate, $endDate));
    }

    /**
     * Retrieves average remaining payout.
     *
     * @param  object  $seller
     * @param  \Carbon\Carbon  $startDate
     * @param  \Carbon\Carbon  $endDate
     */
    public function getAverageRemainingPayout($seller, $startDate, $endDate): ?float
    {
        if (! $this->getRemainingPayout($seller, $startDate, $endDate)
            || ! $this->getTotalPayout($seller, $startDate, $endDate)
        ) {
            return 0;
        }

        return $this->getRemainingPayout($seller, $startDate, $endDate) * 100 /
        ($this->getTotalPayout($seller, $startDate, $endDate) + $this->getRemainingPayout($seller, $startDate, $endDate));
    }

    /**
     * Returns over time stats.
     *
     * @param  object  $seller
     * @param  \Carbon\Carbon  $startDate
     * @param  \Carbon\Carbon  $endDate
     * @param  string  $valueColumn
     * @param  string  $period
     */
    public function getOverTimeStats($seller, $startDate, $endDate, $valueColumn, $period = 'auto'): array
    {
        $config = $this->getTimeInterval($startDate, $endDate, $period);

        $groupColumn = $config['group_column'];

        $results = $this->orderRepository
            ->select(
                DB::raw("$groupColumn AS date"),
                DB::raw("$valueColumn AS total"),
                DB::raw('COUNT(*) AS count')
            )
            ->where('marketplace_seller_id', $seller->seller_id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->get();

        foreach ($config['intervals'] as $interval) {
            $total = $results->where('date', $interval['filter'])->first();

            $stats[] = [
                'label' => $interval['start'],
                'total' => $total?->total ?? 0,
                'count' => $total?->count ?? 0,
            ];
        }

        return $stats;
    }
}
