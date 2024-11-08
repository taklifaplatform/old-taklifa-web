<x-marketplace::shop.layouts>
    <x-slot:title>
        @lang('marketplace::app.shop.sellers.account.product-reviews.index.title')
    </x-slot>

    <!-- Breadcrumbs -->
    @section('breadcrumbs')
        <x-marketplace::shop.breadcrumbs name="seller_product_reviews" />
    @endSection

    <div class="flex items-center justify-between">
        <div class="">
            <h2 class="text-2xl font-medium">
                @lang('marketplace::app.shop.sellers.account.product-reviews.index.title')
            </h2>
        </div>
    </div>

    {!! view_render_event('marketplace.seller.product_reviews.list.before') !!}
    
    <!-- Datagrid -->
    <x-shop::datagrid
        :src="route('shop.marketplace.seller.account.products.review')"
        :isMultiRow="true"
    >
        @php
            $hasPermission = seller()->hasPermission('product_reviews.edit');
        @endphp

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
                <div class="row grid grid-cols-[1fr_1fr_2fr] grid-rows-1 items-center border-b px-4 py-2.5">
                    <div
                        class="flex items-center gap-2.5"
                        v-for="(columnGroup, index) in [['customer_full_name', 'email'], ['product_name', 'created_at', 'product_review_status'], ['rating', 'title', 'comment']]"
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
                                    class="icon-uncheck cursor-pointer rounded-md text-2xl"
                                    :class="[
                                        applied.massActions.meta.mode === 'all' ? 'mp-checked-icon text-blue-600' : (
                                            applied.massActions.meta.mode === 'partial' ? 'icon-checkbox-partial text-blue-600' : ''
                                        ),
                                    ]"
                                >
                                </span>
                            </label>
                        @endif
                        
                        <!-- Product Name, Review Status -->
                        <p class="text-sm font-medium leading-5">
                            <span class="[&>*]:after:content-['_/_']">
                                <template v-for="column in columnGroup">
                                    <span
                                        class="after:content-['/'] last:after:content-['']"
                                        :class="{
                                            'font-medium': applied.sort.column == column,
                                            'cursor-pointer': available.columns.find(columnTemp => columnTemp.index === column)?.sortable,
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
            performAction,
        }">
            <template v-if="! isLoading">
                <div
                    class="row grid grid-cols-[1fr_1fr_2fr] grid-rows-1 border-b px-4 py-2.5"
                    v-for="record in available.records"
                >
                    <!-- customer Name, Email -->
                    <div class="flex gap-2.5">
                        @if ($hasPermission)
                            <input
                                type="checkbox"
                                :name="`mass_action_select_record_${record.product_review_id}`"
                                :id="`mass_action_select_record_${record.product_review_id}`"
                                :value="record.product_review_id"
                                class="peer hidden"
                                v-model="applied.massActions.indices"
                                @change="setCurrentSelectionMode"
                            >

                            <label
                                class="cursor-pointer rounded-md text-2xl peer-checked:text-blue-600"
                                :class="applied.massActions.indices.includes(record.product_review_id) ? 'mp-checked-icon' : 'icon-uncheck'"
                                :for="`mass_action_select_record_${record.product_review_id}`"
                            >
                            </label>
                        @endif
                        
                        <div class="flex flex-col gap-y-1.5">
                            <p
                                class="text-sm font-semibold leading-5 text-gray-800"
                                v-text="record.customer_full_name"
                            >
                            </p>

                            <p
                                class="text-sm leading-5 text-gray-600"
                                v-text="record.email"
                            >
                            </p>
                        </div>
                    </div>

                    <!-- Product Name, Date, Status -->
                    <div class="flex flex-col gap-y-1.5">
                        <p
                            class="text-sm font-semibold leading-5 text-[#2F80ED]"
                            v-html="record.product_name"
                        >
                        </p>

                        <p
                            class="text-sm leading-5 text-gray-600"
                            v-text="record.created_at"
                        >
                        </p>

                        <p
                            class="text-sm leading-5 text-gray-600"
                            v-html="record.product_review_status"
                        >
                        </p>
                    </div>

                    <!-- Rating, Review Title, Comment -->
                    <div class="flex flex-col gap-y-1.5">
                        <div class="flex">
                            <x-marketplace::shop.products.star-rating 
                                :is-editable="false"
                                ::value="record.rating"
                            >
                            </x-marketplace::shop.products.star-rating>
                        </div>

                        <p
                            class="text-sm font-semibold leading-5 text-gray-600"
                            v-text="record.title"
                        >
                        </p>

                        <p
                            class="text-sm leading-5 text-gray-600"
                            v-text="record.comment"
                        >
                        </p>
                    </div>
                </div>
            </template>

            <!-- Datagrid Body Shimmer -->
            <template v-else>
                <x-shop::shimmer.datagrid.table.body :isMultiRow="true" />
            </template>
        </template>
    </x-shop::datagrid>

    {!! view_render_event('marketplace.seller.product_reviews.list.after') !!}
</x-marketplace::shop.layouts>
