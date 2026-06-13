@props(['href' => '#'])

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'inline-flex min-h-11 items-center justify-center rounded-button bg-cimuning-whatsapp px-5 py-3 text-sm font-semibold text-white transition hover:brightness-95 focus:outline-2']) }}>
    {{ $slot }}
</a>
