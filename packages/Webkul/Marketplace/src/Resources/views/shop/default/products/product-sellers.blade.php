<marketplace-seller-info-here>
    @inject ('reviewRepository', 'Webkul\Marketplace\Repositories\ReviewRepository')
    @inject ('productRepository', 'Webkul\Marketplace\Repositories\ProductRepository')

    @php
        $sellerProduct = $productRepository->with('seller')->findOneWhere([
            'product_id' => $product->id,
            'is_owner'   => 1,
        ]);

        if ($sellerProduct) {
            $avgRatings = $reviewRepository->getAverageRating($sellerProduct->seller);
        }
    @endphp

    <div class="mt-8 grid">
        @if ($sellerProduct)
            <div class="text-lg font-medium text-[#757575]">
                @lang('marketplace::app.shop.products.sold-by')
            </div>

            <a
                class="text-lg font-semibold text-navyBlue"
                href="{{ route('marketplace.seller.show', $sellerProduct->seller->url) }}"
            >
                {{ $sellerProduct->seller->shop_title }}
            </a>

            <x-marketplace::shop.products.star-rating
                name="Seller Rating"
                :value="$avgRatings"
            />
        @endif

        @if ($product->type != 'configurable')
            @if ($count = $productRepository->getSellerCount($product))
                <a
                    class="text-lg font-normal text-navyBlue"
                    href="{{ route('marketplace.product.offers.index', $product->id) }}"
                >
                    @lang('marketplace::app.shop.products.seller-count', ['count' => $count])
                </a>
            @endif
        @else
            @php
                $variants = [];

                foreach ($product->variants as $variant) {
                    $variants[$variant->id] = $productRepository->getSellerCount($variant);
                }
            @endphp

            <v-other-seller></v-other-seller>        

            @pushOnce('scripts')
                <script type="text/x-template" id="v-other-seller-template">
                    <a
                        class="text-lg font-normal text-navyBlue"
                        :href="actionUrl"
                        v-if="visible"
                    >
                        @{{ "@lang('marketplace::app.shop.products.seller-count')".replace(':count', count) }}
                    </a>
                </script>
                
                <script type="module">
                    app.component('v-other-seller', {
                        template: '#v-other-seller-template',
        
                        data() {
                            return {
                                visible: false,
                                variants: @json($variants),
                                actionUrl: '',
                                count: 0
                            }
                        },

                        created() {
                            this.listenEvents();
                        },
        
                        methods: {
                            listenEvents(key) {
                                this.$emitter.on('configurable-variant-selected-event', (variantId) => {
                                    if (this.variants[variantId]) {
                                        let url = "{{ route('marketplace.product.offers.index', ':product_id') }}";
                                        
                                        this.actionUrl = url.replace(':product_id', variantId);

                                        this.count = this.variants[variantId];
                                        
                                        this.visible = true;
                                    } else {
                                        this.visible = false;
                                    }
                                });
                            }
                        }
                    });
                </script>
            @endPushOnce
        @endif
    </div>
</marketplace-seller-info-here>
