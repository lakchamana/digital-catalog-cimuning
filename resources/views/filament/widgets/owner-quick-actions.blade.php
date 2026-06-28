<x-filament-widgets::widget class="fi-owner-quick-actions">
    <x-filament::section
        heading="Aksi Cepat"
        description="Kelola profil usaha, katalog, dan keamanan akun dari satu tempat."
        icon="heroicon-o-bolt"
    >
        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            <x-filament::button
                :href="$profileUrl"
                tag="a"
                icon="heroicon-o-building-storefront"
                class="w-full justify-center"
            >
                {{ $profileLabel }}
            </x-filament::button>

            @if ($productsUrl)
                <x-filament::button
                    :href="$productsUrl"
                    tag="a"
                    color="gray"
                    icon="heroicon-o-shopping-bag"
                    class="w-full justify-center"
                >
                    {{ $productsLabel }}
                </x-filament::button>
            @endif

            @if ($publicProfileUrl)
                <x-filament::button
                    :href="$publicProfileUrl"
                    tag="a"
                    target="_blank"
                    rel="noopener noreferrer"
                    color="gray"
                    icon="heroicon-o-arrow-top-right-on-square"
                    class="w-full justify-center"
                >
                    Lihat Profil Publik
                </x-filament::button>
            @endif

            <x-filament::button
                :href="$securityUrl"
                tag="a"
                color="gray"
                icon="heroicon-o-shield-check"
                class="w-full justify-center"
            >
                Keamanan Akun
            </x-filament::button>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
