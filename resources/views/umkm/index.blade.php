<x-public-layout :title="$pageTitle ?? 'Direktori UMKM'">
    <h1 class="sr-only">{{ $pageTitle ?? 'Direktori UMKM' }}</h1>

    <livewire:public.umkm-search :initial-category="$category ?? null" />
</x-public-layout>
