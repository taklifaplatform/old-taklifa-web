{!! view_render_event('bagisto.admin.catalog.product.edit.form.tags.before', ['product' => $product]) !!}

<!-- Panel -->
<div>
    {!! view_render_event('bagisto.admin.catalog.product.edit.form.tags.controls.before', [
        'product' => $product,
    ]) !!}
    <!-- Tags -->
    @php
        $selectedTags = old('tags') ?: $product->tags->pluck('id')->toArray();
    @endphp

    <x-admin::accordion>
        <x-slot:header>
            <p class="required p-2.5 text-base text-gray-800 dark:text-white font-semibold">
                @lang('admin::app.catalog.tags.edit.tags')
            </p>
        </x-slot>

        <x-slot:content>
            @foreach ($tags as $tag)
                <x-admin::form.control-group class="flex gap-2.5 items-center !mb-2 last:!mb-0">
                    <x-admin::form.control-group.control type="checkbox" :id="$tag->name ?? $tag->admin_name" name="tags[]"
                        :value="$tag->id" :for="$tag->name ?? $tag->admin_name" :label="trans('admin::app.catalog.tags.edit.tags')" :checked="in_array($tag->id, $selectedTags)" />

                    <label class="text-xs text-gray-600 dark:text-gray-300 font-medium cursor-pointer"
                        for="{{ $tag->name ?? $tag->admin_name }}">
                        {{ $tag->name ?? $tag->admin_name }}
                    </label>
                </x-admin::form.control-group>
            @endforeach
        </x-slot>
    </x-admin::accordion>


    {!! view_render_event('bagisto.admin.catalog.product.edit.form.tags.controls.after', [
        'product' => $product,
    ]) !!}
</div>

{!! view_render_event('bagisto.admin.catalog.product.edit.form.tags.after', ['product' => $product]) !!}

@pushOnce('scripts')
    <script type="module">
        app.component('v-product-tags', {
            template: '#v-product-tags-template',

            data() {
                return {
                    //
                }
            },

            mounted() {
                this.get();
            },

            methods: {
                //
            }
        });
    </script>
@endpushOnce
