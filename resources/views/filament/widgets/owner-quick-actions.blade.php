<x-filament-widgets::widget class="fi-owner-quick-actions">
    <x-filament::section
        heading="Aksi Cepat"
        description="Pilih tindakan yang ingin Anda lakukan."
        icon="heroicon-o-bolt"
    >
        <nav class="owner-action-list" data-owner-action-list aria-label="Aksi cepat owner">
            @foreach ($actions as $action)
                <a
                    href="{{ $action['url'] }}"
                    @if ($action['external'])
                        target="_blank"
                        rel="noopener noreferrer"
                    @endif
                    @class([
                        'owner-action-link',
                        'owner-action-link-primary' => $action['primary'],
                    ])
                >
                    <span class="owner-action-icon" aria-hidden="true">
                        <x-filament::icon :icon="$action['icon']" />
                    </span>

                    <span class="owner-action-copy">
                        <span class="owner-action-label">{{ $action['label'] }}</span>
                        <span class="owner-action-description">{{ $action['description'] }}</span>
                    </span>

                    <x-filament::icon
                        icon="heroicon-o-chevron-right"
                        class="owner-action-chevron"
                        aria-hidden="true"
                    />
                </a>
            @endforeach
        </nav>

        <style>
            .owner-action-list {
                display: grid;
                grid-template-columns: minmax(0, 1fr);
                gap: 0.5rem;
            }

            .owner-action-link {
                display: grid;
                grid-template-columns: 2.25rem minmax(0, 1fr) 1.25rem;
                min-height: 3.5rem;
                align-items: center;
                gap: 0.75rem;
                border: 1px solid color-mix(in oklab, var(--gray-500) 18%, transparent);
                border-radius: 0.5rem;
                padding: 0.625rem 0.75rem;
                background: transparent;
                transition: border-color 150ms ease, background-color 150ms ease;
            }

            .owner-action-link:hover {
                border-color: color-mix(in oklab, var(--gray-500) 35%, transparent);
                background-color: color-mix(in oklab, var(--gray-100) 65%, transparent);
            }

            .owner-action-link:focus-visible {
                outline: 2px solid var(--primary-600);
                outline-offset: 2px;
            }

            .owner-action-icon {
                display: inline-flex;
                width: 2.25rem;
                height: 2.25rem;
                align-items: center;
                justify-content: center;
                border-radius: 0.5rem;
                background-color: color-mix(in oklab, var(--gray-200) 70%, transparent);
                color: var(--gray-600);
            }

            .owner-action-icon .fi-icon,
            .owner-action-chevron {
                width: 1.125rem;
                height: 1.125rem;
            }

            .owner-action-link-primary .owner-action-icon {
                background-color: color-mix(in oklab, var(--primary-100) 80%, transparent);
                color: var(--primary-700);
            }

            .owner-action-copy {
                display: grid;
                min-width: 0;
                gap: 0.125rem;
            }

            .owner-action-label {
                color: var(--gray-950);
                font-size: 0.875rem;
                font-weight: 600;
                line-height: 1.25;
            }

            .owner-action-description {
                color: var(--gray-500);
                font-size: 0.75rem;
                line-height: 1.3;
            }

            .owner-action-chevron {
                color: var(--gray-400);
            }

            .dark .owner-action-link:hover {
                background-color: color-mix(in oklab, var(--gray-800) 65%, transparent);
            }

            .dark .owner-action-icon {
                background-color: color-mix(in oklab, var(--gray-700) 65%, transparent);
                color: var(--gray-300);
            }

            .dark .owner-action-link-primary .owner-action-icon {
                background-color: color-mix(in oklab, var(--primary-900) 65%, transparent);
                color: var(--primary-300);
            }

            .dark .owner-action-label {
                color: white;
            }

            @media (min-width: 640px) {
                .owner-action-list {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                    gap: 0.75rem;
                }

                .owner-action-link {
                    min-height: 4rem;
                    padding: 0.75rem;
                }
            }
        </style>
    </x-filament::section>
</x-filament-widgets::widget>
