<x-marketplace::shop.layouts>    
    <!-- Page Title -->
    <x-slot:title>
        @lang('marketplace::app.shop.sellers.account.seller-reviews.index.title')
    </x-slot>

    <!-- Breadcrumbs -->
    @section('breadcrumbs')
        <x-marketplace::shop.breadcrumbs name="seller_review" />
    @endSection

    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div class="">
            <h2 class="text-2xl font-medium">
                @lang('marketplace::app.shop.sellers.account.seller-reviews.index.title')
            </h2>
        </div>
    </div>

    {!! view_render_event('marketplace.seller.seller_reviews.list.before') !!}
    
    <!-- Datagrid -->
    <x-shop::datagrid
        :src="route('shop.marketplace.seller.account.seller_reviews.index')"
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
                <div class="row grid grid-cols-[1fr_1fr_2fr] grid-rows-1 items-center border-b px-4 py-2.5">
                    <div
                        class="flex items-center gap-2.5"
                        v-for="(columnGroup, index) in [['customer_name', 'email'], ['status',  'created_at'], ['title', 'rating', 'comment']]"
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
                    class="row grid grid-cols-[1fr_1fr_2fr] grid-rows-1 border-b px-4 py-2.5"
                    v-for="record in available.records"
                >
                    <!-- Customer Name, Email -->
                    <div class="grid content-baseline gap-y-1.5">
                        <p
                            class="text-sm font-semibold leading-5 text-gray-800"
                            v-text="record.customer_name"
                        >
                        </p>

                        <p
                            class="text-sm leading-5 text-gray-600"
                            v-text="record.email"
                        >
                        </p>
                    </div>

                    <!-- Status, Date -->
                    <div class="grid content-baseline gap-y-1.5">
                        <p
                            class="text-sm leading-5 text-gray-600"
                            v-html="record.status"
                        >
                        </p>

                        <p
                            class="text-sm leading-5 text-gray-600"
                            v-html="record.created_at"
                        >
                        </p>
                    </div>

                    <!-- Rating, Comment -->
                    <div class="grid content-baseline gap-y-1.5">
                        <div class="flex">
                            <x-marketplace::shop.products.star-rating
                                :is-editable="false"
                                ::value="record.rating"
                            />
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

    {!! view_render_event('marketplace.seller.seller_reviews.list.after') !!}
</x-marketplace::shop.layouts>
