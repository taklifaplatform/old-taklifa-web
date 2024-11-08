<?php

namespace Webkul\Marketplace\ResponseCache\Replacers;

use Spatie\ResponseCache\Replacers\Replacer;
use Symfony\Component\HttpFoundation\Response;
use Webkul\Product\Repositories\ProductRepository;

class SellerInfoReplacer implements Replacer
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected ProductRepository $productRepository) {}

    public function prepareResponseToCache(Response $response): void
    {
        if (! $response->getContent()) {
            return;
        }

        $slugOrPath = urldecode(trim(request()->getPathInfo(), '/'));

        $product = $this->productRepository->findBySlug($slugOrPath);

        if (! $product) {
            return;
        }

        $replacementString = view('marketplace::shop.products.product-sellers', ['product' => $product])->render();

        $response->setContent(preg_replace(
            '/<marketplace-seller-info-here>.*?<\/marketplace-seller-info-here>/s',
            $replacementString,
            $response->getContent()
        ));
    }

    public function replaceInCachedResponse(Response $response): void
    {
        if (! $response->getContent()) {
            return;
        }

        $slugOrPath = urldecode(trim(request()->getPathInfo(), '/'));

        $product = $this->productRepository->findBySlug($slugOrPath);

        if (! $product) {
            return;
        }

        $replacementString = view('marketplace::shop.products.product-sellers', ['product' => $product])->render();

        $response->setContent(preg_replace(
            '/<marketplace-seller-info-here>.*?<\/marketplace-seller-info-here>/s',
            $replacementString,
            $response->getContent()
        ));
    }
}
