<div class="fixed h-full w-[260px] border-r border-[#E9E9E9] bg-white transition-all duration-300 group-[.sidebar-collapsed]/container:w-[70px] max-lg:hidden">
    <div class="journal-scroll h-[calc(100vh-120px)] overflow-auto group-[.sidebar-collapsed]/container:overflow-y-auto">
        <nav class="grid w-full gap-2">
            @foreach (menu()->getItems('seller') as $menuItem)
                @if (seller()->hasPermission($menuItem->key))
                    <div class="{{ $menuItem->isActive() ? 'active' : 'inactive' }}">
                        <a
                            href="{{ $menuItem->getUrl() }}"
                            class="flex cursor-pointer justify-between border-[#E9E9E9] p-5 hover:bg-[#f3f4f682]"
                        >
                            <div class="flex items-center gap-x-4">
                                <span class="{{ $menuItem->getIcon() }} text-2xl"></span>

                                <span class="whitespace-nowrap font-medium group-[.sidebar-collapsed]/container:hidden">
                                    @lang($menuItem->getName())
                                </span>
                            </div>
                            
                            @if ($menuItem->isActive())
                                <span class="mp-arrow-right-icon text-2xl max-md:hidden"></span>
                            @endif
                        </a>

                        @if ($menuItem->haveChildren())
                            <div class="{{ $menuItem->isActive() ? '!grid bg-gray-100' : '' }} hidden min-w-[180px] ltr:pl-12 rtl:pr-12 rounded-b-lg z-[100] overflow-hidden group-[.sidebar-collapsed]/container:!hidden">
                                @foreach ($menuItem->getChildren() as $subMenuItem)
                                    <a
                                        href="{{ $subMenuItem->getUrl() }}"
                                        class="{{ $subMenuItem->isActive() ? 'text-navyBlue' : '' }} p-2.5 font-medium"
                                    >
                                        @lang($subMenuItem->getName())
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif
            @endforeach
        </nav>
    </div>

    <!-- Collapse menu -->
    <v-sidebar-collapse></v-sidebar-collapse>
</div>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-sidebar-collapse-template"
    >
        <div
            class="fixed bottom-0 w-full max-w-[270px] cursor-pointer border-t border-gray-200 bg-white px-4 transition-all duration-300 hover:bg-gray-100"
            :class="{'max-w-[70px]': isCollapsed}"
            @click="toggle"
        >
            <div class="flex items-center gap-2.5 p-1.5">
                <span
                    class="icon-collapse text-2xl transition-all"
                    :class="[isCollapsed ? 'ltr:rotate-[180deg] rtl:rotate-[0]' : 'ltr:rotate-[0] rtl:rotate-[180deg]']"
                ></span>
            </div>
        </div>
    </script>

    <script type="module">
        app.component('v-sidebar-collapse', {
            template: '#v-sidebar-collapse-template',

            data() {
                return {
                    isCollapsed: {{ request()->cookie('sidebar_collapsed') ?? 0 }},
                }
            },

            methods: {
                toggle() {
                    this.isCollapsed = parseInt(this.isCollapsedCookie()) ? 0 : 1;

                    var expiryDate = new Date();

                    expiryDate.setMonth(expiryDate.getMonth() + 1);

                    document.cookie = 'sidebar_collapsed=' + this.isCollapsed + '; path=/; expires=' + expiryDate.toGMTString();

                    this.$root.$refs.appLayout.classList.toggle('sidebar-collapsed');
                },

                isCollapsedCookie() {
                    const cookies = document.cookie.split(';');

                    for (const cookie of cookies) {
                        const [name, value] = cookie.trim().split('=');

                        if (name === 'sidebar_collapsed') {
                            return value;
                        }
                    }
                    
                    return 0;
                },
            },
        });
    </script>
@endpushOnce