<div class="mt-8 flex max-sm:flex-wrap">
    <div class="border-b-2 px-4 py-2">
        <div class="shimmer h-7 w-[100px]"></div>
    </div>

    <div class="px-4 py-2">
        <div class="shimmer h-7 w-[132px]"></div>
    </div>

    <div class="px-4 py-2">
        <div class="shimmer h-7 w-[130px]"></div>
    </div>

    <div class="px-4 py-2">
        <div class="shimmer h-7 w-[122px]"></div>
    </div>
</div>

<div class="w-full overflow-x-auto rounded-xl border">
    <div class="table-responsive box-shadow grid w-full overflow-hidden rounded bg-white">
        <div class="row grid grid-cols-5 border-b px-4 py-2.5">
            @foreach (range(1, 5) as $i)
                <div class="py-2">
                    <div class="shimmer h-5 w-[136px]"></div>
                </div>
            @endforeach
        </div>
        
        @foreach (range(1, 5) as $i)
            <div class="row grid grid-cols-5 border-b px-4 py-2.5 last:border-none">
                @foreach (range(1, 5) as $i)
                    <div class="grid gap-y-1.5">
                        <div class="shimmer h-5 w-20"></div>

                        <div class="shimmer h-5 w-28"></div>

                        <div class="shimmer h-5 w-20"></div>
                    </div>
                @endforeach
            </div>
        @endforeach

        <div class="flex items-center justify-between p-4">
            <div class="shimmer h-4 w-40"></div>
            
            <div class="shimmer h-9 w-20 rounded-[10px]"></div>
        </div>
    </div>
</div>
