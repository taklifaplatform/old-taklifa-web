@props([
    'name'  => '',
    'entity' => null,
])

<div class="mb-2.5 mt-5 flex justify-start max-lg:hidden">
    <div class="flex items-center gap-x-3.5">        
        {{ Breadcrumbs::view('marketplace::shop.partials.breadcrumbs', $name, $entity) }}
    </div>
</div>
