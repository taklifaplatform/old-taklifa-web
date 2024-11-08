<x-marketplace::shop.layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('marketplace::app.shop.sellers.account.roles.edit.title')
    </x-slot>

    <!-- Breadcrumbs -->
    @section('breadcrumbs')
        <x-marketplace::shop.breadcrumbs name="roles.edit" />
    @endSection

    {!! view_render_event('marketplace.seller.roles.edit.before', ['role' => $role]) !!}

    <x-marketplace::shop.form
        method="PUT"
        :action="route('shop.marketplace.seller.account.roles.edit', $role->id)"
    >

        {!! view_render_event('marketplace.seller.roles.edit.create_form_controls.before', ['role' => $role]) !!}

        <div class="flex items-center justify-between gap-4 max-sm:flex-wrap">
            <p class="text-2xl font-medium">
                @lang('marketplace::app.shop.sellers.account.roles.edit.title')
            </p>

            <div class="flex items-center gap-x-2.5">
                <!-- Back Button -->
                <a
                    href="{{ route('shop.marketplace.seller.account.roles.index') }}"
                    class="transparent-button rounded-xl px-5 py-2.5 font-semibold hover:bg-gray-200"
                >
                    @lang('marketplace::app.shop.sellers.account.roles.edit.back-btn')
                </a>

                <!-- Save Button -->
                <button
                    type="submit"
                    class="primary-button px-5 py-2.5"
                >
                    @lang('marketplace::app.shop.sellers.account.roles.edit.save-btn')
                </button>
            </div>
        </div>

         <!-- body content -->
         <div class="mt-3.5 flex gap-6 max-xl:flex-wrap">
            <!-- Left sub-component -->
            <div class="flex flex-1 flex-col gap-6 max-xl:flex-auto">

                {!! view_render_event('marketplace.seller.roles.edit.card.access_control.before', ['role' => $role]) !!}

                <!-- Access Control Input Fields -->
                <div class="box-shadow rounded-xl border border-[#E9E9E9] bg-white p-5">
                    <p class="mb-4 text-base font-semibold text-gray-800">
                        @lang('marketplace::app.shop.sellers.account.roles.edit.access-control')
                    </p>

                    <!-- Create Role for -->
                    <v-mp-access-control>
                        <!-- Shimmer Effect -->
                        <div class="mb-4">
                            <div class="shimmer mb-1.5 h-4 w-24"></div>

                            <div class="custom-select h-11 w-full rounded-md border bg-white px-3 py-2.5 text-sm font-normal text-gray-600 transition-all hover:border-gray-400"></div>
                        </div>
                    </v-mp-access-control>
                </div>

                {!! view_render_event('marketplace.seller.roles.edit.card.access_control.after', ['role' => $role]) !!}

            </div>

            <!-- Right sub-component -->
            <div class="flex w-[360px] max-w-full flex-col gap-6 max-xl:flex-auto">

                {!! view_render_event('marketplace.seller.roles.edit.card.accordion.general.before', ['role' => $role]) !!}

                <div class="box-shadow rounded-xl border border-[#E9E9E9] bg-white p-5">
                    <p class="p-2.5 text-base font-semibold text-gray-800">
                        @lang('marketplace::app.shop.sellers.account.roles.edit.general')
                    </p>

                    <!-- Name -->
                    <x-marketplace::shop.form.control-group>
                        <x-marketplace::shop.form.control-group.label class="required">
                            @lang('marketplace::app.shop.sellers.account.roles.edit.name')
                        </x-marketplace::shop.form.control-group.label>

                        <x-marketplace::shop.form.control-group.control
                            type="text"
                            id="name"
                            name="name"
                            rules="required"
                            value="{{ old('name') ?: $role->name }}"
                            :label="trans('marketplace::app.shop.sellers.account.roles.edit.name')"
                            :placeholder="trans('marketplace::app.shop.sellers.account.roles.edit.name')"
                        />

                        <x-marketplace::shop.form.control-group.error control-name="name" />
                    </x-marketplace::shop.form.control-group>

                    <!-- Description -->
                    <x-marketplace::shop.form.control-group class="!mb-0">
                        <x-marketplace::shop.form.control-group.label class="required">
                            @lang('marketplace::app.shop.sellers.account.roles.edit.description')
                        </x-marketplace::shop.form.control-group.label>

                        <x-marketplace::shop.form.control-group.control
                            type="textarea"
                            id="description"
                            name="description"
                            rules="required"
                            value="{{ old('description') ?: $role->description }}"
                            :label="trans('marketplace::app.shop.sellers.account.roles.edit.description')"
                            :placeholder="trans('marketplace::app.shop.sellers.account.roles.edit.description')"
                        />

                        <x-marketplace::shop.form.control-group.error control-name="description" />
                    </x-marketplace::shop.form.control-group>
                </div>

                {!! view_render_event('marketplace.seller.roles.edit.card.accordion.general.after', ['role' => $role]) !!}

            </div>
        </div>

        {!! view_render_event('marketplace.seller.roles.edit.create_form_controls.after', ['role' => $role]) !!}

    </x-marketplace::shop.form>

    {!! view_render_event('marketplace.seller.roles.edit.after', ['role' => $role]) !!}

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="v-mp-access-control-template"
        >
            <div>
                <!-- Permission Type -->
                <x-marketplace::shop.form.control-group>
                    <x-marketplace::shop.form.control-group.label class="required">
                        @lang('marketplace::app.shop.sellers.account.roles.edit.permissions')
                    </x-marketplace::shop.form.control-group.label>

                    <x-marketplace::shop.form.control-group.control
                        type="select"
                        name="permission_type"
                        id="permission_type"
                        rules="required"
                        :label="trans('marketplace::app.shop.sellers.account.roles.edit.permissions')"
                        :placeholder="trans('marketplace::app.shop.sellers.account.roles.edit.permissions')"
                        v-model="permission_type"
                    >
                        <option value="custom">
                            @lang('marketplace::app.shop.sellers.account.roles.edit.custom')
                        </option>

                        <option value="all">
                            @lang('marketplace::app.shop.sellers.account.roles.edit.all')
                        </option>
                    </x-marketplace::shop.form.control-group.control>

                    <x-marketplace::shop.form.control-group.error control-name="permission_type" />
                </x-marketplace::shop.form.control-group>

                <div v-if="permission_type == 'custom'">
                    <x-marketplace::shop.tree.view
                        input-type="checkbox"
                        value-field="key"
                        id-field="key"
                        :items="json_encode(marketplace_acl()->getItems())"
                        :value="json_encode($role->permissions ?? [])"
                    />
                </div>

                <x-marketplace::shop.form.control-group.error control-name="permissions" />
            </div>
        </script>

        <script type="module">
            app.component('v-mp-access-control', {
                template: '#v-mp-access-control-template',

                data() {
                    return {
                        permission_type: @json($role->permission_type)
                    };
                }
            })
        </script>
    @endPushOnce
</x-marketplace::shop.layouts>
