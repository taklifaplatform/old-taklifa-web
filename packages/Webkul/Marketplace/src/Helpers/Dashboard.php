<?php

namespace Webkul\Marketplace\Helpers;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Webkul\Admin\Helpers\Reporting\Visitor;
use Webkul\Marketplace\Helpers\Reporting\Customer;
use Webkul\Marketplace\Helpers\Reporting\Product;
use Webkul\Marketplace\Helpers\Reporting\Sale;
use Webkul\Sales\Models\OrderAddress;

class Dashboard
{
    /**
     * Create a controller instance.
     *
     * @return void
     */
    public function __construct(
        protected Customer $customerReporting,
        protected Product $productReporting,
        protected Sale $saleReporting,
        protected Visitor $visitorReporting
    ) {}

    /**
     * Returns the overall statistics.
     *
     * @param  object  $seller
     */
    public function getOverAllStats($seller): array
    {
        return [
            'total_customers'  => $this->customerReporting->getTotalCustomersProgress($seller),
            'total_orders'     => $this->saleReporting->getTotalOrdersProgress($seller),
            'total_sales'      => $this->saleReporting->getTotalSalesProgress($seller),
            'avg_sales'        => $this->saleReporting->getAverageSalesProgress($seller),
            'total_payout'     => $this->saleReporting->getTotalPayoutProgress($seller),
            'remaining_payout' => $this->saleReporting->getRemainingPayoutProgress($seller),
        ];
    }

    /**
     * Returns the top customers statistics.
     *
     * @param  object  $seller
     * @param  int  $limit
     */
    public function getTopCustomers($seller, $limit = 5): object
    {
        return $this->customerReporting->getCustomersWithMostSales($seller, $limit);
    }

    /**
     * Returns top products statistics.
     */
    public function getTopProducts($seller): object
    {
        return $this->productReporting->getTopSellingProductsByRevenue($seller);
    }

    /**
     * Returns top categories statistics.
     */
    public function getTopCategories($seller): object
    {
        return $this->productReporting->getTopSellingCategoriesByRevenue($seller);
    }

    /**
     * Returns top stock threshold products statistics.
     */
    public function getStockThreshold($seller): object
    {
        return $this->productReporting->getStockThresholdProducts($seller);
    }

    /**
     * Returns sales statistics.
     *
     * @param  object  $seller
     */
    public function getSalesStats($seller): array
    {
        return [
            'total_orders' => $this->saleReporting->getTotalOrdersProgress($seller),
            'total_sales'  => $this->saleReporting->getTotalSalesProgress($seller),
            'over_time'    => $this->saleReporting->getCurrentTotalSalesOverTime($seller),
        ];
    }

    /**
     * Returns visitors statistics.
     */
    public function getVisitorStats(): array
    {
        return [
            'total'     => $this->visitorReporting->getTotalVisitorsProgress(),
            'unique'    => $this->visitorReporting->getTotalUniqueVisitorsProgress(),
            'over_time' => $this->visitorReporting->getCurrentTotalVisitorsOverTime(),
        ];
    }

    /**
     * Get the start date.
     *
     * @return \Carbon\Carbon
     */
    public function getStartDate(): Carbon
    {
        return $this->saleReporting->getStartDate();
    }

    /**
     * Get the end date.
     *
     * @return \Carbon\Carbon
     */
    public function getEndDate(): Carbon
    {
        return $this->saleReporting->getEndDate();
    }

    /**
     * Get the orders.
     *
     * @param  object  $seller
     * @return Collection
     */
    public function getRecentOrders($seller)
    {
        foreach (['pending', 'processing', 'completed', 'refunded'] as $status) {
            $currentOrders = $this->getOrders($seller, $status);

            $orders[$status] = $currentOrders->map(function ($order) {
                $order->payment_method = core()->getConfigData('sales.payment_methods.'.$order->payment_method.'.title');

                $method = explode('_', $order->shipping_method);
                $order->shipping_method = core()->getConfigData('sales.carriers.'.current($method).'.title');

                return $order;
            });
        }

        return $orders;
    }

    /**
     * Get the orders by status.
     *
     * @param  object  $seller
     * @param  string  $status
     * @return Collection
     */
    public function getOrders($seller, $status)
    {
        $table_prefix = DB::getTablePrefix();

        $queryBuilder = DB::table('marketplace_orders')
            ->leftJoin('orders', 'marketplace_orders.order_id', '=', 'orders.id')
            ->leftJoin('marketplace_transactions', 'marketplace_orders.id', '=', 'marketplace_transactions.marketplace_order_id')
            ->leftJoin('addresses as order_address_shipping', function ($leftJoin) {
                $leftJoin->on('order_address_shipping.order_id', '=', 'orders.id')
                    ->where('order_address_shipping.address_type', OrderAddress::ADDRESS_TYPE_SHIPPING);
            })
            ->leftJoin('addresses as order_address_billing', function ($leftJoin) {
                $leftJoin->on('order_address_billing.order_id', '=', 'orders.id')
                    ->where('order_address_billing.address_type', OrderAddress::ADDRESS_TYPE_BILLING);
            })
            ->leftJoin('order_payment', 'orders.id', '=', 'order_payment.order_id')
            ->select(
                'marketplace_orders.*',
                'marketplace_orders.id as marketplace_order_id',
                'order_payment.method as payment_method',
                'orders.increment_id',
                'orders.customer_email',
                'orders.shipping_method',
                'orders.total_item_count',
                'orders.status as order_status',
                'marketplace_transactions.base_total',
                'marketplace_transactions.id as marketplace_transaction_id',
            )
            ->addSelect(
                DB::raw('CONCAT('.$table_prefix.'orders.customer_first_name, " ",'.$table_prefix.'orders.customer_last_name) as customer_name'),
                DB::raw('CONCAT('.$table_prefix.'order_address_billing.city, ", ", '.$table_prefix.'order_address_billing.state,", ", '.$table_prefix.'order_address_billing.country) as location')
            )
            ->where('marketplace_orders.marketplace_seller_id', $seller->seller_id)
            ->orderByDesc('orders.created_at');

        if ($status == 'refunded') {
            $queryBuilder->where('marketplace_orders.seller_payout_status', $status);
        } else {
            $queryBuilder->where('marketplace_orders.status', $status);
        }

        return $queryBuilder->limit(5)->get();
    }

    /**
     * Returns date range
     */
    public function getDateRange(): string
    {
        return $this->getStartDate()->format('d M').' - '.$this->getEndDate()->format('d M');
    }
}
