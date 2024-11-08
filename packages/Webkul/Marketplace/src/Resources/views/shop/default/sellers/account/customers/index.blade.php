<x-marketplace::shop.layouts>
    <x-slot:title>
        @lang('marketplace::app.shop.sellers.account.customers.index.title')
    </x-slot>
    
    <!-- Breadcrumbs -->
    @section('breadcrumbs')
        <x-marketplace::shop.breadcrumbs name="seller_customers" />
    @endSection

    <div class="flex items-center justify-between">
        <div class="">
            <h2 class="text-2xl font-medium">
                @lang('marketplace::app.shop.sellers.account.customers.index.title')
            </h2>
        </div>
    </div>

    {!! view_render_event('marketplace.seller.customers.list.before') !!}
    
    <!-- Datagrid -->
    <x-shop::datagrid
        :src="route('shop.marketplace.seller.account.customers.index')"
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
                <div class="row grid grid-cols-[3fr_2fr_1fr] grid-rows-1 items-center border-b px-4 py-2.5">
                    <div
                        class="flex select-none items-center gap-2.5"
                        v-for="(columnGroup, index) in [['full_name', 'email', 'gender'], ['revenue', 'order_count'], ['status', 'group']]"
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
                <x-shop::shimmer.datagrid.table.head :isMultiRow="true"></x-shop::shimmer.datagrid.table.head>
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
                    class="row grid grid-cols-[3fr_2fr_1fr] grid-rows-1 items-center border-b px-4 py-2.5"
                    v-for="record in available.records"
                >
                    <div class="flex gap-2.5">
                        <div class="flex flex-col gap-1.5">
                            <p
                                class="text-sm font-semibold leading-5 text-gray-800"
                                v-text="record.full_name"
                            >
                            </p>

                            <p
                                class="text-sm leading-5 text-gray-600"
                                v-text="record.email"
                            >
                            </p>

                            <p
                                class="text-sm leading-5 text-gray-600"
                                v-text="record.gender"
                            >
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-x-4">
                        <div class="flex flex-col gap-1.5">
                            <p
                                class="text-sm font-semibold leading-5 text-gray-800"
                                v-text="$shop.formatPrice(record.revenue)"
                            >
                            </p>

                            <p
                                class="text-sm leading-5 text-gray-600"
                                v-text="record.order_count"
                            >
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-x-4">
                        <div class="flex flex-col gap-1.5">
                            <p v-html="record.status"></p>

                            <p
                                class="text-sm leading-5 text-gray-600"
                                v-text="record.group"
                            >
                            </p>
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

    {!! view_render_event('marketplace.seller.customers.list.after') !!}
</x-marketplace::shop.layouts>
