@props([
    'name',
    'category',
    'description',
    'location',
    'imageClass' => 'from-cimuning-soft to-white',
    'verified' => true,
    'slug' => null,
    'whatsappUrl' => null,
])

<article {{ $attributes->merge(['class' => 'group overflow-hidden rounded-card border border-cimuning-border bg-white shadow-card transition duration-200 hover:-translate-y-0.5 hover:shadow-card-hover']) }}>
    <div class="aspect-[4/3] bg-gradient-to-br {{ $imageClass }}">
        <div class="flex h-full items-center justify-center p-6 text-center">
            <span class="rounded-button bg-white/80 px-4 py-2 text-sm font-semibold text-cimuning-charcoal shadow-card">{{ $category }}</span>
        </div>
    </div>
    <div class="space-y-4 p-5">
        <div class="flex flex-wrap items-center gap-2">
            <x-category-badge>{{ $category }}</x-category-badge>
            @if ($verified)
                <x-verified-badge />
            @endif
        </div>
        <div>
            <h3 class="text-xl font-bold leading-snug text-cimuning-charcoal">{{ $name }}</h3>
            <p class="mt-2 line-clamp-3 text-base leading-7 text-cimuning-slate">{{ $description }}</p>
        </div>
        <p class="text-sm font-medium text-cimuning-slate">{{ $location }}</p>
        <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
            <x-secondary-button href="{{ $slug ? route('umkm.show', $slug) : route('umkm.index') }}" class="w-full">Lihat Detail</x-secondary-button>
            <x-whatsapp-button href="{{ $whatsappUrl ?? 'https://wa.me/6281234567890' }}" class="w-full">Chat WhatsApp</x-whatsapp-button>
        </div>
    </div>
</article>
