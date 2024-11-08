<div class="grid gap-4 rounded-xl border border-[#E9E9E9] p-7">
    <div class="flex items-center justify-between">
        <div class="shimmer h-8 w-40"></div>

        <div class="shimmer h-11 w-24 rounded-xl"></div>
    </div>

    @foreach (range(1, 5) as $i)
        <div class="flex items-center justify-between border-b py-4 last:border-b-0">
            <div class="grid gap-1">
                <div class="shimmer h-6 w-32"></div>

                <div class="shimmer h-6 w-32"></div>
            </div>

            <div class="flex gap-5">
                <div class="shimmer h-15 w-15 rounded-xl"></div>

                <div class="flex flex-col gap-1">
                    <div class="shimmer h-6 w-16"></div>

                    <div class="shimmer h-6 w-16"></div>
                </div>

                <div class="flex items-center">
                    <div class="shimmer h-6 w-6 rounded-md"></div>
                </div>
            </div>
        </div>
    @endforeach
</div>