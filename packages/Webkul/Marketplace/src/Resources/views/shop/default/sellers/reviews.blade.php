@inject ('reviewRepository', 'Webkul\Marketplace\Repositories\ReviewRepository')

@php
    $avgRatings = $reviewRepository->getAverageRating($seller);

    $totalReviews = $reviewRepository->getTotalReviews($seller);

    $percentageRatings = $reviewRepository->getPercentageRating($seller);
@endphp

<!-- SEO Meta Content -->
@push('meta')
    <meta
        name="title"
        content="{{ $seller->meta_title ?? '' }}"
    />

    <meta
        name="description"
        content="{{ trim($seller->meta_description) != ''
        ? $seller->meta_description
        : Illuminate\Support\Str::limit(strip_tags($seller->description), 120, '') }}"
    />

    <meta
        name="keywords"
        content="{{ $seller->meta_keywords }}"
    />
@endPush

<!-- Page Layout -->
<x-marketplace::shop.layouts.full>
    <!-- Page Title -->
    <x-slot:title>
        {{ $seller->shop_title }}
    </x-slot>

    <div class="container px-[60px] max-lg:px-8 max-sm:px-4">
        <div class="mt-9 grid justify-between gap-3 md:flex">
            <h2 class="text-2xl font-medium">
                @lang('marketplace::app.shop.sellers.profile.reviews.customer-reviews')
            </h2>
        </div>

        <div class="mt-8 grid gap-y-8 md:flex md:gap-x-8">
            <div class="grid content-baseline">
                <div class="grid gap-2">
                    <div class="mt-2.5 flex items-center gap-4">
                        <div class="flex gap-4">
                            <p class="text-3xl font-medium text-[#232323]">
                                {{$avgRatings}}
                            </p>
                        </div>

                        <x-marketplace::shop.products.star-rating 
                            :value="$avgRatings"
                            :is-editable=false
                        />
        
                        <div class="flex gap-4">
                            <p class="text-xs text-[#858585] underline">
                                (@lang('marketplace::app.shop.sellers.profile.reviews.customer-review', ['count' => $totalReviews]))
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mt-4 grid gap-y-4 md:max-w-[365px]">
                    @for ($i = 5; $i >= 1; $i--)
                        <div class="flex items-center justify-between gap-x-6 max-sm:flex-wrap">
                            <div class="whitespace-nowrap text-base font-medium">{{ $i }} Stars</div>
                            <div class="h-4 w-[275px] max-w-full rounded-sm bg-[#E5E5E5]">
                                <div class="h-4 rounded-sm bg-[#FEA82B]" style="width: {{ $percentageRatings[$i] }}%"></div>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>

            <div class="grid w-full gap-y-5">
                @foreach ($reviews as $review)                            
                    <div class="flex min-w-full gap-5 rounded-xl border border-[#e5e5e5] p-6 max-xl:mb-5 max-sm:flex-wrap">
                        <div>
                            <div
                                class="flex max-h-[100px] min-h-[100px] min-w-[100px] max-w-[100px] items-center justify-center rounded-xl bg-[#F5F5F5] max-sm:hidden"
                                title="{{$review->customer->name}}"
                            >
                                @php
                                    $split_name = explode(' ', $review->customer->name);

                                    $uppercase_names = array_map(function ($name) {
                                            return strtoupper($name[0]);
                                        }, $split_name);

                                    $joined_name = implode('', $uppercase_names);
                                @endphp

                                <span class="text-2xl font-semibold text-[#6E6E6E]">
                                    {{$joined_name}}
                                </span>
                            </div>
                        </div>
            
                        <div class="w-full">
                            <div class="flex justify-between">
                                <p class="text-xl font-medium max-sm:text-base">
                                    {{$review->title}}
                                </p>
            
                                <div class="flex items-center">
                                    <x-marketplace::shop.products.star-rating 
                                        :value="$review->rating"
                                    />
                                </div>
                            </div>                    
            
                            <p class="mt-2.5 text-base text-[#757575] max-sm:text-xs">
                                {{$review->comment}}
                            </p>

                            <p class="mt-2.5 text-sm text-[#666666] max-sm:text-xs">
                                @lang('marketplace::app.shop.sellers.profile.reviews.review-by') <span class="font-medium">
                                    {{$review->customer->name}}
                                </span>
                                {{core()->formatDate($review->created_at, 'd M, Y')}}
                            </p>
                        </div>
                    </div>
                @endforeach

                {{ $reviews->links('marketplace::shop.partials.pagination') }}
            </div>
        </div>
    </div>
</x-marketplace::shop.layouts.full> 
