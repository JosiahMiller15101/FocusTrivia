@props([
    'icon', 'title', 'value', 'description', 'iconClass' => ''
])
<div class="bg-white rounded-lg shadow p-4 ring-2 ring-gray-400">
    <div class="pb-2">
        <div class="text-sm font-medium">{{ $title }}</div>
    </div>
    <div>
        <div class="flex items-center">
            {!! $icon !!}
            <div class="text-2xl font-bold">{{ $value }}</div>
        </div>
        <p class="text-xs text-gray-500 mt-1">{{ $description }}</p>
    </div>
</div>
