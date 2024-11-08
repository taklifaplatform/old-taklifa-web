@unless ($breadcrumbs->isEmpty())
    <nav aria-label="">
        <ol class="flex">
            @foreach ($breadcrumbs as $breadcrumb)
                @if ($breadcrumb->url 
                    && ! $loop->last
                )
                    <li class="flex items-center gap-x-2.5 text-base font-medium">
                        <a href="{{ $breadcrumb->url }}">
                            {{ $breadcrumb->title }}
                        </a>

                        <span class="mr-2.5 text-base text-[#727272] after:content-['/']"></span>
                    </li>
                @else
                    <li 
                        class="mr-2.5 flex items-center gap-x-2.5 text-base font-medium text-[#727272] after:content-['/'] after:last:hidden" 
                        aria-current="page"
                    >
                        {{ $breadcrumb->title }}
                    </li>
                @endif
            @endforeach
        </ol>
    </nav>
@endunless
