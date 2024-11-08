<x-marketplace::shop.layouts>
    <x-slot:title>
        @lang('marketplace::app.shop.sellers.account.products.index.title')
    </x-slot:title>

    <!-- Breadcrumbs -->
    @section('breadcrumbs')
        <x-marketplace::shop.breadcrumbs name="seller_products" />
    @endSection

    <div class="flex items-center justify-between gap-4 max-sm:flex-wrap">
        <!-- Page Title -->
        <h1 class="text-2xl font-medium">
            @lang('marketplace::app.shop.sellers.account.products.index.title')
        </h1>

        <!-- Create Product Button -->
        <div class="flex items-center gap-x-2.5">
            {!! view_render_event('marketplace.seller.products.index.add_product_button.before') !!}

            @if (seller()->hasPermission('products.create'))
                <a
                    href="{{ route('shop.marketplace.seller.account.products.create') }}"
                    class="primary-button px-5 py-2.5"
                >
                    @lang('marketplace::app.shop.sellers.account.products.index.add-new-product')
                </a>
            @endif

            {!! view_render_event('marketplace.seller.products.index.add_product_button.after') !!}
        </div>
    </div>

    {!! view_render_event('marketplace.seller.products.list.before') !!}
    
    <!-- Datagrid -->
    <x-shop::datagrid
        :src="route('shop.marketplace.seller.account.products.index')"
        :isMultiRow="true"
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
                        v-for="(columnGroup, index) in [['name', 'sku', 'product_number'], ['base_image', 'price', 'quantity', 'product_id'], ['status', 'category_name', 'product_type']]"
                    >
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
                                class="icon-uncheck cursor-pointer rounded-md text-2xl"
                                :class="[
                                    applied.massActions.meta.mode === 'all' ? 'mp-checked-icon text-blue-600' : (
                                        applied.massActions.meta.mode === 'partial' ? 'icon-checkbox-partial text-blue-600' : ''
                                    ),
                                ]"
                            >
                            </span>
                        </label>

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
                <x-shop::shimmer.datagrid.table.head :isMultiRow="true"></x-shop::shimmer.datagrid.table.head>
            </template>
        </template>

        <!-- Datagrid Body -->
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
                    <!-- Name, SKU, Product Number -->
                    <div class="flex gap-2.5">
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
                            class="cursor-pointer rounded-md text-2xl peer-checked:text-blue-600"
                            :class="applied.massActions.indices.includes(record.marketplace_product_id) ? 'mp-checked-icon' : 'icon-uncheck'"
                            :for="`mass_action_select_record_${record.marketplace_product_id}`"
                        >
                        </label>

                        <div class="flex flex-col gap-1.5">
                            <p
                                class="text-sm font-semibold leading-5 text-gray-800"
                                v-text="record.name"
                            >
                            </p>

                            <p
                                class="text-sm leading-5 text-gray-600"
                                v-text="record.sku"
                            >
                            </p>

                            <p
                                class="text-sm leading-5 text-gray-600"
                                v-text="record.product_number"
                            >
                            </p>
                        </div>
                    </div>

                    <!-- Image, Price, product Id, Stock -->
                    <div class="flex gap-3">
                        <div class="relative">
                            <template v-if="record.base_image">
                                <img
                                    class="max-h-16 min-h-full min-w-16 max-w-16 rounded"
                                    :src="`{{ Storage::url('') }}`+record.base_image"
                                />

                                <span
                                    class="absolute bottom-px rounded-full bg-rose-500 px-1.5 text-xs font-bold leading-normal text-white ltr:left-px rtl:right-px"
                                    v-text="record.images_count"
                                >
                                </span>
                            </template>

                            <template v-else>
                                <div class="relative h-15 max-h-15 w-full max-w-15 rounded border border-dashed border-gray-300">
                                    <img src="{{ bagisto_asset('images/small-product-placeholder.webp') }}">
                                </div>
                            </template>
                        </div>

                        <div class="flex flex-col">
                            <p
                                class="text-sm font-semibold leading-5 text-gray-800"
                                v-text="record.price"
                            >
                            </p>

                            <!-- Product Quantity -->
                            <div v-if="['configurable', 'bundle', 'grouped', 'downloadable'].includes(record.product_type)">
                                <p class="text-gray-600">
                                    <span class="text-red-600" v-text="'N/A'"></span>
                                </p>
                            </div>

                            <div v-else>
                                <p
                                    class="text-gray-600"
                                    v-if="record.quantity > 0"
                                >
                                    <span class="text-green-600">
                                        @{{ "@lang('marketplace::app.shop.sellers.account.products.index.datagrid.total-quantity')".replace(':quantity', record.quantity) }}
                                    </span>
                                </p>

                                <p
                                    class="text-gray-600"
                                    v-else
                                >
                                    <span class="text-red-600">
                                        @lang('marketplace::app.shop.sellers.account.products.index.datagrid.out-of-stock')
                                    </span>
                                </p>
                            </div>

                            <p class="text-sm font-normal">
                                @{{ "@lang('marketplace::app.shop.sellers.account.products.index.datagrid.product_id')".replace(':product_id', record.marketplace_product_id) }}
                            </p>
                        </div>
                    </div>

                    <!-- Status, Category, Type Columns -->
                    <div class="flex items-center justify-between gap-x-4">
                        <div class="flex flex-col gap-1.5">
                            <div class="flex gap-1">
                                <p :class="[record.status ? 'label-active': 'label-info']">
                                    @{{ record.status ? "@lang('marketplace::app.shop.sellers.account.products.index.datagrid.active')" : "@lang('marketplace::app.shop.sellers.account.products.index.datagrid.disable')" }}
                                </p>

                                <p :class="[record.is_approved ? 'label-active': 'label-info']">
                                    @{{ record.is_approved ? "@lang('marketplace::app.shop.sellers.account.products.index.datagrid.approved')" : "@lang('marketplace::app.shop.sellers.account.products.index.datagrid.disapproved')" }}
                                </p>
                            </div>
                            
                            <p
                                class="text-sm font-normal"
                                v-text="record.category_name ?? 'N/A'"
                            >
                            </p>

                            <p
                                class="text-sm font-normal"
                                v-text="record.product_type"
                            >
                            </p>
                        </div>

                        <div
                            v-if="record.actions.length"
                            class="flex items-center"
                        >
                            <a @click="performAction(record.actions.find(action => action.method === 'DELETE'))">
                                <span
                                    :class="record.actions.find(action => action.method === 'DELETE')?.icon"
                                    class="cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-200 max-sm:place-self-center"
                                    :title="record.actions.find(action => action.method === 'DELETE')?.title"
                                >
                                </span>
                            </a>

                            <a :href="record.actions.find(action => action.method === 'GET')?.url">
                                <span
                                    :class="record.actions.find(action => action.method === 'GET')?.icon"
                                    class="cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-200 max-sm:place-self-center"
                                    :title="record.actions.find(action => action.method === 'GET')?.title"
                                >
                                </span>
                            </a>
                        </div>

                    </div>
                </div>
            </template>

            <!-- Datagrid Body Shimmer -->
            <template v-else>
                <x-shop::shimmer.datagrid.table.body :isMultiRow="true"></x-shop::shimmer.datagrid.table.body>
            </template>
        </template>
    </x-shop::datagrid>

    {!! view_render_event('marketplace.seller.products.list.after') !!}
</x-marketplace::shop.layouts>
