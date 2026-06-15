<?php

namespace App\Livewire\Public;

use App\Models\Category;
use App\Models\Umkm;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class UmkmSearch extends Component
{
    use WithPagination;

    #[Url(as: 'search', except: '')]
    public string $search = '';

    #[Url(as: 'category', except: '')]
    public string $category = '';

    #[Url(as: 'rw', except: '')]
    public string $rw = '';

    #[Url(as: 'verified', except: true)]
    public bool $verified = true;

    #[Url(as: 'services', except: [])]
    public array $services = [];

    #[Url(as: 'sort', except: 'latest')]
    public string $sort = 'latest';

    #[Url(as: 'perPage', except: 9)]
    public int|string $perPage = 9;

    public function mount(?string $initialCategory = null): void
    {
        if ($this->category === '' && $initialCategory) {
            $this->category = $initialCategory;
        }

        $this->normalizeFilters();
    }

    public function updated(string $property): void
    {
        $this->normalizeFilters();

        if ($property !== 'page') {
            $this->resetPage();
        }
    }

    public function submitSearch(): void
    {
        $this->normalizeFilters();
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'category', 'rw', 'services']);
        $this->verified = true;
        $this->sort = 'latest';
        $this->perPage = 9;
        $this->resetPage();
    }

    public function clearFilter(string $filter): void
    {
        if (str_starts_with($filter, 'service:')) {
            $service = substr($filter, strlen('service:'));
            $this->services = collect($this->services)
                ->reject(fn ($value) => $value === $service)
                ->values()
                ->all();
        } else {
            match ($filter) {
                'search' => $this->search = '',
                'category' => $this->category = '',
                'rw' => $this->rw = '',
                'verified' => $this->verified = true,
                'sort' => $this->sort = 'latest',
                'perPage' => $this->perPage = 9,
                default => null,
            };
        }

        $this->normalizeFilters();
        $this->resetPage();
    }

    public function render(): View
    {
        $this->normalizeFilters();

        $categories = $this->categories();
        $rws = $this->rws();
        $umkms = $this->umkms();

        return view('livewire.public.umkm-search', [
            'categories' => $categories,
            'rws' => $rws,
            'umkms' => $umkms,
            'activeFilterCount' => $this->activeFilterCount(),
            'activeFilters' => $this->activeFilters($categories),
            'resultHeading' => $this->resultHeading($categories),
        ]);
    }

    protected function categories(): Collection
    {
        return Category::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    protected function rws(): Collection
    {
        return Umkm::query()
            ->where('is_active', true)
            ->where('status', 'verified')
            ->whereNotNull('rw')
            ->distinct()
            ->orderBy('rw')
            ->pluck('rw');
    }

    protected function umkms(): LengthAwarePaginator
    {
        $search = trim($this->search);
        $allowedServices = $this->allowedServices();
        $services = $this->validatedServices();

        return Umkm::query()
            ->with('category')
            ->where('is_active', true)
            ->where('status', 'verified')
            ->when($this->category !== '', fn (Builder $query) => $query->whereHas(
                'category',
                fn (Builder $categoryQuery) => $categoryQuery->where('slug', $this->category),
            ))
            ->when($this->rw !== '', fn (Builder $query) => $query->where('rw', $this->rw))
            ->when($search !== '', function (Builder $query) use ($search) {
                $query->where(function (Builder $nested) use ($search) {
                    $nested->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('rw', 'like', "%{$search}%")
                        ->orWhereHas('category', fn (Builder $categoryQuery) => $categoryQuery->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('products', function (Builder $productQuery) use ($search) {
                            $productQuery->where('is_active', true)
                                ->where(function (Builder $productNested) use ($search) {
                                    $productNested->where('name', 'like', "%{$search}%")
                                        ->orWhere('description', 'like', "%{$search}%");
                                });
                        });
                });
            })
            ->when($services !== [], function (Builder $query) use ($services, $allowedServices) {
                foreach ($services as $service) {
                    $query->where($allowedServices[$service], true);
                }
            })
            ->tap(fn (Builder $query) => $this->applySort($query))
            ->paginate($this->validatedPerPage());
    }

    protected function applySort(Builder $query): void
    {
        match ($this->sort) {
            'az' => $query->orderBy('name'),
            'popular' => $query->orderByDesc('view_count')->latest(),
            default => $query->latest(),
        };
    }

    protected function validatedPerPage(): int
    {
        $perPage = (int) $this->perPage;

        return in_array($perPage, [9, 18, 27], true) ? $perPage : 9;
    }

    protected function allowedServices(): array
    {
        return [
            'delivery' => 'service_delivery',
            'cod' => 'service_cod',
            'custom_order' => 'service_custom_order',
            'physical_store' => 'has_physical_store',
        ];
    }

    protected function activeFilterCount(): int
    {
        return collect([
            $this->search !== '',
            $this->category !== '',
            $this->rw !== '',
            $this->sort !== 'latest',
            (int) $this->perPage !== 9,
            count($this->validatedServices()) > 0,
            ! $this->verified,
        ])->filter()->count();
    }

    /**
     * @return array<int, array{key: string, label: string, value: string}>
     */
    protected function activeFilters(Collection $categories): array
    {
        return collect([
            $this->search !== '' ? [
                'key' => 'search',
                'label' => 'Kata kunci',
                'value' => $this->search,
            ] : null,
            $this->category !== '' ? [
                'key' => 'category',
                'label' => 'Kategori',
                'value' => $categories->firstWhere('slug', $this->category)?->name ?? $this->category,
            ] : null,
            $this->rw !== '' ? [
                'key' => 'rw',
                'label' => 'RW',
                'value' => $this->rw,
            ] : null,
            ! $this->verified ? [
                'key' => 'verified',
                'label' => 'Status',
                'value' => 'Semua yang tampil publik',
            ] : null,
            ...collect($this->validatedServices())->map(fn (string $service) => [
                'key' => 'service:'.$service,
                'label' => 'Layanan',
                'value' => $this->serviceLabels()[$service] ?? $service,
            ])->all(),
            $this->sort !== 'latest' ? [
                'key' => 'sort',
                'label' => 'Urutan',
                'value' => match ($this->sort) {
                    'az' => 'A-Z',
                    'popular' => 'Populer',
                    default => 'Terbaru',
                },
            ] : null,
            (int) $this->perPage !== 9 ? [
                'key' => 'perPage',
                'label' => 'Jumlah',
                'value' => $this->perPage.' per halaman',
            ] : null,
        ])->filter()->values()->all();
    }

    protected function resultHeading(Collection $categories): string
    {
        if ($this->search !== '') {
            return 'Hasil untuk "'.$this->search.'"';
        }

        if ($this->category !== '') {
            return 'UMKM kategori '.($categories->firstWhere('slug', $this->category)?->name ?? $this->category);
        }

        if ($this->rw !== '') {
            return 'UMKM di '.$this->rw;
        }

        if (count($this->validatedServices()) > 0) {
            return 'UMKM sesuai layanan';
        }

        return 'Semua UMKM verified';
    }

    /**
     * @return array<string, string>
     */
    protected function serviceLabels(): array
    {
        return [
            'delivery' => 'Delivery',
            'cod' => 'COD',
            'custom_order' => 'Custom order',
            'physical_store' => 'Toko fisik',
        ];
    }

    /**
     * @return array<int, string>
     */
    protected function validatedServices(): array
    {
        return collect($this->services)
            ->filter(fn ($service) => is_string($service) && array_key_exists($service, $this->allowedServices()))
            ->unique()
            ->values()
            ->all();
    }

    protected function normalizeFilters(): void
    {
        $this->search = trim($this->search);

        if (! in_array($this->sort, ['latest', 'az', 'popular'], true)) {
            $this->sort = 'latest';
        }

        $this->perPage = $this->validatedPerPage();
        $this->services = $this->validatedServices();

        if ($this->category !== '' && ! $this->validCategorySlug($this->category)) {
            $this->category = '';
        }

        if ($this->rw !== '' && ! $this->validRw($this->rw)) {
            $this->rw = '';
        }
    }

    protected function validCategorySlug(string $slug): bool
    {
        return Category::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->whereHas('umkms', fn (Builder $umkmQuery) => $umkmQuery
                ->where('is_active', true)
                ->where('status', 'verified'))
            ->exists();
    }

    protected function validRw(string $rw): bool
    {
        return Umkm::query()
            ->where('is_active', true)
            ->where('status', 'verified')
            ->where('rw', $rw)
            ->exists();
    }
}
