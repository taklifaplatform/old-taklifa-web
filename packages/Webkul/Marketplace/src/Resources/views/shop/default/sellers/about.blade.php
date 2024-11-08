<!-- About Seller Vue Component -->
<v-about-seller />

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-about-seller-template"
    >
        <div class="mt-3 grid md:flex">
            <div class="hidden min-w-[246px] content-baseline gap-y-2 md:grid">
                {!! view_render_event('bagisto.shop.marketplace.seller.profile.about.tabs.before', ['seller' => $seller]) !!}
                
                <span
                    class="cursor-pointer pb-2 text-lg"
                    :class="{ 'font-medium': activeTab == 'about_us' }"
                    @click="changeTab('about_us')"
                >
                    @lang('marketplace::app.shop.sellers.profile.about-us')
                </span>

                <span
                    class="cursor-pointer py-2 text-lg"
                    :class="{ 'font-medium': activeTab == 'shipping_policy' }"
                    @click="changeTab('shipping_policy')"
                >
                    @lang('marketplace::app.shop.sellers.profile.shipping-policy')
                </span>

                <span
                    class="cursor-pointer py-2 text-lg"
                    :class="{ 'font-medium': activeTab == 'return_policy' }"
                    @click="changeTab('return_policy')"
                >
                    @lang('marketplace::app.shop.sellers.profile.return-policy')
                </span>

                <span
                    class="cursor-pointer py-2 text-lg"
                    :class="{ 'font-medium': activeTab == 'privacy_policy' }"
                    @click="changeTab('privacy_policy')"
                >
                    @lang('marketplace::app.shop.sellers.profile.privacy-policy')
                </span>

                {!! view_render_event('bagisto.shop.marketplace.seller.profile.about.tabs.after', ['seller' => $seller]) !!}
            </div>

            <div class="flex w-[768px] flex-col gap-y-16 max-1180:hidden">
                {!! view_render_event('bagisto.shop.marketplace.seller.profile.about.tab_content.before', ['seller' => $seller]) !!}
                
                <div id="about_us">
                    <h2 class="text-2xl font-medium">
                        @lang('marketplace::app.shop.sellers.profile.about-us')
                    </h2>
            
                    <p class="mt-3 text-base font-normal">
                        {!! $seller->description !!}
                    </p>
                </div>
                
                <div id="shipping_policy">
                    <h2 class="text-2xl font-medium">
                        @lang('marketplace::app.shop.sellers.profile.shipping-policy')
                    </h2>
            
                    <p class="mt-3 text-base font-normal">
                        {!! $seller->shipping_policy !!}
                    </p>
                </div>
            
                <div id="return_policy">
                    <h2 class="text-2xl font-medium">
                        @lang('marketplace::app.shop.sellers.profile.return-policy')
                    </h2>
            
                    <p class="mt-3 text-base font-normal">
                        {!! $seller->return_policy !!}
                    </p>
                </div>
            
                <div id="privacy_policy">
                    <h2 class="text-2xl font-medium">
                        @lang('marketplace::app.shop.sellers.profile.privacy-policy')
                    </h2>
            
                    <p class="mt-3 text-base font-normal">
                        {!! $seller->privacy_policy !!}
                    </p>
                </div>

                {!! view_render_event('bagisto.shop.marketplace.seller.profile.about.tab_content.after', ['seller' => $seller]) !!}
            </div>
            
            <div class="grid gap-y-8 1180:hidden">
                {!! view_render_event('bagisto.shop.marketplace.seller.profile.about.tab_content.accordion.before', ['seller' => $seller]) !!}
                
                <!-- About Us Tab -->
                <x-shop::accordion :is-active="true">
                    <x-slot:header>
                        <h2 class="text-2xl font-medium">
                            @lang('marketplace::app.shop.sellers.profile.about-us')
                        </h2>
                    </x-slot:header>
            
                    <x-slot:content>
                        <p class="mt-3 text-base font-normal">
                            {!! $seller->description !!}
                        </p>
                    </x-slot:content>
                </x-shop::accordion>
            
                <!-- Shipping Policy Tab -->
                <x-shop::accordion :is-active="false">
                    <x-slot:header>
                        <h2 class="text-2xl font-medium">
                            @lang('marketplace::app.shop.sellers.profile.shipping-policy')
                        </h2>
                    </x-slot:header>
            
                    <x-slot:content>
                        <p class="mt-3 text-base font-normal">
                            {!! $seller->shipping_policy !!}
                        </p>
                    </x-slot:content>
                </x-shop::accordion>
            
                <!-- Return Policy Tab -->
                <x-shop::accordion :is-active="false">
                    <x-slot:header>
                        <h2 class="text-2xl font-medium">
                            @lang('marketplace::app.shop.sellers.profile.return-policy')
                        </h2>
                    </x-slot:header>
            
                    <x-slot:content>
                        <p class="mt-3 text-base font-normal">
                            {!! $seller->return_policy !!}
                        </p>
                    </x-slot:content>
                </x-shop::accordion>
            
                <!-- Privacy Policy Tab -->
                <x-shop::accordion :is-active="false">
                    <x-slot:header>
                        <h2 class="text-2xl font-medium">
                            @lang('marketplace::app.shop.sellers.profile.privacy-policy')
                        </h2>
                    </x-slot:header>
            
                    <x-slot:content>
                        <p class="mt-3 text-base font-normal">
                            {!! $seller->privacy_policy !!}
                        </p>
                    </x-slot:content>
                </x-shop::accordion>

                {!! view_render_event('bagisto.shop.marketplace.seller.profile.about.tab_content.accordion.after', ['seller' => $seller]) !!}
            </div>    
        </div>
    </script>

    <script type="module">
        app.component('v-about-seller', {
            template: '#v-about-seller-template',

            data() {
                return {
                    activeTab: 'about_us',
                }
            },

            methods: {
                changeTab(tab) {
                    this.activeTab = tab;

                    const element = document.getElementById(tab);

                    element.scrollIntoView({
                        behavior: "smooth",
                        block: "center",
                    });
                }
            }
        });
    </script>
@endPushOnce