@props(['href' => null])

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => 'inline-flex min-h-11 items-center justify-center rounded-button bg-cimuning-red px-5 py-3 text-sm font-semibold text-white transition hover:bg-cimuning-deep focus:outline-2']) }}>
        {{ $slot }}
    </a>
@else
    <button type="submit" {{ $attributes->merge(['class' => 'inline-flex min-h-11 items-center justify-center rounded-button bg-cimuning-red px-5 py-3 text-sm font-semibold text-white transition hover:bg-cimuning-deep focus:outline-2']) }}>
        {{ $slot }}
    </button>
@endif
