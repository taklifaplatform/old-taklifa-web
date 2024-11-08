<div class="mt-8 grid gap-2.5 rounded-xl border border-[#E9E9E9] p-5">
    <div class="shimmer h-7 w-40"></div>

    <div class="grid grid-cols-2 gap-2.5 md:grid-cols-3">
        @foreach (range(1, 6) as $i)
            <div class="grid gap-1 py-2.5">
                <div class="flex items-end gap-1">
                    <div class="shimmer h-7 w-24"></div>

                    <div class="shimmer h-4 w-8"></div>
                </div>

                <div class="shimmer h-5 w-40"></div>
            </div>
        @endforeach
    </div>
</div>