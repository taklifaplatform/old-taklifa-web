<x-admin::layouts>
    <!-- Title of the page -->
    <x-slot:title>
        @lang('marketplace::app.admin.seller-flag-reasons.index.title')
    </x-slot:title>

    <v-seller-flag-reasons>
        <div class="flex items-center justify-between gap-4 max-sm:flex-wrap">
            <p class="py-2.5 text-xl font-bold text-gray-800 dark:text-white">
                @lang('marketplace::app.admin.seller-flag-reasons.index.title')
            </p>

            <div class="flex items-center gap-x-2.5">
                <!-- Create reason Button -->
                <button
                    type="button"
                    class="primary-button"
                >
                    @lang('marketplace::app.admin.seller-flag-reasons.index.create-btn')
                </button>
            </div>
        </div>

        <!-- DataGrid Shimmer -->
        <x-admin::shimmer.datagrid/>
    </v-seller-flag-reasons>

    @pushOnce('scripts')
        <script 
            type="text/x-template" 
            id="v-seller-flag-reasons-template"
        >
            <!-- Create Button -->
            <div class="flex items-center justify-between gap-4 max-sm:flex-wrap">
                <p class="text-xl font-bold text-gray-800 dark:text-white">
                    @lang('marketplace::app.admin.seller-flag-reasons.index.title')
                </p>

                <div class="flex items-center gap-x-2.5">
                    {!! view_render_event('marketplace.admin.seller_flag_reasons.index.create_button.before') !!}
                    
                    <!-- Create reason Button -->
                    @if (bouncer()->hasPermission('marketplace.seller-flag-reasons.create'))
                        <button 
                            type="button"
                            class="primary-button"
                            @click="selectedReason={}; $refs.sellerFlagReasons.toggle()"
                        >
                            @lang('marketplace::app.admin.seller-flag-reasons.index.create-btn')
                        </button>
                    @endif

                    {!! view_render_event('marketplace.admin.seller_flag_reasons.index.create_button.after') !!}
                </div>
            </div>

            {!! view_render_event('marketplace.admin.seller_flag_reasons.list.before') !!}
            
            <!-- Datagrid -->
            <x-admin::datagrid
                src="{{ route('admin.marketplace.seller_flag_reasons.index') }}"
                ref="datagrid"
            >
                @php
                    $hasPermission = bouncer()->hasPermission('marketplace.seller-flag-reasons.mass-delete');
                @endphp

                <!-- Datagrid Header -->
                <template #header="{
                    available,
                    applied,
                    selectAll,
                    sort,
                    performAction
                }">
                    <div class="row grid grid-cols-[2fr_1fr_1fr] grid-rows-1 items-center border-b px-4 py-2.5 dark:border-gray-800"
                    :style="'grid-template-columns: repeat(' + ({{$hasPermission}} ? 5 : 4) + ', 1fr);'">
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

                        <div
                            class="flex cursor-pointer gap-2.5"
                            v-for="(columnGroup, index) in ['id', 'reason', 'status']"
                        >
                            <p class="text-gray-600 dark:text-gray-300">
                                <span class="[&>*]:after:content-['_/_']">
                                    <span
                                        class="after:content-['/'] last:after:content-['']"
                                        :class="{
                                            'text-gray-800 dark:text-white font-medium': applied.sort.column == columnGroup,
                                            'cursor-pointer hover:text-gray-800 dark:hover:text-white': available.columns.find(columnTemp => columnTemp.index === columnGroup)?.sortable,
                                        }"
                                        @click="
                                            available.columns.find(columnTemp => columnTemp.index === columnGroup)?.sortable ? sort(available.columns.find(columnTemp => columnTemp.index === columnGroup)): {}
                                        "
                                    >
                                        @{{ available.columns.find(columnTemp => columnTemp.index === columnGroup)?.label }}
                                    </span>
                                </span>

                                <!-- Filter Arrow Icon -->
                                <i
                                    class="align-text-bottom text-base text-gray-800 dark:text-white ltr:ml-1 rtl:mr-1"
                                    :class="[applied.sort.order === 'asc' ? 'icon-down-stat': 'icon-up-stat']"
                                    v-if="columnGroup.includes(applied.sort.column)"
                                ></i>
                            </p>
                        </div>

                        <!-- Actions -->
                        @if (bouncer()->hasPermission('marketplace.seller-flag-reasons.edit')
                            || bouncer()->hasPermission('marketplace.seller-flag-reasons.delete')
                        )
                            <p class="flex justify-end gap-2.5">
                                @lang('admin::app.components.datagrid.table.actions')
                            </p>
                        @endif
                    </div>
                </template>

                <!-- DataGrid Body -->
                <template #body="{
                    available,
                    applied,
                    selectAll,
                    sort,
                    performAction
                }">
                    <div
                        v-for="record in available.records"
                        class="row grid items-center gap-2.5 border-b px-4 py-4 text-gray-600 transition-all hover:bg-gray-50 dark:border-gray-800 dark:text-gray-300 dark:hover:bg-gray-950"
                        :style="'grid-template-columns: repeat(' + (record.actions.length ? 5 : 4) + ', 1fr);'"
                    >
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

                        <!-- Id -->
                        <p v-text="record.id"></p>

                        <!-- Reason -->
                        <p v-text="record.reason"></p>

                        <!-- Status -->
                        <p v-html="record.status"></p>

                        <!-- Actions -->
                        <div class="flex justify-end">
                            @if (bouncer()->hasPermission('marketplace.seller-flag-reasons.edit'))
                                <a @click="editModal(record.actions.find(action => action.title === 'Edit')?.url)">
                                    <span
                                        :class="record.actions.find(action => action.title === 'Edit')?.icon"
                                        class="cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-200 dark:hover:bg-gray-800 max-sm:place-self-center"
                                        :title="record.actions.find(action => action.title === 'Edit')?.title"
                                    >
                                    </span>
                                </a>
                            @endif

                            @if (bouncer()->hasPermission('marketplace.seller-flag-reasons.delete'))
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
                </template>
            </x-admin::datagrid>

            {!! view_render_event('marketplace.admin.seller_flag_reasons.list.after') !!}

            <!-- Seller Flag Reason Create form -->
            <x-admin::form
                v-slot="{ meta, errors, handleSubmit }"
                as="div"
                ref="modalForm"
            >
                <form
                    @submit="handleSubmit($event, updateOrCreate)"
                    ref="flagCreateForm"
                >
                    <x-admin::modal ref="sellerFlagReasons">
                        <!-- Modal Header -->
                        <x-slot:header>
                            <p
                                class="text-lg font-bold text-gray-800 dark:text-white"
                                v-if="selectedReason.id"
                            >
                                @lang('marketplace::app.admin.seller-flag-reasons.index.edit.title')
                            </p>

                            <p 
                                class="text-lg font-bold text-gray-800 dark:text-white"
                                v-else
                            >
                                @lang('marketplace::app.admin.seller-flag-reasons.index.create.title')
                            </p>
                        </x-slot:header>

                        <!-- Modal Content -->
                        <x-slot:content>
                            {!! view_render_event('marketplace.admin.seller_flag_reasons.index.create_from_controls.before') !!}
                            
                            <!-- Id -->
                            <x-admin::form.control-group.control
                                type="hidden"
                                name="id"
                                v-model="selectedReason.id"
                            />

                            <!-- Reason -->
                            <x-admin::form.control-group class="mb-2.5">
                                <x-admin::form.control-group.label class="required">
                                    @lang('marketplace::app.admin.seller-flag-reasons.index.create.reason')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    name="reason"
                                    :value="old('reason')"
                                    rules="required"
                                    v-model="selectedReason.reason"
                                    :label="trans('marketplace::app.admin.seller-flag-reasons.index.create.reason')"
                                    :placeholder="trans('marketplace::app.admin.seller-flag-reasons.index.create.reason')"
                                />

                                <x-admin::form.control-group.error control-name="reason" />
                            </x-admin::form.control-group>

                            <!-- Status -->
                            <x-admin::form.control-group class="!mb-0 w-full flex-1">
                                <x-admin::form.control-group.label>
                                    @lang('marketplace::app.admin.seller-flag-reasons.index.create.status')
                                </x-admin::form.control-group.label>

                                <div class="mt-2.5 w-full gap-2.5">    
                                    <x-admin::form.control-group.control
                                        type="hidden"
                                        name="status"
                                        value="0"
                                    />
                                    
                                    <x-admin::form.control-group.control
                                        type="switch"
                                        name="status"
                                        :value="1"
                                        :label="trans('marketplace::app.admin.seller-flag-reasons.index.create.status')"
                                        ::checked="selectedReason.status"
                                    />
                                    
                                    <x-admin::form.control-group.error control-name="status" />
                                </div>
                            </x-admin::form.control-group>

                            {!! view_render_event('marketplace.admin.seller_flag_reasons.index.create_from_controls.after') !!}
                        </x-slot:content>
                        
                        <!-- Modal Footer -->
                        <x-slot:footer>
                            <!-- Modal Submission -->
                            <div class="flex items-center gap-x-2.5">
                                <button class="primary-button">
                                    @lang('marketplace::app.admin.seller-flag-reasons.index.create.save-btn')
                                </button>
                            </div>
                        </x-slot:footer>
                    </x-admin::modal>
                </form>
            </x-admin::form>
        </script>

        <script type="module">
            app.component('v-seller-flag-reasons', {
                template: '#v-seller-flag-reasons-template',

                data() {
                    return {
                        selectedReason: {},
                    }
                },

                methods: {
                    updateOrCreate(params, { resetForm, setErrors }) {
                        let formData = new FormData(this.$refs.flagCreateForm);

                        if (params.id) {
                            formData.append('_method', 'put');
                        }

                        this.$axios.post(params.id ? "{{ route('admin.marketplace.seller_flag_reasons.update')}}" : "{{ route('admin.marketplace.seller_flag_reasons.store') }}", formData)
                            .then((response) => {
                                this.$refs.sellerFlagReasons.toggle();

                                this.$refs.datagrid.get();

                                this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
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
                                    this.selectedReason = response.data;

                                    this.$refs.sellerFlagReasons.toggle();
                                } else {
                                    this.$emitter.emit('add-flash', { type: 'error', message: response.data.message });
                                }
                            })
                            .catch(error => [
                                this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message })
                            ]);
                    },
                }
            });
        </script>
    @endPushOnce
</x-admin::layouts>
