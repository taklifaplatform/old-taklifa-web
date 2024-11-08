<!-- SEO Meta Content -->
@push('meta')
    <meta name="description" content="@lang('marketplace::app.shop.sellers.account.forgot-password.title')"/>

    <meta name="keywords" content="@lang('marketplace::app.shop.sellers.account.forgot-password.title')"/>
@endPush

<x-marketplace::shop.layouts.full
    :has-header="false"
    :has-feature="false"
    :has-footer="false"
>
    <!-- Page Title -->
    <x-slot:title>
        @lang('marketplace::app.shop.sellers.account.forgot-password.title')
    </x-slot>

    <div class="container mt-20 max-1180:px-5">
        {!! view_render_event('marketplace.seller.account.forgot_password.logo.before') !!}
        
        <!-- Company Logo -->
        <div class="flex items-center gap-x-14 max-[1180px]:gap-x-9">
            <a
                href="{{ route('shop.home.index') }}"
                class="m-[0_auto_20px_auto]"
                aria-label="@lang('marketplace::app.shop.sellers.account.forgot-password.bagisto')"
            >
                <img
                    src="{{ core()->getCurrentChannel()->logo_url ?? bagisto_asset('images/logo.svg') }}"
                    alt="{{ config('app.name') }}"
                    width="131"
                    height="29"
                >
            </a>
        </div>

        {!! view_render_event('marketplace.seller.account.forgot_password.logo.after') !!}

        <!-- Form Container -->
        <div
            class="m-auto w-full max-w-[870px] rounded-xl border border-[#E9E9E9] p-16 px-[90px] max-md:px-8 max-md:py-8"
        >
            <h1 class="font-dmserif text-4xl max-sm:text-2xl">
                @lang('marketplace::app.shop.sellers.account.forgot-password.title')
            </h1>

            <p class="mt-4 text-xl text-[#6E6E6E] max-sm:text-base">
                @lang('marketplace::app.shop.sellers.account.forgot-password.forgot-password-text')
            </p>

            {!! view_render_event('marketplace.seller.account.forgot_password.before') !!}
            
            <div class="mt-14 rounded max-sm:mt-8">
                <x-shop::form :action="route('marketplace.seller.forgot_password.store')">
                    {!! view_render_event('marketplace.seller.account.forgot_password.form_controls.before') !!}
                    
                    <x-shop::form.control-group>
                        <x-shop::form.control-group.label class="required">
                            @lang('marketplace::app.shop.sellers.account.login.email')
                        </x-shop::form.control-group.label>

                        <x-shop::form.control-group.control
                            type="email"
                            class="rounded-lg !p-[20px_25px]"
                            name="email"
                            rules="required|email"
                            value=""
                            :label="trans('marketplace::app.shop.sellers.account.login.email')"
                            placeholder="email@example.com"
                            aria-label="@lang('marketplace::app.shop.sellers.account.login.email')"
                            aria-required="true"
                        />

                        <x-shop::form.control-group.error control-name="email" />
                    </x-shop::form.control-group>

                    <div>
                        {!! Captcha::render() !!}
                    </div>

                    <div class="mt-8 flex flex-wrap items-center gap-9">
                        <button
                            class="primary-button m-0 mx-auto block w-full max-w-[374px] rounded-2xl px-11 py-4 text-center text-base ltr:ml-0 rtl:mr-0"
                            type="submit"
                        >
                            @lang('marketplace::app.shop.sellers.account.forgot-password.submit')
                        </button>
                    </div>

                    <p class="mt-5 font-medium text-[#6E6E6E]">
                        @lang('marketplace::app.shop.sellers.account.forgot-password.back')

                        <a class="text-navyBlue"
                            href="{{ route('marketplace.seller.session.index') }}"
                        >
                            @lang('marketplace::app.shop.sellers.account.forgot-password.sign-in-button')
                        </a>
                    </p>

                    {!! view_render_event('marketplace.seller.account.forgot_password.form_controls.after') !!}
                </x-shop::form>
            </div>

            {!! view_render_event('marketplace.seller.account.forgot_password.after') !!}
        </div>

        <p class="mb-4 mt-8 text-center text-xs text-[#6E6E6E]">
            @lang('marketplace::app.shop.sellers.account.forgot-password.footer', ['current_year'=> date('Y') ])
        </p>
    </div>

    @push('scripts')
        {!! Captcha::renderJS() !!}
    @endpush
</x-marketplace::shop.layouts.full>
