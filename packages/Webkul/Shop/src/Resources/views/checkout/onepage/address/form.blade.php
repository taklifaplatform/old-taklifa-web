@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-checkout-address-form-template"
    >
        <div class="mt-2 max-sm:max-w-full max-sm:flex-auto">
            <x-shop::form.control-group class="hidden">
                <x-shop::form.control-group.control
                    type="text"
                    ::name="controlName + '.id'"
                    ::value="address.id"

                />
            </x-shop::form.control-group>

            <!-- First Name-->
            <div class="grid grid-cols-2 gap-x-5">
                <x-shop::form.control-group>
                    <x-shop::form.control-group.label class="!mt-0 required">
                        @lang('shop::app.checkout.onepage.address.first-name')
                    </x-shop::form.control-group.label>

                    <x-shop::form.control-group.control
                        type="text"
                        ::name="controlName + '.first_name'"
                        ::value="address.first_name"
                        rules="required"
                        :label="trans('shop::app.checkout.onepage.address.first-name')"
                        :placeholder="trans('shop::app.checkout.onepage.address.first-name')"
                    />

                    <x-shop::form.control-group.error ::name="controlName + '.first_name'" />
                </x-shop::form.control-group>

                {!! view_render_event('bagisto.shop.checkout.onepage.address.form.first_name.after') !!}

                <!-- Last Name -->
                <x-shop::form.control-group>
                    <x-shop::form.control-group.label class="!mt-0 required">
                        @lang('shop::app.checkout.onepage.address.last-name')
                    </x-shop::form.control-group.label>

                    <x-shop::form.control-group.control
                        type="text"
                        ::name="controlName + '.last_name'"
                        ::value="address.last_name"
                        rules="required"
                        :label="trans('shop::app.checkout.onepage.address.last-name')"
                        :placeholder="trans('shop::app.checkout.onepage.address.last-name')"
                    />

                    <x-shop::form.control-group.error ::name="controlName + '.last_name'" />
                </x-shop::form.control-group>

                {!! view_render_event('bagisto.shop.checkout.onepage.address.form.last_name.after') !!}
            </div>

            <div class="grid grid-cols-2 gap-x-5">
            <!-- Email -->
            <x-shop::form.control-group>
                <x-shop::form.control-group.label class="!mt-0 required">
                    @lang('shop::app.checkout.onepage.address.email')
                </x-shop::form.control-group.label>

                <x-shop::form.control-group.control
                    type="email"
                    ::name="controlName + '.email'"
                    ::value="address.email"
                    rules="required|email"
                    :label="trans('shop::app.checkout.onepage.address.email')"
                    placeholder="email@example.com"
                />

                <x-shop::form.control-group.error ::name="controlName + '.email'" />
            </x-shop::form.control-group>

            {!! view_render_event('bagisto.shop.checkout.onepage.address.form.email.after') !!}

            <!-- Phone Number -->
            <x-shop::form.control-group>
                <x-shop::form.control-group.label class="!mt-0 required">
                    @lang('shop::app.checkout.onepage.address.telephone')
                </x-shop::form.control-group.label>

                <x-shop::form.control-group.control
                    type="text"
                    ::name="controlName + '.phone'"
                    ::value="address.phone"
                    rules="required|numeric"
                    :label="trans('shop::app.checkout.onepage.address.telephone')"
                    :placeholder="trans('shop::app.checkout.onepage.address.telephone')"
                />

                <x-shop::form.control-group.error ::name="controlName + '.phone'" />
            </x-shop::form.control-group>

            {!! view_render_event('bagisto.shop.checkout.onepage.address.form.phone.after') !!}

        </div>

        <div class="grid grid-cols-2 gap-x-5">

             <!-- Country -->
        <x-shop::form.control-group class="!mb-4">
            <x-shop::form.control-group.label class="required !mt-0">
                @lang('shop::app.checkout.onepage.address.country')
            </x-shop::form.control-group.label>

            <x-shop::form.control-group.control
                type="text"
                ::name="controlName + '.country'"
                ::value="'السعودية'"
                rules="required"
                :label="trans('shop::app.checkout.onepage.address.country')"
                readonly
            />

            <x-shop::form.control-group.error ::name="controlName + '.country'" />
        </x-shop::form.control-group>

        {!! view_render_event('bagisto.shop.checkout.onepage.address.form.country.after') !!}
                <!-- City -->
                <x-shop::form.control-group>
                    <x-shop::form.control-group.label class="!mt-0 required">
                        @lang('shop::app.checkout.onepage.address.city')
                    </x-shop::form.control-group.label>

                    <x-shop::form.control-group.control
                        list="cities"
                        ::name="controlName + '.city'"
                        ::value="address.city"
                        rules="required"
                        :label="trans('shop::app.checkout.onepage.address.city')"
                        :placeholder="trans('shop::app.checkout.onepage.address.city')"
                    />
                    <datalist id="cities">
                        <option value="الوسطى">الوسطى</option>
                        <option value="الشمالية">الشمالية</option>
                        <option value="الجنوبية">الجنوبية</option>
                        <option value="الغربية">الغربية</option>
                        <option value="الشرقية">الشرقية</option>
                      </datalist>

                    <x-shop::form.control-group.error ::name="controlName + '.city'" />
                </x-shop::form.control-group>

                {!! view_render_event('bagisto.shop.checkout.onepage.address.form.city.after') !!}
            </div>
        </div>
    </script>

    <script type="module">
        app.component('v-checkout-address-form', {
            template: '#v-checkout-address-form-template',

            props: {
                controlName: {
                    type: String,
                    required: false,
                },

                address: {
                    type: Object,

                    default: () => ({
                        id: 0,
                        first_name: '',
                        last_name: '',
                        email: '',
                        country: 'Saudi Arabia',
                        city: '',
                        phone: '',
                        address: [' '],
                    }),
                },
            },

            data() {
                return {
                    selectedCountry: this.address.country,

                    countries: [],
                }
            },

            mounted() {
                this.getCountries();
            },

            methods: {
                getCountries() {
                    this.$axios.get("{{ route('shop.api.core.countries') }}")
                        .then(response => {
                            this.countries = response.data.data;
                        })
                        .catch(() => {});
                },

                haveStates(countryCode) {
                    return !!this.states[countryCode]?.length;
                },
            }
        });
    </script>
@endPushOnce
