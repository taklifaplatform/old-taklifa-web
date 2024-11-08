@if (auth()->guard('seller')->user()->is_suspended)
    <div class="bg-rose-600 px-1.5 py-2 text-center text-sm font-semibold text-white">
        <p> @lang('marketplace::app.shop.sellers.account.suspended-message') </p>
    </div>
@endif