<x-admin::layouts>
    <x-slot:title>
        @lang('marketplace::app.admin.seller-reviews.index.title')
    </x-slot:title>

    <div class="flex items-center justify-between gap-4 max-sm:flex-wrap">
        <p class="py-2.5 text-xl font-bold text-gray-800 dark:text-white">
            @lang('marketplace::app.admin.seller-reviews.index.title')
        </p>
    </div>

    {!! view_render_event('marketplace.admin.seller_reviews.list.before') !!}
    
    <x-admin::datagrid
        src="{{ route('admin.marketplace.seller_reviews.index') }}"
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
                <div class="row grid grid-cols-[1.5fr_1fr_2fr_0.2fr] grid-rows-1 items-center border-b px-4 py-2.5 dark:border-gray-800">
                    <div
                        class="flex items-center gap-2.5"
                        v-for="(columnGroup, index) in [['customer_name', 'status', 'id'], ['seller_name', 'rating', 'created_at'], ['shop_title', 'title', 'comment']]"
                    >
                        @if (bouncer()->hasPermission('marketplace.seller-reviews.mass-update'))
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
                    class="row grid grid-cols-[1.5fr_1fr_2fr_0.2fr] border-b px-4 py-2.5 transition-all hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-gray-950"
                    v-for="record in available.records"
                >
                    <div class="flex gap-2.5">
                        @if (bouncer()->hasPermission('marketplace.seller-reviews.mass-update'))
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

                        <!-- Customer Name, Status, Id -->
                        <div class="grid content-baseline gap-y-1.5">
                            <p
                                class="text-base font-semibold text-gray-800 dark:text-white"
                                v-text="record.customer_name"
                            >
                            </p>

                            <p v-html="record.status"></p>

                            <p
                                class="text-gray-600 dark:text-gray-300"
                            >
                                @{{ "@lang('marketplace::app.admin.seller-reviews.index.datagrid.review-id')".replace(':review_id', record.id) }}
                            </p>
                        </div>
                    </div>

                    <!-- Seller Name, Rating, Date -->
                    <div class="grid content-baseline gap-y-1.5">
                        <p
                            class="text-base font-semibold text-gray-800 dark:text-white"
                            v-text="record.seller_name"
                        >
                        </p>

                        <div class="flex">
                            <x-admin::star-rating 
                                :is-editable="false"
                                ::value="record.rating"
                            >
                            </x-admin::star-rating>
                        </div>

                        <p
                            class="text-gray-600 dark:text-gray-300"
                            v-text="record.created_at"
                        >
                        </p>
                    </div>

                    <!-- Shop Title, Review Title, Comment -->
                    <div class="grid content-baseline gap-y-1.5">
                        <p
                            class="text-base font-semibold text-gray-800 dark:text-white"
                            v-text="record.shop_title"
                        >
                        </p>

                        <p
                            class="text-base font-semibold text-gray-600 dark:text-white"
                            v-text="record.title"
                        >
                        </p>

                        <p
                            class="text-gray-600 dark:text-gray-300"
                            v-text="record.comment"
                        >
                        </p>
                    </div>

                    <!-- View Button -->
                    <a
                        v-if="record.actions.find(action => action.title === 'View')"
                        @click="edit(record.actions.find(action => action.title === 'View')?.url)"
                    >
                        <span class="icon-view cursor-pointer rounded-md p-1.5 text-xl transition-all hover:bg-gray-200 dark:hover:bg-gray-800 ltr:ml-1 rtl:mr-1">
                        </span>
                    </a>
                </div>
            </template>

            <!-- Datagrid Body Shimmer -->
            <template v-else>
                <x-admin::shimmer.datagrid.table.body :isMultiRow="true"></x-admin::shimmer.datagrid.table.body>
            </template>
        </template>
    </x-admin::datagrid>

    {!! view_render_event('marketplace.admin.seller_reviews.list.after') !!}
</x-admin::layouts>
