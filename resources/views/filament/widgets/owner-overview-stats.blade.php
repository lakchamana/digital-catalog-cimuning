@php
    $pollingInterval = $this->getPollingInterval();
@endphp

<x-filament-widgets::widget
    data-owner-stats-compact
    :attributes="
        (new \Illuminate\View\ComponentAttributeBag)
            ->merge([
                'wire:poll.' . $pollingInterval => $pollingInterval ? true : null,
            ], escape: false)
            ->class([
                'fi-wi-stats-overview',
                'fi-owner-overview-stats',
            ])
    "
>
    {{ $this->content }}

    <style>
        .fi-owner-overview-stats .fi-wi-stats-overview-stat {
            border-radius: 0.5rem;
        }

        @media (max-width: 639px) {
            .fi-owner-overview-stats .fi-grid {
                gap: 0.75rem;
            }

            .fi-owner-overview-stats .fi-wi-stats-overview-stat {
                min-height: 6.5rem;
                padding: 1rem;
            }

            .fi-owner-overview-stats .fi-wi-stats-overview-stat-content {
                gap: 0.5rem;
            }

            .fi-owner-overview-stats .fi-wi-stats-overview-stat-label-ctn {
                align-items: flex-start;
                gap: 0.375rem;
            }

            .fi-owner-overview-stats .fi-wi-stats-overview-stat-label {
                font-size: 0.75rem;
                line-height: 1.25;
            }

            .fi-owner-overview-stats .fi-wi-stats-overview-stat-value {
                overflow-wrap: anywhere;
                font-size: 1.125rem;
                line-height: 1.35;
            }

            .fi-owner-overview-stats .fi-wi-stats-overview-stat-description {
                display: none;
            }
        }
    </style>
</x-filament-widgets::widget>
