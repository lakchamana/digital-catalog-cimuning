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
    public int $perPage = 9;

    public function mount(?string $initialCategory = null): void
    {
        if ($this->category === '' && $initialCategory) {
            $this->category = $initialCategory;
        }
    }

    public function updated(string $property): void
    {
        if ($property !== 'page') {
            $this->resetPage();
        }
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'category', 'rw', 'services']);
        $this->verified = true;
        $this->sort = 'latest';
        $this->perPage = 9;
        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.public.umkm-search', [
            'categories' => $this->categories(),
            'rws' => $this->rws(),
            'umkms' => $this->umkms(),
            'activeFilterCount' => $this->activeFilterCount(),
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
        $services = collect($this->services)
            ->filter(fn ($service) => array_key_exists($service, $allowedServices))
            ->values()
            ->all();

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
        return in_array($this->perPage, [9, 18, 27], true) ? $this->perPage : 9;
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
            $this->perPage !== 9,
            count($this->services) > 0,
            ! $this->verified,
        ])->filter()->count();
    }
}
