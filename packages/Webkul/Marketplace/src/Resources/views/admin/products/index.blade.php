<x-admin::layouts>
    <x-slot:title>
        @lang('marketplace::app.admin.products.index.title')
    </x-slot:title>

    <div class="flex items-center justify-between gap-4 max-sm:flex-wrap">
        <p class="py-2.5 text-xl font-bold text-gray-800 dark:text-white">
            @lang('marketplace::app.admin.products.index.title')
        </p>
    </div>

    {!! view_render_event('marketplace.admin.products.list.before') !!}
    
    <!-- Datagrid -->
    <x-admin::datagrid
        src="{{ route('admin.marketplace.products.index') }}"
        :isMultiRow="true"
    >
        <!-- Datagrid Header -->
        @php 
            $hasPermission = bouncer()->hasPermission('marketplace.products.mass-update') || bouncer()->hasPermission('marketplace.products.mass-delete');
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
                        v-for="(columnGroup, index) in [['product_flat_name', 'sku', 'product_type', 'product_number'], ['base_image', 'price', 'quantity', 'product_id'], ['seller_name', 'is_owner', 'is_approved']]"
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
                    <!-- Products Name, sku, Product Number -->
                    <div class="flex gap-2.5">
                        @if ($hasPermission)
                            <input
                                type="checkbox"
                                :name="`mass_action_select_record_${record.marketplace_product_id}`"
                                :id="`mass_action_select_record_${record.marketplace_product_id}`"
                                :value="record.marketplace_product_id"
                                class="peer hidden"
                                v-model="applied.massActions.indices"
                                @change="setCurrentSelectionMode"
                            >

                            <label
                                class="icon-uncheckbox peer-checked:icon-checked cursor-pointer rounded-md text-2xl peer-checked:text-blue-600"
                                :for="`mass_action_select_record_${record.marketplace_product_id}`"
                            ></label>
                        @endif

                        <div class="grid gap-y-1.5">
                            <p
                                class="text-base font-semibold text-gray-800 dark:text-white"
                                v-html="record.product_flat_name"
                            >
                            </p>

                            <div class="5 flex gap-x-1">
                                <p
                                    class="text-gray-600 dark:text-gray-300"
                                >
                                    @{{record.sku}} |
                                </p>
                                <p
                                    class="text-gray-600 dark:text-gray-300"
                                    v-html="record.product_type"
                                >
                                </p>
                            </div>

                            <p
                                class="text-gray-600 dark:text-gray-300"
                                v-text="record.product_number"
                            >
                            </p>
                        </div>
                    </div>

                    <!-- Image, Price, Quantity, Product ID -->
                    <div class="flex gap-1.5">
                        <div class="relative">
                            <template v-if="record.base_image">
                                <img
                                    class="max-h-16 min-h-full min-w-16 max-w-16 rounded"
                                    :src="`{{ Storage::url('') }}`+record.base_image"
                                />

                                <span
                                    class="bg-darkPink absolute bottom-px rounded-full px-1.5 text-xs font-bold leading-normal text-white ltr:left-px rtl:right-px"
                                    v-text="record.images_count"
                                >
                                </span>
                            </template>

                            <template v-else>
                                <div class="relative h-[60px] max-h-[60px] w-full max-w-[60px] rounded border border-dashed border-gray-300 dark:border-gray-800 dark:mix-blend-exclusion dark:invert">
                                    <img src="{{ bagisto_asset('images/product-placeholders/front.svg')}}">
                                </div>

                            </template>
                        </div>

                        <div class="grid gap-y-1.5">
                            <p
                                class="text-gray-600 dark:text-gray-300"
                                v-text="$admin.formatPrice(record.price)"
                            >
                            </p>

                            <!-- Product Quantity -->
                            <div v-if="['configurable', 'bundle', 'grouped', 'downloadable'].includes(record.product_type)">
                                <p class="text-gray-600 dark:text-gray-300">
                                    <span class="text-red-600" v-text="'N/A'"></span>
                                </p>
                            </div>

                            <div v-else>
                                <p
                                    class="text-gray-600 dark:text-gray-300"
                                    v-if="record.quantity > 0"
                                >
                                    <span class="text-green-600">
                                        @{{ "@lang('marketplace::app.admin.products.index.datagrid.total-quantity')".replace(':quantity', record.quantity) }}
                                    </span>
                                </p>

                                <p
                                    class="text-gray-600 dark:text-gray-300"
                                    v-else
                                >
                                    <span class="text-red-600">
                                        @lang('marketplace::app.admin.products.index.datagrid.out-of-stock')
                                    </span>
                                </p>
                            </div>

                            <p
                                class="text-gray-600 dark:text-gray-300"
                            >
                                @{{ "@lang('marketplace::app.admin.products.index.datagrid.product-id')".replace(':product_id', record.product_id) }}
                            </p>
                        </div>
                    </div>

                    <!-- Seller Name, Is Owner, Is Approved, Product Type -->
                    <div class="flex items-center justify-between gap-x-4">
                        <div class="grid gap-1.5">
                            <p
                                class="text-gray-600 dark:text-gray-300"
                                v-text="record.seller_name"
                            >
                            </p>

                            <p
                                class="text-gray-600 dark:text-gray-300"
                                v-html="record.is_owner"
                            >
                            </p>

                            <p
                                class="text-gray-600 dark:text-gray-300"
                                v-html="record.is_approved"
                            >
                            </p>
                        </div>

                        <div class="flex items-center">
                            @if (bouncer()->hasPermission('marketplace.products.delete'))
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

    {!! view_render_event('marketplace.admin.products.list.after') !!}
</x-admin::layouts>
