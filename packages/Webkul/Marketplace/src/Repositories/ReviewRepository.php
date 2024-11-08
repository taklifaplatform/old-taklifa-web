<?php

namespace Webkul\Marketplace\Repositories;

use Illuminate\Support\Facades\DB;
use Webkul\Core\Eloquent\Repository;
use Webkul\Marketplace\Contracts\Review;

class ReviewRepository extends Repository
{
    /**
     * Specify Model class name
     */
    public function model(): string
    {
        return Review::class;
    }

    /**
     * @param  int  $categoryId
     * @return Collection
     */
    public function getRecentReviews($sellerId = null)
    {
        return $this->with('customer')->scopeQuery(function ($query) use ($sellerId) {
            return $query->distinct()
                ->where('marketplace_seller_id', $sellerId)
                ->where('status', 'approved')
                ->orderBy('id', 'desc');
        })->limit(5);
    }

    /**
     * Returns the seller's avg rating
     *
     * @param  Seller  $seller
     * @return float
     */
    public function getReviews($seller)
    {
        return $seller->reviews()->where('status', 'approved');
    }

    /**
     * Returns the seller's avg rating
     *
     * @param  Seller  $seller
     * @return float
     */
    public function getAverageRating($seller)
    {
        return number_format(round($seller->reviews()->where('status', 'approved')->average('rating'), 2), 1);
    }

    /**
     * Returns the total review of the seller
     *
     * @param  Seller  $seller
     * @return int
     */
    public function getTotalReviews($seller)
    {
        return $seller->reviews()->where('status', 'approved')->count();
    }

    /**
     * Returns the total rating of the seller
     *
     * @param  Seller  $seller
     * @return int
     */
    public function getTotalRating($seller)
    {
        return $seller->reviews()->where('status', 'approved')->sum('rating');
    }

    /**
     * Returns the Percentage rating of the seller
     *
     * @param  Seller  $seller
     * @return int
     */
    public function getPercentageRating($seller)
    {
        $reviews = $seller->reviews()->where('status', 'approved')
            ->select('rating', DB::raw('count(*) as total'))
            ->groupBy('rating')
            ->orderBy('rating', 'desc')
            ->get();

        for ($i = 5; $i >= 1; $i--) {
            if ($reviews->isEmpty()) {
                $percentage[$i] = 0;

                continue;
            }

            foreach ($reviews as $review) {
                if ($review->rating != $i) {
                    $percentage[$i] = 0;

                    continue;
                }

                $percentage[$i] = round(($review->total / $this->getTotalReviews($seller)) * 100);

                break;
            }
        }

        return $percentage;
    }
}
