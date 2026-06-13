<?php

namespace App\Filament\Widgets;

use App\Models\LeadEvent;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class LeadStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $user = Filament::auth()->user();
        $leadQuery = LeadEvent::query()->visibleTo($user);
        $topUmkm = (clone $leadQuery)
            ->selectRaw('umkm_id, count(*) as aggregate')
            ->with('umkm:id,name')
            ->groupBy('umkm_id')
            ->orderByDesc('aggregate')
            ->first();

        return [
            Stat::make('Klik WhatsApp', (clone $leadQuery)->where('type', 'whatsapp')->count())
                ->description('Minat kontak langsung')
                ->color('success')
                ->icon('heroicon-o-chat-bubble-left-right'),
            Stat::make('Klik Maps', (clone $leadQuery)->where('type', 'maps')->count())
                ->description('Minat kunjungan lokasi')
                ->color('info')
                ->icon('heroicon-o-map-pin'),
            Stat::make('Klik 7 hari terakhir', (clone $leadQuery)->where('created_at', '>=', Carbon::now()->subDays(7))->count())
                ->description('Aktivitas terbaru')
                ->color('warning')
                ->icon('heroicon-o-chart-bar'),
            Stat::make('UMKM paling diminati', $topUmkm?->umkm?->name ?? '-')
                ->description($topUmkm ? "{$topUmkm->aggregate} klik kontak" : 'Belum ada lead')
                ->color('gray')
                ->icon('heroicon-o-building-storefront'),
        ];
    }
}
