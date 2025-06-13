<button {{ $attributes->merge(['class' => 'rounded-md bg-slate-500 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-slate-600 focus-visible:outline-slate-500', 'type' => 'submit']) }}>
    {{$slot}}
</button>

