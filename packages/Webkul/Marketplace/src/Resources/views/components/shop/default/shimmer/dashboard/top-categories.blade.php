<div class="grid gap-4 rounded-xl border border-[#E9E9E9] p-7">
    <div class="shimmer h-8 w-44"></div>

    @foreach (range(1, 5) as $i)
        <div class="grid gap-2 py-6">
            <div class="shimmer h-6 w-60"></div>

            <div class="flex justify-between gap-5">                                
                <div class="shimmer h-3 w-4/5"></div>

                <div class="shimmer h-3 w-10"></div>
            </div>
        </div>
    @endforeach
</div>