<v-categories-carousel src="{{ $src }}" title="{{ $title }}" navigation-link="{{ $navigationLink ?? '' }}">
    <x-shop::shimmer.categories.carousel :count="10" :navigation-link="$navigationLink ?? false" />
</v-categories-carousel>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-categories-carousel-template"
    >
        <div
            class="bg-[#0F5837] max-md:px-0 max-lg:px-8 py-6"
            v-if="! isLoading && categories?.length"
        >
            <div class="relative max-md:px-0 px-16">
                <div
                    ref="swiperContainer"
                    class="flex max-md:gap-1 gap-2 overflow-auto scroll-smooth scrollbar-hide"
                >
                    <div
                    class="grid grid-cols-1 max-md:gap-1 gap-2 justify-items-center max-md:min-w-[80px] min-w-[100px] max-md:max-w-[80px] max-w-[100px] font-medium"
                        v-for="category in categories"
                    >
                        <a
                            :href="category.slug"
                            class="max-md:w-[70px] w-[90px] max-md:h-[70px] h-[90px] rounded-full p-1"
                            :aria-label="category.name"
                        >
                            <x-shop::media.images.lazy
                                ::src="category.logo?.large_image_url || '{{ bagisto_asset('images/small-product-placeholder.webp') }}'"
                                 width="100%"
                                    height="100%"
                                    class="max-md:w-[64px] w-[84px] max-md:h-[64px] h-[84px] rounded-full"

                                ::alt="category.name"
                                 style="z-index: 2;"
                            />
                        </a>

                        <a
                            :href="category.slug"
                            class=""
                        >
                        <p
                        class="text-center text-white text-sm max-sm:font-normal"
                        v-text="category.name"
                    >
                    </p>
                        </a>
                    </div>
                </div>

                <span
                    class="icon-arrow-left-stylish"
                    role="button"
                    aria-label="@lang('shop::components.carousel.previous')"
                    tabindex="0"
                    @click="swipeLeft"
                >
                </span>

                <span
                    class="icon-arrow-right-stylish"
                    role="button"
                    aria-label="@lang('shop::components.carousel.next')"
                    tabindex="0"
                    @click="swipeRight"
                >
                </span>
            </div>
        </div>

        <!-- Category Carousel Shimmer -->
        <template v-if="isLoading">
            <x-shop::shimmer.categories.carousel
                :count="8"
                :navigation-link="$navigationLink ?? false"
            />
        </template>
    </script>

    <script type="module">
        app.component('v-categories-carousel', {
            template: '#v-categories-carousel-template',

            props: [
                'src',
                'title',
                'navigationLink',
            ],

            data() {
                return {
                    isLoading: true,

                    categories: [],

                    offset: 323,
                };
            },

            mounted() {
                this.getCategories();
            },

            methods: {
                getCategories() {
                    this.$axios.get(this.src)
                        .then(response => {
                            this.isLoading = false;

                            this.categories = response.data.data;
                        }).catch(error => {
                            console.log(error);
                        });
                },

                swipeLeft() {
                    const container = this.$refs.swiperContainer;
                    container.scrollBy({
                        left: -this.offset,
                        behavior: 'smooth'
                    });
                },

                swipeRight() {
                    const container = this.$refs.swiperContainer;
                    container.scrollBy({
                        left: this.offset,
                        behavior: 'smooth'
                    });
                },
            },
        });
    </script>

    <style>
        .icon-arrow-left-stylish {
            position: absolute;
            left: 10px;
            /* Adjust as needed to align properly */
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 40px;
            width: 40px;
            border-radius: 50%;
            border: 2px solid #0F5837;
            background-color: white;
            color: #0F5837;
            font-size: 20px;
            transition: all 0.3s;
        }

        .icon-arrow-left-stylish:hover {
            background-color: #0F5837;
            color: white;
        }

        .icon-arrow-right-stylish {
            position: absolute;
            right: 10px;
            /* Adjust as needed to align properly */
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 40px;
            width: 40px;
            border-radius: 50%;
            border: 2px solid #0F5837;
            background-color: white;
            color: #0F5837;
            font-size: 20px;
            transition: all 0.3s;
        }

        .icon-arrow-right-stylish:hover {
            background-color: #0F5837;
            color: white;
        }
    </style>
@endPushOnce
