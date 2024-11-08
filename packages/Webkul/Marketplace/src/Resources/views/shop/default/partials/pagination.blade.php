@if ($paginator->hasPages())
    <div class="mt-5 flex items-center justify-center gap-5">
        <!-- Previous Page Link -->
        @if ($paginator->onFirstPage())
            <a class="flex items-center rounded-md border p-1">
                <span
                    class="icon-arrow-left cursor-pointer text-2xl opacity-60"
                    role="button"
                >
                </span>
            </a>
        @else
            <a
                data-page="{{ urldecode($paginator->previousPageUrl()) }}"
                href="{{ urldecode($paginator->previousPageUrl()) }}"
                id="previous"
                class="flex items-center rounded-md border p-1 transition-all hover:bg-[#F1EADF]"
            >
                <span
                    class="icon-arrow-left cursor-pointer text-2xl"
                    role="button"
                >
                </span>
            </a>
        @endif

        <!-- Pagination Elements -->
        @foreach ($elements as $element)
            <!-- "Three Dots" Separator -->
            @if (is_string($element))
                <a
                    class="disabled flex h-[34px] w-[34px] cursor-pointer items-center justify-center rounded-md border"
                    aria-disabled="true"
                >
                    {{ $element }}
                </a>
            @endif

            <!-- Array Of Links -->
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <a class="flex h-[34px] w-[34px] cursor-pointer items-center justify-center rounded-md border bg-[#F1EADF]">
                            {{ $page }}
                        </a>
                    @else
                        <a
                            class="flex h-[34px] w-[34px] cursor-pointer items-center justify-center rounded-md border transition-all hover:bg-[#F1EADF]"
                            href="{{ urldecode($url) }}"
                        >
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        <!-- Next Page Link -->
        @if ($paginator->hasMorePages())
            <a
                href="{{ urldecode($paginator->nextPageUrl()) }}"
                data-page="{{ urldecode($paginator->nextPageUrl()) }}"
                id="next"
                class="flex items-center rounded-md border p-1 transition-all hover:bg-[#F1EADF]"
            >
                <span
                    class="icon-arrow-right cursor-pointer text-2xl"
                    role="button"
                >
                </span>
            </a>
        @else
            <a class="flex items-center rounded-md border p-1">
                <span
                    class="icon-arrow-right cursor-pointer text-2xl opacity-60"
                    role="button"
                >
                </span>
            </a>
        @endif
    </div>
@endif
