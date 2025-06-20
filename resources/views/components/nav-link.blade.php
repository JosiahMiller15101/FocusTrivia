@props(['active' => false, 'icon' => null, 'badgeCount' => null])

<a class="{{ $active ? 'rounded-md bg-gray-900 px-3 py-2 text-sm font-medium text-white' : 'rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white'}} relative"
    aria-current="{{ $active ? 'page' : 'false' }}"
    {{ $attributes }}
>
    @if($icon)
      <span class="relative inline-block">
        {!! $icon !!}
        @if($badgeCount && $badgeCount > 0)
          <span class="absolute -top-1 -right-1 bg-red-600 text-white text-xs rounded-full px-1">
            {{ $badgeCount }}
          </span>
        @endif
      </span>
    @endif

    @if(! $icon)
      {{ $slot }}
    @endif
</a>
