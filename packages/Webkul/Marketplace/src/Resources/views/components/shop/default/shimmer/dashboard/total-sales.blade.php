<div class="grid gap-4 rounded-xl border border-[#E9E9E9] p-7">
    <div class="flex max-h-11 justify-between gap-2">
        <div class="shimmer h-7 w-28"></div>

        <div class="shimmer h-11 w-[104px] rounded-xl"></div>
    </div>

    <!-- Graph Chart -->
    <div class="flex gap-1.5">
        <div class="grid">
            @foreach (range(1, 10) as $i)
                <div class="shimmer h-2.5 w-[34px]">
                </div>
            @endforeach
        </div>

        <div class="grid w-full gap-1.5">
            <div class="flex w-[285px] items-end border-b border-l pl-2.5 md:h-[240px] md:w-full">
                <div class="flex aspect-[2] w-full items-end justify-between gap-2.5">
                    @foreach (range(1, 14) as $i)
                        <div class="shimmer flex w-full" style="height: {{ rand(10, 100) }}%"></div>
                    @endforeach
                </div>
            </div>

            <div class="flex justify-between gap-5 pl-2.5 max-lg:gap-4 max-sm:gap-2.5">
                @foreach (range(1, 10) as $i)
                    <div class="shimmer mt-1 flex h-10 w-3 rotate-45"></div>
                @endforeach
            </div>
        </div>
    </div>
</div>