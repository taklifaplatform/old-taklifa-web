<x-admin::layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('admin::app.catalog.tags.create.title')
    </x-slot>

    {!! view_render_event('bagisto.admin.catalog.tags.create.before') !!}

    <!-- Tag Create Form -->
    <x-admin::form
        :action="route('admin.catalog.tags.store')"
        enctype="multipart/form-data"
    >
        {!! view_render_event('bagisto.admin.catalog.tags.create.create_form_controls.before') !!}

        <div class="flex gap-4 justify-between items-center max-sm:flex-wrap">
            <p class="text-xl text-gray-800 dark:text-white font-bold">
                @lang('admin::app.catalog.tags.create.title')
            </p>

            <div class="flex gap-x-2.5 items-center">
                <a href="{{ route('admin.catalog.tags.index') }}" class="transparent-button hover:bg-gray-200 dark:hover:bg-gray-800 dark:text-white">
                    @lang('admin::app.catalog.tags.create.back-btn')
                </a>

                <button type="submit" class="primary-button">
                    @lang('admin::app.catalog.tags.create.save-btn')
                </button>
            </div>
        </div>

        <div class="flex gap-2.5 mt-3.5 max-xl:flex-wrap">
            <!-- Left Section -->
            <div class="flex flex-col gap-2 flex-1 max-xl:flex-auto">
                {!! view_render_event('bagisto.admin.catalog.tags.create.card.general.before') !!}

                <!-- General Section -->
                <div class="p-4 bg-white dark:bg-gray-900 rounded box-shadow">
                    <p class="mb-4 text-base text-gray-800 dark:text-white font-semibold">
                        @lang('admin::app.catalog.tags.create.general')
                    </p>

                    <!-- Name Input -->
                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label class="required">
                            @lang('admin::app.catalog.tags.create.name')
                        </x-admin::form.control-group.label>

                        <v-field type="text" name="name"  value="{{ old('name') }}" label="{{ trans('admin::app.catalog.tags.create.name') }}">
                            <input type="text" id="name" :class="[errors['name'] ? 'border border-red-600 hover:border-red-600' : '']" class="flex w-full min-h-[39px] py-2 px-3 border rounded-md text-sm text-gray-600 dark:text-gray-300 transition-all hover:border-gray-400 dark:hover:border-gray-400 focus:border-gray-400 dark:focus:border-gray-400 dark:bg-gray-900 dark:border-gray-800" name="name" v-bind="field" placeholder="{{ trans('admin::app.catalog.tags.create.name') }}">
                        </v-field>

                        <x-admin::form.control-group.error control-name="name" />
                    </x-admin::form.control-group>
                </div>

                {!! view_render_event('bagisto.admin.catalog.tags.create.card.general.after') !!}
            </div>
        </div>

        {!! view_render_event('bagisto.admin.catalog.tags.create.create_form_controls.after') !!}
    </x-admin::form>

    {!! view_render_event('bagisto.admin.catalog.tags.create.after') !!}

    @pushOnce('scripts')

        <script type="module">
            app.component('v-description', {
                template: '#v-description-template',

                data() {
                   //
                },

                mounted() {
                   //
                },
            });
        </script>
    @endPushOnce
</x-admin::layouts>
