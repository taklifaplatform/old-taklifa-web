<x-marketplace::shop.layouts>    
    <!-- Page Title -->
    <x-slot:title>
        @lang('marketplace::app.shop.sellers.account.users.index.title')
    </x-slot>

    <!-- Breadcrumbs -->
    @section('breadcrumbs')
        <x-marketplace::shop.breadcrumbs name="users" />
    @endSection

    <v-seller-user>
        <div class="flex items-center justify-between gap-4 max-sm:flex-wrap">
            <!-- Page Title -->
            <h2 class="text-2xl font-medium">
                @lang('marketplace::app.shop.sellers.account.users.index.title')
            </h2>

            <button
                type="button"
                class="primary-button px-5 py-2.5"
            >
                @lang('marketplace::app.shop.sellers.account.users.index.create-btn')
            </button>
        </div>

        <!-- DataGrid Shimmer -->
        <x-shop::shimmer.datagrid/>
    </v-seller-user>

    @pushOnce('scripts')
        <script 
            type="text/x-template" 
            id="v-seller-user-template"
        >
            <div class="flex items-center justify-between gap-4 max-sm:flex-wrap">
                <!-- Page Title -->
                <h2 class="text-2xl font-medium">
                    @lang('marketplace::app.shop.sellers.account.users.index.title')
                </h2>

                <button
                    type="button"
                    class="primary-button px-5 py-2.5"
                    @click="selectedSeller={}; $refs.userCreateModel.toggle()"
                >
                    @lang('marketplace::app.shop.sellers.account.users.index.create-btn')
                </button>
            </div>

            {!! view_render_event('marketplace.seller.users.list.before') !!}

            <!-- Datagrid -->
            <x-shop::datagrid
                :src="route('shop.marketplace.seller.account.users.index')"
                :isMultiRow="true"
                ref="datagrid"
            >
                <!-- Datagrid Header -->
                <template #header="{
                    isLoading,
                    available,
                    applied,
                    selectAll,
                    sort,
                    performAction
                }">
                    <template v-if="! isLoading">
                        <div class="row grid grid-cols-[1.5fr_1fr_1fr] grid-rows-1 items-center border-b px-4 py-2.5">
                            <div
                                class="flex select-none items-center gap-2.5"
                                v-for="(columnGroup, index) in [['seller_id', 'seller_name'], ['seller_email', 'flag_count'], ['is_suspended', 'role_name']]"
                            >
                                <p class="text-sm font-medium leading-5">
                                    <span class="[&>*]:after:content-['_/_']">
                                        <template v-for="column in columnGroup">
                                            <span
                                                class="after:content-['/'] last:after:content-['']"
                                                :class="{
                                                    'text-gray-800 font-medium': applied.sort.column == column,
                                                    'cursor-pointer hover:text-gray-800': available.columns.find(columnTemp => columnTemp.index === column)?.sortable,
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
                                        class="align-text-bottom text-base text-gray-800 ltr:ml-1 rtl:mr-1"
                                        :class="[applied.sort.order === 'asc' ? 'icon-down-stat': 'icon-up-stat']"
                                        v-if="columnGroup.includes(applied.sort.column)"
                                    ></i>
                                </p>
                            </div>
                        </div>
                    </template>               

                    <!-- Datagrid Head Shimmer -->
                    <template v-else>
                        <x-shop::shimmer.datagrid.table.head :isMultiRow="true" />
                    </template>
                </template>

                <template #body="{
                    isLoading,
                    available,
                    applied,
                    sort,
                    performAction
                }">
                    <template v-if="! isLoading">
                        <div
                            class="row grid grid-cols-[1.5fr_1fr_1fr] grid-rows-1 border-b px-4 py-2.5 transition-all"
                            v-for="record in available.records"
                        >
                            <!-- Id, Seller Name -->
                            <div class="flex flex-col gap-y-1.5">
                                <p class="text-sm leading-5 text-gray-600 font-medium">
                                    @{{ "@lang('marketplace::app.shop.sellers.account.users.index.datagrid.id-value')".replace(':id', record.seller_id) }}
                                </p>

                                <p
                                    class="text-sm leading-5 text-gray-600 font-medium"
                                    v-text="record.seller_name"
                                >
                                </p>
                            </div>

                            <!-- Email, Flags -->
                            <div class="flex flex-col gap-y-1.5">
                                <p
                                    class="text-sm leading-5 text-gray-600 font-medium"
                                    v-html="record.seller_email"
                                >
                                </p>

                                <p class="text-sm leading-5 text-gray-600 font-medium">
                                    @{{ "@lang('marketplace::app.shop.sellers.account.users.index.datagrid.flags-value')".replace(':count', record.flag_count) }}
                                </p>
                            </div>

                            <div class="flex items-center justify-between gap-x-4">
                                <!-- Status, Permission -->
                                <div class="flex flex-col gap-y-1.5">
                                    <p
                                        class="text-sm font-semibold leading-5"
                                        :class="[record.is_suspended ? 'label-info' : 'label-active']"
                                    >
                                        <template v-if="record.is_suspended">
                                            @lang('marketplace::app.shop.sellers.account.users.index.datagrid.status.options.suspended')
                                        </template>

                                        <template v-else>
                                            @lang('marketplace::app.shop.sellers.account.users.index.datagrid.status.options.active')
                                        </template>
                                    </p>

                                    <p
                                        class="text-sm leading-5 text-gray-600 font-medium"
                                        v-text="record.role_name"
                                    >
                                    </p>
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center gap-x-1.5">
                                    @if (seller()->hasPermission('sellers.edit'))
                                        <a @click="editModal(record.actions.find(action => action.method === 'GET')?.url)">
                                            <span
                                                :class="record.actions.find(action => action.method === 'GET')?.icon"
                                                class="cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-200"
                                                :title="record.actions.find(action => action.method === 'GET')?.title"
                                            >
                                            </span>
                                        </a>
                                    @endif

                                    @if (seller()->hasPermission('sellers.delete'))
                                        <a @click="performAction(record.actions.find(action => action.method === 'DELETE'))">
                                            <span
                                                :class="record.actions.find(action => action.method === 'DELETE')?.icon"
                                                class="cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-200"
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
                        <x-shop::shimmer.datagrid.table.body :isMultiRow="true" />
                    </template>
                </template>
            </x-shop::datagrid>

            {!! view_render_event('marketplace.seller.users.list.after') !!}

            <!-- Seller Flag Reason Create form -->
            <x-marketplace::shop.form
                v-slot="{ meta, errors, handleSubmit }"
                as="div"
            >
                <form
                    @submit="handleSubmit($event, updateOrCreate)"
                    ref="userCreateForm"
                >
                    <x-marketplace::shop.modal ref="userCreateModel">
                        <!-- Modal Header -->
                        <x-slot:header>
                            <p
                                class="text-lg font-bold text-gray-800"
                                v-if="selectedSeller.id"
                            >
                                @lang('marketplace::app.shop.sellers.account.users.index.edit-title')
                            </p>

                            <p 
                                class="text-lg font-bold text-gray-800"
                                v-else
                            >
                                @lang('marketplace::app.shop.sellers.account.users.index.create-title')
                            </p>
                        </x-slot:header>

                        <!-- Modal Content -->
                        <x-slot:content>                            
                            <!-- Id -->
                            <x-marketplace::shop.form.control-group.control
                                type="hidden"
                                name="id"
                                v-model="selectedSeller.id"
                            />

                            <!-- Name -->
                            <x-marketplace::shop.form.control-group class="mb-2.5">
                                <x-marketplace::shop.form.control-group.label class="required">
                                    @lang('marketplace::app.shop.sellers.account.users.index.name')
                                </x-marketplace::shop.form.control-group.label>

                                <x-marketplace::shop.form.control-group.control
                                    type="text"
                                    name="name"
                                    rules="required"
                                    v-model="selectedSeller.name"
                                    :placeholder="trans('marketplace::app.shop.sellers.account.users.index.name')"
                                />

                                <x-marketplace::shop.form.control-group.error control-name="name" />
                            </x-marketplace::shop.form.control-group>

                            <div class="flex gap-x-2.5 max-sm:flex-wrap">
                                <!-- Email -->
                                <x-marketplace::shop.form.control-group class="mb-2.5">
                                    <x-marketplace::shop.form.control-group.label class="required">
                                        @lang('marketplace::app.shop.sellers.account.users.index.email')
                                    </x-marketplace::shop.form.control-group.label>

                                    <x-marketplace::shop.form.control-group.control
                                        type="text"
                                        name="email"
                                        rules="required"
                                        v-model="selectedSeller.email"
                                        :placeholder="trans('marketplace::app.shop.sellers.account.users.index.email')"
                                    />

                                    <x-marketplace::shop.form.control-group.error control-name="email" />
                                </x-marketplace::shop.form.control-group>

                                <!-- Phone Number -->
                                <x-marketplace::shop.form.control-group class="mb-2.5">
                                    <x-marketplace::shop.form.control-group.label class="required">
                                        @lang('marketplace::app.shop.sellers.account.users.index.phone-number')
                                    </x-marketplace::shop.form.control-group.label>

                                    <x-marketplace::shop.form.control-group.control
                                        type="text"
                                        name="phone"
                                        rules="required|numeric"
                                        v-model="selectedSeller.phone"
                                        :label="trans('marketplace::app.shop.sellers.account.users.index.phone-number')"
                                        :placeholder="trans('marketplace::app.shop.sellers.account.users.index.phone-number')"
                                    />

                                    <x-marketplace::shop.form.control-group.error control-name="phone" />
                                </x-marketplace::shop.form.control-group>
                            </div>

                            <div class="flex gap-x-2.5 max-sm:flex-wrap">
                                <!-- Password -->
                                <x-marketplace::shop.form.control-group class="mb-2.5">
                                    <x-marketplace::shop.form.control-group.label ::class="[! selectedSeller.id ? 'required' : '']">
                                        @lang('marketplace::app.shop.sellers.account.users.index.password')
                                    </x-marketplace::shop.form.control-group.label>

                                    <x-marketplace::shop.form.control-group.control
                                        type="password"
                                        name="password"
                                        ::rules="selectedSeller.id ? 'min:6' : 'min:6|required'"
                                        :placeholder="trans('marketplace::app.shop.sellers.account.users.index.password')"
                                    />

                                    <x-marketplace::shop.form.control-group.error control-name="password" />
                                </x-marketplace::shop.form.control-group>

                                <!-- Password Confirmation -->
                                <x-marketplace::shop.form.control-group class="mb-2.5">
                                    <x-marketplace::shop.form.control-group.label ::class="[! selectedSeller.id ? 'required' : '']">
                                        @lang('marketplace::app.shop.sellers.account.users.index.confirm-password')
                                    </x-marketplace::shop.form.control-group.label>

                                    <x-marketplace::shop.form.control-group.control
                                        type="password"
                                        name="password_confirmation"
                                        ::rules="selectedSeller.id ? 'min:6' : 'min:6|required'"
                                        :label="trans('marketplace::app.shop.sellers.account.users.index.confirm-password')"
                                        :placeholder="trans('marketplace::app.shop.sellers.account.users.index.confirm-password')"
                                    />

                                    <x-marketplace::shop.form.control-group.error control-name="password_confirmation" />
                                </x-marketplace::shop.form.control-group>
                            </div>

                            <div class="flex gap-x-2.5 max-sm:flex-wrap">
                                <!-- Role -->
                                <x-marketplace::shop.form.control-group class="w-full">
                                    <x-marketplace::shop.form.control-group.label class="required">
                                        @lang('marketplace::app.shop.sellers.account.users.index.role')
                                    </x-marketplace::shop.form.control-group.label>

                                    <x-marketplace::shop.form.control-group.control
                                        type="select"
                                        id="marketplace_role_id"
                                        name="marketplace_role_id"
                                        rules="required"
                                        v-model="selectedSeller.marketplace_role_id"
                                        :label="trans('marketplace::app.shop.sellers.account.users.index.role')"
                                        :placeholder="trans('marketplace::app.shop.sellers.account.users.index.role')"
                                    >
                                        <option value="">
                                            @lang('marketplace::app.shop.sellers.account.users.index.select-role')
                                        </option>

                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}">
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </x-marketplace::shop.form.control-group.control>

                                    <x-marketplace::shop.form.control-group.error control-name="marketplace_role_id" />
                                </x-marketplace::shop.form.control-group>

                                <!-- Status -->
                                <x-marketplace::shop.form.control-group class="w-full">
                                    <x-marketplace::shop.form.control-group.label>
                                        @lang('marketplace::app.shop.sellers.account.users.index.status')
                                    </x-marketplace::shop.form.control-group.label>

                                    <div class="mt-2.5 w-full gap-2.5">    
                                        <x-marketplace::shop.form.control-group.control
                                            type="hidden"
                                            name="is_suspended"
                                            value="1"
                                        />
                                        
                                        <x-marketplace::shop.form.control-group.control
                                            type="switch"
                                            name="is_suspended"
                                            :value="0"
                                            :label="trans('marketplace::app.shop.sellers.account.users.index.status')"
                                            ::checked="selectedSeller.is_suspended == 0"
                                        />
                                    </div>
                                </x-marketplace::shop.form.control-group>
                            </div>
                        </x-slot:content>
                        
                        <!-- Modal Footer -->
                        <x-slot:footer>
                            <button class="primary-button">
                                @lang('marketplace::app.shop.sellers.account.users.index.save-btn')
                            </button>
                        </x-slot:footer>
                    </x-marketplace::shop.modal>
                </form>
            </x-marketplace::shop.form>
        </script>

        <script type="module">
            app.component('v-seller-user', {
                template: '#v-seller-user-template',

                data() {
                    return {
                        selectedSeller: {},
                    }
                },

                methods: {
                    updateOrCreate(params, { resetForm, setErrors }) {
                        let formData = new FormData(this.$refs.userCreateForm);

                        if (params.id) {
                            formData.append('_method', 'put');
                        }                        

                        this.$axios.post(params.id ? "{{ route('shop.marketplace.seller.account.users.update')}}" : "{{ route('shop.marketplace.seller.account.users.store') }}", formData)
                            .then((response) => {
                                this.$refs.userCreateModel.toggle();

                                this.$refs.datagrid.get();

                                this.$emitter.emit('add-flash', {
                                    type: 'success',
                                    message: response.data.message
                                });

                                resetForm();
                            })
                            .catch(error => {
                                if (error.response.status == 422) {
                                    setErrors(error.response.data.errors);
                                }
                            });
                    },

                    editModal(url) {
                        this.$axios.get(url)
                            .then((response) => {
                                if (response.data.id) {
                                    this.selectedSeller = response.data;

                                    console.log(this.selectedSeller);
                                    

                                    this.$refs.userCreateModel.toggle();
                                } else {
                                    this.$emitter.emit('add-flash', {
                                        type: 'error',
                                        message: response.data.message
                                    });
                                }
                            })
                            .catch(error => {});
                    },
                }
            });
        </script>
    @endPushOnce
</x-marketplace::shop.layouts>
