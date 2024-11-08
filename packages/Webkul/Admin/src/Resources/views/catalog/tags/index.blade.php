<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.catalog.tags.index.title')
    </x-slot>

    <div class="flex gap-4 justify-between items-center max-sm:flex-wrap">
        <p class="text-xl text-gray-800 dark:text-white font-bold">
            @lang('admin::app.catalog.tags.index.title')
        </p>

        <div class="flex gap-x-2.5 items-center">
            {!! view_render_event('bagisto.admin.catalog.tags.index.create-button.before') !!}

            @if (bouncer()->hasPermission('catalog.tags.create'))
                <a href="{{ route('admin.catalog.tags.create') }}">
                    <div class="primary-button">
                        @lang('admin::app.catalog.tags.index.add-btn')
                    </div>
                </a>
            @endif

            {!! view_render_event('bagisto.admin.catalog.tags.index.create-button.after') !!}
        </div>
    </div>

    {!! view_render_event('bagisto.admin.catalog.tags.list.before') !!}

    <x-admin::datagrid src="{{ route('admin.catalog.tags.index') }}" />

    {!! view_render_event('bagisto.admin.catalog.tags.list.after') !!}

</x-admin::layouts>
