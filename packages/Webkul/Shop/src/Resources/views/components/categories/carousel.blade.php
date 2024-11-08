<v-categories-carousel
    src="{{ $src }}"
    title="{{ $title }}"
    navigation-link="{{ $navigationLink ?? '' }}"
>
    <x-shop::shimmer.categories.carousel
        :count="10"
        :navigation-link="$navigationLink ?? false"
    />
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
                            class="img-gradient max-md:w-[70px] w-[90px] max-md:h-[70px] h-[90px] rounded-full p-1"
                            :aria-label="category.name"
                        >
                            <template v-if="category.images.logo_url">
                                <x-shop::media.images.lazy
                                    ::src="category.images.logo_url"
                                    width="100%"
                                    height="100%"
                                    class="max-md:w-[64px] w-[84px] max-md:h-[64px] h-[84px] rounded-full"
                                    ::alt="category.name"
                                    style="z-index: 2;"
                                />
                            </template>
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
                    class="max-md:hidden flex items-center justify-center absolute top-12 left-4 w-[40px] h-[40px] border border-white text-white rounded-full transition icon-arrow-left-stylish text-1xl hover:bg-black hover:text-white cursor-pointer"
                    role="button"
                    aria-label="@lang('shop::components.carousel.previous')"
                    tabindex="0"
                    @click="swipeLeft"
                >
                </span>

                <span
                    class="max-md:hidden flex items-center justify-center absolute top-12 right-4 w-[40px] h-[40px] border border-white text-white rounded-full transition icon-arrow-right-stylish text-1xl hover:bg-black hover:text-white cursor-pointer"
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

                    container.scrollLeft -= this.offset;
                },

                swipeRight() {
                    const container = this.$refs.swiperContainer;

                    container.scrollLeft += this.offset;
                },
            },
        });
    </script>
@endPushOnce
