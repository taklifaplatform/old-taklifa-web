<span
    class="w-full text-xs font-normal text-zinc-500"
    v-if="item.shop_title"
>
    @lang('marketplace::app.shop.checkout.cart.sold-by')

    <span class="font-medium text-black">@{{ item.shop_title }}</span>
</span>
