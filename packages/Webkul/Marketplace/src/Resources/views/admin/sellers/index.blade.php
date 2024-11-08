<x-admin::layouts>
    <!-- Title of the page -->
    <x-slot:title>
        @lang('marketplace::app.admin.sellers.index.title')
    </x-slot:title>

    <div class="flex items-center justify-between">
        <p class="py-2.5 text-xl font-bold text-gray-800 dark:text-white">
            @lang('marketplace::app.admin.sellers.index.title')
        </p>

        <div class="flex items-center gap-x-2.5">
            <div class="flex items-center gap-x-2.5">
                <!-- Seller Create Vue Component -->
                <v-create-sellers-form>
                    <button class="primary-button">
                        @lang('marketplace::app.admin.sellers.index.add-btn')
                    </button>
                </v-create-sellers-form>
            </div>
        </div>
    </div>

    {!! view_render_event('marketplace.admin.sellers.list.before') !!}
    
    <!-- Datagrid -->
    <x-admin::datagrid
        src="{{ route('admin.marketplace.sellers.index') }}"
        :isMultiRow="true"
        ref="seller_data"
    >
        <!-- Datagrid Header -->
        @php 
            $hasPermission = bouncer()->hasPermission('marketplace.sellers.mass-update') || bouncer()->hasPermission('marketplace.sellers.mass-delete');
        @endphp

        <template #header="{
            isLoading,
            available,
            applied,
            selectAll,
            sort,
            performAction
        }">
            <template v-if="! isLoading">
                <div class="row grid grid-cols-[2fr_1fr_1fr] grid-rows-1 items-center border-b px-4 py-2.5 dark:border-gray-800">
                    <div
                        class="flex select-none items-center gap-2.5"
                        v-for="(columnGroup, index) in [['name', 'email', 'url'], ['created_at', 'is_approved', 'id'], ['assign_product', 'flags']]"
                    >
                        @if ($hasPermission)
                            <label
                                class="flex w-max cursor-pointer select-none items-center gap-1"
                                for="mass_action_select_all_records"
                                v-if="! index"
                            >
                                <input
                                    type="checkbox"
                                    name="mass_action_select_all_records"
                                    id="mass_action_select_all_records"
                                    class="peer hidden"
                                    :checked="['all', 'partial'].includes(applied.massActions.meta.mode)"
                                    @change="selectAll"
                                >

                                <span
                                    class="icon-uncheckbox cursor-pointer rounded-md text-2xl"
                                    :class="[
                                        applied.massActions.meta.mode === 'all' ? 'peer-checked:icon-checked peer-checked:text-blue-600' : (
                                            applied.massActions.meta.mode === 'partial' ? 'peer-checked:icon-checkbox-partial peer-checked:text-blue-600' : ''
                                        ),
                                    ]"
                                >
                                </span>
                            </label>
                        @endif

                        <p class="text-gray-600 dark:text-gray-300">
                            <span class="[&>*]:after:content-['_/_']">
                                <template v-for="column in columnGroup">
                                    <span
                                        class="after:content-['/'] last:after:content-['']"
                                        :class="{
                                            'text-gray-800 dark:text-white font-medium': applied.sort.column == column,
                                            'cursor-pointer hover:text-gray-800 dark:hover:text-white': available.columns.find(columnTemp => columnTemp.index === column)?.sortable,
                                        }"
                                        @click="
                                            available.columns.find(columnTemp => columnTemp.index === column)?.sortable ? sort(available.columns.find(columnTemp => columnTemp.index === column)): {}
                                        "
                                    >
                                        @{{ available.columns.find(columnTemp => columnTemp.index === column)?.label }}
                                    </span>
                                </template>
                            </span>

                            <i
                                class="align-text-bottom text-base text-gray-800 dark:text-white ltr:ml-1 rtl:mr-1"
                                :class="[applied.sort.order === 'asc' ? 'icon-down-stat': 'icon-up-stat']"
                                v-if="columnGroup.includes(applied.sort.column)"
                            ></i>
                        </p>
                    </div>
                </div>
            </template>

            <!-- Datagrid Head Shimmer -->
            <template v-else>
                <x-admin::shimmer.datagrid.table.head :isMultiRow="true"></x-admin::shimmer.datagrid.table.head>
            </template>
        </template>

        <!-- Datagrid Body -->
        <template #body="{
            isLoading,
            available,
            applied,
            selectAll,
            sort,
            performAction
        }">
            <template v-if="! isLoading">
                <div
                    class="row grid grid-cols-[2fr_1fr_1fr] grid-rows-1 border-b px-4 py-2.5 transition-all hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-gray-950"
                    v-for="record in available.records"
                >
                    <!-- Customer Name, email, Shop URL -->
                    <div class="flex gap-2.5">
                        @if ($hasPermission)
                            <input
                                type="checkbox"
                                :name="`mass_action_select_record_${record.id}`"
                                :id="`mass_action_select_record_${record.id}`"
                                :value="record.id"
                                class="peer hidden"
                                v-model="applied.massActions.indices"
                                @change="setCurrentSelectionMode"
                            >

                            <label
                                class="icon-uncheckbox peer-checked:icon-checked cursor-pointer rounded-md text-2xl peer-checked:text-blue-600"
                                :for="`mass_action_select_record_${record.id}`"
                            ></label>
                        @endif

                        <div class="flex flex-col gap-1.5">
                            <p
                                class="text-base font-semibold text-gray-800 dark:text-white"
                                v-html="record.name"
                            >
                            </p>

                            <p
                                class="text-gray-600 dark:text-gray-300"
                                v-text="record.email"
                            >
                            </p>

                            <template v-if="record.parent_id">
                                <p
                                    class="text-gray-600 dark:text-gray-300"
                                >
                                    N/A
                                </p>
                            </template>

                            <template v-else>
                                <a
                                    class="text-base text-blue-800"
                                    target="_blank"
                                    :href="`{{ route('marketplace.seller.show', '') }}/${record.url}`"
                                >
                                    @{{ record.url }}
                                </a>
                            </template>
                        </div>
                    </div>

                    <!-- Created At, Is Approved, Id-->
                    <div class="flex gap-1.5">
                        <div class="flex flex-col gap-1.5">
                            <p
                                class="text-gray-600 dark:text-gray-300"
                                v-text="record.created_at"
                            >
                            </p>

                            <div class="flex gap-2">
                                <p
                                    class="text-gray-600 dark:text-gray-300"
                                    v-html="record.is_approved"
                                >
                                </p>

                                <p
                                    class="text-gray-600 dark:text-gray-300"
                                    v-if="record.is_suspended"
                                >
                                    <label class="label-canceled py-1">{{ trans('marketplace::app.admin.sellers.index.datagrid.suspended') }}</label>
                                </p>
                            </div>

                            <p
                                class="text-gray-600 dark:text-gray-300"
                            >
                                @{{ "@lang('marketplace::app.admin.sellers.index.datagrid.seller-id')".replace(':seller_id', record.id) }}
                            </p>
                        </div>
                    </div>

                    <!-- Assign Product -->
                    <div class="flex items-center justify-between gap-x-4">
                        <div class="flex flex-col gap-1.5">
                            <template v-if="record.parent_id">
                                <button
                                    type="button"
                                    class="font-semibold text-gray-600"
                                >
                                    N/A
                                </button>
                            </template>

                            <template v-else>
                                <a
                                    :href="`{{ route('admin.marketplace.sellers.products.search', '') }}/${record.id}`"
                                    class="secondary-button"
                                >
                                    @lang('marketplace::app.admin.sellers.index.datagrid.add-product')
                                </a>
                            </template>

                            <p class="text-gray-600 dark:text-gray-300">
                                @{{ "@lang('marketplace::app.admin.sellers.index.datagrid.total-flags')".replace(':count', record.flags)}}                             
                            </p>
                        </div>

                        <div class="flex items-center">
                            @if (bouncer()->hasPermission('marketplace.sellers.edit'))
                                <a @click="performAction(record.actions.find(action => action.method === 'GET'))">
                                    <span
                                        :class="record.actions.find(action => action.method === 'GET')?.icon"
                                        class="cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-200 dark:hover:bg-gray-800 max-sm:place-self-center"
                                        :title="record.actions.find(action => action.method === 'GET')?.title"
                                    >
                                    </span>
                                </a>
                            @endif

                            @if (bouncer()->hasPermission('marketplace.sellers.delete'))
                                <a @click="performAction(record.actions.find(action => action.method === 'DELETE'))">
                                    <span
                                        :class="record.actions.find(action => action.method === 'DELETE')?.icon"
                                        class="cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-200 dark:hover:bg-gray-800 max-sm:place-self-center"
                                        :title="record.actions.find(action => action.method === 'DELETE')?.title"
                                    >
                                    </span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </template>

            <!-- Datagrid Body Shimmer -->
            <template v-else>
                <x-admin::shimmer.datagrid.table.body :isMultiRow="true"></x-admin::shimmer.datagrid.table.body>
            </template>
        </template>
    </x-admin::datagrid>

    {!! view_render_event('marketplace.admin.sellers.list.after') !!}

    @pushOnce('scripts')
        <script 
            type="text/x-template" 
            id="v-create-sellers-form-template"
        >   
            {!! view_render_event('marketplace.admin.sellers.index.add_button.before') !!}
            
            <!-- Create Button -->
            @if (bouncer()->hasPermission('marketplace.sellers.create'))
                <button
                    class="primary-button"
                    @click="$refs.sellerCreateModal.toggle()"
                >
                    @lang('marketplace::app.admin.sellers.index.add-btn')
                </button>
            @endif

            {!! view_render_event('marketplace.admin.sellers.index.add_button.after') !!}

            <!-- Seller Create form -->
            <x-admin::form
                v-slot="{ meta, errors, handleSubmit }"
                as="div"
            >
                <form @submit="handleSubmit($event, create)">
                    <x-admin::modal ref="sellerCreateModal">
                        <!-- Modal Header -->
                        <x-slot:header>
                            <p class="text-lg font-bold text-gray-800 dark:text-white">
                                @lang('marketplace::app.admin.sellers.index.create.title')
                            </p>
                        </x-slot:header>

                        <!-- Modal Content -->
                        <x-slot:content>
                            {!! view_render_event('marketplace.admin.sellers.index.create_form_controls.before') !!}
                            
                            <div class="flex gap-4 max-sm:flex-wrap">
                                <!-- Name -->
                                <x-admin::form.control-group class="mb-2.5 w-full">
                                    <x-admin::form.control-group.label class="required">
                                        @lang('marketplace::app.admin.sellers.index.create.name')
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.control
                                        type="text"
                                        name="name"
                                        id="name"
                                        rules="required"
                                        :label="trans('marketplace::app.admin.sellers.index.create.name')"
                                        :placeholder="trans('marketplace::app.admin.sellers.index.create.name')"
                                    />

                                    <x-admin::form.control-group.error control-name="name" />
                                </x-admin::form.control-group>

                                <!-- Email -->
                                <x-admin::form.control-group class="mb-2.5 w-full">
                                    <x-admin::form.control-group.label class="required">
                                        @lang('marketplace::app.admin.sellers.index.create.email')
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.control
                                        type="email"
                                        name="email"
                                        id="email"
                                        rules="required|email"
                                        :label="trans('marketplace::app.admin.sellers.index.create.email')"
                                        :placeholder="trans('marketplace::app.admin.sellers.index.create.email')"
                                    />

                                    <x-admin::form.control-group.error control-name="email" />
                                </x-admin::form.control-group>
                            </div>

                            <div class="flex gap-4 max-sm:flex-wrap">
                                <!-- Shop Title -->
                                <x-admin::form.control-group class="w-full">
                                    <x-admin::form.control-group.label class="required">
                                        @lang('marketplace::app.admin.sellers.index.create.shop-title')
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.control
                                        type="text"
                                        name="shop_title"
                                        id="shop_title"
                                        rules="required"
                                        :label="trans('marketplace::app.admin.sellers.index.create.shop-title')"
                                        :placeholder="trans('marketplace::app.admin.sellers.index.create.shop-title')"
                                    />

                                    <x-admin::form.control-group.error control-name="shop_title" />
                                </x-admin::form.control-group>

                                <!-- Shop Url -->
                                <x-admin::form.control-group class="w-full">
                                    <x-admin::form.control-group.label class="required">
                                        @lang('marketplace::app.admin.sellers.index.create.shop-url')
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.control
                                        type="text"
                                        name="url"
                                        id="url"
                                        rules="required"
                                        :label="trans('marketplace::app.admin.sellers.index.create.shop-url')"
                                        :placeholder="trans('marketplace::app.admin.sellers.index.create.shop-url')"
                                    />

                                    <x-admin::form.control-group.error control-name="url" />
                                </x-admin::form.control-group>
                            </div>

                            <div class="flex gap-4 max-sm:flex-wrap">
                                <!-- Address -->
                                <x-admin::form.control-group class="w-full">
                                    <x-admin::form.control-group.label class="required">
                                        @lang('marketplace::app.admin.sellers.index.create.address')
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.control
                                        type="text"
                                        name="address[0]"
                                        rules="required"
                                        :label="trans('marketplace::app.admin.sellers.index.create.street-address')"
                                        :placeholder="trans('marketplace::app.admin.sellers.index.create.street-address')"
                                    />

                                    <x-admin::form.control-group.error control-name="address[0]" />
                                </x-admin::form.control-group>

                                <!-- Phone -->
                                <x-admin::form.control-group class="w-full">
                                    <x-admin::form.control-group.label class="required">
                                        @lang('marketplace::app.admin.sellers.index.create.phone')
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.control
                                        type="text"
                                        name="phone"
                                        id="phone"
                                        rules="required|phone|min:10"
                                        :label="trans('marketplace::app.admin.sellers.index.create.phone')"
                                        :placeholder="trans('marketplace::app.admin.sellers.index.create.phone')"
                                    />

                                    <x-admin::form.control-group.error control-name="phone" />
                                </x-admin::form.control-group>
                            </div>

                            <div class="flex gap-4 max-sm:flex-wrap">
                                <!-- Postcode -->
                                <x-admin::form.control-group class="w-full">
                                    <x-admin::form.control-group.label class="required">
                                        @lang('marketplace::app.admin.sellers.index.create.postcode')
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.control
                                        type="text"
                                        name="postcode"
                                        id="postcode"
                                        rules="required|integer"
                                        :label="trans('marketplace::app.admin.sellers.index.create.postcode')"
                                        :placeholder="trans('marketplace::app.admin.sellers.index.create.postcode')"
                                    />

                                    <x-admin::form.control-group.error control-name="postcode" />
                                </x-admin::form.control-group>

                                <!-- City -->
                                <x-admin::form.control-group class="w-full">
                                    <x-admin::form.control-group.label class="required">
                                        @lang('marketplace::app.admin.sellers.index.create.city')
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.control
                                        type="text"
                                        name="city"
                                        id="city"
                                        rules="required"
                                        :label="trans('marketplace::app.admin.sellers.index.create.city')"
                                        :placeholder="trans('marketplace::app.admin.sellers.index.create.city')"
                                    />

                                    <x-admin::form.control-group.error control-name="city" />
                                </x-admin::form.control-group>
                            </div>

                            <div class="flex gap-4 max-sm:flex-wrap">
                                <!-- Country -->
                                <x-admin::form.control-group class="w-full">
                                    <x-admin::form.control-group.label>
                                        @lang('marketplace::app.admin.sellers.index.create.country')
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.control
                                        type="select"
                                        name="country"
                                        rules="required"
                                        v-model="country"
                                        :label="trans('marketplace::app.admin.sellers.index.create.country')"
                                    >
                                        <option value="">
                                            @lang('marketplace::app.admin.sellers.index.create.select')
                                        </option>

                                        @foreach (core()->countries() as $country)
                                            <option 
                                                {{ $country->code === config('app.default_country') ? 'selected' : '' }}  
                                                value="{{ $country->code }}"
                                            >
                                                {{ $country->name }}
                                            </option>
                                        @endforeach
                                    </x-admin::form.control-group.control>

                                    <x-admin::form.control-group.error control-name="country" />
                                </x-admin::form.control-group>

                                <!-- State -->
                                <x-admin::form.control-group class="w-full">
                                    <x-admin::form.control-group.label class="required">
                                        @lang('marketplace::app.admin.sellers.index.create.state')
                                    </x-admin::form.control-group.label>

                                    <template v-if="haveStates()">
                                        <x-admin::form.control-group.control
                                            type="select"
                                            id="state"
                                            name="state"
                                            rules="required"
                                            v-model="state"
                                            :label="trans('marketplace::app.admin.sellers.index.create.state')"
                                            :placeholder="trans('marketplace::app.admin.sellers.index.create.state')"
                                        >
                                            <option 
                                                v-for='(state, index) in countryStates[country]'
                                                :value="state.code"
                                                v-text="state.default_name"
                                            >
                                            </option>
                                        </x-admin::form.control-group.control>
                                    </template>

                                    <template v-else>
                                        <x-admin::form.control-group.control
                                            type="text"
                                            name="state"
                                            v-model="state"
                                            rules="required"
                                            :label="trans('marketplace::app.admin.sellers.index.create.state')"
                                            :placeholder="trans('marketplace::app.admin.sellers.index.create.state')"
                                        />
                                    </template>

                                    <x-admin::form.control-group.error control-name="state" />
                                </x-admin::form.control-group>
                            </div>

                            {!! view_render_event('marketplace.admin.sellers.index.create_form_controls.after') !!}
                        </x-slot:content>
                        
                        <!-- Modal Footer -->
                        <x-slot:footer>
                            <!-- Modal Submission -->
                            <div class="flex items-center gap-x-2.5">
                                <button class="primary-button">
                                    <div
                                        v-if="isLoading"
                                        class="align-center inline-block h-4 w-4 animate-spin rounded-full border-4 border-solid border-r-transparent motion-reduce:animate-[spin_1.5s_linear_infinite]"
                                    >
                                    </div>
                                    @lang('marketplace::app.admin.sellers.index.create.save-btn')
                                </button>
                            </div>
                        </x-slot:footer>
                    </x-admin::modal>
                </form>
            </x-admin::form>
        </script>

        <script type="module">
            app.component('v-create-sellers-form', {
                template: '#v-create-sellers-form-template',

                data() {
                    return {
                        isLoading: false,

                        country: null,

                        countryStates: @json(core()->groupedStatesByCountries()),
                    }
                },

                methods: {
                    create(params, { resetForm, setErrors }) {
                        this.isLoading = true;

                        this.$axios.post("{{ route('admin.marketplace.sellers.store')}}", params)
                            .then((response) => {
                                this.$refs.sellerCreateModal.close();

                                this.$root.$refs.seller_data.get();

                                this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });

                                resetForm();
                            })
                            .catch(error => {
                                if (error.response.status == 422) {
                                    setErrors(error.response.data.errors);
                                }
                            })
                            .then(() => {
                                this.isLoading = false;
                            });
                    },

                    haveStates() {
                        return !! this.countryStates[this.country]?.length;
                    },
                }
            });
        </script>
    @endPushOnce
</x-admin::layouts>
