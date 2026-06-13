@props(['href' => null])

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => 'inline-flex min-h-11 items-center justify-center rounded-button border border-cimuning-red bg-white px-5 py-3 text-sm font-semibold text-cimuning-red transition hover:bg-cimuning-soft focus:outline-2']) }}>
        {{ $slot }}
    </a>
@else
    <button type="button" {{ $attributes->merge(['class' => 'inline-flex min-h-11 items-center justify-center rounded-button border border-cimuning-red bg-white px-5 py-3 text-sm font-semibold text-cimuning-red transition hover:bg-cimuning-soft focus:outline-2']) }}>
        {{ $slot }}
    </button>
@endif
